if (!window.application) {
	window.application = {}
}

window.application.addClass = {
	config: {
		activeClass: 'active',
		fixedClass : 'fixed',
		hiddenClass : 'hidden',
		largeClass : 'large',
		animClass : 'animated',
		animSpeed : 800
	},
	createElements: function(){
		var self = this;
		this.html = jQuery('html');
		this.body = jQuery('body');
		this.header = jQuery('.header');
		this.homeHeader = jQuery('.cms-home .header');
		this.headerAlter = jQuery('.header.container.alter');
		this.footer = jQuery('#footer');
		this.win = jQuery(window);
		this.wrapper = jQuery('#wrapper');
		this.main = jQuery('#main');
		this.adsBlock = this.wrapper.find('.ads-block');
		this.adsBlockCloseBtn = this.adsBlock.find('> .btn-close7');
		this.searchForm = this.header.find('.search-form');
		this.searchInput = this.searchForm.find('#search');
		this.tabset = jQuery('.tabset-holder.fix');
		this.tabsetLinks = this.tabset.find('a');
		// this.tabsetOffset = this.tabset.offset().top;
		this.contentBox = jQuery('.content-box');
	},
	attachListeners: function(){
		var self = this;

		self.adsBlockCloseBtn.on({
			'click': function(event){
				event.preventDefault();
				self.adsBlock.addClass(self.config.hiddenClass);
				tabInit();
			}
		});

		self.searchInput.on({
			'focus': function(event){
				self.searchForm.addClass(self.config.largeClass);
			},
			'focusout': function(event){
				self.searchForm.removeClass(self.config.largeClass);
			}
		})

		if (self.homeHeader.length){
		console.log('find');
			jQuery(window).on('scroll', function(){
			console.log('scroll');
				var topBar = self.homeHeader.find('.top-bar'),
					barHeight = topBar.outerHeight();
					console.log(barHeight);
				if( (jQuery(window).scrollTop() >= barHeight) ){
					self.homeHeader.addClass(self.config.fixedClass);
				} else {
					self.homeHeader.removeClass(self.config.fixedClass);
				}
			});
		}

		function tabInit(){
			if (self.tabset.length){
				var contentBoxPadding = self.contentBox.css('padding-top'),
					tabsetOffset = self.tabset.offset().top;
				$(window).on('scroll', function(){
					if( $(window).scrollTop() >= tabsetOffset){
						self.tabset.addClass(self.config.fixedClass);
						self.contentBox.css({
							'padding-top' : self.tabset.outerHeight() + parseInt(contentBoxPadding)
						});
					} else{
						self.tabset.removeClass(self.config.fixedClass);
						self.contentBox.css({
							'padding-top' : ''
						});
					}
				});

				self.tabsetLinks.on({
					'click': jQuery.proxy(function(event){
						if (self.tabset.hasClass('fixed')){
							self.body.addClass(self.config.animClass);
							self.html.add(self.body).animate({
								scrollTop: tabsetOffset - 1
							}, self.config.animSpeed, function(){
								self.body.removeClass(self.config.animClass);
							});
						} else {
							return false;
						}
					})
				});
			}
		}

		tabInit();

		if (self.headerAlter.length){
			var headerAlterOffset = self.headerAlter.offset().top + 18;
			jQuery(window).on('scroll', function(){
				if( (jQuery(window).scrollTop() >= headerAlterOffset) ){
					self.headerAlter.addClass(self.config.fixedClass);
				} else {
					self.headerAlter.removeClass(self.config.fixedClass);
				}
			});
		}

	},
	init: function(){
		if(typeof jQuery !== 'function')
			return window.application;

		this.createElements();
		this.attachListeners();

		return window.application;
	}
}