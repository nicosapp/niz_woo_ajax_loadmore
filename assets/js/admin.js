(function($){
	$(document).ready(function(){
		$('#niz-button-shortcode-generator').click(function(){
			var container=$('#shortcode-generated-value'),
				atts_value="",
				atts={};
				
			$('.niz-panel-wrapper').find('.niz-atts').each(function(){
				var value="";
				if( $(this).attr('type')=='checkbox') value = $(this).prop('checked') ? 1 : 0;
				else value = $(this).val();
				atts[$(this).attr('name')]=value;
			});

			$.each(atts,function(att,val){
				if(!val || val.length==0) return;
				atts_value+=att+'='+val+' ';
			})
			var shortcode_value='['+niz_ad_params.prefix+' '+atts_value.trim()+']';
			container.text(shortcode_value);
		})
	});
	
}(jQuery))