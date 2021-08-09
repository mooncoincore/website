
/**
 * Polyfill for older browsers https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/isArray
 * 
 * @since 4.8
 * @retrun boolean
 */
if ( ! Array.isArray ) 
{
	Array.isArray = function( arg ) 
	{
		return Object.prototype.toString.call( arg ) === '[object Array]';
	};
}

(function($)
{	
    "use strict";
	
    $(document).ready(function()
    {	    
        var aviabodyclasses = AviaBrowserDetection('html');

		$.avia_utilities = $.avia_utilities || {};
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && 'ontouchstart' in document.documentElement)
    	{
    		$.avia_utilities.isMobile =  true;
    	}
    	else
    	{
    		$.avia_utilities.isMobile =  false;
    	}

        //show scroll top but1ton
        avia_scroll_top_fade();
                
        //calculate width of content
        aviaCalcContentWidth();
        
        //creates search tooltip
        new $.AviaTooltip({
					"class": 'avia-search-tooltip',
					data: 'avia-search-tooltip', 
					event: 'click', 
					position: 'bottom', 
					scope: "body", 
					attach: 'element', 
					within_screen: true,
					close_keys: 27
				});

        //creates relate posts tooltip
        new $.AviaTooltip({
					"class": 'avia-related-tooltip',
					data: 'avia-related-tooltip', 
					scope: ".related_posts, .av-share-box", 
					attach: 'element', 
					delay: 0
				});

        //creates ajax search
        new $.AviaAjaxSearch({scope:'#header, .avia_search_element'});

		// actiavte portfolio sorting
		if( $.fn.avia_iso_sort )
		{
			$('.grid-sort-container').avia_iso_sort();
		}

		// Checks height of content and sidebar and applies shadow class to the higher one
        AviaSidebarShaowHelper();

        $.avia_utilities.avia_ajax_call();
		
		
    });

	$.avia_utilities = $.avia_utilities || {};
	
	$.avia_utilities.avia_ajax_call = function(container)
	{
		if( typeof container == 'undefined' )
		{ 
			container = 'body';
		};
		
		$('a.avianolink').on('click', function(e){ e.preventDefault(); });
        $('a.aviablank').attr('target', '_blank');

        //activates the prettyphoto lightbox
        if($.fn.avia_activate_lightbox)
		{
        	$(container).avia_activate_lightbox();
        }
        
        //scrollspy for main menu. must be located before smoothscrolling
		if( $.fn.avia_scrollspy )
		{
			if(container == 'body')
			{
				$('body').avia_scrollspy({target:'.main_menu .menu li > a'});
			}
			else
			{
				$('body').avia_scrollspy('refresh');
			}
		}
		
		
		
		//smooth scrooling
		if( $.fn.avia_smoothscroll )
		{
			$('a[href*="#"]', container).avia_smoothscroll(container);
		}

		avia_small_fixes(container);

		avia_hover_effect(container);

		avia_iframe_fix(container);

		//activate html5 video player
		if( $.fn.avia_html5_activation && $.fn.mediaelementplayer )
		{
			$(".avia_video, .avia_audio", container).avia_html5_activation({ratio:'16:9'});
		}

	};
	
	// -------------------------------------------------------------------------------------------
	// Error log helper
	// -------------------------------------------------------------------------------------------
	$.avia_utilities.log = function( text, type, extra )
	{
		if( typeof console == 'undefined' )
		{
			return;
		} 
		
		if( typeof type == 'undefined' )
		{
			type = "log";
		} 
		
		type = "AVIA-" + type.toUpperCase(); 
		
		console.log( "["+type+"] "+text ); 
		
		if( typeof extra != 'undefined' ) 
		{
			console.log( extra );
		} 
	};



	// -------------------------------------------------------------------------------------------
	// keep track of the browser and content width
	// -------------------------------------------------------------------------------------------


	
	function aviaCalcContentWidth()
	{
			
	var win			= $(window),
		width_select= $('html').is('.html_header_sidebar') ? "#main" : "#header",
		outer		= $(width_select),
		outerParent = outer.parents('div').eq( 0 ),
		the_main	= $(width_select + ' .container').first(),
		css_block	= "",
		calc_dimensions = function()
		{	
			var css			= "",
				w_12 		= Math.round( the_main.width() ),
				w_outer		= Math.round( outer.width() ),
				w_inner		= Math.round( outerParent.width() );
			
			//css rules for mega menu	
			css += " #header .three.units{width:"	+ ( w_12 * 0.25)+	"px;}";
			css += " #header .six.units{width:"		+ ( w_12 * 0.50)+	"px;}";
			css += " #header .nine.units{width:"	+ ( w_12 * 0.75)+	"px;}";
			css += " #header .twelve.units{width:"	+( w_12 )		+	"px;}";
			
			//css rules for tab sections
			css += " .av-framed-box .av-layout-tab-inner .container{width:"	+( w_inner )+	"px;}";
			css += " .html_header_sidebar .av-layout-tab-inner .container{width:"	+( w_outer )+	"px;}";
			css += " .boxed .av-layout-tab-inner .container{width:"	+( w_outer )+	"px;}";
			
			//css rules for submenu container
			css += " .av-framed-box#top .av-submenu-container{width:"	+( w_inner )+	"px;}";

			//ie8 needs different insert method
			try{
				css_block.text(css); 
			}
			catch(err){
				css_block.remove();
				var headFirst = $( 'head' ).first();
				css_block = $("<style type='text/css' id='av-browser-width-calc'>"+css+"</style>").appendTo( headFirst );
			}
			
		};
		
		
		
		if($('.avia_mega_div').length > 0 || $('.av-layout-tab-inner').length > 0 || $('.av-submenu-container').length > 0)
		{
			var headFirst = $( 'head' ).first();
			css_block = $("<style type='text/css' id='av-browser-width-calc'></style>").appendTo( headFirst );
			win.on( 'debouncedresize', calc_dimensions);
			calc_dimensions();
		}
	}


    // -------------------------------------------------------------------------------------------
    // Tiny helper for sidebar shadow
    // -------------------------------------------------------------------------------------------

	function AviaSidebarShaowHelper(){

		var $sidebar_container = $('.sidebar_shadow#top #main .sidebar');
		var $content_container = $('.sidebar_shadow .content');

		if ($sidebar_container.height() >= $content_container.height()) 
		{
			$sidebar_container.addClass('av-enable-shadow');
		}
		else
		{
			$content_container.addClass('av-enable-shadow');
		}
		
	}


	// -------------------------------------------------------------------------------------------
	// modified SCROLLSPY by bootstrap
	// -------------------------------------------------------------------------------------------

	
	  function AviaScrollSpy(element, options)
	  {
	  	var self = this;
	  
		    var process = $.proxy(self.process, self)
		      , refresh = $.proxy(self.refresh, self)
		      , $element = $(element).is('body') ? $(window) : $(element)
		      , href;
		    self.$body = $('body');
		    self.$win = $(window);
		    self.options = $.extend({}, $.fn.avia_scrollspy.defaults, options);
		    self.selector = (self.options.target
		      || ((href = $(element).attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
		      || '');
		    
		   	self.activation_true = false;
		   	
		    if(self.$body.find(self.selector + "[href*='#']").length)
		    {
		    	self.$scrollElement = $element.on('scroll.scroll-spy.data-api', process);
		    	self.$win.on('av-height-change', refresh);
		    	self.$body.on('av_resize_finished', refresh);
		    	self.activation_true = true;
		    	self.checkFirst();
		    	
		    	setTimeout(function()
	  			{
		    		self.refresh();
		    		self.process();
		    		
		    	},100);
		    }
	    
	  }
	
	  AviaScrollSpy.prototype = {
	
	      constructor: AviaScrollSpy
		, checkFirst: function () {
		
			var current = window.location.href.split('#')[0],
				matching_link = this.$body.find(this.selector + "[href='"+current+"']").attr('href',current+'#top');
		}
	    , refresh: function () {
	    
	    if(!this.activation_true) return;
	    
	        var self = this
	          , $targets;
	
	        this.offsets = $([]);
	        this.targets = $([]);
	
	        $targets = this.$body
	          .find(this.selector)
	          .map(function () {
	            var $el = $(this)
	              , href = $el.data('target') || $el.attr('href')
	              , hash = this.hash
	              , hash = hash.replace(/\//g, "")
	              , $href = /^#\w/.test(hash) && $(hash);
	             
				//	$.isWindow deprecated 3.3 https://api.jquery.com/jquery.iswindow/
				var obj = self.$scrollElement.get(0);
				var isWindow = obj != null && obj === obj.window;
				
	            return ( $href
	              && $href.length
	              && [[ $href.position().top + ( ! isWindow && self.$scrollElement.scrollTop() ), href ]] ) || null;
	          })
	          .sort(function (a, b) { return a[0] - b[0]; })
	          .each(function () {
	            self.offsets.push(this[0]);
	            self.targets.push(this[1]);
	          });
	          
	      }
	
	    , process: function () {
	    	
	    	if(!this.offsets) return;
	    	if(isNaN(this.options.offset)) this.options.offset = 0;
	    	
	        var scrollTop = this.$scrollElement.scrollTop() + this.options.offset
	          , scrollHeight = this.$scrollElement[0].scrollHeight || this.$body[0].scrollHeight
	          , maxScroll = scrollHeight - this.$scrollElement.height()
	          , offsets = this.offsets
	          , targets = this.targets
	          , activeTarget = this.activeTarget
	          , i;

	        if (scrollTop >= maxScroll) {
	          return activeTarget != (i = targets.last()[0])
	            && this.activate ( i );
	        }
			
	        for (i = offsets.length; i--;) {
	          activeTarget != targets[i]
	            && scrollTop >= offsets[i]
	            && (!offsets[i + 1] || scrollTop <= offsets[i + 1])
	            && this.activate( targets[i] );
	        }
	      }
	
	    , activate: function (target) {
	        var active
	          , selector;
	
	        this.activeTarget = target;
	
	        $(this.selector)
	          .parent('.' + this.options.applyClass)
	          .removeClass(this.options.applyClass);
	
	        selector = this.selector
	          + '[data-target="' + target + '"],'
	          + this.selector + '[href="' + target + '"]';
	
	
	
	        active = $(selector)
	          .parent('li')
	          .addClass(this.options.applyClass);
			  
	        if (active.parent('.sub-menu').length)  {
	          active = active.closest('li.dropdown_ul_available').addClass(this.options.applyClass);
	        }
	
	        active.trigger('activate');
	      }
	
	  };
	
	
	 /* AviaScrollSpy PLUGIN DEFINITION
	  * =========================== */
	
	  $.fn.avia_scrollspy = function (option) {
	    return this.each(function () {
	      var $this = $(this)
	        , data = $this.data('scrollspy')
	        , options = typeof option == 'object' && option;
	      if (!data) $this.data('scrollspy', (data = new AviaScrollSpy(this, options)));
	      if (typeof option == 'string') data[option]();
	    });
	  };
	
	  $.fn.avia_scrollspy.Constructor = AviaScrollSpy;
	
	  $.fn.avia_scrollspy.calc_offset = function()
	  {
		  var 	offset_1 = (parseInt($('.html_header_sticky #main').data('scroll-offset'), 10)) || 0,
		  		offset_2 = ($(".html_header_sticky:not(.html_top_nav_header) #header_main_alternate").outerHeight()) || 0,
		  		offset_3 = ($(".html_header_sticky.html_header_unstick_top_disabled #header_meta").outerHeight()) || 0,
		  		offset_4 =  1,
		  		offset_5 = parseInt($('html').css('margin-top'),10) || 0,
		  		offset_6 = parseInt($('.av-frame-top ').outerHeight(),10) || 0;
		  
		  return offset_1 + offset_2 + offset_3 + offset_4 + offset_5 + offset_6;
	  };
	
	  $.fn.avia_scrollspy.defaults = 
	  {
	    offset: $.fn.avia_scrollspy.calc_offset(),
	    applyClass: 'current-menu-item'
	  };
	  

	


    // -------------------------------------------------------------------------------------------
    // detect browser and add class to body
    // -------------------------------------------------------------------------------------------

    function AviaBrowserDetection(outputClassElement)
    {
	    
	    //code from the old jquery migrate plugin
	    var current_browser = {},
	    
	    	uaMatch = function( ua ) {
			ua = ua.toLowerCase();
		
			var match = /(edge)\/([\w.]+)/.exec( ua ) ||
	        /(opr)[\/]([\w.]+)/.exec( ua ) ||
	        /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
	        /(iemobile)[\/]([\w.]+)/.exec( ua ) ||
	        /(version)(applewebkit)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec( ua ) ||
	        /(webkit)[ \/]([\w.]+).*(version)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec( ua ) ||
	        /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
	        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
	        /(msie) ([\w.]+)/.exec( ua ) ||
	        ua.indexOf("trident") >= 0 && /(rv)(?::| )([\w.]+)/.exec( ua ) ||
	        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
	        [];
		
			return {
		          browser: match[ 5 ] || match[ 3 ] || match[ 1 ] || "",
		          version: match[ 2 ] || match[ 4 ] || "0",
		          versionNumber: match[ 4 ] || match[ 2 ] || "0"
		        };
		};
		
		var matched = uaMatch( navigator.userAgent );
	
		if ( matched.browser ) 
		{
			current_browser.browser = matched.browser;
			current_browser[ matched.browser ] = true;
			current_browser.version = matched.version;
		}
	
		// Chrome is Webkit, but Webkit is also Safari.
		if ( current_browser.chrome ) {
			current_browser.webkit = true;
		} else if ( current_browser.webkit ) {
			current_browser.safari = true;
		}	    
	    
        if(typeof(current_browser) !== 'undefined')
        {
            var bodyclass = '', version = current_browser.version ? parseInt(current_browser.version) : "";

            if(current_browser.msie || current_browser.rv || current_browser.iemobile){
                bodyclass += 'avia-msie';
            }else if(current_browser.webkit){
                bodyclass += 'avia-webkit';
            }else if(current_browser.mozilla){
                bodyclass += 'avia-mozilla';
            }

            if(current_browser.version) bodyclass += ' ' + bodyclass + '-' + version + ' ';
            if(current_browser.browser) bodyclass += ' avia-' + current_browser.browser + ' avia-' +current_browser.browser +'-' + version + ' ';
        }
		
        if(outputClassElement) $(outputClassElement).addClass(bodyclass);
        
        return bodyclass;
    }

    // -------------------------------------------------------------------------------------------
	// html 5 videos
	// -------------------------------------------------------------------------------------------
    $.fn.avia_html5_activation = function(options)
	{	
		var defaults =
		{
			ratio: '16:9'
		};

		var options  = $.extend(defaults, options),
			isMobile = $.avia_utilities.isMobile;
		
		// if(isMobile) return;
		
		this.each(function()
		{
		var fv 			= $(this),
	      	id_to_apply = '#' + fv.attr('id'),
	      	posterImg 	= fv.attr('poster');
		

		fv.mediaelementplayer({
		    // if the <video width> is not specified, this is the default
		    defaultVideoWidth: 480,
		    // if the <video height> is not specified, this is the default
		    defaultVideoHeight: 270,
		    // if set, overrides <video width>
		    videoWidth: -1,
		    // if set, overrides <video height>
		    videoHeight: -1,
		    // width of audio player
		    audioWidth: 400,
		    // height of audio player
		    audioHeight: 30,
		    // initial volume when the player starts
		    startVolume: 0.8,
		    // useful for <audio> player loops
		    loop: false,
		    // enables Flash and Silverlight to resize to content size
		    enableAutosize: false,
		    // the order of controls you want on the control bar (and other plugins below)
		    features: ['playpause','progress','current','duration','tracks','volume'],
		    // Hide controls when playing and mouse is not over the video
		    alwaysShowControls: false,
		    // force iPad's native controls
		    iPadUseNativeControls: false,
		    // force iPhone's native controls
		    iPhoneUseNativeControls: false,
		    // force Android's native controls
		    AndroidUseNativeControls: false,
		    // forces the hour marker (##:00:00)
		    alwaysShowHours: false,
		    // show framecount in timecode (##:00:00:00)
		    showTimecodeFrameCount: false,
		    // used when showTimecodeFrameCount is set to true
		    framesPerSecond: 25,
		    // turns keyboard support on and off for this instance
		    enableKeyboard: true,
		    // when this player starts, it will pause other players
		    pauseOtherPlayers: false,
		    poster: posterImg,
		    success: function (mediaElement, domObject, instance) { 
         	
         	//make the medialement instance accesible by storing it. usually not necessary but safari has problems since wp version 4.9
         	$.AviaVideoAPI.players[ fv.attr('id').replace(/_html5/,'') ] = instance;
         	
				setTimeout(function()
				{
					if (mediaElement.pluginType == 'flash') 
					{	
						mediaElement.addEventListener('canplay', function() { fv.trigger('av-mediajs-loaded'); }, false);
					}
					else
					{
				        fv.trigger('av-mediajs-loaded').addClass('av-mediajs-loaded');
					}
				         
				     mediaElement.addEventListener('ended', function() {  fv.trigger('av-mediajs-ended'); }, false); 
				
					var html5MediaElement = document.getElementById($(mediaElement).attr('id') + '_html5');
                    				if (html5MediaElement && html5MediaElement !== mediaElement) {
			                        	mediaElement.addEventListener("ended", function() {
	                        			    $(html5MediaElement).trigger('av-mediajs-ended');
        	                		});
                    }
				     
				},10);
		         
		    },
		    // fires when a problem is detected
		    error: function () { 
		
		    },
		    
		    // array of keyboard commands
		    keyActions: []
			});
				
			});
	};



 	// -------------------------------------------------------------------------------------------
	// hover effect for images
	// -------------------------------------------------------------------------------------------
    function avia_hover_effect(container)
    {
    	//hover overlay for mobile device doesnt really make sense. in addition it often slows down the click event
    	if($.avia_utilities.isMobile) return;
	    
		if( $('body').hasClass( 'av-disable-avia-hover-effect' ) )
		{
			return;
		}
	    
		var overlay = "", cssTrans = $.avia_utilities.supports('transition');
		
		if(container == 'body')
    	{
    		var elements = $('#main a img').parents('a').not('.noLightbox, .noLightbox a, .avia-gallery-thumb a, .ls-wp-container a, .noHover, .noHover a, .av-logo-container .logo a').add('#main .avia-hover-fx');
    	}
    	else
    	{
    		var elements = $('a img', container).parents('a').not('.noLightbox, .noLightbox a, .avia-gallery-thumb a, .ls-wp-container a, .noHover, .noHover a, .av-logo-container .logo a').add('.avia-hover-fx', container);
    	}

	   elements.each(function(e)
       {
            var link = $(this), 
            	current = link.find('img').first();

            if(current.hasClass('alignleft')) link.addClass('alignleft').css({float:'left', margin:0, padding:0});
            if(current.hasClass('alignright')) link.addClass('alignright').css({float:'right', margin:0, padding:0});
            if(current.hasClass('aligncenter')) link.addClass('aligncenter').css({float:'none','text-align':'center', margin:0, padding:0});

            if(current.hasClass('alignnone'))
            {
               link.addClass('alignnone').css({margin:0, padding:0});;
               if(!link.css('display') || link.css('display') == 'inline') { link.css({display:'inline-block'}); }
            }
            
            if(!link.css('position') || link.css('position') == 'static') { link.css({position:'relative', overflow:'hidden'}); }
            
            var url		 	= link.attr('href'),
				span_class	= "overlay-type-video",
				opa			= link.data('opacity') || 0.7,
				overlay_offset = 5,
				overlay 	= link.find('.image-overlay');
            	
            	if(url)
				{
					if( url.match(/(jpg|gif|jpeg|png|tif)/) ) span_class = "overlay-type-image";
					if(!url.match(/(jpg|gif|jpeg|png|\.tif|\.mov|\.swf|vimeo\.com|youtube\.com)/) ) span_class = "overlay-type-extern";
				}
				
				if(!overlay.length)
				{
					overlay = $("<span class='image-overlay "+span_class+"'><span class='image-overlay-inside'></span></span>").appendTo(link);
				}
            	
            	link.on('mouseenter', function(e)
				{
					var current = link.find('img').first(),
						_self	= current.get(0),
						outerH 	= current.outerHeight(),
						outerW 	= current.outerWidth(),
						pos		= current.position(),
						linkCss = link.css('display'),
						overlay = link.find('.image-overlay');
					
					if(outerH > 100)
					{
						
						if(!overlay.length)
						{
							overlay = $("<span class='image-overlay "+span_class+"'><span class='image-overlay-inside'></span></span>").appendTo(link);
							
						}
						//can be wrapped into if !overlay.length statement if chrome fixes fade in problem
						if(link.height() == 0) { link.addClass(_self.className); _self.className = ""; }
						if(!linkCss || linkCss == 'inline') { link.css({display:'block'}); }
						//end wrap
						
						overlay.css({left:(pos.left - overlay_offset) + parseInt(current.css("margin-left"),10), top:pos.top + parseInt(current.css("margin-top"),10)})
							   .css({overflow:'hidden',display:'block','height':outerH,'width':(outerW + (2*overlay_offset))});
							   
						if(cssTrans === false ) overlay.stop().animate({opacity:opa}, 400);
					}
					else
					{
						overlay.css({display:"none"});
					}
		
				}).on('mouseleave', elements, function(){
		
					if(overlay.length)
					{
						if(cssTrans === false ) overlay.stop().animate({opacity:0}, 400);
					}
				});
        });
    }


// -------------------------------------------------------------------------------------------
// Smooth scrooling when clicking on anchor links
// todo: maybe use https://github.com/ryanburnette/scrollToBySpeed/blob/master/src/scrolltobyspeed.jquery.js in the future
// -------------------------------------------------------------------------------------------

	(function($)
	{
		$.fn.avia_smoothscroll = function( apply_to_container )
		{
			if( ! this.length ) 
			{
				return;
			}
				
			var the_win = $(window),
				$header = $('#header'),
				$main 	= $('.html_header_top.html_header_sticky #main').not('.page-template-template-blank-php #main'),
				$meta 	= $('.html_header_top.html_header_unstick_top_disabled #header_meta'),
				$alt  	= $('.html_header_top:not(.html_top_nav_header) #header_main_alternate'),
				menu_above_logo = $('.html_header_top.html_top_nav_header'),
				shrink	= $('.html_header_top.html_header_shrinking').length,
				frame	= $('.av-frame-top'),
				fixedMainPadding = 0,
				isMobile = $.avia_utilities.isMobile,
				sticky_sub = $('.sticky_placeholder').first(), 
				calc_main_padding= function()
				{
					if( $header.css('position') == "fixed" )
					{
						var tempPadding  		= parseInt($main.data('scroll-offset'),10) || 0,
							non_shrinking		= parseInt($meta.outerHeight(),10) || 0,
							non_shrinking2		= parseInt($alt.outerHeight(),10) || 0; 
						
						if( tempPadding > 0 && shrink ) 
						{
							tempPadding = (tempPadding / 2 ) + non_shrinking + non_shrinking2;
						}
						else
						{
							tempPadding = tempPadding + non_shrinking + non_shrinking2;
						}
						
						tempPadding += parseInt($('html').css('margin-top'),10);
						fixedMainPadding = tempPadding; 
					}
					else
					{
						fixedMainPadding = parseInt($('html').css('margin-top'),10);
					}
					
					if( frame.length )
					{
						fixedMainPadding += frame.height();
					}
					
					if( menu_above_logo.length )
					{
						//if menu is above logo and we got a sticky height header
						fixedMainPadding = $('.html_header_sticky #header_main_alternate').height() + parseInt($('html').css('margin-top'),10);
					}
					
					if( isMobile )
					{
						fixedMainPadding = 0;
					}
					
				};
			
			if( isMobile ) 
			{
				shrink = false;
			}
			
			calc_main_padding();
			the_win.on( "debouncedresize av-height-change", calc_main_padding );

			var hash = window.location.hash.replace(/\//g, "");
			
			//if a scroll event occurs at pageload and an anchor is set and a coresponding element exists apply the offset to the event
			if( fixedMainPadding > 0 && hash && apply_to_container == 'body' && hash.charAt(1) != "!" && hash.indexOf("=") === -1 )
			{
				var scroll_to_el = $(hash), modifier = 0;
				
				if( scroll_to_el.length )
				{
					the_win.on('scroll.avia_first_scroll', function()
					{	
						setTimeout( function()		//small delay so other scripts can perform necessary resizing
						{	
							if( sticky_sub.length && scroll_to_el.offset().top > sticky_sub.offset().top ) 
							{ 
								modifier = sticky_sub.outerHeight() - 3; 
							}
							
							the_win.off( 'scroll.avia_first_scroll' ).scrollTop( scroll_to_el.offset().top - fixedMainPadding - modifier );
						},10); 
				    });
			    }
			}
			
			return this.each(function()
			{
				$(this).on('click', function(e) 
				{

					var newHash = this.hash.replace(/\//g, ""),
						clicked = $(this),
						data = clicked.data();

					if( newHash != '' && newHash != '#' && newHash != '#prev' && newHash != '#next' && !clicked.is('.comment-reply-link, #cancel-comment-reply-link, .no-scroll'))
					{
						var container = "", 
							originHash = "";

						if( '#next-section' == newHash )
						{
							originHash = newHash;

							//	@since 4.8.3 check to scroll to visible sections only (e.g. sections could be hidden on different devices
							var next_containers = clicked.parents('.container_wrap').eq( 0 ).nextAll('.container_wrap');
							next_containers.each( function()
							{
							   var cont = $( this );

							   if( cont.css( 'display' ) == 'none' || cont.css( 'visibility' ) == 'hidden' )
							   {
								   return;
							   }

							   container = cont;
							   return false;
							});
							
							if( 'object' == typeof container && container.length > 0 )
							{
								newHash = '#' + container.attr('id') ;
							}
						}
						else
						{
							 container = $( this.hash.replace(/\//g, "") );
						}

						if( container.length )
						{
							var cur_offset = the_win.scrollTop(),
								container_offset = container.offset().top,
								target =  container_offset - fixedMainPadding,
								hash = window.location.hash,
								hash = hash.replace(/\//g, ""),
								oldLocation = window.location.href.replace(hash, ''),
								newLocation = this,
								duration = data.duration || 1200,
								easing = data.easing || 'easeInOutQuint';
								
							if( sticky_sub.length && container_offset > sticky_sub.offset().top ) 
							{ 
								target -= sticky_sub.outerHeight() - 3;
							}
							
							// make sure it's the same location
							if( oldLocation+newHash == newLocation || originHash )
							{
								if( cur_offset != target ) // if current pos and target are the same dont scroll
								{
									if( ! ( cur_offset == 0 && target <= 0 ) ) // if we are at the top dont try to scroll to top or above
									{
										the_win.trigger('avia_smooth_scroll_start'); 
										
										// animate to target and set the hash to the window.location after the animation
										$('html:not(:animated),body:not(:animated)').animate({ scrollTop: target }, duration, easing, function() 
										{
											// add new hash to the browser location
											//window.location.href=newLocation;
											if( window.history.replaceState )
											{
												window.history.replaceState( "", "", newHash );
											}
										});
									}
								}
								
								// cancel default click action
								e.preventDefault();
							}
						}
					}
				});
			});
		};
	})(jQuery);


	// -------------------------------------------------------------------------------------------
	// iframe fix for firefox and ie so they get proper z index
	// -------------------------------------------------------------------------------------------
	function avia_iframe_fix(container)
	{
		var iframe 	= jQuery('iframe[src*="youtube.com"]:not(.av_youtube_frame)', container),
			youtubeEmbed = jQuery('iframe[src*="youtube.com"]:not(.av_youtube_frame) object, iframe[src*="youtube.com"]:not(.av_youtube_frame) embed', container).attr('wmode','opaque');

			iframe.each(function()
			{
				var current = jQuery(this),
					src 	= current.attr('src');

				if(src)
				{
					if(src.indexOf('?') !== -1)
					{
						src += "&wmode=opaque&rel=0";
					}
					else
					{
						src += "?wmode=opaque&rel=0";
					}

					current.attr('src', src);
				}
			});
	}

	// -------------------------------------------------------------------------------------------
	// small js fixes for pixel perfection :)
	// -------------------------------------------------------------------------------------------
	function avia_small_fixes(container)
	{
		if(!container) container = document;

		//make sure that iframes do resize correctly. uses css padding bottom iframe trick
		var win		= jQuery(window),
			iframes = jQuery('.avia-iframe-wrap iframe:not(.avia-slideshow iframe):not( iframe.no_resize):not(.avia-video iframe)', container),
			adjust_iframes = function()
			{
				iframes.each(function(){

					var iframe = jQuery(this), parent = iframe.parent(), proportions = 56.25;

					if(this.width && this.height)
					{
						proportions = (100/ this.width) * this.height;
						parent.css({"padding-bottom":proportions+"%"});
					}
				});
			};

			adjust_iframes();

	}

   function avia_scroll_top_fade()
   {
   		 var win 		= $(window),
   		 	 timeo = false,
   		 	 scroll_top = $('#scroll-top-link'),
   		 	 set_status = function()
             {
             	var st = win.scrollTop();

             	if(st < 500)
             	{
             		scroll_top.removeClass('avia_pop_class');
             	}
             	else if(!scroll_top.is('.avia_pop_class'))
             	{
             		scroll_top.addClass('avia_pop_class');
             	}
             };

   		 win.on( 'scroll',  function(){ window.requestAnimationFrame( set_status ); } );
         set_status();
	}
	



	$.AviaAjaxSearch  =  function(options)
	{
	   var defaults = {
            delay: 300,                //delay in ms until the user stops typing.
            minChars: 3,               //dont start searching before we got at least that much characters
            scope: 'body'

        };

        this.options = $.extend({}, defaults, options);
        this.scope   = $(this.options.scope);
        this.timer   = false;
        this.lastVal = "";
		
        this.bind_events();


	};


	$.AviaAjaxSearch.prototype =
    {

        bind_events: function()
        {
            this.scope.on('keyup', '#s:not(".av_disable_ajax_search #s")' , $.proxy( this.try_search, this));
            this.scope.on('click', '#s.av-results-parked' , $.proxy( this.reset, this));

        },

        try_search: function(e)
        {
            var form = $(e.currentTarget).parents('form').eq( 0 ),
                resultscontainer = form.find('.ajax_search_response');

            clearTimeout(this.timer);
			
			// clear on ESC
            if( e.keyCode === 27 )
			{
                this.reset(e);
				return;
			}

            //only execute search if chars are at least "minChars" and search differs from last one
            if(e.currentTarget.value.length >= this.options.minChars && this.lastVal != e.currentTarget.value.trim() )
            {
                //wait at least "delay" milliseconds to execute ajax. if user types again during that time dont execute
                this.timer = setTimeout($.proxy( this.do_search, this, e), this.options.delay);
            }
            //remove the results container if the input field has been emptied
            else if (e.currentTarget.value.length == 0)
			{
                this.timer = setTimeout($.proxy( this.reset, this, e), this.options.delay);
			}
        },

		reset: function(e){
            var form = $(e.currentTarget).parents('form').eq( 0 ),
				resultscontainer = form.find('.ajax_search_response'),
				alternative_resultscontainer = $(form.attr('data-ajaxcontainer')).find('.ajax_search_response'),
				searchInput = $(e.currentTarget);

            // bring back results that were hidden when user clicked outside the form element
            if ($(e.currentTarget).hasClass('av-results-parked')) {
                resultscontainer.show();
                alternative_resultscontainer.show();

                // in case results container is attached to body
                $('body > .ajax_search_response').show();
			}
			else {
                // remove results and delete the input value
                resultscontainer.remove();
                alternative_resultscontainer.remove();
                searchInput.val('');

                // in case results container is attached to body
                $('body > .ajax_search_response').remove();
			}


		},

        do_search: function(e)
        {
            var obj          				= this,
                currentField 				= $(e.currentTarget).attr( "autocomplete", "off" ),
				currentFieldWrapper         = $(e.currentTarget).parents('.av_searchform_wrapper').eq( 0 ),
                currentField_position 		= currentFieldWrapper.offset(),
                currentField_width 			= currentFieldWrapper.outerWidth(),
                currentField_height 		= currentFieldWrapper.outerHeight(),
				form         				= currentField.parents('form').eq( 0 ),
				submitbtn       		    = form.find('#searchsubmit'),
                resultscontainer 			= form,
                results      				= resultscontainer.find('.ajax_search_response'),
                loading     				= $('<div class="ajax_load"><span class="ajax_load_inner"></span></div>'),
                action 		 				= form.attr('action'),
                values       				= form.serialize();
                values     					+= '&action=avia_ajax_search';

            // define results div if not found
            if(!results.length) {
                results = $('<div class="ajax_search_response" style="display:none;"></div>');
            }

            // add class to differentiate betweeen search element and header search
			if (form.attr('id') == 'searchform_element'){
				results.addClass('av_searchform_element_results');
			}

            //check if the form got get parameters applied and also apply them
           	if(action.indexOf('?') != -1)
           	{
           		action  = action.split('?');
           		values += "&" + action[1];
           	}

           	//check if there is a results container defined
			if (form.attr('data-ajaxcontainer')) {

                var rescon = form.attr('data-ajaxcontainer');

                // check if defined container exists
                if ($(rescon).length) {

                	// remove previous search results
					$(rescon).find('.ajax_search_response').remove();

                    resultscontainer = $(rescon);
				}

			}

			/*
			 * For the placement option: "Under the search form - overlay other content",
			 * we have to attach the results to the body in order to overlay the other content,
			 * and we calculate it's position using the search field
			 */

            results_css = {};

            if ( form.hasClass('av_results_container_fixed') ){

                // remove previous search results
				$('body').find('.ajax_search_response').remove();

            	resultscontainer = $('body');

                // add class and position to results if defined above
				var results_css = {
                    top: currentField_position.top + currentField_height,
                    left: currentField_position.left,
                    width: currentField_width
				};

				// make sure default stylesheet is applied
				results.addClass('main_color');

                // remove results and reset if window is resized
                $( window ).resize(function() {
                    results.remove();
                    $.proxy( this.reset, this);
                    currentField.val('');
                });

            }

            // add additional styles
            if ( form.attr('data-results_style') ) {
                var results_style = JSON.parse(form.attr('data-results_style'));
                results_css = Object.assign(results_css, results_style);

                // add class if font color is applied, so we can use color: inherit
                if ("color" in results_css) {
                    results.addClass('av_has_custom_color');
                }

            }

            // apply inline styles
            results.css(results_css);

            // add .container class if resultscontainer in a color section
			if (resultscontainer.hasClass('avia-section')){
				results.addClass('container');
			}

            // append results to defined container
            results.appendTo(resultscontainer);


            //return if we already hit a no result and user is still typing
            if(results.find('.ajax_not_found').length && e.currentTarget.value.indexOf(this.lastVal) != -1) return;

            this.lastVal = e.currentTarget.value;

            $.ajax({
				url: avia_framework_globals.ajaxurl,
				type: "POST",
				data:values,
				beforeSend: function()
				{
					// add loader after submit button
					loading.insertAfter(submitbtn);
					form.addClass('ajax_loading_now');
				},
				success: function(response)
				{
				    if(response == 0) response = "";
                    results.html(response).show();
				},
				complete: function()
				{
				    loading.remove();
                    form.removeClass('ajax_loading_now');
                }
			});

            // Hide search resuls if user clicks anywhere outside the form element
			$(document).on('click',function(e) {
			 if(!$(e.target).closest(form).length) {
				 if($(results).is(":visible")) {
					 $(results).hide();
                     currentField.addClass('av-results-parked');
				 }
			 }
			});
        }
    };


	$.AviaTooltip = function( options )
	{
	   var defaults = {
            delay: 1500,                //delay in ms until the tooltip appears
            delayOut: 300,             	//delay in ms when instant showing should stop
            delayHide: 0,             	//delay hiding of tooltip in ms
            "class": "avia-tooltip",   	//tooltip classname for css styling and alignment
            scope: "body",             	//area the tooltip should be applied to
            data:  "avia-tooltip",     	//data attribute that contains the tooltip text
            attach: "body",          	//either attach the tooltip to the "mouse" or to the "element" // todo: implement mouse, make sure that it doesnt overlap with screen borders
            event: 'mouseenter',       	//mousenter and leave or click and leave
            position: 'top',             //top or bottom
            extraClass: 'avia-tooltip-class', //extra class that is defined by a tooltip element data attribute
            permanent: false, 			// always display the tooltip?
            within_screen: false,		// if the tooltip is displayed outside the screen adjust its position
            close_keys: null			// string|[] of keyCodes to close the tooltip (there is no check for empty value !! )
        };
		
        this.options = $.extend({}, defaults, options);
		
		var close_keys = '';
		if( this.options.close_keys != null )
		{
			if( ! Array.isArray( this.options.close_keys ) )
			{
				this.options.close_keys = [ this.options.close_keys ];
			}
			close_keys = ' data-close-keys="' + this.options.close_keys.join( ',' ) + '" ';
		}
		
        this.body    = $('body');
        this.scope   = $(this.options.scope);
		this.tooltip = $( '<div class="' + this.options['class'] + ' avia-tt"' + close_keys + '><span class="avia-arrow-wrap"><span class="avia-arrow"></span></span></div>' );
        this.inner   = $( '<div class="inner_tooltip"></div>').prependTo(this.tooltip);
        this.open    = false;
        this.timer   = false;
        this.active  = false;
		
        this.bind_events();
	};

	$.AviaTooltip.openTTs = [];
	$.AviaTooltip.openTT_Elements = [];
	
    $.AviaTooltip.prototype =
    {
        bind_events: function()
        {
	        var perma_tooltips		= '.av-permanent-tooltip [data-'+this.options.data+']',
	        	default_tooltips	= '[data-'+this.options.data+']:not( .av-permanent-tooltip [data-'+this.options.data+'])';
	        
	        this.scope.on('av_permanent_show', perma_tooltips, $.proxy( this.display_tooltip, this) );
	        $(perma_tooltips).addClass('av-perma-tooltip').trigger('av_permanent_show');
	        
	        
			this.scope.on(this.options.event + ' mouseleave', default_tooltips, $.proxy( this.start_countdown, this) );
	        
            if(this.options.event != 'click')
            {
                this.scope.on('mouseleave', default_tooltips, $.proxy( this.hide_tooltip, this) );
				this.scope.on('click', default_tooltips, $.proxy( this.hide_on_click_tooltip, this) );
            }
            else
            {
                this.body.on('mousedown', $.proxy( this.hide_tooltip, this) );
            }

			if( this.options.close_keys != null )
			{
				this.body.on( 'keyup', $.proxy( this.close_on_keyup, this ) );
			}
        },

        start_countdown: function(e)
        {
            clearTimeout(this.timer);
			
			var target 		= this.options.event == "click" ? e.target : e.currentTarget,
            	element 	= $(target);

            if(e.type == this.options.event)
            {
                var delay = this.options.event == 'click' ? 0 : this.open ? 0 : this.options.delay;

                this.timer = setTimeout($.proxy( this.display_tooltip, this, e), delay );
            }
            else if(e.type == 'mouseleave')
            {
				if( ! element.hasClass( 'av-close-on-click-tooltip' ) )
				{
					this.timer = setTimeout($.proxy( this.stop_instant_open, this, e), this.options.delayOut);
				}
            }
            e.preventDefault();
        },

        reset_countdown: function(e)
        {
            clearTimeout(this.timer);
            this.timer = false;
        },

        display_tooltip: function(e)
        {
            var _self		= this,
            	target 		= this.options.event == "click" ? e.target : e.currentTarget,
            	element 	= $(target),
                text    	= element.data(this.options.data),
                tip_index  	= element.data('avia-created-tooltip'),
            	extraClass 	= element.data('avia-tooltip-class'),
                attach  	= this.options.attach == 'element' ? element : this.body,
                offset  	= this.options.attach == 'element' ? element.position() : element.offset(),
                position	= element.data('avia-tooltip-position'),
                align		= element.data('avia-tooltip-alignment'),
                force_append= false,
				newTip		= false,
				is_new_tip	= false;
           
            text = 'string' == typeof text ? text.trim() : '';
            
            if(element.is('.av-perma-tooltip'))
            {
	            offset = {top:0, left:0 };
	        	attach = element;
				force_append = true;
            }
            
			if( text == "" ) 
			{
				return;
			}
			if( position == "" || typeof position == 'undefined' ) 
			{
				position = this.options.position;
			}
			if( align == "" || typeof align == 'undefined' ) 
			{
				align = 'center';
			}
			
			if( typeof tip_index != 'undefined' )
			{
				newTip = $.AviaTooltip.openTTs[tip_index];
			}
			else
			{
				this.inner.html(text); 
				newTip = this.tooltip.clone();
				is_new_tip = true;
				
				if( this.options.attach == 'element' && force_append !== true )
				{
					newTip.insertAfter(attach);
				}
				else
				{
					newTip.appendTo(attach);
				}
                
                if(extraClass != "") 
				{
					newTip.addClass(extraClass);
				}
			}
				
			if( this.open && this.active == newTip )
			{
				return;
			}
			
			if( element.hasClass( 'av-close-on-click-tooltip' ) )
			{
				this.hide_all_tooltips();
			}
			
            this.open = true;
            this.active = newTip;

            if( ( newTip.is(':animated:visible') && e.type == 'click' ) || element.is( '.' + this.options['class'] ) || element.parents( '.' + this.options['class'] ).length != 0 ) 
			{
				return;
			}

            var animate1 = {}, 
				animate2 = {}, 
				pos1 = "", 
				pos2 = "";
		
			if( position == "top" || position == "bottom" )
			{
				switch(align)
				{
					case "left": 
						pos2 = offset.left; 
						break;
					case "right": 
						pos2 = offset.left + element.outerWidth() - newTip.outerWidth();  
						break;
					default: 
						pos2 = ( offset.left + ( element.outerWidth() / 2 ) ) - ( newTip.outerWidth() / 2 ); 
						break;
				}	
				
				if(_self.options.within_screen) //used to keep search field inside screen
				{
					var boundary = element.offset().left + (element.outerWidth() / 2) - (newTip.outerWidth() / 2) + parseInt(newTip.css('margin-left'),10);
					if(boundary < 0)
					{
						pos2 = pos2 - boundary;
					}
				}
			}
			else
			{
				switch(align)
				{
					case "top": 
						pos1 = offset.top; 
						break;
					case "bottom": 
						pos1 = offset.top + element.outerHeight() - newTip.outerHeight();  
						break;
					default: 
						pos1 = ( offset.top + (element.outerHeight() / 2 ) ) - ( newTip.outerHeight() / 2 ); 
						break;
				}	
			}
	
			switch(position)
			{
				case "top": 
					pos1 = offset.top - newTip.outerHeight();
					animate1 = {top: pos1 - 10, left: pos2};
					animate2 = {top: pos1};
					break;
				case "bottom": 	
					pos1 = offset.top + element.outerHeight();
					animate1 = {top: pos1 + 10, left: pos2};
					animate2 = {top: pos1};
					break;
				case "left": 
					pos2 = offset.left  - newTip.outerWidth();
					animate1 = {top: pos1, left: pos2 -10};
					animate2 = {left: pos2};	
					break;
				case "right": 	
					pos2 = offset.left + element.outerWidth();
					animate1 = {top: pos1, left: pos2 + 10};
					animate2 = {left: pos2};	
					break;
			}
			
			animate1['display'] = "block";
			animate1['opacity'] = 0;
			animate2['opacity'] = 1;
			
            newTip.css(animate1).stop().animate(animate2,200);
			
            newTip.find('input, textarea').trigger('focus');
			if( is_new_tip )
			{
				$.AviaTooltip.openTTs.push(newTip);
				$.AviaTooltip.openTT_Elements.push(element);
				element.data('avia-created-tooltip', $.AviaTooltip.openTTs.length - 1);
			}
        },
		
		hide_on_click_tooltip: function(e)
		{
			if( this.options.event == "click" )
			{
				return;
			}
			
			var element = $( e.currentTarget );
			
			if( ! element.hasClass('av-close-on-click-tooltip') )
			{
				return;
			}
			
			if( ! element.find( 'a' ) )
			{
				e.preventDefault();
			}
			
			//	Default behaviour when using mouse - click on active tooltip closes it (moving mouse to another tooltip close others automatically
			//	On mobile devices or when using touchscreen we show element on click (= old behaviour) and hide when same element
			var ttip_index = element.data('avia-created-tooltip');
			
			if( 'undefined' != typeof ttip_index )
			{
				var current = $.AviaTooltip.openTTs[ttip_index];
				if( 'undefined' != typeof current && current == this.active )
				{
					this.hide_all_tooltips();
				}
			}

		},
		
		close_on_keyup: function( e )
		{
			if( this.options.close_keys == null )
			{
				return;
			}
			
			if( $.inArray( e.keyCode, this.options.close_keys ) < 0 )
			{
				return;
			}
			
			this.hide_all_tooltips( e.keyCode );
		},
		
		hide_all_tooltips: function( keyCode )
		{
			var ttip,
				position,
				element,
				keyCodeCheck = 'undefined' != typeof keyCode ? keyCode + '' : null;
			
			for( var index = 0; index < $.AviaTooltip.openTTs.length; ++index ) 
			{
				ttip = $.AviaTooltip.openTTs[index];
				element = $.AviaTooltip.openTT_Elements[index];
				position = element.data('avia-tooltip-position');
				
				//	check if tooltip can be closed on keyup
				if( keyCodeCheck != null )
				{
					var keys = ttip.data( 'close-keys' );
					if( 'undefined' == typeof keys )
					{
						continue;
					}
					
					keys = keys + '';
					keys = keys.split( ',' );
					if( $.inArray( keyCodeCheck, keys ) < 0 )
					{
						continue;
					}
				}
				
				this.animate_hide_tooltip( ttip, position );
			}
			
			this.open = false;
			this.active  = false;
		},

        hide_tooltip: function(e)
        {
            var element 	= $(e.currentTarget) , newTip, animateTo, 
            	position	= element.data('avia-tooltip-position'),
                align		= element.data('avia-tooltip-alignment'),
				newTip		= false;
                
            if( position == "" || typeof position == 'undefined' ) 
			{
				position = this.options.position;
			}
			
			if( align == "" || typeof align == 'undefined' ) 
			{
				align = 'center';
			}

            if( this.options.event == 'click' )
            {
                element = $(e.target);

                if( ! element.is( '.' + this.options['class'] ) && element.parents( '.' + this.options['class'] ).length == 0 )
                {
                    if( this.active.length ) 
					{
						newTip = this.active; 
						this.active = false;
					}
                }
            }
            else
            {
				if( ! element.hasClass( 'av-close-on-click-tooltip' ) )
				{
					newTip = element.data('avia-created-tooltip');
					newTip = typeof newTip != 'undefined' ? $.AviaTooltip.openTTs[newTip] : false;
				}
            }

            this.animate_hide_tooltip( newTip, position );
        },
		
		animate_hide_tooltip: function( ttip, position )
		{
			if(ttip)
            {
            	var animate = {opacity:0};
				
            	switch(position)
            	{
            		case "top": 	
						animate['top'] = parseInt(ttip.css('top'),10) - 10;	
						break;
					case "bottom": 	
						animate['top'] = parseInt(ttip.css('top'),10) + 10;	
						break;
					case "left": 	
						animate['left'] = parseInt(ttip.css('left'), 10) - 10;
						break;
					case "right": 	
						animate['left'] = parseInt(ttip.css('left'), 10) + 10;
						break;
            	}
            	
                ttip.animate( animate, 200, function()
                {
                    ttip.css({display:'none'});
                });
			}
		},

        stop_instant_open: function(e)
        {
            this.open = false;
        }
    };

})( jQuery );


/*!
Waypoints - 4.0.2
Copyright © 2011-2016 Caleb Troughton (up to 4.0.1)
Licensed under the MIT license.
https://github.com/imakewebthings/waypoints/blob/master/licenses.txt
*/
!function(){"use strict";function t(o){if(!o)throw new Error("No options passed to Waypoint constructor");if(!o.element)throw new Error("No element option passed to Waypoint constructor");if(!o.handler)throw new Error("No handler option passed to Waypoint constructor");this.key="waypoint-"+e,this.options=t.Adapter.extend({},t.defaults,o),this.element=this.options.element,this.adapter=new t.Adapter(this.element),this.callback=o.handler,this.axis=this.options.horizontal?"horizontal":"vertical",this.enabled=this.options.enabled,this.triggerPoint=null,this.group=t.Group.findOrCreate({name:this.options.group,axis:this.axis}),this.context=t.Context.findOrCreateByElement(this.options.context),t.offsetAliases[this.options.offset]&&(this.options.offset=t.offsetAliases[this.options.offset]),this.group.add(this),this.context.add(this),i[this.key]=this,e+=1}var e=0,i={};t.prototype.queueTrigger=function(t){this.group.queueTrigger(this,t)},t.prototype.trigger=function(t){this.enabled&&this.callback&&this.callback.apply(this,t)},t.prototype.destroy=function(){this.context.remove(this),this.group.remove(this),delete i[this.key]},t.prototype.disable=function(){return this.enabled=!1,this},t.prototype.enable=function(){return this.context.refresh(),this.enabled=!0,this},t.prototype.next=function(){return this.group.next(this)},t.prototype.previous=function(){return this.group.previous(this)},t.invokeAll=function(t){var e=[];for(var o in i)e.push(i[o]);for(var n=0,r=e.length;r>n;n++)e[n][t]()},t.destroyAll=function(){t.invokeAll("destroy")},t.disableAll=function(){t.invokeAll("disable")},t.enableAll=function(){t.Context.refreshAll();for(var e in i)i[e].enabled=!0;return this},t.refreshAll=function(){t.Context.refreshAll()},t.viewportHeight=function(){return window.innerHeight||document.documentElement.clientHeight},t.viewportWidth=function(){return document.documentElement.clientWidth},t.adapters=[],t.defaults={context:window,continuous:!0,enabled:!0,group:"default",horizontal:!1,offset:0},t.offsetAliases={"bottom-in-view":function(){return this.context.innerHeight()-this.adapter.outerHeight()},"right-in-view":function(){return this.context.innerWidth()-this.adapter.outerWidth()}},window.Waypoint=t}(),function(){"use strict";function t(t){window.setTimeout(t,1e3/60)}function e(t){this.element=t,this.Adapter=n.Adapter,this.adapter=new this.Adapter(t),this.key="waypoint-context-"+i,this.didScroll=!1,this.didResize=!1,this.oldScroll={x:this.adapter.scrollLeft(),y:this.adapter.scrollTop()},this.waypoints={vertical:{},horizontal:{}},t.waypointContextKey=this.key,o[t.waypointContextKey]=this,i+=1,n.windowContext||(n.windowContext=!0,n.windowContext=new e(window)),this.createThrottledScrollHandler(),this.createThrottledResizeHandler()}var i=0,o={},n=window.Waypoint,r=window.onload;e.prototype.add=function(t){var e=t.options.horizontal?"horizontal":"vertical";this.waypoints[e][t.key]=t,this.refresh()},e.prototype.checkEmpty=function(){var t=this.Adapter.isEmptyObject(this.waypoints.horizontal),e=this.Adapter.isEmptyObject(this.waypoints.vertical),i=this.element==this.element.window;t&&e&&!i&&(this.adapter.off(".waypoints"),delete o[this.key])},e.prototype.createThrottledResizeHandler=function(){function t(){e.handleResize(),e.didResize=!1}var e=this;this.adapter.on("resize.waypoints",function(){e.didResize||(e.didResize=!0,n.requestAnimationFrame(t))})},e.prototype.createThrottledScrollHandler=function(){function t(){e.handleScroll(),e.didScroll=!1}var e=this;this.adapter.on("scroll.waypoints",function(){(!e.didScroll||n.isTouch)&&(e.didScroll=!0,n.requestAnimationFrame(t))})},e.prototype.handleResize=function(){n.Context.refreshAll()},e.prototype.handleScroll=function(){var t={},e={horizontal:{newScroll:this.adapter.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.adapter.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};for(var i in e){var o=e[i],n=o.newScroll>o.oldScroll,r=n?o.forward:o.backward;for(var s in this.waypoints[i]){var a=this.waypoints[i][s];if(null!==a.triggerPoint){var l=o.oldScroll<a.triggerPoint,h=o.newScroll>=a.triggerPoint,p=l&&h,u=!l&&!h;(p||u)&&(a.queueTrigger(r),t[a.group.id]=a.group)}}}for(var c in t)t[c].flushTriggers();this.oldScroll={x:e.horizontal.newScroll,y:e.vertical.newScroll}},e.prototype.innerHeight=function(){return this.element==this.element.window?n.viewportHeight():this.adapter.innerHeight()},e.prototype.remove=function(t){delete this.waypoints[t.axis][t.key],this.checkEmpty()},e.prototype.innerWidth=function(){return this.element==this.element.window?n.viewportWidth():this.adapter.innerWidth()},e.prototype.destroy=function(){var t=[];for(var e in this.waypoints)for(var i in this.waypoints[e])t.push(this.waypoints[e][i]);for(var o=0,n=t.length;n>o;o++)t[o].destroy()},e.prototype.refresh=function(){var t,e=this.element==this.element.window,i=e?void 0:this.adapter.offset(),o={};this.handleScroll(),t={horizontal:{contextOffset:e?0:i.left,contextScroll:e?0:this.oldScroll.x,contextDimension:this.innerWidth(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:e?0:i.top,contextScroll:e?0:this.oldScroll.y,contextDimension:this.innerHeight(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};for(var r in t){var s=t[r];for(var a in this.waypoints[r]){var l,h,p,u,c,d=this.waypoints[r][a],f=d.options.offset,w=d.triggerPoint,y=0,g=null==w;d.element!==d.element.window&&(y=d.adapter.offset()[s.offsetProp]),"function"==typeof f?f=f.apply(d):"string"==typeof f&&(f=parseFloat(f),d.options.offset.indexOf("%")>-1&&(f=Math.ceil(s.contextDimension*f/100))),l=s.contextScroll-s.contextOffset,d.triggerPoint=Math.floor(y+l-f),h=w<s.oldScroll,p=d.triggerPoint>=s.oldScroll,u=h&&p,c=!h&&!p,!g&&u?(d.queueTrigger(s.backward),o[d.group.id]=d.group):!g&&c?(d.queueTrigger(s.forward),o[d.group.id]=d.group):g&&s.oldScroll>=d.triggerPoint&&(d.queueTrigger(s.forward),o[d.group.id]=d.group)}}return n.requestAnimationFrame(function(){for(var t in o)o[t].flushTriggers()}),this},e.findOrCreateByElement=function(t){return e.findByElement(t)||new e(t)},e.refreshAll=function(){for(var t in o)o[t].refresh()},e.findByElement=function(t){return o[t.waypointContextKey]},window.onload=function(){r&&r(),e.refreshAll()},n.requestAnimationFrame=function(e){var i=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||t;i.call(window,e)},n.Context=e}(),function(){"use strict";function t(t,e){return t.triggerPoint-e.triggerPoint}function e(t,e){return e.triggerPoint-t.triggerPoint}function i(t){this.name=t.name,this.axis=t.axis,this.id=this.name+"-"+this.axis,this.waypoints=[],this.clearTriggerQueues(),o[this.axis][this.name]=this}var o={vertical:{},horizontal:{}},n=window.Waypoint;i.prototype.add=function(t){this.waypoints.push(t)},i.prototype.clearTriggerQueues=function(){this.triggerQueues={up:[],down:[],left:[],right:[]}},i.prototype.flushTriggers=function(){for(var i in this.triggerQueues){var o=this.triggerQueues[i],n="up"===i||"left"===i;o.sort(n?e:t);for(var r=0,s=o.length;s>r;r+=1){var a=o[r];(a.options.continuous||r===o.length-1)&&a.trigger([i])}}this.clearTriggerQueues()},i.prototype.next=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints),o=i===this.waypoints.length-1;return o?null:this.waypoints[i+1]},i.prototype.previous=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints);return i?this.waypoints[i-1]:null},i.prototype.queueTrigger=function(t,e){this.triggerQueues[e].push(t)},i.prototype.remove=function(t){var e=n.Adapter.inArray(t,this.waypoints);e>-1&&this.waypoints.splice(e,1)},i.prototype.first=function(){return this.waypoints[0]},i.prototype.last=function(){return this.waypoints[this.waypoints.length-1]},i.findOrCreate=function(t){return o[t.axis][t.name]||new i(t)},n.Group=i}(),function(){"use strict";function t(t){this.$element=e(t)}var e=window.jQuery,i=window.Waypoint;e.each(["innerHeight","innerWidth","off","offset","on","outerHeight","outerWidth","scrollLeft","scrollTop"],function(e,i){t.prototype[i]=function(){var t=Array.prototype.slice.call(arguments);return this.$element[i].apply(this.$element,t)}}),e.each(["extend","inArray","isEmptyObject"],function(i,o){t[o]=e[o]}),i.adapters.push({name:"jquery",Adapter:t}),i.Adapter=t}(),function(){"use strict";function t(t){return function(){var i=[],o=arguments[0];return 'function'===typeof arguments[0]&&(o=t.extend({},arguments[1]),o.handler=arguments[0]),this.each(function(){var n=t.extend({},o,{element:this});"string"==typeof n.context&&(n.context=t(this).closest(n.context)[0]),i.push(new e(n))}),i}}var e=window.Waypoint;window.jQuery&&(window.jQuery.fn.waypoint=t(window.jQuery)),window.Zepto&&(window.Zepto.fn.waypoint=t(window.Zepto))}();



// http://paulirish.com/2011/requestanimationframe-for-smart-animating/ + http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating
// requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel. can be removed if IE9 is no longer supported or all parallax scripts are gone 
// MIT license
(function(){var lastTime=0;var vendors=['ms','moz','webkit','o'];for(var x=0;x<vendors.length&&!window.requestAnimationFrame;++x){window.requestAnimationFrame=window[vendors[x]+'RequestAnimationFrame'];window.cancelAnimationFrame=window[vendors[x]+'CancelAnimationFrame']||window[vendors[x]+'CancelRequestAnimationFrame']}if(!window.requestAnimationFrame)window.requestAnimationFrame=function(callback,element){var currTime=new Date().getTime();var timeToCall=Math.max(0,16-(currTime-lastTime));var id=window.setTimeout(function(){callback(currTime+timeToCall)},timeToCall);lastTime=currTime+timeToCall;return id};if(!window.cancelAnimationFrame)window.cancelAnimationFrame=function(id){clearTimeout(id)}}());

jQuery.expr.pseudos.regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ? 
                        matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
};

