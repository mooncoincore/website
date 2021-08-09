(function($)
{ 
	"use strict";

	
	$('body').on('click','.av-lazyload-video-embed .av-click-to-play-overlay', function(e){
		
		var clicked = $(this);
		
		//	check if videos are disabled by user setting via cookie - or user must opt in.
		var cookie_check = $('html').hasClass('av-cookies-needs-opt-in') || $('html').hasClass('av-cookies-can-opt-out');
		var allow_continue = true;
		var silent_accept_cookie = $('html').hasClass('av-cookies-user-silent-accept');

		if( cookie_check && ! silent_accept_cookie )
		{
			if( ! document.cookie.match(/aviaCookieConsent/) || $('html').hasClass('av-cookies-session-refused') )
			{
				allow_continue = false;
			}
			else
			{
				if( ! document.cookie.match(/aviaPrivacyRefuseCookiesHideBar/) )
				{
					allow_continue = false;
				}
				else if( ! document.cookie.match(/aviaPrivacyEssentialCookiesEnabled/) )
				{
					allow_continue = false;
				}
				else if( document.cookie.match(/aviaPrivacyVideoEmbedsDisabled/) )
				{
					allow_continue = false;
				}
			}
		}
		
		var container = clicked.parents( '.av-lazyload-video-embed' );
		if( container.hasClass( 'avia-video-lightbox' ) && container.hasClass( 'avia-video-standard-html' ) )
		{
			allow_continue = true;
		}
		
		if( ! allow_continue )
		{
			if( typeof e.originalEvent == 'undefined' ) { return; } //human click only
			
			var src_url = container.data('original_url');
			if( src_url ) window.open(src_url , '_blank', 'noreferrer noopener' ); 
			
			return;
		}
	
		
		var video = container.find('.av-video-tmpl').html();
		var link = '';
		
		if( container.hasClass( 'avia-video-lightbox' ) )
		{
			link = container.find( 'a.lightbox-link' );
			if( link.length == 0 )
			{
				container.append( video );

				// DOM not ready
				setTimeout(function(){
							link = container.find( 'a.lightbox-link' );
							if( $( 'html' ).hasClass( 'av-default-lightbox' ) )
							{
								link.addClass( 'lightbox-added' ).magnificPopup( $.avia_utilities.av_popup );
								link.trigger( 'click' );
							}
							else
							{
								link.trigger( 'avia-open-video-in-lightbox' );
							}
					}, 100 );
			}
			else
			{
				link.trigger( 'click' );
			}
		}
		else
		{
			container.html( video );
		}
			
	});
	
	$('.av-lazyload-immediate .av-click-to-play-overlay').trigger('click');

}(jQuery));