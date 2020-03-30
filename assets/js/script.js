(function ($) {
	var Load_More_Product={
    ajax: function(){
      $(document).on('click','.load-more-ajax.products',function(){

        var button = $(this),
            data = {
          'action': 'niz_loadmore_products',
          'query': niz_woo_posts,
          'loop' : niz_woo_loop,
          'page' : niz_woo_current_page,
          'security' : ajax_loadmore.loadmore_nonce,
        };
      console.log(ajax_loadmore);
        button.addClass('loading');
        var ajaxpreloader=$(document).find('ul.ajax-preloader'),
          result_count=$('.niz-result-count');


        ajaxpreloader.slideDown();
        $.ajax({ 
          url : ajax_loadmore.ajaxurl,
          data : data,
          type : 'POST',
          success : function( response ){
            console.log(response);
            if( response.success ) { 
              ajaxpreloader.hide();
              button.removeClass( 'loading' ).closest('.woocommerce').find('ul.products').append(response.data.html); // insert new posts
              result_count.replaceWith(response.data.result_count);
              niz_woo_current_page++;
     
              if ( niz_woo_current_page == niz_woo_max_page ) 
                button.hide(); // if last page, remove the button
            } else {
              button.hide(); // if no data, remove the button as well
            }
          }
        });
      });

    }
  };
  $(document).ready(function(){ Load_More_Product.ajax(); });

})(jQuery);