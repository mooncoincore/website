
/**
 * Add a linebreak tag that is not removed by wp_autop and allows multiple empty lines
 */
	
(function($)
{
	"use strict";

	if(typeof avia_globals == "undefined") return;
	
	var av_key = 'av_builder_linebreak';		// $this->button['id']
	var content = avia_globals.sc[av_key].content_open;
	var access_key = typeof avia_globals.sc[av_key].access_key != 'undefined' && avia_globals.sc[av_key].access_key != '' ? avia_globals.sc[av_key].access_key : '';
	var title = avia_globals.sc[av_key].title;
	if( '' != access_key )
	{
		title += ' (' + access_key + ')';
	}
	
	tinymce.create('tinymce.plugins.'+av_key, {
				init : function(editor, url) {
						editor.addButton( av_key, {
								 title : title,
								 image : avia_globals.sc[av_key].image, 
								 onclick : function() {
												editor.selection.setContent( content );
											}
								});
						if( access_key != '' )
						{
							editor.shortcuts.add( access_key, avia_globals.sc[av_key].title, function() {
													editor.selection.setContent( content );
												  });
						}
					},
				createControl : function(n, cm) {
											return null;
								}
				});
				
	tinymce.PluginManager.add( av_key, tinymce.plugins[av_key] );

})(jQuery);