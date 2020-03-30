<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$woo_shortcodes=array(
	'product',
	'product_page',
	'product_category',
	'product_categories',
	'add_to_cart',
	'add_to_cart_url',
	'products',
	'recent_products',
	'sale_products',
	'best_selling_products',
	'top_rated_products',
	'featured_products',
	'product_attribute',
	'related_products'
);
$settings_field='niz_woo_ajload';
$settings_field_array=$settings_field.'[]';
?>

<div class="wrap">
<?php settings_errors(); ?>
<h1>Niz Products Ajax Load More for Woocommerce</h1>
<p><?php _e('Activate Ajax Load More Products for the following items','niz-woopc'); ?></p>

<form method="post" action="options.php"> 
	<?php settings_fields( 'niz_woo_ajload' ); ?>
	<?php do_settings_sections( 'niz_woo_ajload' ); ?>

	<h2><?php _e('Activate Ajax Load More Products'); ?></h2>

	<table class="form-table">
		<?php $checked=get_option($settings_field)['shop_page'] ? 'checked' : ''; ?>
		<tr>
			<th scope="row">
				<label><?php _e('Activate on Shop page','niz-woo-ajload');?></label>
			</th>
			<td>
				<input type="checkbox" name="<?php printf('%s[%s]', $settings_field, 'shop_page'); ?>" <?php echo $checked; ?>/>
			</td>
		</tr>
		<?php $checked=get_option($settings_field)['woo_shortcodes'] ? 'checked' : ''; ?>
		<tr>
			<th scope="row">
				<label><?php _e('Activate with Shortcodes','niz-woo-ajload');?></label>
			</th>
			<td>
				<input type="checkbox" name="<?php printf('%s[%s]', $settings_field, 'woo_shortcodes'); ?>" <?php echo $checked; ?>/>
			</td>
		</tr>
	</table>

	<h2><?php _e('Customization'); ?></h2>

	<table class="form-table">

		<tr>
			<?php 
				$value=get_option($settings_field)['button_text']; 
				$value= $value ? $value : "Load More";
			?>
			<th scope="row">
				<label><?php _e('Load More button text','niz-woo-ajload');?></label>
			</th>
			<td>
				<input type="text" name="<?php printf('%s[%s]', $settings_field, 'button_text'); ?>" value="<?php echo $value; ?>"/>
			</td>
		</tr>
		<tr>
			<?php 
				$value=get_option($settings_field)['button_class']; 
				$value= $value ? $value : "";
			?>
			<th scope="row">
				<label><?php _e('Load More button custom class','niz-woo-ajload');?></label>
			</th>
			<td>
				<input type="text" size="100" name="<?php printf('%s[%s]', $settings_field, 'button_class'); ?>" value="<?php echo $value; ?>"/>
				<p class="description">Add your custom classes with a comma separation. ex: class1, class2,...</p> 
			</td>
		</tr>
	</table>

		
	<?php submit_button(); ?>
</form>
</div>