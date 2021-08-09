/**
 * This file holds the main javascript functions needed for the functionallity of the widget area creator at wp-admin/widgets.php
 *
 * @author		Christian "Kriesi" Budschedl
 * @copyright	Copyright ( c ) Christian Budschedl
 * @link		http://kriesi.at
 * @link		http://aviathemes.com
 * @since		Version 1.0
 * @package 	AviaFramework
 */

(function($)
{
	var AviaSidebar = function(){
    
        this.widget_wrap = $('.widget-liquid-right');
        this.widget_area = $('#widgets-right');
        this.widget_add  = $('#avia-tmpl-add-widget');
       
        this.create_form();
        this.add_del_button();
        this.bind_events();
    		
	};
	
    AviaSidebar.prototype = {
    
        create_form: function()
        {
            this.widget_wrap.append(this.widget_add.html());
            this.widget_name = this.widget_wrap.find('input[name="avia-sidebar-widgets"]');
            this.nonce       = this.widget_wrap.find('input[name="avia-delete-custom-sidebar-nonce"]').val();   
        },
        
        add_del_button: function()
        {
            this.widget_area.find('.sidebar-avia-custom').append('<span class="avia-custom-sidebar-area-delete"></span>');
        },
        
        bind_events: function()
        {
            this.widget_wrap.on('click', '.avia-custom-sidebar-area-delete', $.proxy( this.delete_sidebar, this));
        },
        
        
        //delete the sidebar area with all widgets within, then re calculate the other sidebar ids and re save the order
        delete_sidebar: function(e)
        {
        	var delete_it = confirm("Do you really want to delete this widget area?");

        	if(delete_it == false) return false;
        
            var widget      = $(e.currentTarget).parents('.widgets-holder-wrap').eq( 0 ),
                title       = widget.find('.sidebar-name h3 , .sidebar-name h2'),
                spinner     = title.find('.spinner'),
                widget_name = title.text().trim(),
                obj         = this;
				
            $.ajax({
	 		  type: "POST",
	 		  url: window.ajaxurl,
	 		  data: {
	 		     action: 'avia_ajax_delete_custom_sidebar',
	 		     name: widget_name,
	 		     _wpnonce: obj.nonce
	 		  },
	 		  
	 		  beforeSend: function()
	 		  {
	 		        spinner.addClass('activate_spinner');
	 		  },
	 		  success: function(response)
	 		  {     
                   if(response == 'sidebar-deleted')
                   {
                        widget.slideUp(200, function(){
                            
                            $('.widget-control-remove', widget).trigger('click'); //delete all widgets inside
                            widget.remove();
                            
                            
                            //re calculate widget ids - no longer necessary since wp 4.2
                            
/*
                            obj.widget_area.find('.widgets-holder-wrap .widgets-sortables').each(function(i) 
                            {
                                $(this).attr('id','sidebar-' + (i + 1));
                            });
                            
                            wpWidgets.saveOrder();
*/
                            
                        });
                   } 
	 		  }
	 		});
        }
    
    };
	
	$(function()
	{
		new AviaSidebar();
 	});

	
})(jQuery);	 

(function($)
{
	var objAviaWidgetConditionalHide = null;

	/*
	 * Implement an easy CSS solution for conditional hiding of input elements in widget backend page
	 * 
	 * Currently only supports select boxes
	 */
	var AviaWidgetConditionalHide = function(){
		
		objAviaWidgetConditionalHide = this;
		
		this.body = $( "body" );
			
		this.bind_events();
		
		this.body.find('form .widget-content .avia-coditional-widget-select').trigger('change');
	};
	
	AviaWidgetConditionalHide.prototype = {
		
		bind_events: function()
		{
			this.body.on( 'change', 'form .widget-content .avia-coditional-widget-select', this.select_changed );
			
			$(document).on( 'widget-updated', function(){
						var save_button = $( this.activeElement );
						var select = save_button.closest('form').find('.avia-coditional-widget-select');
						if( select.length > 0 )
						{
							select.trigger('change');
						}
				});
		},
		
		select_changed: function( event )
		{
			var select = $(this);
			var form_container = select.closest( '.avia_widget_conditional_form' ).first();
			
			select.find('option').each( function(){
						var value = $(this).val();
						form_container.removeClass( value );
					});
					
			var option_selected = select.find('option:selected');
			var value = option_selected.val();
			form_container.addClass( value );
		}

		
	};
	
	$(function()
	{
		new AviaWidgetConditionalHide();
 	});
	
})(jQuery);	 

(function($)
{
	/**
	 * Allow colorpicker in widget area - only necessary when a new widget is added.
	 * Already existing colorpickers are correctly initialised on page load.
	 * 
	 * To work correctly you need to add:
	 *		- class avia-colorpicker-to-attach for template
	 *		- class avia-colorpicker-attached to already existing widgets
	 */
	var sidebar_colorpicker = function() { 
			
			var self = this;
			$(document).on( 'widget-added', function(){
						self.check_colorpickers();
				});
				
			$(document).on( 'widget-updated', function( e ){
						
						var button = $( this.activeElement );
						var form = button.closest( 'form' );
						var widget = form.closest( 'div.widgets-sortables' );
						var pickers = form.find( '.avia_color_picker.avia-colorpicker-attached' );
						pickers.removeClass( 'avia-colorpicker-attached' ).addClass( 'avia-colorpicker-to-attach' );
						self.check_colorpickers( widget );
				});
			
	};
	
	sidebar_colorpicker.prototype = {
		
		check_colorpickers: function( widget ){
					
					var widget_areas = 'undefined' != typeof widget ? widget : $( 'div.widgets-sortables' );
					if( widget_areas.length == 0 )
					{
						return;
					}
					
					var colorpicker = widget_areas.find( '.avia_color_picker.avia-colorpicker-to-attach' );
					
					colorpicker.each( function( index ) {
						
								var color = $( this );
								color.removeClass( 'avia-colorpicker-to-attach' ).addClass( 'avia-colorpicker-attached' );
								
								//	We need to select surrounding container otherwise it will not work !!!
								color.closest( '.avia_colorpicker_style_wrap' ).avia_color_picker_activation();
							});
				}
	};
	
	var obj_cp = new sidebar_colorpicker();
	
	
})(jQuery);	 
