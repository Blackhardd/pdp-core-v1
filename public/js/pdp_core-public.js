(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function(){
		pdp_init_sliders();
	});

	function is_json(str){
		try{
			JSON.parse(str);
		}
		catch(e){
			return false;
		}

		return true;
	}

	function pdp_ajax_query(form_data, beforeSendAction = false, succesAction = false){
		let data = null;
		if(form_data instanceof jQuery){
			data = form_data.serialize();
		}
		else{
			data = form_data;
		}

		$.ajax({
			method: 'POST',
			url: pdp.ajaxurl,
			data: data,
			beforeSend: function(xhr){
				if(beforeSendAction){
					beforeSendAction();
				}
			},
			success: function(data) {
				if(succesAction){
					if(is_json(data)){
						data = JSON.parse(data);
					}

					succesAction(data);
				}
			}
		});
	}


	function pdp_init_sliders(){
		if($('.service-categories__slider, .salons-slider, .teamSlider').length){
			$('.service-categories__slider, .salons-slider, .teamSlider').each(function(i){
				let offset = $(this).offset().left;
				$(this).width('calc(100vw - ' + offset + 'px)');
			});

			$('.service-categories__slider, .salons-slider').slick({
				infinite: false,
				slidesToShow: 4,
				slidesToScroll: 1,
				variableWidth: true,
				autoplay: false,
				autoplaySpeed: 4000,
				swipeToSlide: true,
				arrows: false,
				responsive: [{
					breakpoint: 1024,
					settings: {
						slidesToShow: 2
					}
				},
					{
						breakpoint: 550,
						settings: {
							slidesToShow: 1
						}
					}
				]
			});
		}
	}
})( jQuery );
