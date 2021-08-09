/**
 * This file holds the main javascript functions needed for the option pages. also holds the alert plugin to notify users
 *
 * @author		Christian "Kriesi" Budschedl
 * @copyright	Copyright ( c ) Christian Budschedl
 * @link		http://kriesi.at
 * @link		http://aviathemes.com
 * @since		Version 1.0
 * @package 	AviaFramework
 */
 
jQuery(function($) {
    
    $('#avia_options_page').avia_framework_option_pages();
    $('#avia_options_page').avia_create_option_navigation();
    $('#avia_options_page .avia_tab_container').avia_media_advanced_plugin();
    $('body').avia_popups();
	$('body').trigger('avia_options_page_loaded');
  });



(function($)
{
	$.fn.avia_create_option_navigation = function(single_page) 
	{
		return this.each(function()
		{
			if(!$('#avia_options_page').length) return;
		
			var container = $(this),
				innerContainer =  $('.avia_options_container',container),
				headContainer = $('.avia_section_header',container),
				sidebar = $('.avia_sidebar_content'),
				urlHash = window.location.hash.replace(/^\#goto_/,"avia_"),
				hashActive = $('.avia_subpage_container', container).filter('[id="'+urlHash+'"]');	
			
	
			headContainer.each(function()
			{
				var heading = $(this),
					subContainer = heading.parent('.avia_subpage_container'),
					hashtarget	= subContainer.attr('id').replace(/^\avia_/,"goto_");
					
					if(hashActive.length)
					{
						if(subContainer.is('#'+urlHash))
						{
							heading.addClass('avia_active_nav');
							$('.avia_subpage_container').removeClass('avia_active_container');
							subContainer.addClass('avia_active_container');
						}
					}
					else
					{
						if(subContainer.is(':visible'))
						{
							heading.addClass('avia_active_nav');
						}
					}
					
					
					heading.clone(false)
						   .appendTo(sidebar)
						   .css({display:'block'})
						   .addClass(hashtarget)
						   .on('click', function()
						   {
						   		if(!subContainer.is(':visible'))
						   		{
						   			$('.avia_subpage_container').removeClass('avia_active_container');
						   			subContainer.addClass('avia_active_container');
						   			$('.avia_active_nav').removeClass('avia_active_nav');
						   			$(this).addClass('avia_active_nav');
						   		}
						   });
				});
				
				
				innerContainer.find('a[href*="goto_"]').on('click', function()
				{
					$(this.hash.replace("#",".")).trigger('click');
					return false;
				});
				

		});
		
		
		
	};
})(jQuery);	





(function($)
{
	$.fn.avia_framework_option_pages = function(variables) 
	{
		return this.each(function()
		{
			//gather form data
			var container = $(this);
			if(container.length != 1) return;
			
			var saveButtons = $('.avia_submit', this),
				resetButtons = $('.avia_reset', this),
				resetSelectedButtons = $('.avia_reset_selected', this),
				importButton = $('.avia_import_button', this),
				deleteDemoButton = $('.avia_import_delete_demo_button', this),
				importParentSettingsButton = $('.avia_import_parent_button', this),
				hiddenDataContainer = $('#avia_hidden_data', this),
				saveData = {
								container: 		$(this),
								ajaxUrl :		$('input[name=admin_ajax_url]', hiddenDataContainer).val(),
								prefix :		$('input[name=avia_options_prefix]', hiddenDataContainer).val(),
								optionSlug :	$('input[name=avia_options_page_slug]', hiddenDataContainer).val(),
								action :		$('input[name=action]', hiddenDataContainer).val(),
								actionReset :	$('input[name=resetaction]', hiddenDataContainer).val(),
								nonce  :		$('input[name=avia-nonce]', hiddenDataContainer).val(),
								nonceReset  :	$('input[name=avia-nonce-reset]', hiddenDataContainer).val(),
								nonceImport  :	$('input[name=avia-nonce-import]', container).val(),
								nonceImportParent  :	$('input[name=avia-nonce-import-parent]', container).val(),
								ref	   :		$('input[name=_wp_http_referer]', hiddenDataContainer).val(),
								first_call:		$('input[name=avia_options_first_call]', hiddenDataContainer),
								saveButtons: 	saveButtons,
								object:			methods
							 };

						
			//bind actions:
			saveButtons.on('click', {set: saveData}, methods.save);								//saves the current form
			resetButtons.on('click', {set: saveData}, methods.reset);							//resets the option page
			resetSelectedButtons.on('click', {set: saveData}, methods.resetSelected);	//resets the option page for selected elements
			importButton.on('click', {set: saveData}, methods.do_import);						//download demo files and imports demo date
			deleteDemoButton.on('click', {set: saveData}, methods.delete_demo_files);			//delete downloaded demo files from server
			importParentSettingsButton.on('click', {set: saveData}, methods.do_parent_import);	//imports parent theme data
			
			//add "form listener"
			methods.activateSaveButton(container);
			methods.activateResetAllButton( container, resetButtons );
			
			//sidebar toggle
			methods.sidebarToggle(container);
			
			//default saving to database on first call
			if(saveData.first_call.length > 0)
			{
				setTimeout(function(){ methods.save(saveData, true); }, 1000);
			}
			
		});
	};
	
	var	methods = {
				
		/**
		 * adds the functionality for the sidebar toggle on the left of the option pages
		 */
		sidebarToggle: function(container)
		{
			var button = $('.avia_shop_option_link', container),
				wrapContainer = $('.avia_options_page_inner', container),
				allSubContainer = $('.avia_subpage_container', container);
				value = button.text();
				
				button.on('click', function()
				{
					if(wrapContainer.is('.avia_sidebar_active'))
					{
						wrapContainer.removeClass('avia_sidebar_active');
						button.html('[-]');
					}
					else
					{
						wrapContainer.addClass('avia_sidebar_active');
						button.html(value);
					}
					
					return false;
				});
		
		},
		
		/**
		 * Show/Hide/Activate Theme options "Reset All Button"
		 */
		activateResetAllButton: function( container, resetButtons )
		{
			var reset_switcher = container.find( 'select[name="reset_options_button"]' );
			if( reset_switcher.length == 0 )
			{
				return;
			}
			
			reset_switcher.on( 'change', function(){
								var selected = $(this).children( "option:selected" ).val();
								
								if( '' == selected )
								{
									resetButtons.removeClass( 'avia_hidden avia_reset_inactive' ).addClass( 'avia_reset_active' );
								}
								else
								{
									resetButtons.removeClass( 'avia_reset_active' ).addClass( 'avia_hidden avia_reset_inactive' );
								}
								
							});
							
			reset_switcher.trigger( 'change', { context: 'manually' } );
		},
		
		/**
		 * Save Buttons are not active by default. They get active when the user changes an option 
		 */
		activateSaveButton: function( container )
		{	
			var saveButton = $('.avia_header .avia_button_inactive, .avia_footer .avia_button_inactive'),
				elements = $('input, select, textarea', container).not('.avia_button_inactive').not('.avia_dont_activate_save_buttons');
				
			//bind click events
			elements.on( 'keydown change', function( e, params )
			{
				if( 'object' == typeof params && 'string' == typeof params.context && 'manually' == params.context )
				{
					return;
				}
				
				saveButton.removeClass( 'avia_button_inactive' );
			});
				
			$( '.avia_clone_set, .avia_remove_set, .avia_dynamical_add_elements' ).on( 'click', function()
			{
				saveButton.removeClass( 'avia_button_inactive' );
			});
		},
		
		/**
		 *  SAVE: gather all form data and convert it to a single string, then send that string via ajax request to the admin-ajax.php file
		 *  
		 */
		save: function( passed, hiddensave )
		{
			if( typeof hiddensave == 'undefined' ) 
			{
				hiddensave = false;
			}
		
			var me = hiddensave == true ? passed : passed.data.set,
				buttonClicked = $(this),		//button that was clicked
				elements = $('input:text, input:hidden, input:radio:checked, input:checkbox, select, textarea','.avia_options_container'), //elements with values
				dataString = "",		// data string passed to the ajax script
				save_succeded = false;
			
			//if no options have changed do not save
			if( buttonClicked.is('.avia_button_inactive') && ! hiddensave ) 
			{
				return false;
			}
			
			elements.each( function()
			{
				var currentElement = $(this),					//form element we are currently iterating
					value = currentElement.val(),				//field value
					name = currentElement.attr('name');			//field name
				
				if( typeof name != 'undefined' && name.trim() !== '' )
				{
					//special case for inputs:checkbox set their value to empty if they are not checked
					if( currentElement.is('input:checkbox') && ! currentElement.is('input:checked') ) 
					{
						value = "disabled";
					}
						
					dataString  += "&" + name + "=" + encodeURIComponent(value);
				}
			});
			
			dataString = dataString.substr(1);
			///////// end of building the data string /////////
			
			
			//sort order for dynamic elements
			//
			// deprecated 4.8.2
//			var dynamicOrder = "",
//				dynamicElements = $('.avia_section, .avia_set').not(".avia_single_set .avia_section"),
//				id_order_string = "";
//				
//			if( dynamicElements.length && $('.avia_row').length )
//			{
//				
//				dynamicElements.each(function()
//				{
//					id_order_string = this.id.replace(/^avia_/,'').replace(/-__-0$/,'');
//					dynamicOrder += id_order_string + '-__-';
//				});
//			}
			  
			//sends the request. calls the the wp_ajax_avia_ajax_save_options_page php function
			$.ajax({
					type: "POST",
					url: me.ajaxUrl,
					data: 
					{
						action: me.action,
						_wpnonce: me.nonce,
						_wp_http_referer: me.ref,
						prefix: me.prefix,
						slug: me.optionSlug,
						data: dataString
//						dynamicOrder: dynamicOrder
						
					},
					beforeSend: function()
					{
						if(hiddensave) return;
					
						//show loader
						 $('.avia_header .avia_loading, .avia_footer .avia_loading',  me.container).css({opacity:0, display:"block", visibility:'visible'}).animate({opacity:1});
						
						//set buttons to inactive
						me.saveButtons.addClass('avia_button_inactive');
					},
					error: function()
					{
						if(hiddensave) return;
					
						//allow saving again
						$('body').avia_alert({the_class:'error', text:'Saving didnt work! <br/> Please reload the page and try again', show:4500});
						me.saveButtons.removeClass('avia_button_inactive');
					},
					success: function(response)
					{
						if(hiddensave) return;
					
						//reset the input elements that tell the php script to clone or remove
						if(response.match('avia_save'))
						{
							$('body').avia_alert();
							save_succeded = true;
						}
						else
						{
							var answer = "";
							
							if(response.length > 3)
							{
								answer = 'Saving didnt work! <br/>The script returned the following error: <br/><br/>'+response;
							}
							else
							{
								answer = 'Saving didnt work! <br/> Please reload the page and try again';
							}
							
							$('body').avia_alert({the_class:'error', text: answer , show:4500});
							me.saveButtons.removeClass('avia_button_inactive');
						}
						
					},
					complete: function(response)
					{	
						if(hiddensave) return;
					
						$('.avia_loading',  me.container).fadeOut();
						
						var param = {
								success: save_succeded
							};
						
						$('body').trigger( 'avia_options_data_saved', param );
					}
				});
			
			return false;
		},
		
		do_parent_import: function(passed)
		{
			var button = $(this),
				me = passed.data.set,
				waitLabel = $('.avia_import_parent_wait', me.container),
				answer = "",
				activate = true;
								
			
			if(button.is('.avia_button_inactive')) return false;
			
			activate = confirm('Importing the Parent Theme Settings will overwrite your current Settings. Proceed anyways?');
			
      if(activate == false) return false;
			
			$.ajax({
						type: "POST",
						url: me.ajaxUrl,
						data: 
						{
							action: 'avia_ajax_import_parent_settings',
							_wpnonce: me.nonceImportParent,
							_wp_http_referer: me.ref
						},
						beforeSend: function()
						{
							//show loader
							$('.avia_import_loading_parent',  me.container).css({opacity:0, display:"block", visibility:'visible'}).animate({opacity:1});
							button.addClass('avia_button_inactive');
							waitLabel.slideDown();
						},
						error: function()
						{
							//script error occured
							$('body').avia_alert({	the_class:'error', 
													text:'Importing didnt work! <br/> You might want to try reloading the page and then try again', 
													show:4500});
							button.removeClass('avia_button_inactive');
							
						},
						success: function(response)
						{
							if(response.match('avia_import'))
							{
								var resultcontainer = $('.avia_import_result_parent', me.container);
								//resultcontainer.css('display','none').html(response).slideDown();
								$('body').avia_alert({text: 'Alright!<br/>Import worked out, no problems whatsoever. <br/>The page will now be reloaded to reflect the changes'}, function()
								{
									window.location.hash = "#wpwrap";
						 			window.location.reload(true);
								});
								
							}
							else
							{
								button.removeClass('avia_button_inactive');
								//script was called but aborted before finishing import
								$('body').avia_alert({	the_class:'error', 
														text:'Importing didnt work! <br/> You might want to try reloading the page and then try again <br/> (The script returned the following message: <br/><br/>'+response+')', 
														show:4500});
							}
						},
						complete: function(response)
						{	
							$('.avia_import_loading_parent',  me.container).fadeOut();
							waitLabel.slideUp();
						}
					});
					
			return false;
		},
		
		delete_demo_files: function(passed)
		{
			passed.preventDefault();
			
			var button = $(this),
				me = passed.data.set,
				demo_wrap = button.closest('#avia_demo'),
				container = button.parents('.avia_section').eq(0),
				waitLabel = $('.avia_import_wait.delete-wait', container),
				wrap = button.closest('.av-import-wrap-delete-demo'),
				import_wrap = container.find('.av-import-wrap'),
				immediate_delete = wrap.hasClass('av-demo-immediate-delete');
				
			if( demo_wrap.hasClass('av-demo-ajax-active') || button.hasClass('avia_button_inactive') || ! wrap.hasClass('av-demo-downloaded') ) 
			{
				return false;
			}
				
			var send_data = {
							action: 'avia_ajax_delete_demo_files',
							_wpnonce: me.nonceImport,
							_wp_http_referer: me.ref,
							delete_demo: button.data('import'),
							demo_name: button.data('demo_name'),
							demo_full_name: button.data('demo_full_name')
					};
				
			$.ajax({
						type: "POST",
						url: me.ajaxUrl,
						data: send_data,
						beforeSend: function()
						{
							//show loader
							$('.avia_import_loading',  container).css({opacity:0, display:"block", visibility:'visible'}).animate({opacity:1});
							button.addClass('avia_button_inactive');
							demo_wrap.addClass('av-demo-ajax-active');
							waitLabel.slideDown();
						},
						error: function()
						{
							//script error occured
							$('body').avia_alert({	the_class:'error', 
													text:button.data('error_msg'), 
													show:4500});
													
							button.removeClass('avia_button_inactive');
						},
						success: function(response)
						{
							if(response.match('avia_error'))
							{
								response = response.replace('avia_error-','');
								
								$('body').avia_alert({	the_class:'error',
														text: response, 
														show: 4500 });
							}
							else if(response.match('avia_demo_deleted'))
							{
								response = response.replace('avia_demo_deleted-','');
								
								if( ! immediate_delete )
								{
									$('body').avia_alert({	text:response,
															show:4500});
								}
								else
								{
									window.location.hash = "#wpwrap";
									window.location.reload( true );
								}
													
								wrap.removeClass('av-demo-downloaded').addClass('av-demo-must-download');
								import_wrap.removeClass('av-demo-downloaded').addClass('av-demo-must-download');
							}
							else
							{
								//script was called but aborted before finishing delete operation or invalid parameters
								$('body').avia_alert({	the_class:'error', 
														text: button.data('script_error_msg') + ' ' + response,
														show:4500});
							}
							
						},
						complete: function(response)
						{	
							$('.avia_import_loading',  container).fadeOut();
							waitLabel.slideUp();
							button.removeClass('avia_button_inactive');
							demo_wrap.removeClass('av-demo-ajax-active');
						}
				});
			
			return false;
		},
		
		/**
		 * Start Importing the wordpress dummy content if a user clicks this button
		 */
		do_import: function(passed)
		{
			var button = $(this),
				me = passed.data.set,
				demo_wrap = button.closest('#avia_demo'),
				container = button.parents('.avia_section').eq(0),
				wrap = button.closest('.av-import-wrap'),
				waitLabelImport = $('.avia_import_wait.import-wait', container),
				waitLabelDownload = $('.avia_import_wait.download-wait', container),
				waitLabel = null,
				delete_button = container.find('.av-import-wrap-delete-demo'),
				exec_download = false,
				execImport = true,
				immediate_delete = wrap.hasClass('av-demo-immediate-delete'),
				confirm_message = wrap.data('confirm_import'),
				download_error = wrap.data('download_error'),
				import_error = wrap.data('import_error'),
				script_error = wrap.data('script_error');
			
			if( demo_wrap.hasClass('av-demo-ajax-active') )
			{
				return false;
			}
			
			if( wrap.hasClass('av-disable-import') )
			{
				return false;
			}
			
			if( ! wrap.hasClass('av-demo-must-download') )
			{
				if( button.is('.avia_button_inactive') ) 
				{
					return false;
				}			
				
				//	Check if we have a 1 click import and demo was downloaded - in this case do not ask again to start import
				if( ! ( immediate_delete && wrap.hasClass( 'av-demo-was-downloaded' ) ) )
				{
					execImport = confirm( confirm_message );
					if( execImport === false ) 
					{
						return false;
					}
				}
			}
			else
			{
				if( button.hasClass('avia_button_doing_download') )  
				{
					return false;
				}
				
				//	Check to allow import because we have a 1 click import
				if( immediate_delete )
				{
					execImport = confirm( confirm_message );
					if( execImport === false ) 
					{
						return false;
					}
				}
				
				exec_download = true;
			}
			
			var send_data = {
							action: 'avia_ajax_import_data',
							_wpnonce: me.nonceImport,
							_wp_http_referer: me.ref,
							subaction: exec_download ? 'download_demos' : 'import_demos',
							demo_full_name: button.data('demo_full_name')
					};
					
			if( wrap.hasClass('av-downloadable-zip') )
			{
				send_data.download_url = button.data('download');
				send_data.import_dir = button.data('import');
				send_data.demo_name = button.data('demo_name');
				waitLabel = wrap.hasClass('av-demo-must-download') ? waitLabelDownload : waitLabelImport;
			}
			else
			{
				send_data.files = button.data('files');
				waitLabel = waitLabelImport;
			}
			
			$.ajax({
						type: "POST",
						url: me.ajaxUrl,
						data: send_data,
						beforeSend: function()
						{
							//show loader
							$('.avia_import_loading',  container).css({opacity:0, display:"block", visibility:'visible'}).animate({opacity:1});
							button.addClass('avia_button_inactive');
							demo_wrap.addClass('av-demo-ajax-active');
							if( exec_download )
							{
								button.addClass('avia_button_doing_download');
							}
							
							waitLabel.slideDown();
						},
						error: function()
						{
							//script error occured
							var msg = '';
							
							if( exec_download )
							{
								msg = download_error;
							}
							else
							{
								msg = import_error;
							}
							
							$('body').avia_alert({	the_class:'error', 
													text:msg, 
													show:4500});
													
							button.removeClass('avia_button_inactive');
						},
						success: function(response)
						{
							var msg = '';
							
							if( response.match('avia_error') )
							{
								button.removeClass('avia_button_inactive');
								response = response.replace('avia_error-','')
												   .replace('avia_import-','');
								
								$('body').avia_alert({	the_class:'error',
														text: response, 
														show: 15500 });
							}
							else if(response.match('avia_import'))
							{
								response = response.replace('avia_import-','')
												   .replace('<p>Remember to update the passwords and roles of imported users.</p>','');
								
//								var resultcontainer = $('.avia_import_result', me.container);
								//resultcontainer.css('display','none').html(response).slideDown();
								
								if( immediate_delete )
								{
									setTimeout( function(){
										delete_button.find( '.avia_import_delete_demo_button' ).trigger( 'click' );
									}, 500 );
								}
								
								$('body').avia_alert({text: response, show:4500}, function()
								{
									if( ! immediate_delete )
									{
										window.location.hash = "#wpwrap";
										window.location.reload( true );
									}
								});
							}
							else if( response.match('avia_downloaded') )
							{
								response = response.replace('avia_downloaded-','');
								
								if( ! immediate_delete )
								{
									$('body').avia_alert({	text:response,
															show:4500});
								}
													
								wrap.removeClass('av-demo-must-download').addClass('av-demo-downloaded').addClass('av-demo-was-downloaded');
								if( delete_button.length > 0 )
								{
									delete_button.removeClass('av-demo-must-download').addClass('av-demo-downloaded');
								}
								button.removeClass('avia_button_inactive');
								
								if( immediate_delete )
								{
									setTimeout( function(){
										button.trigger( 'click' )
									}, 500 );
								}
							}
							else
							{
								button.removeClass('avia_button_inactive');
								
								//script was called but aborted before finishing import or invalid parameters
								if( exec_download )
								{
									msg = download_error + script_error + response;
								}
								else
								{
									msg = import_error + script_error + response;
								}
								
								$('body').avia_alert({	the_class:'error', 
														text:msg,
														show:4500},
														function() { window.location.reload(true); }
									
						 			);
							}
						},
						complete: function(response)
						{	
							$('.avia_import_loading',  container).fadeOut();
							waitLabel.slideUp();
							button.removeClass('avia_button_doing_download');
							demo_wrap.removeClass('av-demo-ajax-active');
						}
					});
					
			return false;
		},
		
		/**
		 * Reset only selected options button.
		 */
		resetSelected: function(passed)
		{
			passed.preventDefault();
			
			var button = $(this);
			var filter_keys = [ 'filter_tabs', 'filter_values', 'skip_tabs', 'skip_values' ];
			var filter = {};
			var found = false;
			var data = button.data();

			/*
			 * It is possible to use a reset button and add filters hardcoded using filter_keys when defining the element.
			 * If a single filterkey exists we do not check for input fields
			 */
			$.each( filter_keys, function( index, value )
			{
				if( 'undefined' != typeof data[value] )
				{
					filter[value] = data[value];
					found = true;
				}
			});

			if( ! found )
			{
				var container = button.closest('#avia_upload');

				if( container.find('input#reset_filter_checkbox:checked').length > 0)
				{
					if( container.find('input#reset_keep_quick_css:checked').length > 0)
					{
						filter.skip_values = 'avia:quick_css';
					}

					var selectedValues = container.find('select#reset_filter_tabs').val();

					//	jQuery > 3.0 returns 0
					if( null != selectedValues && selectedValues.length > 0 )
					{
						filter.filter_tabs = selectedValues.join(',');
					}
				}
			}

			passed.data.set.filter = filter;
			passed.data.set.button_id = 'undefined' != typeof( button.attr('id') ) ? button.attr('id') : '';
			passed.data.set.object.reset.call( this, passed );
		},
		
		
		/**
		 *  reset all options by removing the database set that saves them.
		 *  Since 4.6.4 we also support filtered reset.
		 */
		reset: function(passed)
		{
			var me = passed.data.set;
			var button = me.container.find( '.avia_reset' );

			if( button.hasClass( 'avia_reset_inactive' ) )
			{
				alert( 'Reset of theme options has been blocked by option settings - no options have been changed.' );
				return false;
			}
			
			var filter = 'undefined' != typeof( me.filter ) ? me.filter : {};
			var button_id = 'undefined' != typeof( me.button_id ) ? me.button_id : '';
		
			var	answer = confirm("This will delete every theme setting made so far and revert the theme option pages to factory settings. \nDo you really want to do that? ");
			
			if(answer)
			{
				$.ajax({
						type: "POST",
						url: me.ajaxUrl,
						data: 
						{
							action: me.actionReset,
							avia_filter: filter,
							avia_id: button_id,
							avia_request: true,
							_wpnonce: me.nonceReset,
							_wp_http_referer: me.ref
						},
						beforeSend: function()
						{
							//show loader
							$('.avia_header .avia_loading, .avia_footer .avia_loading, .avia_reset_selected_button .avia_loading',  me.container).css({opacity:0, display:"block", visibility:'visible'}).animate({opacity:1});
						},
						error: function()
						{
							//allow saving again
							$('body').avia_alert({the_class:'error', text:'Resetting didnt work! <br/> Please wait a few seconds and try again', show:4500});
							
						},
						success: function(response)
						{
							if(response.match('avia_reset'))
							{
								$('body').avia_alert({text: 'Alright!<br/>Reset of options was successful. <br/>The page will now be reloaded to reflect the changes'}, function()
									{
										// window.location.hash = "#goto_importexport";
										window.location.hash = "wpwrap";
										window.location.reload(true);
									});
							}
							else
							{	
								var answer = "";
								
								if(response.length > 3)
								{
									answer = 'Resetting didnt work! <br/>The script returned the following error: <br/><br/>'+response;
								}
								else
								{
									answer = 'Resetting didnt work! <br/> Please wait a few seconds and try again';
								}
							
								$('body').avia_alert({	the_class:'error', 
														text: answer, 
														show:4500});
							}
						
						},
						complete: function(response)
						{	
							$('.avia_loading',  me.container).fadeOut();
						}
					});
			}
			
			return false;
		}
		
	};	//	end methods
	
})(jQuery);	 


(function($)
{
	$.fn.avia_alert = function(variables, callback) 
	{
		var defaults = 
		{
			the_class: 'success',		//success, alert
			text:  'Alright!<br/>All Options saved, no problems whatsoever.',
			show:	2200
		};
		
		var options = $.extend(defaults, variables);
		
		return this.each(function()
		{
			var container = $(this),
				notification = $('<div/>').addClass('avia_notification avia_notification_'+options.the_class)
										  .css('opacity',0)
										  .html('<div class="avia_notification_close_icon" href="#"></div><span class="avia_notification_icon"></span><div>'+options.text+'</div>')
										  .appendTo(container);
								  
				notification.find('.avia_notification_close_icon').on( 'click', function(e){
													e.preventDefault();
													$(this).closest('.avia_notification').hide();
													return false;
											});
										  
				notification.animate({opacity:0.9}, function()
				{
					notification.delay(options.show).fadeOut(function()
					{
						notification.remove();
						if(typeof callback == 'function') callback();
					});
				});
		});
	};
})(jQuery);	


(function($)
{
	$.fn.avia_popups = function(variables, callback) 
	{
		var defaults = 
		{
			template: '<div class="avia-popup {extra_class}"><div class="avia-popup-inner"><a href="#" class="popup-close script-close-avia-popup">×</a>{content}</div></div><div class="avia-popup-backdrop"></div>',
			selector: '*[data-avia-popup], .av-modal-image'
		};
		
		var options  = $.extend(defaults, variables),
			_self	 = this,
			_body	 = $('body'),
			popup_open = false,
			$template = $();
		
		_self.on('click', options.selector, function()
		{
			var current  		= $(this),
				templateName 	= current.data('avia-popup'),
				template		= "",
				extra_class		= "";
				
				if( current.is('.av-modal-image') ) 
				{
					template = "<img src='" + this.href + "' alt='' title='' class='av-modal-popup-image' />";
					extra_class = "av-modal-window-autoposition";
				}
				else
				{
					template = $('#'+ templateName).html();
				}
				
				options.template = options.template.replace('{content}', template);
				options.template = options.template.replace('{extra_class}', extra_class);
				
				$template = $(options.template).appendTo(_body);
				popup_open = true;
				return false;
		});
		
		
		_self.on('click', '.script-close-avia-popup, .avia-popup-backdrop', function()
		{
			popup_open = false;
			$('.avia-popup-backdrop, .avia-popup').remove();
			return false;
		});
		
		_self.on('keydown', function(e)
		{
			if (popup_open == true && e.keyCode == 27)
			{ 
				popup_open = false;
				$('.avia-popup-backdrop, .avia-popup').remove();
				return false;
			}
		});
		
	};
	
})(jQuery);	

