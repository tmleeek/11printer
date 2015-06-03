var amshopby_working = false;
var amshopby_blocks = {};

function amshopby_ajax_init() {
    $$('div.block-layered-nav a', amshopby_toolbar_selector + ' a').each(function(e) {
        var p = e.up();
        if (p.hasClassName('amshopby-cat-level-1') || p.hasClassName('amshopby-cat-level-2') || p.hasClassName('clearer')) {
            return
        }
        e.onclick = function() {
            if (this.hasClassName('checked')) {
                this.removeClassName('checked')
            } else {
                this.addClassName('checked')
            }
            var s = this.href;
            if (s.indexOf('#') > 0) {
                s = s.substring(0, s.indexOf('#'))
            }
            amshopby_ajax_request(s);
            return false
        }
    });
    $$('div.block-layered-nav select.amshopby-ajax-select', amshopby_toolbar_selector + ' select').each(function(e) {
        e.onchange = 'return false';
        Event.observe(e, 'change', function(e) {
            amshopby_ajax_request(this.value);
            Event.stop(e)
        })
    });
    if (typeof(amshopby_external) != 'undefined') {
        amshopby_external()
    }
}

function amshopby_ajax_request(url) {
	console.log('req-begin');
    var block = $$('div.amshopby-page-container')[0];
    if (block) {
        block.scrollTo()
    }
    amshopby_working = true;
    $$('div.amshopby-overlay').each(function(e) {
        e.show()
    });
    var request = new Ajax.Request(url, {
        method: 'get',
        parameters: {
            'is_ajax': 1
        },
        onSuccess: function(response) {
            data = response.responseText;
            if (!data.isJSON()) {
                setLocation(url)
            }
            data = data.evalJSON();
            if (!data.page || !data.blocks) {
                setLocation(url)
            }
            amshopby_ajax_update(data);
            amshopby_working = false
        },
        onFailure: function() {
            amshopby_working = false;
            setLocation(url)
        }
    })
	console.log('req-end');
}
	function parseValue() {
		jQuery(this).val(jQuery(this).siblings('.spinner').val());
	}
	
function amshopby_ajax_update(data) {
    var tmp = document.createElement('div');
    tmp.innerHTML = data.page;
    var block = $$('div.amshopby-page-container')[0];
    if (block) {
        block.parentNode.replaceChild(tmp.firstDescendant(), block)
    } else {
        alert('Please add the <div class="amshopby-page-container"> to the list template as per installtion guide. Enable template hints to find the right file if needed.')
    }
    var blocks = data.blocks;
    for (id in blocks) {
        var html = blocks[id];
        if (html) {
            tmp.innerHTML = html
        }
        block = $$('div.' + id)[0];
        if (html) {
            if (!block) {
                block = amshopby_blocks[id];
                amshopby_blocks[id] = null
            }
            if (block) {
                block.parentNode.replaceChild(tmp.firstDescendant(), block)
            }
        } else {
            if (block) {
                var empty = document.createTextNode('');
                amshopby_blocks[id] = empty;
                block.parentNode.replaceChild(empty, block)
            }
        }
    }
    amshopby_start();
    amshopby_ajax_init();
	console.log('updated');
	jQuery.noConflict();
	if(typeof jQuery.fn.spinner === 'function'){
		jQuery( ".spinner" ).spinner({ 
			min: 1,
			spin: function() {
				jQuery(this).siblings('.spinner-text').val(jQuery(this).val());
				jQuery(this).parent().parent().parent().find('input.hidden').val(jQuery(this).val());
			},
			stop: function() {
				jQuery(this).siblings('.spinner-text').val(jQuery(this).val());
				jQuery(this).parent().parent().parent().find('input.hidden').val(jQuery(this).val());
			}
		}).parent().append('<input class="spinner-text" type="text">');
		jQuery('.spinner-text').each(parseValue);
		jQuery('.spinner-text').change(function() {
			jQuery(this).siblings('.spinner').val(parseInt(jQuery(this).val()));
		});
		jQuery('.spinner-text').blur(parseValue);
	}

	if(typeof jQuery.fn.spinner === 'function'){
		jQuery( ".category-products #slider-range" ).slider({
			range: true,
			min: 0,
			max: 500,
			values: [ 75, 300 ],
			slide: function( event, ui ) {
				jQuery("#min-amount").val( ui.values[ 0 ] );
				jQuery("#max-amount").val( ui.values[ 1 ] );
			}
		});
		jQuery( "#min-amount" ).val( jQuery( "#slider-range" ).slider( "values", 0 ) );
		jQuery( "#max-amount" ).val( jQuery( "#slider-range" ).slider( "values", 1 ) );
	}
	jQuery('.amshopby-filters-top #narrow-by-list ol li a.amshopby-attr-selected').parent().addClass('amshopby-attr-selected');
	
}
document.observe("dom:loaded", function() {
    amshopby_ajax_init()
});


var amshopby_toolbar_selector = 'div.toolbar';

function amshopby_external() {
    //add here all external scripts for page reloading
    // like igImgPreviewInit(); 
}