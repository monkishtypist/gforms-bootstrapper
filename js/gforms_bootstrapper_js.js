(function($){

	/**
	 * File Uploads - Multiple files
	 * Fix for Horizontal layout to move uploaded files to right col offset
	 */
	$("form").each(function(){
		var formId	= $(this).attr("id").split("_").pop(),
			div		= $(this).find('div[id^="gform_preview_'+formId+'_"]'),
			lcol	= $(this).attr('lcol'),
			rcol	= $(this).attr('rcol');
		
		if ( $(div).siblings(".ginput_container").size() > 0 ) {
			var colClass = $(div).addClass('col-sm-'+rcol);
			var offClass = $(div).addClass('col-sm-offset-'+lcol);
		}
	});
	/* end file uploads fix */
		
})(jQuery);