/* 
 * Leaflet Maps implementation
 * 
 * @since 4.8.2
 */

(function($)
{
	"use strict";
	
	$.AviaLeaflet = function() 
	{
		this.self = this;
		this.queryURL = ' https://nominatim.openstreetmap.org/search?';
		this.maxResultsInfo = 4;
		
		this.default_location = {
					street:		'',
					postalcode:	'',
					city:		'',
					country:	'',
					county:		'',
					state:		''
				};
				
		this.default_long_lat = {
					lng:	'',
					lat:	''
				};
	};
	
	$.AviaLeaflet.prototype = 
   	{
		findGeoLocationModalContainer: function( actionButton )
		{
			var btn = $( actionButton );
			var btn_container = btn.closest( '.avia-element-action_button' );
			var btn_name = btn_container.find('.avia-name-description > strong' );
			var btn_desc = btn_container.find('.avia-name-description > div' );
			var container = btn.closest( '.avia-modal-toggle-container-inner' );
			var inputs = container.find( '.avia-form-element-container.avia-element-input' );
			var btn_text = btn.data( 'text' );
			var btn_text_active = btn.data( 'text_active' );
			
			var self = this;
			var location = $.extend( {}, this.default_location );
			var long_lat = $.extend( {}, this.default_long_lat );
			
			//	scan input fields to build address query and find destination fields
			inputs.each( function( )
			{
				var input_cont = $( this );
				var input = input_cont.find( 'input' );
				var id = input.attr( 'id' );
				
				if( 'undefined' == typeof id )
				{
					return;
				}
				
				var new_id = id.replace( /aviaTB/g, '' );
				var geo_key = new_id.replace( /geo_/g, '' )
				
				if( 'undefined' != typeof long_lat[ geo_key ] )
				{
					long_lat[ geo_key ] = input;
				}
				else if( 'undefined' != typeof location[ geo_key ] )
				{
					location[ geo_key ] = input.val().trim();
				}
			});
			
			var empty = true;
			
			$.each( location, function( key ) 
			{
				if( this.trim() != '' )
				{
					empty = false;
					return false;
				}
			});

			if( empty )
			{
				alert( avia_modal_L10n.leafletNoAddress );
				return;
			}

			location.format = 'json';
			
			var queryString = this.queryURL + $.param( location );
			
			$.ajax({
				type: "GET",
				url: queryString,
				dataType: 'json',
				cache: false,
				beforeSend: function( jqXHR, settings )
				{
					if( typeof btn_text_active != 'undefined' )
					{
						btn.html( btn_text_active );
					}
				},
				success: function(response, textStatus, jqXHR)
				{
					if( textStatus != 'success')
					{
						alert( avia_modal_L10n.connection_error );
						return;
					}

					if( response.length == 0 )
					{
						alert( avia_modal_L10n.leafletNotFound );
						return;
					}

					if( typeof long_lat.lng == 'object' )
					{
						long_lat.lng.val( response[0].lon );
					}

					if( typeof long_lat.lat == 'object' )
					{
						long_lat.lat.val( response[0].lat );
					}

					var result = '',
						result_end = '',
						li_start = '',
						li_end = '';

					if( response.length > 1 )
					{
						result += '<ul>';
						result_end = '</ul>';
						li_start = '<li>';
						li_end = '</li>';
					}

					$.each( response, function( index )
					{
						var found = this.display_name;

						if( response.length > 1 && index == 0 )
						{
							found = ' ---> ' + found;
						}

						result += li_start + found + li_end;

						if( index >= ( self.maxResultsInfo - 1 ) )
						{
							result += li_start + '........' + li_end;
							return false;
						}
					});

					result += result_end;

					btn_name.html( avia_modal_L10n.leafletResults );
					btn_desc.html( result );
				},
				error: function(errorObj) 
				{
					alert( avia_modal_L10n.connection_error );
				},
				complete: function(test) 
				{
					if( typeof btn_text_active != 'undefined' )
					{
						//	delay to avoid flicker
						 setTimeout( function(){ btn.html( btn_text ); }, 300 );
					}
				}
			});
			

		}
	};
	
	$( document ).ready( function() 
	{
		$.avia_leaflet = new $.AviaLeaflet();
	});
	
})(jQuery);	 


