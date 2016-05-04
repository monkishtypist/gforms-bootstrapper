/**
	 * Gform Bootstraper JS for configuring the horizontal layout value
	 * Applys when user select Layout type Horizontal
	 */
jQuery(document).ready(function(){

var get_layout_value = jQuery( "#bootstrap_form_layout_settings" ).val();
	
	checkFormLayoutValue(get_layout_value);// Function call when the admin updates the form settings
	
/* Function for getting the value of Bootstrap Layout value */	
jQuery( "#bootstrap_form_layout_settings" ).change(function() {
	get_layout_value = jQuery(this).val();
	checkFormLayoutValue(get_layout_value);
});

/* Function for checking the value of Bootstrap Layout ( Bootstrap Column Widths will be shown when the Bootstrap Layout value is horizontal. Otherwise it is Hidden.) */
function checkFormLayoutValue(layoutValue)
{
	if(layoutValue == 'horizontal')
	{
		jQuery('#bootstrap_form_columns_settings').fadeIn();
	}
	else
	{
		jQuery('#bootstrap_form_columns_settings').fadeOut();
	}
}
});