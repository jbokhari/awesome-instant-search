/**
 *
 * Ajax Search
 * Author: Jameel Bokhari
 * Author URL: http://jameelbokhari.com
 * @package Awesome_AJAX_Search
 * @uses Obj Awesome_AJAX_Search_Options to get options from php side
**/

// Utility
if (typeof Object.create !== 'function' ){
	Object.create = function(obj){
		function F(){};
		F.prototype = obj;
		return new F();
	}
}

(function($, document, undefined){
	var $window = $(window);
	var AwesomeInstantSearch = {
/************************
 * Import Plugin Settings
 * Call starter functions
************************/
		init : function(){
			var self = this;
			self.pluginOptions();
			self.elementCache();
			self.initAutocomplete();
			// console.log(self);
			self.$input.on(self.actions, function(){
				self.doingTime(this);
			})
		},
		pluginOptions : function(){
			var self = this;
			self.options = Awesome_AJAX_Search_Options;
			self.fadeOutSpeed = (self.options.fadeOutSpeed) ? self.options.fadeOutSpeed : 149;
			self.fadeInSpeed = (self.options.fadeInSpeed) ? self.options.fadeInSpeed : 98;
			self.intervalLength = (self.options.intervalLength) ? self.options.intervalLength : 98;

			self.input = ( self.options.input ) ? self.options.input : ".search-field";
			self.container = ( self.options.content ) ? self.options.content : "#content";
			self.results = ( self.options.results ) ? self.options.results : "#content article.hentry";
			self.before = ( self.options.before ) ? self.options.before : "<header class='page-header'><h1 class='page-title'>Search Results for: <code>%%SEARCH_TERM%%</code></h1></header>";
			self.after = ( self.options.after ) ? self.options.after : "";
			self.alsohide = ( self.options.alsohide ) ? self.options.alsohide : "";
			// self.unhide = ( self.options.alsohide ) ? self.options.alsohide : true;
			self.autocomplete = ( self.options.autocomplete ) ? self.options.autocomplete : false;
			if ( self.autocomplete ){
				self.availableTags = self.options.pages;
			}
			self.pluginDir = ( self.options.pluginDir ) ? self.options.pluginDir : '';
			self.useContentGif = ( self.options.useContentGif ) ? self.options.useContentGif : true;
			self.useSearchBarGif = ( self.options.useSearchBarGif ) ? self.options.useSearchBarGif : true;
			self.url = ( self.options.urlbase ) ? self.options.urlbase : "?s=";
			self.screenmin = ( self.options.screenmin ) ? self.options.screenmin : 0;
		},
		getHideElements : function ( ){
			var self = this;
			var hide = $(self.alsohide);
			// hide.each(function(){
			// 	if($(this).is(":hidden")){ //if it's already hidden, we may unhide on accident, so ignore hidden elements
			// 		hide = hide.not(this);

			// 	}
			// });
			// console.log(hide);
			return hide;
		},
		elementCache : function(){
			var self = this;
			self.$body = $('body');
			self.val; //used in loadSearch()
			self.$container = $(self.container);
			self.$results = $('<div></div>');
			self.interval;
			self.cachedVal;
			self.firstsearch = true;
			self.cleared = true;
			self.$input = $(self.input);
			self.$contentGif;
			self.$searchGif;
			self.actions = "keyup keypress paste change";
			self.$main = self.getHideElements();
		},
		initAutocomplete : function(){
			var self = this;
			if( self.autocomplete ){
				self.$input.autocomplete({
		     		 source: self.availableTags,
		     		 autoFocus : true,
		     		 appendTo: "#secondary" //need option here
		  		});
			}
			self.createGifs();
			// self.actions = self.actions + " autocomplete"; // someting like this for autocomplete action?
		},
		createGifs : function(){
			var self = this;
			if (self.useContentGif){
				self.$contentGif = $('<div style="margin: 0 auto; width: 220px;"><img width="220" height="19" src="' + self.pluginDir + 'images/content-loader.gif" alt="" /></div>');
				self.$contentGif.prependTo(self.$container).hide();
			}
			if (self.useSearchBarGif){
				self.$searchGif =$('<img width="16" height="16" src="' + self.pluginDir + 'images/search-loader.gif" alt="" />').css({ position: "absolute", zIndex: 5000, display: 'none' });
				self.$searchGif.prependTo(self.$body);
			}
		},
		setGifPosition : function( trigger ){
			var self = this;
			var $trigger = trigger;
			var cords = $trigger.offset();
			var heightOffset = cords.top + ( $trigger.outerHeight() / 2 ) - 8;
			var width = $trigger.width();
			var leftEnd = cords.left + $trigger.width() - 16;
			self.$searchGif.css({ top: heightOffset, left: leftEnd }).hide();
		},
		clearSearch : function(close){
			var self = this;
			self.$body.removeClass("aas-search-finished").addClass("aas-search-cleared");
			if(self.cleared){
				return false;
			}
			self.$results.hide();
			//removes inline style rather than set display block to elements that are supposed to be hidden by css
			self.$main.css('display', '');
			self.$main.stop();
			self.$main.css('opacity', 0);
			self.$main.animate({opacity: 1}, {duration: self.fadeInSpeed});
			self.cleared = true;

		},
		doingTime : function(trigger){
			// console.log( interval ? 'Set' : "Not Set" );
			var self = this;
			self.$body.addClass("aas-waiting");
			
			clearInterval( self.interval );
			var count = 0;
			self.interval = setInterval(function(){
				count++;
				var view_width = (document.documentElement.clientWidth) ? document.documentElement.clientWidth : 1;
				if (view_width >= self.screenmin){
					// console.log("window inner width " + document.documentElement.clientWidth);
					if ( count >= 1 ){
						self.loadSearch(trigger);
						clearInterval(self.interval);
					}
				}
			}, self.intervalLength);
		},
		fetch: function( searchUrl ){
			return $.ajax( {
				url : searchUrl,
				dataType: "html",
				type: 'get'
			});
		},
		loadSearch : function(trigger){
			var self = this;
			self.$body.removeClass("aas-doing-search aas-search-cleared").addClass("aas-search-finished");
			$trigger = $(trigger);
			var val = $trigger.val();
			var change = (val === self.cachedVal) ? false : true;
			self.cachedVal = val;
			if (val.length > 2 && change){
				self.$results.fadeOut(self.fadeOutSpeed);
				self.$main.fadeOut(self.fadeOutSpeed);
				var safeval = encodeURI(val);
				safeval = safeval.replace(/%20/g, '+');
				var searchUrl = self.url + val;
				self.setGifPosition($trigger);
				self.$contentGif.show();
				self.$searchGif.show();
				self.fetch( searchUrl ).done( 
					function( results ){
						self.showResults( results, val );
					}
				);
			} else if (val == ''){
				
				self.clearSearch();
			}
		},
		showResults : function( r, val ){
			var self = this;
			if (!r){
				console.log('showResults possibly called incorrectly.');
				return;
			};
			if (self.$results){ //remove old results
				self.$results.remove();
			}
			self.$results = $(self.results, r); // find the results with jQuery
			var newHTML = '';
			var header = self.before.replace(/%%SEARCH_TERM%%/, "<span id='aas-search-term'>"+val+"</span>");
			var footer = self.after.replace(/%%SEARCH_TERM%%/, "<span id='aas-search-term'>"+val+"</span>");
			newHTML += header;
			self.$results.each(function(){
				var add = $('<div>').append($(this).clone()).html(); 
				newHTML += add;
			});
			self.$results = $('<div></div>').html(newHTML);
			self.$container.prepend(self.$results);
			self.$results.hide();
			self.$results.stop();
			self.$results.show();
			self.$results.css('opacity', 0);
			self.$contentGif.hide();
			self.$results.animate({opacity : 1}, {duration: self.fadeInSpeed, complete: function(){self.$searchGif.hide();}});
			
			self.cleared = false;
		}
	};
	$.fn.awesomeInstantSearch = function( options ) {
			var instantSearch = Object.create( AwesomeInstantSearch );
			instantSearch.init( options );
	};

})(jQuery, document);

jQuery(document).ready(function($){
	//plans for future, test computer speed
	var thisIshIsFastEnough = true;
	if( thisIshIsFastEnough ){
		$.fn.awesomeInstantSearch();
	}

});
