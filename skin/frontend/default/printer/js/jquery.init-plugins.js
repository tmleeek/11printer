if(!window.application){
	window.application = {};
}

window.application.initPlugins = function(){
	'use strict';

	

	var win = jQuery(window),
		tabset = jQuery('.tabset'),
		itemsTabset = tabset.find('li'),
		itemCount = itemsTabset.length + 1;

/* tab elements z-index */
	function tabIndex(){
		itemsTabset.each(function(){
			jQuery(this).css('z-index', itemCount - 1);
			itemCount--;
		});
	}
	tabIndex();
/* tab elements z-index */

	if(typeof jQuery.fn.scrollGallery === 'function'){
		jQuery('.visual').scrollGallery({
			mask: '.holder',
			btnPrev: '.btn-prev',
			btnNext: '.btn-next',
			stretchSlideToMask: true,
			generatePagination: 'div.pager',
			pagerListItem: '<li><button></button></li>',
			step: 1
		});

		jQuery('.footer-bar .prmodels').scrollGallery({
			mask: '.mask',
			btnPrev: '.btn-prev',
			btnNext: '.btn-next',
			step: 1
		});
		
jQuery('.footer-bar .prgoods').scrollGallery({
			mask: '.mask',
			btnPrev: '.btn-prev',
			btnNext: '.btn-next',
			step: 1
		});
	}

	if(typeof jQuery.fn.openClose === 'function'){
		jQuery('.footer-bar > ul > li').openClose({
			effect: 'slide',
			hideOnClickOutside: false,
			animStart: function(){
				if(typeof jQuery.fn.scrollGallery === 'function'){
					jQuery('.footer-bar .product-list').scrollGallery({
						mask: '.mask',
						btnPrev: '.btn-prev',
						btnNext: '.btn-next',
						step: 1
					});
				}
			}
		});

		jQuery('#sidebar .block.open-close').openClose({
			effect: 'slide',
			activeClass:'active',
			opener:'.opener',
			slider:'.holder'
		});

		jQuery('.product-feeds .btn-holder, .questions-section .btn-holder').openClose({
			effect: 'fade',
			activeClass:'expanded',
			hideOnClickOutside: true
		});

		jQuery('.question-list > li').openClose({
			effect: 'slide',
			activeClass:'expanded',
			hideOnClickOutside: false
		});

	}

	

	if(typeof jQuery.fn.customCheckbox === 'function'){
		jQuery('input:checkbox').customCheckbox();
	}

	if(typeof jQuery.fn.contentTabs === 'function'){
		jQuery('ul.tabset').contentTabs({
			addToParent: true,
			autoHeight: true,
			effect: 'none'
		});
	}

}
