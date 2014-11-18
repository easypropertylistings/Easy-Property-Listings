jQuery(document).ready(function($) {
	if( $('form#post').length ) {
		jQuery("form#post").validationEngine();
	}
	$('.epl-price-bar').hover(
		   function(){ $(this).addClass('shine') },
		   function(){ $(this).removeClass('shine') }
	)
});
