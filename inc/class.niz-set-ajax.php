<?php

class NizSetAjax{
	public static $settings;
	public static $woo_shortcodes=array(
			'product',
			'product_page',
			'product_category',
			'product_categories',
			'products',
			'recent_products',
			'sale_products',
			'best_selling_products',
			'top_rated_products',
			'featured_products',
			'product_attribute',
			'related_products',
		);
	public function __construct() {
		self::$settings=NizWooAjaxLoadMore::get_settings();
		$this->ajax();
		
	}
	public function ajax(){
		if(self::$settings['shop_name'] && is_shop()) $this->set_ajax();
		else if(self::$settings['woo_shortcodes']) $this->set_ajax();
		
	}
	public function set_ajax(){
		foreach(self::$woo_shortcodes as $name) :
			add_action("woocommerce_shortcode_before_{$name}_loop",
				function($atts){ $this->shortcode_product_loop_ajax_loadmore_handler($atts,$name); },10, 1);
		endforeach;
		add_action('woocommerce_after_shop_loop', array( &$this, 'products_loop_ajax_loadmore_button'), 20);
		remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10 ); //on enleve la pagination
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		add_action('woocommerce_after_shop_loop', array( &$this, 'products_loop_setup_ajax_loadmore_params'),10);

		add_action('wp_ajax_niz_loadmore_products', array( &$this, 'loadmore_products_ajax_handler') ); 
		add_action('wp_ajax_nopriv_niz_loadmore_products', array( &$this, 'loadmore_products_ajax_handler') ); 

		add_action('niz_product_loop_result_count', array( &$this, 'product_loop_result_count_content') );
	}
	
	public function shortcode_product_loop_ajax_loadmore_handler($atts, $shortcode_name){
		$shortcode = new WC_Shortcode_Products( $atts, $shortcode_name);
		$q = new WP_Query( $shortcode->get_query_args() );
		set_transient('niz_ajax_loadmore_products_custom_query',$q, 10);
	}

	public function loadmore_products_ajax_handler(){
		$postdata=wp_unslash($_POST);

	    if( !wp_verify_nonce($postdata['security'], 'loadmore_nonce_value') ){
	        wp_send_json_error();
	        die();
	    }
		$args =json_decode( wp_unslash($postdata['query']), true ) ;
		$loop_config =json_decode( wp_unslash( $postdata['loop']), true ) ;
		$args['paged'] = $postdata['page'] + 1; // we need next page to be loaded
		
		$ajax_query = new WP_Query( $args );
	 	ob_start();
	 	wc_setup_loop($loop_config);

		if( $ajax_query->have_posts() ) :
	 	
			// run the loop
			while( $ajax_query->have_posts() ): $ajax_query->the_post();

				wc_get_template_part( 'content', 'product' );
	 
			endwhile;
	 
		endif;

		$loop_content=ob_get_clean();
		ob_start();
		//result_count
		$this->ajax_query_results_count_state($ajax_query->found_posts, $args['posts_per_page'], $args['paged']);
		$result_count=ob_get_clean();

		wp_send_json_success(array('html'=>$loop_content, 'result_count'=>$result_count) );
	 	die(); 
	}

	public function products_loop_setup_ajax_loadmore_params(){
		// GET_TRANSIENT : sinon override le woocommerce shortcode : solution faire 2 ajaxloader : 1 shortcode et 1 shop
		global $wp_query;

		$q=get_transient('niz_ajax_loadmore_products_custom_query') ? get_transient('niz_ajax_loadmore_products_custom_query') : $wp_query;

		if($q->is_main_query() || get_transient('niz_ajax_loadmore_products_custom_query')): ?>
			<script>
				var niz_woo_posts = '<?php echo json_encode( $q->query_vars ) ?>',
				niz_woo_loop = '<?php echo json_encode( $woocommerce_loop ) ?>',
			    niz_woo_current_page = <?php echo isset($q->query_vars['paged']) && !empty($q->query_vars['paged']) ? $q->query_vars['paged'] : 1; ?>,
			    niz_woo_max_page = <?php echo $q->max_num_pages; ?>,
			    niz_woo_found_post = <?php echo $q->found_posts; ?>,
			    niz_woo_post_count = <?php echo $q->post_count; ?>;
			</script>
		<?php endif;
	}

	public function products_loop_ajax_loadmore_button(){
		global $wp_query;
		$q=get_transient('niz_ajax_loadmore_products_custom_query') ? get_transient('niz_ajax_loadmore_products_custom_query') : $wp_query;
		delete_transient('niz_ajax_loadmore_products_custom_query');	
		
		?><div class="nav-ajax-loadmore-wrapper"><?php

		$this->ajax_query_results_count_state($q->found_posts, $q->get('posts_per_page'), 1);

		if (  $q->max_num_pages > 1 ) :
		    //loadmore button
		    echo $this->get_loadmore_nav_button('products');

		endif;

		?></div><?php
	}

	public function get_loadmore_nav_button($class){ 
		$class_str=(is_array($class)) ? implode(' ',$class) : $class;
		$button_class=explode(',',self::$settings['button_class']);
		$button_class=count($button_class) > 0 ? implode(' ',$button_class) : '';
		$class_str.=' '.$button_class;
		$button_text=self::$settings['button_text'];
		ob_start();
		?>
			<nav class="ajax-load-more-nav">
			    <button class="dokan-btn dokan-btn-theme load-more-ajax btn <?php echo $class_str; ?>"><?php printf('%s',$button_text); ?></button>
			</nav>
		<?php return ob_get_clean();
	}
	
	public function product_loop_result_count_content($variables){
		//number of posts out of total
	    niz_get_template_part('result-count.php',$variables);

	}

	public function ajax_query_results_count_state($total, $per_page, $current){
		$variables = array(
			'total'    => $total,
			'per_page' => $per_page,
			'current'  => $current,
		);
		do_action('niz_product_loop_result_count',$variables);
	}

}