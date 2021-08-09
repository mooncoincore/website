/* 
 * Adds support for custom element templates in backend
 * 
 * @since 4.8
 */

(function($)
{
	"use strict";
	
	/**
	 * 
	 * @param {AviaBuilder} builder_obj
	 */
    $.AviaElementTemplates = function( builder_obj )
    {
		// reference to main object
		this.builder = builder_obj;
		
		// html container
		this.body_container = $( 'body' ).eq( 0 );
		
		this.builder_mode = [];
		
		//	in ms timeout for modal popup e.g. success messages
		this.modalAutocloseTime = 2000;
		
		// container for elements
		this.elements_container = $( '.layout-builder-wrap .avia_layout_builder_custom_elements' );
		
		// the canvas we use to display the interface
        this.canvas_elements = $( '.layout-builder-wrap #aviaALBCustomElements' );
		
		// field that stores alb content
        this.secureContent_elements = $( '.layout-builder-wrap #aviaALBCleanDataCustomElements' );
		
		// toggle ALB and custom elements
		this.custom_element_toggle = $( '#avia-select-shortcode-type-dropdown' );
		
		//	set default behaviuor 
		this.subitem_element_handling = 'one-element';
		
		if( this.body_container.hasClass( 'avia-subitem-individual-element' ) )
		{
			this.subitem_element_handling = 'individual-element';
		}
		else if( this.body_container.hasClass( 'avia-subitem-no-element' ) )
		{
			this.subitem_element_handling = 'no-element';
		}
		
		this.custom_elements_enabled = this.body_container.hasClass( 'avia-custom-elements-enabled' );
		
		//buttons in ALB shortcode tabs area
		this.tab_container = [];
		this.add_new_btn = [];
		this.edit_btn = [];
		this.end_edit_btn = [];
		this.custom_element_select = [];
		
	};
	
	$.AviaElementTemplates.prototype = {
		
		init: function()
		{
			if( this.custom_element_toggle.length <= 0 )
			{
				return;
			}
			
			this.tab_container = this.custom_element_toggle.closest( '.avia-tab-container' );
			this.add_new_btn = this.tab_container.find( '.element-button-add-new' ).first();
			this.edit_btn = this.tab_container.find( '.element-button-edit' ).first();
			this.end_edit_btn = this.tab_container.find( '.element-button-end-edit' ).first();
			this.custom_element_select = this.tab_container.find( '.av-custom-element-select-buttons select' ).first();
			
			this.builder.save_modal_handler = this.save_element_modal;
			this.builder.close_modal_handler = this.close_element_modal;
			
			this.add_behaviour();
		},
		
		add_behaviour: function()
		{
			this.hookEvents();
			this.toggleShortcodeButtonTypes();
			this.toggleCondensedCustomElementTab();
			this.createNewElementButton();
			this.editElementsButton();
			this.filterCustomEditScButtons();
			this.bindShortcodeButtonActions();
		},
		
		//	hook into events
		hookEvents: function()
		{
			var obj = this;
			
			this.tab_container.on( 'avia_shortcode_buttons_sorted', function( e )
			{
				//	resorting buttons removes the action handlers
				obj.bindShortcodeButtonActions.call( obj, 'reset' );
			});
			
			this.body_container.on( 'avia_builder_expanded', function( e, data )
			{
				var builder = data.builder;
				var sc_container = obj.builder.shortcode_wrap;
				
				if( sc_container.hasClass( 'avia-edit-elements-clicked' ) )
				{
					obj.end_edit_btn.trigger( 'click' );
					builder.find( '.avia-fixed-controls .shortcode_button_wrap' ).removeClass( 'avia-edit-elements-clicked' );
				}
			});
		},
		
		clearEditingArea: function()
		{
			if( this.builder_mode.length == 0 )
			{
				this.builder_mode = this.builder.canvas.closest( '.layout-builder-wrap' ).first();
			}
			
			//	skip in debug mode
			if( this.builder_mode.hasClass( 'avia_mode_debug' ) )
			{
				return;
			}
			
			this.canvas_elements.empty();
			this.secureContent_elements.val( '' );
		},
		
		//	Allow user to select which type of shortcode element icons are available in ALB. Also shows/hides the edit buttons for custom elements.
		toggleShortcodeButtonTypes: function()
		{
			var obj = this;
			
			if( obj.custom_element_toggle.hasClass( 'avia-condensed' ) )
			{
				return;
			}
			
			obj.custom_element_toggle.on( 'click', 'a', function( e )
			{
				e.preventDefault();
				
				obj.custom_element_toggle.find( 'a' ).removeClass( 'shortcode-type-active' );
				var link = $(this);
				var container = link.closest( '.avia-tab-title-container' );
				var sel = link.data( 'sc_type' );
				var text = link.html();
				
				obj.custom_element_toggle.find( '.avia-sc-type-label' ).html( text );
				link.addClass( 'shortcode-type-active' );
				
				if( sel == 'custom' )
				{
					container.find( '.avia-alb-tab' ).removeClass( 'active-tab' ).hide();
					obj.tab_container.find( '.av-custom-element-buttons' ).show();
					container.find( '.avia-custom-element-tab' ).show().first().addClass( 'avia-needs-margin ' ).trigger( 'click' );
				}
				else
				{
					container.find( '.avia-custom-element-tab').removeClass( 'active-tab' ).hide();
					obj.tab_container.find( '.av-custom-element-buttons' ).hide();
					container.find( '.avia-alb-tab' ).show().first().addClass( 'avia-needs-margin' ).trigger( 'click' );
				}
			});
			
			setTimeout( function() 
						{ 
							var sel = obj.custom_element_toggle.data( 'init_sc_type' );
							var link = obj.custom_element_toggle.find( 'a[data-sc_type="' + sel + '"]' );
							
							if( link.length == 0 )
							{
								link = obj.custom_element_toggle.find( '.avia-shortcode-type-list-element a' ).first();
							}
							
							link.trigger('click'); 
							
						}, 100 );
		},
		
		//	handle only one tab for custom elements
		toggleCondensedCustomElementTab: function()
		{
			var obj = this;
			
			if( ! obj.custom_element_toggle.hasClass( 'avia-condensed' ) )
			{
				return;
			}
			
			obj.tab_container.addClass( 'avia-custom-elements-condensed' );
			
			var alb_tabs = obj.tab_container.find( 'a.avia-alb-tab' );
			var custom_tab = obj.tab_container.find( 'a.avia-custom-element-tab' );
			
			alb_tabs.on( 'avia-tab-title-container-clicked', function( e, tab )
			{
				obj.tab_container.addClass( 'av-alb-tab-selected' ).removeClass( 'av-custom-tab-selected' );
				obj.tab_container.find( '.av-custom-element-buttons' ).hide();
			});
			
			custom_tab.on( 'avia-tab-title-container-clicked', function( e, tab )
			{
				obj.tab_container.addClass( 'av-custom-tab-selected' ).removeClass( 'av-alb-tab-selected' );
				obj.tab_container.find( '.av-custom-element-buttons' ).show();
			});
		},
		
		//	Open a modal popup, creates a basic template and open modal popup to edit
		createNewElementButton: function()
		{
			if( this.add_new_btn.length == 0 )
			{
				return;
			}
			
			var obj = this;
			var modal_content = $( '#avia-tmpl-add-new-element-modal-content' );
			
			if( modal_content.length == 0 )
			{
				alert( 'Missing script for creating a new template. Not able to create one.' );
				return;
			}
			
			this.add_new_btn.on( 'click', function( e )
			{
				var params = {};
				
				params.scope = obj;
				params.modal_title = modal_content.data( 'modal_title' );
				params.modal_class = 'avia-create-new-custom-element hide-save-button';
				params.modal_content = modal_content.html();
				params.on_load = 'modal_new_custom_element';
				params.on_save = obj.newElementCreated;
				params.button = '<a href="#create_element" class="avia-modal-create-new-element avia-modal-create-update-btn button button-primary button-large">' + modal_content.data( 'modal_button' ) + '</a>';;
				
				var modal = new $.AviaModal( params );
			});
			
		},
		
		//	Add logic to handle "Edit Elements" and "End Edit Elemnts"
		editElementsButton: function()
		{
			if( this.edit_btn.length == 0 || this.end_edit_btn.length == 0 )
			{
				return;
			}
			
			var sc_container = this.builder.shortcode_wrap;
			
			this.edit_btn.on( 'click', function( e )
			{
				sc_container.addClass( 'avia-edit-elements-clicked show-action-buttons' );
				
				setTimeout( function() { sc_container.removeClass( 'show-action-buttons' ); }, 1000 );
			});
			
			this.end_edit_btn.on( 'click', function( e )
			{
				sc_container.removeClass( 'avia-edit-elements-clicked' );
			});
		},
		
		//	filter the shortcode buttons in edit custom elements tab
		filterCustomEditScButtons: function()
		{
			if( this.custom_element_select.length == 0 )
			{
				return;
			}
			
			var obj = this;
			
			this.custom_element_select.on( 'change', function( e )
			{
				e.stopImmediatePropagation();
				
				var select = $( this );
				var selected = select.find('option:selected').val();
				
				obj.tab_container.removeClass( 'av-sc-buttons-custom-base-only av-sc-buttons-custom-item-only' );
				
				if( selected == 'base_elements_only' )
				{
					obj.tab_container.addClass( 'av-sc-buttons-custom-base-only' );
				}
				else if( selected == 'item_elements_only' )
				{
					obj.tab_container.addClass( 'av-sc-buttons-custom-item-only' );
				}
				
			});
			
			var init = this.custom_element_select.data( 'initial_select' );
			if( 'undefined' == typeof init )
			{
				init = '';
			}
			
			this.custom_element_select.val( init ).trigger( 'change' );
		},
		
		//	Callback when modal popup created the new element - add it to canvas
		newElementCreated: function( response, element_updated )
		{
			element_updated = 'undefined' == typeof element_updated ? false : element_updated;
			
			var shortcode_wrap = this.builder.shortcode_wrap;
			
			$('#avia-loader-nonce').val( response._ajax_nonce);
			
			var tab_content = shortcode_wrap.find( '.av-custom-element-tab[data-custom_content_name="' + response.tab + '"]');
			if( tab_content.length == 0 )
			{
				tab_content = shortcode_wrap.find( '.av-custom-element-tab[data-custom_content_name="' + response.default_tab + '"]');
				if( tab_content.length == 0 )
				{
					tab_content = shortcode_wrap.find( '.av-custom-element-tab' ).first();
				}
				
				if( tab_content.length == 0 )
				{
					new $.AviaModalNotification( { mode: 'error', msg: response.no_tab_message } );
					return false;
				}
			}
			
			var firstIcon = null;
			
			var icons = response.icons;
			
			$.each( icons, function( iconID, icon )
			{
				var found = $( iconID );
				icon = $( icon );
				
				if( found.length > 0 )
				{
					found.replaceWith( icon );
				}
				else
				{
					tab_content.append( icon );
				}
				
				if( null === firstIcon )
				{
					firstIcon = icon;
				}
			});
			
			this.builder.replace_js_templates( response.js_templates );
			
			this.builder.activate_element_dragging( shortcode_wrap, '' );
			this.bindShortcodeButtonActions();
			
			if( false !== element_updated )
			{
				return true;
			}
			
			var edit = firstIcon.find( '.element-edit' );
			
			//	Allow modal to close before opening new one
			setTimeout(function() 
			{ 
				edit.trigger( 'click' ); 
			}, 300 );
			
			return true;
		},
		
		//	bind action buttons events to shortcode buttons (edit, delete, clone)
		bindShortcodeButtonActions: function( reset_handler )
		{
			var obj = this;
			
			//	There is no way to check if events are bound to the buttons
			//	Proposed way $._data( element, 'events' ); does not work (jQuery 3.5.1 and below)
			//	As a hack we add class av-handler-attached. When e.g. buttons are sorted there are no longer
			//	handlers attached - we need to remove the class manually
			var reset = 'undefined' == typeof reset_handler ? false : reset_handler;

			var actionButtons = [
				{ selector: '.element-edit', callback: 'editSingleElement' },
				{ selector: '.element-delete', callback: 'deleteSingleElement', nocheck: true },
				{ selector: '.element-clone', callback: 'cloneSingleElement' },
				{ selector: '.element-custom-action', callback: 'customActionSingleElement' },
			];
			
			$.each( actionButtons, function( i, btn ) 
			{
				if( false !== reset )
				{
					var reset_btns = obj.builder.shortcode_wrap.find( '.avia-custom-element-button ' + btn.selector );
					reset_btns.removeClass( 'av-handler-attached' );
				}
				
				var buttons = obj.builder.shortcode_wrap.find( '.avia-custom-element-button ' + btn.selector + ':not(.av-handler-attached)' );
				buttons.each( function( i, actionButton )
				{
					var action = $( actionButton );
					var elTemplateID = action.closest( '.avia-custom-elements-actions-overlay' ).data('el_template' );
					
					if( btn.nocheck !== true )
					{
						if( 'undefined' == typeof elTemplateID || $('#avia-tmpl-' + elTemplateID ).length == 0 )
						{
							action.remove();
							return;
						}
					}

					action.addClass( 'av-handler-attached' );
					action.on( 'click', { objElTemplate: obj }, obj[ btn.callback ] );
			
				});
			});
		},
		
		//	open modal popup to edit an element
		editSingleElement: function( event )
		{
			//	prevent to add element to default canvas
			event.stopImmediatePropagation();
			
			var obj = event.data.objElTemplate;
			var button = $( event.target );
			var overlay = button.closest( '.avia-custom-elements-actions-overlay' );
			var icon = button.closest( '.shortcode_insert_button ' );
			var templateID = overlay.data('el_template' );
			var element_id = overlay.data('element_id' );
			var script = $( '#avia-tmpl-' + templateID );
			var is_item = icon.data( 'is_item' );
			
			if( is_item === true && obj.subitem_element_handling != 'individual-element' )
			{
				alert( avia_modal_L10n.noPermission );
				return false;
			}
			
			if( script.length == 0 )
			{
				alert( avia_modal_L10n.noTemplate );
				return false;
			}
			
			var template = $( script.html() );
			
			var shortcode_select = icon.data( 'shortcode_handler' );
			if( is_item === true )
			{
				shortcode_select += ';' + icon.data( 'base_shortcode' );
			}
			
			template.data( 'custom_is_item', is_item );
			template.data( 'custom_modal_title', icon.data( 'modal_title' ) );
			template.data( 'custom_modal_subitem_title', icon.data( 'modal_subitem_title' ) );
			template.data( 'custom_element_title', icon.data( 'element_title' ) );
			template.data( 'custom_element_tooltip', icon.data( 'element_tooltip' ) );
			template.data( 'custom_element_shortcode_select', shortcode_select );
			
			obj.canvas_elements.html( template );
			obj.builder.updateTextarea( false, '#aviaALBCustomElements', '#aviaALBCleanDataCustomElements' );
			
			//	add template ID to textarea to be able to identify whot to update in backend
			obj.secureContent_elements.data( 'element_id', element_id );
			
			obj.canvas_elements.find( '.avia-edit-element' ).first().trigger( 'click' );
		},
		
		//	Route saving to default or element containers .... and save to DB if a custom element
		save_element_modal: function( values, element_container, return_it, obj_modal )
		{
			var element_containers = '';
			
			if( 'object' == typeof obj_modal && obj_modal.hasOwnProperty( 'modal' ) )
			{
				//	modal groups need no reroute
				if( obj_modal.modal.hasClass( 'avia-modal-edit-custom-element' ) && obj_modal.modal.hasClass( 'avia-base-shortcode' ) )
				{
					element_containers = '#aviaALBCustomElements,#aviaALBCleanDataCustomElements';
				}
			}
			
			var shortcode = this.send_to_datastorage( values, element_container, return_it, element_containers );
			
			//	for modal preview we only need to return the value
			if( return_it == 'return' )
			{
				return shortcode;
			}
			
			//	return for normal ALB elements or for a modal group 
			if( ! obj_modal.modal.hasClass( 'avia-modal-edit-custom-element' ) || obj_modal.modal.hasClass( 'avia-modal-group-shortcode' ) )
			{
				return shortcode;
			}
			
			//	we do not need to save to DB in this case
			if( obj_modal.modal.hasClass( 'element_template_changed' ) || obj_modal.options.template_changed === true )
			{
				obj_modal.options.template_changed = false;
				return shortcode;
			}
			
			obj_modal.show_loading_icon();
			obj_modal.disable_save_button();
			
			this.element_templates.saveSingleElementToDB( obj_modal, this, values, shortcode );
			
			//	do not close modal window
			return false;
		},
		
		close_element_modal: function( modal )
		{
			var builder = this;
			
			if( modal.hasClass( 'avia-base-shortcode' ) && modal.hasClass( 'avia-modal-edit-custom-element' ) )
			{
				if( ! modal.hasClass( 'element_template_changed' ) )
				{
					builder.element_templates.clearEditingArea();
				}
			}
		},
		
		//	Save element content to DB
		saveSingleElementToDB: function( modal, obj_builder, values_array, shortcode )
		{
			var obj = this;
			var builder = obj_builder;
			var element_id =  obj.secureContent_elements.data( 'element_id' );
			var close_modal = false;
			
			var senddata = {
							action: 'avia_alb_element_template_update_content',
							element_id: element_id,
							shortcode: shortcode,
							params: values_array,
							avia_request: true,
							_ajax_nonce: $('#avia-loader-nonce').val()
						};
						
			$.ajax({
						type: "POST",
						url: ajaxurl,
						dataType: 'json',
						cache: false,
						data: senddata,
						success: function(response, textStatus, jqXHR)
						{
							if( response.success == true )
							{
								$( '#avia-loader-nonce' ).val( response._ajax_nonce);
								
								builder.replace_js_templates( response.js_templates );
								
								new $.AviaModalNotification( { mode: 'success', msg: response.message, autoclose: obj.modalAutocloseTime } );
								
								close_modal = true;
							}
							else if( typeof response.expired_nonce != 'undefined' && response.expired_nonce != '' )
							{
								new $.AviaModalNotification( { mode: 'error', msg: response.expired_nonce } );
							}
							else if( typeof response.message != 'undefined' && response.message != '' )
							{
								new $.AviaModalNotification( { mode: 'error', msg: response.message } );
							}
						},
						error: function(errorObj) {
									new $.AviaModalNotification( { mode: 'error', msg: avia_modal_L10n.connection_error } );
								},
						complete: function(test) {
									modal.hide_loading_icon();
									modal.enable_save_button();
									
									if( close_modal )
									{
										modal.close();
									}
								}
					});
		},
		
		deleteSingleElement: function( event )
		{
			//	prevent to add element to default canvas
			event.stopImmediatePropagation();
			
			var obj = event.data.objElTemplate;
			var button = $( event.target );
			var overlay = button.closest( '.avia-custom-elements-actions-overlay' );
			var icon = button.closest( '.shortcode_insert_button ' );
			var loading = overlay.find( '.avia-sc-button-loading' );
			var templateID = overlay.data('template' );
			var el_templateID = overlay.data('el_template' );
			var element_id = overlay.data('element_id' );
			var script = $( '#avia-tmpl-' + templateID );
			var el_script = $( '#avia-tmpl-' + el_templateID );
			
			var title = icon.data( 'sort_name' );
			
			if( ! window.confirm( title + ': ' + avia_modal_L10n.deleteTemplate ) )
			{
				alert( title + ': ' + avia_modal_L10n.notDeletedTempl );
				return false;
			}
			
			loading.addClass( 'loading' );
			
			var success = false;
			
			var senddata = {
							action: 'avia_alb_element_template_delete',
							element_id: element_id,
							title: title,
							shortcode: icon.data( 'shortcode_handler' ),
							baseShortcode: icon.data( 'base_shortcode' ),
							isItem: icon.data( 'is_item' ),
							avia_request: true,
							_ajax_nonce: $('#avia-loader-nonce').val()
						};
						
			$.ajax({
					type: "POST",
					url: ajaxurl,
					dataType: 'json',
					cache: false,
					data: senddata,
					success: function(response, textStatus, jqXHR)
					{
						if( response.success == true )
						{
							$( '#avia-loader-nonce' ).val( response._ajax_nonce);
							
							icon.remove();
							script.remove();
							el_script.remove();
							success = true;
							
							new $.AviaModalNotification( { mode: 'success', msg: response.message, autoclose: obj.modalAutocloseTime } );
						}
						else if( typeof response.expired_nonce != 'undefined' && response.expired_nonce != '' )
						{
							new $.AviaModalNotification( { mode: 'error', msg: response.expired_nonce } );
						}
						else if( typeof response.message != 'undefined' && response.message != '' )
						{
							new $.AviaModalNotification( { mode: 'error', msg: response.message } );
						}
					},
					error: function(errorObj) {
								new $.AviaModalNotification( { mode: 'error', msg: avia_modal_L10n.connection_error } );
							},
					complete: function(test) {
								if( ! success )
								{
									loading.removeClass( 'loading' );
								}
							}
				});
			
		},
		
		customActionSingleElement: function( event )
		{
			var clicked = $( event.target );
			var button;
			
			if( clicked.hasClass( 'element-sc-action-button') )
			{
				button = clicked;
			}
			else
			{
				button = clicked.closest( '.element-sc-action-button' );
			}
			
			//	Currently only support opening link in new window (used by WPML to tranlate an element
			var url = button.data( 'external_link' );
			
			if( 'undefined' == typeof url )
			{
				return;
			}
			
			//	prevent to add element to default canvas
			event.stopImmediatePropagation();
			
			window.open( url, '_blank', 'noopener,noreferrer' );
			return false;
		},
		
		cloneSingleElement: function( event )
		{
			//	prevent to add element to default canvas
			event.stopImmediatePropagation();
			
			alert( 'Currently not implemented: Clone Element' );
		}
		
	};
	
	/**
	 * 
	 * @param {AviaModal} modal
	 * @returns {undefined}
	 */
	$.AviaModalElementTemplates = function( modal )
    {
		this.instance = this;
		this.objModal = modal;
		
		this.templateSelect = [];
		this.edit_subelements = [];
		this.show_locked_options = [];
		
		//	ManageCPTData
		this.select = null;
		this.title_input = null;
		this.save_btn = null;
		
		this.args = null;
		this.args_action = '';
		this.set_title = '',
		this.last_checked_title = '';
		this.hide_loading_count = 0;
	};
		
	$.AviaModalElementTemplates.prototype = 
	{
		//	Add behaviour for template handling
		initTemplateSupport: function()
		{
			var self = this;
			
			this.objModal.body.on( 'avia_modal_finished', function( e )
			{
				self.bindTemplateSelectBox();
				self.bindModalGroupSelectBox();
				self.bindLockedOptionsVisibility();
			});
		},
		
		bindTemplateSelectBox: function()
		{
			var obj = this.objModal;
			var self = this;
			
			//	avoid multiple assign of event handler
			if( this.templateSelect.length > 0 )
			{
				return;
			}
			
			this.templateSelect = obj.modal.find( 'select[data-template_selector="element"]' );
				
			if( this.templateSelect.length > 0 )
			{
				var selected = parseInt( this.templateSelect.find('option:selected').val(), 10 );
				if( typeof selected == 'number' && selected > 0 )
				{
					obj.modal.addClass( 'av-element-template-selected' ).removeClass( 'av-no-element-template' );
				}
				else
				{
					obj.modal.addClass( 'av-no-element-template' ).removeClass( 'av-element-template-selected' );
				}

				this.templateSelect.on( 'change' + obj.namespace, function(e)
				{
					var el = $( this );
					var i = self;

					//	if selected id is negativ, we are editing templates and user wants to have own element as template
					var selected = parseInt( el.find('option:selected').val(), 10 );

					if( typeof selected == 'number' && selected < 0 )
					{
						el.find('option[value="' + el.data( 'initial' ) + '"]').prop( 'selected', 'selected' );

						alert( avia_modal_L10n.alb_same_element );
						return false;
					}

					//	Set class to allow custom templates to skip saving to DB - not necessary at this moment
					obj.modal.addClass( 'element_template_changed' );
					obj.modal.find( '.avia-modal-save' ).trigger( 'click' );

					//	reopen with new template settings
					if( obj.options.obj_clicked != null )
					{
						if( typeof obj.options.save_param.addClass == 'function' )
						{
							obj.options.save_param.addClass( 'element_template_changed' );

							if( typeof selected == 'number' && selected > 0 )
							{
								obj.options.save_param.addClass( 'element_template_selected' ).removeClass( 'no_element_template' );
							}
							else
							{
								obj.options.save_param.addClass( 'no_element_template' ).removeClass( 'element_template_selected' );
							}
						}
						obj.options.obj_clicked.trigger( 'click' );
					}
				});
			}
		},
		
		//	If edit template and element with subelements we need to hide/show base element options
		bindModalGroupSelectBox: function()
		{
			var obj = this.objModal;
			
			//	avoid multiple assign of event handler
			if( this.edit_subelements.length > 0 )
			{
				return;
			}
			
			this.edit_subelements = obj.modal.find( '.av-elements-item-select select' );
			
			if( this.edit_subelements.length > 0 )
			{
				this.edit_subelements.on( 'change' + obj.namespace, function(e)
				{
					var el = $( this );
					var selected = el.find('option:selected').val();

					if( selected == 'item' )
					{
						obj.modal.addClass( 'avia-edit-item-template' ).removeClass( 'avia-edit-base-template' );
					}
					else
					{
						obj.modal.addClass( 'avia-edit-base-template' ).removeClass( 'avia-edit-item-template' );
					}
				});

				//	A saved element type cannot be changed
				if( obj.body.hasClass( 'post-php' ) )
				{
					this.edit_subelements.prop( 'disabled', 'disabled' );
				}

				this.edit_subelements.trigger( 'change' + obj.namespace );
			}
		},
		
		//	Hide or show locked options
		bindLockedOptionsVisibility: function()
		{
			var obj = this.objModal;
			
			//	avoid multiple assign of event handler
			if( this.show_locked_options.length > 0 )
			{
				return;
			}
			
			this.show_locked_options = obj.modal.find( 'input.avia-element-show-locked-options' );
			
			if( this.show_locked_options.length > 0 )
			{
				obj.modal.find( 'input.avia-element-show-locked-options' ).on( 'change'  + obj.namespace, function(e)
				{
					e.preventDefault();

					if( $(this).prop('checked') )
					{
						obj.modal.addClass( 'show-locked-input-element' );
					}
					else
					{
						obj.modal.removeClass( 'show-locked-input-element' );
					}

				});

				this.show_locked_options.trigger( 'change' + obj.namespace );
			}
		},
				
		
		//	Handle CPT actions in a modal popup:
		//	
		//		'new_custom_element':		Creates a new custom element template based on a clean ALB element
		//		'update_element_post_data':	Updates title and tooltip for an existing element
		//		'new_element_from_alb':		Creates a new custom element template based on the settings of an ALB element (supports base and item elements)
		//		
		ManageCPTData: function()
		{
			var default_args = {
							action: 'new_custom_element'
						};

			var self = this.objModal,
				title_message = $( '<div class="av-title-warning-message"></div>' );
				
			this.args = $.extend( {}, default_args, self.options.args );
			this.args_action = 'string' == typeof this.args.action ? this.args.action : 'new_custom_element';
			this.select = self.modal.find( 'select.av_add_new_element_shortcode' );
			this.title_input = self.modal.find( 'input.avia-elements-check-title' );
			this.save_btn = self.modal.find( '.avia-modal-create-update-btn' );
			this.set_title = this.args.title;
			this.last_checked_title = '';
			this.hide_loading_count = 0;

			this.title_input.after( title_message );
	
			this.select.on( 'change', { instance: this.instance }, this.selected_element_changed );
			this.title_input.on( 'keyup change', { instance: this.instance }, this.title_changed );
			this.save_btn.on( 'click', { instance: this.instance }, this.save_element_data );

			this.select.trigger( 'change' );
			this.title_input.trigger( 'change' );
		},
		
		show_loading: function()
		{
			this.hide_loading_count ++;
			this.objModal.show_loading_icon();
			this.save_button_visibility();
		},
			
		hide_loading: function( force_hide )
		{
			if( 'undefined' == typeof force_hide )
			{
				force_hide = false; 
			}

			this.hide_loading_count --;
			if( this.hide_loading_count < 0 )
			{
				this.hide_loading_count == 0;
			}

			if( force_hide || this.hide_loading_count == 0 )
			{
				this.objModal.hide_loading_icon();
			}

			this.save_button_visibility();
		},
		
		save_button_visibility: function()
		{
			var element = this.select.find('option:selected').val();
			var title = this.title_input.val().trim();

			if( element == '' || title.length <= 3 )
			{
				this.objModal.modal.addClass( 'hide-save-button' );
			}
			else
			{
				this.objModal.modal.removeClass( 'hide-save-button' );
			}

			if( this.hide_loading_count > 0 )
			{
				this.objModal.modal.addClass( 'disable-save-button' );
			}
			else
			{
				this.objModal.modal.removeClass( 'disable-save-button' );
			}
		},
		
		selected_element_changed: function( e )
		{
			e.preventDefault();
			
			var instance = e.data.instance;

			instance.save_button_visibility();
		},
			
		title_changed: function( e )
		{
			e.preventDefault();
			e.stopImmediatePropagation();
			
			var instance = e.data.instance;
			var el = $( this );
			var title = el.val().trim();

			if( instance.last_checked_title == title )
			{
				return;
			}

			instance.last_checked_title = title;

			instance.save_button_visibility();

			if( title.length > 3 )
			{
				instance.ajax_check_title( title, instance );
			}
		},
		
		save_element_data: function( e )
		{
			e.preventDefault();
			
			var instance = e.data.instance;
			var self = instance.objModal;

			if( ! self.canSaveModal() )
			{
				return false;
			}

			//	remove attribute otherwise this field is ignored
			instance.select.prop( 'disabled', false );
			var modal_params = self.get_final_values();
			instance.select.prop( 'disabled', true );

			switch( instance.args_action )
			{
				case 'new_element_from_alb':
					break;
				case 'update_element_post_data':
					modal_params.element_id = instance.args.element_id;
					break;
			}

			var closeModal = false;
			var senddata = {
						action: 'avia_alb_element_template_cpt_actions',
						subaction: instance.args_action,
						modal_params: modal_params,
						ajax_param: self.options.ajax_param,
						avia_request: true,
						_ajax_nonce: $('#avia-loader-nonce').val()
					};

			instance.show_loading();

			$.ajax({
					type: "POST",
					url: ajaxurl,
					dataType: 'json',
					cache: false,
					data: senddata,
					success: function(response, textStatus, jqXHR)
					{
						if( response.success == true )
						{
							if( typeof self.options['on_save'] == 'function' )
							{
								self.options['on_save'].call( self.options.scope, response );
							}

							setTimeout(function() 
							{
								var autoclose = $.avia_builder.element_templates.modalAutocloseTime;
								new $.AviaModalNotification( { mode: 'success', msg: response.message, autoclose: autoclose } );
							}, 400 );

							closeModal = true;
						}
						else if( typeof response.expired_nonce != 'undefined' && response.expired_nonce != '' )
						{
							new $.AviaModalNotification( { mode: 'error', msg: response.expired_nonce } );
						}
						else if( typeof response.message != 'undefined' && response.message != '' )
						{
							new $.AviaModalNotification( { mode: 'error', msg: response.message } );
						}
					},
					error: function(errorObj) {
								new $.AviaModalNotification( { mode: 'error', msg: avia_modal_L10n.connection_error } );
							},
					complete: function(test) {
								instance.hide_loading( true );
								if( closeModal )
								{
									self.close();
								}
							}
				});

		},
		
		ajax_check_title: function( title, instance )
		{
			var modal = instance.objModal.modal,
				title_message = modal.find( '.av-title-warning-message' );

			if( title === '' || title === instance.set_title )
			{
				title_message.html( '' );
				return;
			}

			instance.show_loading();

			var senddata = {
						action: 'avia_alb_element_check_title',
						title: title,
						avia_request: true,
						_ajax_nonce: $('#avia-loader-nonce').val()
					};
			$.ajax({
					type: "POST",
					url: ajaxurl,
					dataType: 'json',
					cache: false,
					data: senddata,
					success: function(response, textStatus, jqXHR)
					{
						if( response.success == true )
						{
							$('#avia-loader-nonce').val( response._ajax_nonce);
							title_message.html( response.message );
						}
					},
					error: function(errorObj) {
//									console.log( 'avia_alb_shortcode_buttons_order error: ', errorObj );
							},
					complete: function(test) {
								instance.hide_loading();
							}
				});
		}

	};

})(jQuery);	 

