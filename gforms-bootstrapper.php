<?php
/*
Plugin Name: Gravity Forms Bootstrapper
Plugin URI: https://github.com/monkishtypist/gforms-bootstrapper
Description: A Gravity Forms add-on to add Bootstrap CSS classes to forms
Version: 1.1
Author: MonkishTypist
Author URI: http://www.monkishtypist.com
Documentation: http://www.gravityhelp.com/documentation/page/GFAddOn

------------------------------------------------------------------------
Copyright 2018 MonkishTypist

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

------------------------------------------------------------------------
Things to do...

- Icons and error messages? Validation messages?

- Email confirmation

- Date field issues

- Number field issues

*/

//exit if accessed directly
if(!defined('ABSPATH')) exit;

//------------------------------------------
if (class_exists("GFForms")) {
    GFForms::include_addon_framework();

    class GFBootstrapper extends GFAddOn {

        protected $_version = "1.0";
        protected $_min_gravityforms_version = "1.8.9999";
        protected $_slug = "gforms-bootstrapper";
        protected $_path = "gforms-bootstrapper/gforms-bootstrapper.php";
        protected $_full_path = __FILE__;
        protected $_title = "GForms Bootstrapper";
        protected $_short_title = "Bootstrap Settings";


        public function pre_init(){
            parent::pre_init();
            // add tasks or filters here that you want to perform during the class constructor - before WordPress has been completely initialized
        }

        public function init(){
            parent::init();
            add_filter( 'gform_form_tag', array($this, 'bootstrap_form_tag'), 10, 2);
            add_filter( 'gform_submit_button', array($this, 'bootstrap_submit_button'), 10, 2 );
            //add_filter("gform_field_content", array($this, "bootstrap_styles_for_gravityforms_fields"), 10, 5);
        }

        public function init_admin(){
            parent::init_admin();
            // add tasks or filters here that you want to perform only in admin
            add_filter( 'gform_form_settings', array($this, 'bootstrap_setting_submit_button_classes'), 10, 2 );
            add_filter( 'gform_pre_form_settings_save', array($this, 'save_bootstrap_setting_submit_button_classes') );
            add_filter( 'gform_form_settings', array($this, 'bootstrap_setting_submit_button_size'), 10, 2 );
            add_filter( 'gform_pre_form_settings_save', array($this, 'save_bootstrap_setting_submit_button_size') );
            add_filter( 'gform_form_settings', array($this, 'bootstrap_setting_submit_button_alignment'), 10, 2 );
            add_filter( 'gform_pre_form_settings_save', array($this, 'save_bootstrap_setting_submit_button_alignment') );
            add_filter( 'gform_form_settings', array($this, 'bootstrap_setting_form_layout'), 10, 2 );
            add_filter( 'gform_pre_form_settings_save', array($this, 'save_bootstrap_setting_form_layout') );
            add_filter( 'gform_form_settings', array($this, 'bootstrap_setting_form_columns'), 10, 2 );
            add_filter( 'gform_pre_form_settings_save', array($this, 'save_bootstrap_setting_form_columns') );
            add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' ); // Add "hidden" option for Sub-Label placement
        }

        public function init_frontend(){
            parent::init_frontend();
            // add tasks or filters here that you want to perform only in the front end
            // if ( ! wp_style_is( 'bootstrap-css' ) && ! wp_style_is( 'pgb-bootstrap-css' ) ) {
            //     add_action( 'wp_enqueue_scripts', array($this, 'gform_default_bootstrap_styles'), 10 );
            // }
            // if ( ! wp_script_is( 'bootstrap' ) && ! wp_script_is( 'pgb-bootstrapjs' ) ) {
            //     add_action( 'wp_enqueue_scripts', array($this, 'gform_default_bootstrap_scripts'), 10 );
            // }
            add_action( 'wp_enqueue_scripts', array($this, 'gform_bootstrapper_styles'), 10 );
            add_action( 'wp_enqueue_scripts', array($this, 'gform_bootstrapper_scripts'), 10 );
            add_filter( 'gform_field_css_class', array($this, 'gform_bootstrapper_css_classes'), 10, 3);
            add_filter( 'gform_field_content', array($this, 'gform_bootstrapper_field_content'), 10, 5 );
            add_filter( 'gform_field_input' , array($this, 'gform_bootstrapper_field_input'), 10, 5 );
            add_filter( 'gform_get_form_filter', array($this, 'gform_bootstrapper_gravity_form_filter'), 10, 2 );
            add_filter( 'gform_tabindex', '__return_false' );
        }

        public function init_ajax(){
            parent::init_ajax();
            // add tasks or filters here that you want to perform only during ajax requests
        }

        /**
         * gform_form_tag
         *
         * This filter is executed when the form is displayed and can be used to completely change the form tag (i.e. <form method="post">).
         *
         */
        public function bootstrap_form_tag($form_tag, $form){
            $settings = $this->get_form_settings($form);
            $col_r = ( isset($form['bootstrap_form_columns']) ? $form['bootstrap_form_columns'] : 10 );
            $col_l = 12 - $col_r;
            if ( ! isset($form['bootstrap_form_layout']) || $form['bootstrap_form_layout'] == 'basic' ) {
                $form_tag = str_replace( '<form ', '<form class="form-basic form-bootstrapped" ', $form_tag );
            }
            else if ( $form['bootstrap_form_layout'] == 'inline' ) {
                $form_tag = str_replace( '<form ', '<form class="form-inline form-bootstrapped" ', $form_tag );
            }
            else if ( $form['bootstrap_form_layout'] == 'horizontal' ) {
                $form_tag = str_replace( '<form ', '<form class="form-horizontal form-bootstrapped" lcol="'.$col_l.'" rcol="'.$col_r.'" ', $form_tag );
            }
            return $form_tag;
        }

        /**
         * gform_submit_button
         *
         * This filter is executed when the form is displayed and can be used to completely change the form button tag (i.e. <input type="submit">).
         *
         */
        public function bootstrap_submit_button( $button, $form ) {
            $settings = $this->get_form_settings($form);
            $col_r = ( isset($form['bootstrap_form_columns']) ? $form['bootstrap_form_columns'] : 10 );
            $col_l = 12 - $col_r;
            $classes = 'btn btn-primary ';
            $classes .= isset( $form['button']['bootstrap_submit_classes'] ) ? $form['button']['bootstrap_submit_classes'] : '';
            if ( isset($settings['btnsize']) ) {
                if ( $settings['btnsize'] == 'large' )
                    $classes .= 'btn-lg ';
                if ( $settings['btnsize'] == 'small' )
                    $classes .= 'btn-sm ';
            }
            $button = preg_replace( '/(class\=)("|\')([\w\s\-\_]+)("|\')/i', 'class="'.$classes.'"', $button );
            $button = str_replace( '>', 'data-loading-text="Processing..." >', $button );
            
            if ( isset( $form['bootstrap_form_layout'] ) && $form['bootstrap_form_layout'] == 'horizontal' ) {
                $button = '<div class="col-sm-offset-'.$col_l.' col-sm-'.$col_r.'">' . $button . '</div>';
            }
            return $button;
        }

        /**
         * gform_field_css_class
         *
         * This filter can be used to dynamically add/remove CSS classes to a field
         *
         */
        public function gform_bootstrapper_css_classes( $classes, $field, $form ){
            if ( $field['type'] == "name" ) {
                $classes .= " custom_name_class";
            }
            $settings = $this->get_form_settings( $form );
            $has_error = ( $field['failed_validation'] == 1 ? 'has-error' : '' );
            $size = ( isset( $settings['btnsize'] ) ? $settings['btnsize'] : '' );
            $classes .= " form-group " . $has_error;
            if ( isset( $form['bootstrap_form_layout'] ) && $form['bootstrap_form_layout'] == 'horizontal' ) {
                if ( $size == 'large' )
                    $classes .= " form-group-lg";
                if ( $size == 'small' )
                    $classes .= " form-group-sm";
            }
            return $classes;
        }

        /**
         * gform_field_input
         *
         * This filter is executed before creating the field's input tag, allowing users to modify the field's input tag. It can also be used to create custom field types.
         *
         */
        public function gform_bootstrapper_field_input( $input, $field, $value, $lead_id, $form_id ) {
            $form = GFAPI::get_form( $form_id );
            $settings = $this->get_form_settings( $form );
            $layout = 'basic';
            if ( isset( $form['bootstrap_form_layout'] ) ) {
                $layout = $form['bootstrap_form_layout'];
            }
            $col_r = ( isset( $form['bootstrap_form_columns'] ) ? $form['bootstrap_form_columns'] : 10 );

            $_input_type = 'default';
            $field_type = ( $field->type == 'hidden' ? 'hidden' : 'text' );
            $input_before = '';
            $input_after = '';
            $input_array = array(
                'name'          => 'input_' . $field->id,
                'id'            => 'input_' . $field->formId . '_' . $field->id,
                'type'          => $field_type,
                'value'         => $value,
                'class'         => 'form-control ' . $field->size . ' ',
                'placeholder'   => $field->placeholder,
                );

            switch ( $field->type ) {

                /**
                 * Address
                 *
                 * @return string
                 */
                case 'address':
                    $_input_type = false;

                    $_cols_arr = array( 'col-sm-12', 'col-sm-12', 'col-sm-12', 'col-sm-12', 'col-sm-12', 'col-sm-12' );

                    $input_this = array();

                    $input_before = '';

                    if ( $field->enableCopyValuesOption ) {
                        $input_before = '<div class="checkbox copy_values_option_container col-sm-12" id="input_' . $field->formId . '_' . $field->id . '_copy_values_option_container">'.
                                '<label for="input_' . $field->formId . '_' . $field->id . '_copy_values_activated" id="input_' . $field->formId . '_' . $field->id . '_copy_values_option_label" class="copy_values_option_label">'.
                                    '<input '.
                                        'name="input_' . $field->id . '_copy_values_activated" '.
                                        'type="checkbox" '.
                                        'value="' . $field->enableCopyValuesOption . '" '.
                                        'id="input_' . $field->formId . '_' . $field->id . '_copy_values_activated" '.
                                        'class="copy_values_activated" '.
                                        ( ( isset( $value[ $field->id . '_copy_values_activated' ] ) && $value[ $field->id . '_copy_values_activated' ] == 1 ) || ( ! isset( $value[ $field->id . '_copy_values_activated' ] ) && $field->copyValuesOptionDefault == 1 ) ? 'checked="checked"' : '' ) . '>'.
                                    $field->copyValuesOptionLabel.
                                '</label>'.
                            '</div>';
                    }

                    foreach ($field->inputs as $k => $v) {

                        $_input_id = explode( '.', $v['id'] );

                        if ( isset( $v['isHidden'] ) && $v['isHidden'] == 1 ) {
                            $_cols_arr[ $k ] = 'hidden';
                        }

                        $_this_label = ( ( ! isset( $v['isHidden'] ) || $v['isHidden'] != 1 ) ? '<label '.
                                'for="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '" '.
                                'id="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '_label">'.
                                ( isset( $v['customLabel'] ) ? $v['customLabel'] : $v['label'] ).
                            '</label>' : '' );

                        if ( $v['label'] == 'Country' && $field->addressType == 'international' ) {
                            $_this_input = $this->gform_country_select( $field->id, $_input_id[1], $field->formId, $value );
                        }
                        elseif ( $v['label'] == 'Country' && ( $field->addressType == 'us' || $field->addressType == 'canadian' ) ) {
                            $_this_input = '<input '.
                                    'type="' . ( ! isset( $v['isHidden'] ) || $v['isHidden'] != 1 ? 'text' : 'hidden' ) . '" '.
                                    'name="input_' . $v['id'] . '" '.
                                    'id="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '" '.
                                    'class="form-control ' . $field->size . '" '.
                                    'value="' . $field->defaultCountry . '" '.
                                    ( isset( $v['isHidden'] ) && $v['isHidden'] == 1 ? '' : 'readonly' ) . ' />';
                        }
                        else {
                            $_this_input = '<input '.
                                    'type="' . ( ! isset( $v['isHidden'] ) || $v['isHidden'] != 1 ? 'text' : 'hidden' ) . '" '.
                                    'name="input_' . $v['id'] . '" '.
                                    'id="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '" '.
                                    'class="form-control ' . $field->size . '" '.
                                    'value="' . ( ! empty( $value[ (string) $v['id'] ] ) ? $value[ (string) $v['id'] ] : ( isset( $v['defaultValue'] ) ? $v['defaultValue'] : ( $v['label'] == 'ZIP / Postal Code' ? $field->defaultState : '' ) ) ) . '" '.
                                    'placeholder="' . ( isset( $v['placeholder'] ) ? $v['placeholder'] : '' ) . '" />';
                        }
                        switch ( $field->subLabelPlacement ) {
                            case 'below':
                                $input_this[] = '<div class="' . $_cols_arr[ $k ] . '">' . $_this_input . $_this_label . '</div>';
                                break;
                            case 'hidden':
                            case 'hidden_label':
                                $input_this[] = '<div class="' . $_cols_arr[ $k ] . '">' . $_this_input . '</div>';
                                break;
                            case 'above':
                            default:
                                $input_this[] = '<div class="' . $_cols_arr[ $k ] . '">' . $_this_label . $_this_input . '</div>';
                                break;
                        }
                    }

                    $input = '<div class="row">'. 
                        $input_before. 
                        '<div '.
                        'id="input_' . $field->formId . '_' . $field->id . '" '.
                        'class="ginput_complex" '.
                        ( 
                            ( isset( $value[ $field->id . '_copy_values_activated' ] ) && $value[ $field->id . '_copy_values_activated' ] == 1 ) 
                            || ( ! isset( $value[ $field->id . '_copy_values_activated' ] ) && $field->copyValuesOptionDefault == 1 ) 
                                ? 'style="display: none;" ' 
                                : '' 
                        ).
                        '>'.
                        implode('', $input_this).
                        '</div></div>';
                    
                    break;

                /**
                 * Checkboxes
                 *
                 * @return string
                 */                
                case 'checkbox':
                    $_input_type = false;
                    $input_this = array();
                    $is_conditional = ( is_array( $field->conditionalLogicFields ) && ! empty( $field->conditionalLogicFields ) 
                        ? 'onclick="gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . ']);" ' 
                        : ' ' );
                    foreach ($field->choices as $k => $v) {
                        $input_this[] = '<div class="checkbox" id="input_' . $field->formId . '_' . $field->id . '_' . ($k+1) . '">'.
                                '<label for="choice_' . $field->formId . '_' . $field->id . '_' . ($k+1) . '" id="label_' . $field->formId . '_' . $field->id . '_' . ($k+1) . '">'.
                                    '<input '.
                                        'name="input_' . $field->inputs[ $k ]['id'] . '" '.
                                        'type="checkbox" '.
                                        'value="' . $v['value'] . '" '.
                                        'id="choice_' . $field->formId . '_' . $field->id . '_' . ($k+1) . '" '.
                                        $is_conditional.
                                        ( 
                                            ( ! empty( $value[ (string) $field->inputs[ $k ]['id'] ] ) && $v['value'] == $value[ (string) $field->inputs[ $k ]['id'] ] ) 
                                                ? 'checked="checked"' 
                                                : ( empty( $value ) && isset( $v['isSelected'] ) && $v['isSelected'] == 1 
                                                    ? 'checked="checked"' 
                                                    : '' ) 
                                        ) . '>'.
                                    $v['text'].
                                '</label>'.
                            '</div>';
                    }
                    $input = implode('', $input_this);
                    break;
                
                /**
                 * Date
                 *
                 * @return string
                 */
                case 'date':
                    switch ($field->dateType) {
                        // Date Field is 3 input fields
                        case 'datefield':
                            $_input_type = false;
                            $input_this = array();
                            foreach ($field->inputs as $k => $v) {
                                switch ($v['label']) {
                                    case 'DD':
                                        $min = 1;
                                        $max = 31;
                                        break;
                                    
                                    case 'MM':
                                        $min = 1;
                                        $max = 12;
                                        break;
                                    
                                    case 'YYYY':
                                        $min = date("Y") - 100;
                                        $max = date("Y") + 100;
                                        break;
                                    
                                    default:
                                        # code...
                                        break;
                                }
                                $input_this[] = '<div class="gfield_date_day col-4 col-xs-4 col-sm-2 col-md-1" id="input_' . $field->formId . '_' . $field->id . '_' . ($k+1) . '_container">
                                    <input 
                                        type="number" 
                                        maxlength="' . strlen($v['label']) . '" 
                                        name="input_' . $field->id . '[]" 
                                        id="input_' . $field->formId . '_' . $field->id . '_' . ($k+1) . '" 
                                        class="form-control ' . $field->size . '" 
                                        value="' . $v['defaultValue'] . '" 
                                        min="' . $min . '" 
                                        max="' . $max . '" 
                                        step="1" 
                                        placeholder="' . $v['placeholder'] . '">
                                    <label for="input_' . $field->formId . '_' . $field->id . '_' . ($k+1) . '">' . ( ( isset($v['customLabel']) && ! empty($v['customLabel']) ) ? $v['customLabel'] : $v['label'] ) . '</label>
                                </div>';
                            }
                            $input = '<div class="row">' . implode('', $input_this) . '</div>';
                            break;
                        // datedropdown is 3 select fields
                        case 'datedropdown':
                            $_input_type = false;
                            $input_this = array();
                            $order = array_slice( str_split( $field->dateFormat ), 0, 3 );
                            foreach ( $order as $k => $v ) {
                                switch ( $v ) {
                                    case 'm':
                                        $min = 1;
                                        $max = 12;
                                        $index = 0;
                                        break;
                                    case 'd':
                                        $min = 1;
                                        $max = 31;
                                        $index = 1;
                                        break;
                                    case 'y':
                                        $min = date("Y") - 100;
                                        $max = date("Y") + 100;
                                        $index = 2;
                                        break;
                                }
                                $_this_input = '<div class="gfield_date_day col-sm-4" id="input_' . $field->formId . '_' . $field->id . '_' . ($index+1) . '_container">
                                    <select name="input_' . $field->id . '[]" id="input_' . $field->formId . '_' . $field->id . '_' . ($index+1) . '" class="form-control ' . $field->size . '">
                                        <option>' . ( ( isset( $field->inputs[ $index ]['customLabel'] ) && ! empty( $field->inputs[ $index ]['customLabel'] ) ) ? $field->inputs[ $index ]['customLabel'] : $field->inputs[ $index ]['label'] ) . '</option>';
                                for ( $i = $min; $i < $max + 1 ; $i++) { 
                                    $_this_input .= '<option value="' . $i . '" ' . ( $value[ $k ] == $i ? 'selected="selected"' : ( empty( $value[ $k ] ) && $field->inputs[ $index ]['defaultValue'] == $i ? 'selected="selected"' : ( empty( $value[ $k ] ) && empty( $field->inputs[ $index ]['defaultValue'] ) && date("Y") == $i ? 'selected="selected"' : '' ) ) ) . '>' . $i . '</option>';
                                }
                                $_this_input .= '</select></div>';
                                $input_this[] = $_this_input;
                            }
                            $input = '<div class="row">' . implode('', $input_this) . '</div>';
                            break;
                        // datepicker is jquery datepicker UI
                        case 'datepicker':
                        default:
                            $input_before = '<div class="input-group">';
                            $classes = array(
                                'datepicker',
                                'form-control',
                                $field->dateFormat,
                                $field->size
                            );
                            $input_array['class'] = implode(' ', $classes);
                            $input_after = '</div>';
                            if ( $field->calendarIconType == 'calendar' ) {
                                $input_after = '<div class="input-group-addon">
                                    <img class="ui-datepicker-trigger" src="' . get_bloginfo('url') . '/wp-content/plugins/gravityforms/images/calendar.png" alt="..." title="...">
                                    <input type="hidden" id="gforms_calendar_icon_input_' . $field->formId . '_' . $field->id . '" class="gform_hidden" value="' . get_bloginfo('url') . '/wp-content/plugins/gravityforms/images/calendar.png">
                                    </div>' . $input_after;
                            }
                            break;
                    }
                    break;
                
                /**
                 * Email
                 *
                 * @return string
                 */
                case 'email':
                    $input_array['type'] = 'email';
                    break;
                
                /**
                 * File Upload
                 *
                 * @return string
                 */
                case 'fileupload':
                    $max_upload = (int)(ini_get('upload_max_filesize'));
                    $maxFileSize = $field->maxFileSize * 1024 * 1024;
                    if ( $field->multipleFiles != 1 ) {
                        $input_before = '<input type="hidden" name="MAX_FILE_SIZE" value="' . min($max_upload, $maxFileSize) . '">';
                        $input_array['type'] = 'file';
                    }
                    else {
                        $_input_type = false;
                        // fixes for multi-file upload field through CSS and JS
                    }
                    break;
                
                /**
                 * List
                 *
                 * @return string
                 */
                case 'list':
                    $_input_type = false;
                    $_colgroup  = array();
                    $_thead     = array();
                    $_tr        = array();
                    // define choices
                    $_n = 1;
                    if ( is_array($field->choices) ) {
                        foreach ($field->choices as $k => $v) {
                            $_colval = $v['value'];
                            $_colgroup[] = '<col id="gfield_list table_'.$field->id.'_col_'.$_n.'" class="gfield_list table_col_'.($_n % 2 == 0 ? 'even' : 'odd').'">';
                            $_thead[] = '<th>'.$v['text'].'</th>';
                            $_n++;
                        }
                        $_colgroup[] = '<col id="gfield_list table_'.$field->id.'_col_'.$_n.'" class="gfield_list table_col_'.($_n % 2 == 0 ? 'even' : 'odd').'">';
                        
                        // define value rows/cols
                        $_m = 1;
                        $_c = count( $value );
                        foreach ($value as $k => $v) {
                            $_m = $_l   = 1;
                            $_td        = array();
                            $_tr[ $k ]  = '<tr class="gfield_list table_row_'.($_m % 2 == 0 ? 'even' : 'odd').' gfield_list_row_'.($_m % 2 == 0 ? 'even' : 'odd').'">';
                            foreach ($v as $w) {
                                $_td[] = '<td class="gfield_list table_cell table_'.$field->id.'_cell'.$_l.'"><input type="text" name="input_'.$field->id.'[]" value="'.$w.'" class="form-control" /></td>';
                                $_l++;
                            }
                            $_td[] = '<td class="gfield_list table_icons '.( empty( $field->addIconUrl ) ? 'icons-default' : '' ).'">
                                    <img '.
                                        'src="'.( ! empty( $field->addIconUrl ) ? $field->addIconUrl : plugins_url() . '/gravityforms/images/blankspace.png' ).'" '.
                                        'class="add_list_item" '.
                                        'title="Add another row" '.
                                        'alt="Add a row" '.
                                        'onclick="gformAddListItem(this, 0)" '.
                                        'style="cursor:pointer; margin:0 3px;" '.
                                        '>'.
                                    '<img '.
                                        'src="'.( ! empty( $field->deleteIconUrl ) ? $field->deleteIconUrl : plugins_url() . '/gravityforms/images/blankspace.png' ).'" '.
                                        'title="Remove this row" '.
                                        'alt="Remove this row" '.
                                        'class="delete_list_item" '.
                                        'style="cursor: pointer; '.($_c == 1 ? 'visibility: hidden;' : '').'" '.
                                        'onclick="gformDeleteListItem(this, 0)" '.
                                        '>'.
                                '</td>';
                            $_tr[ $k ] .= implode( "\n", $_td );
                            $_tr[ $k ] .= '</tr>';
                            $_m++;
                        }
                        $input = '<input type="text" id="input_'.$field->formId.'_'.$field->id.'_shim" style="position:absolute;left:-999em;" onfocus="jQuery( &quot;#field_'.$field->formId.'_'.$field->id.' table tr td:first-child input&quot; ).focus();">'.
                            '<table class="gfield_list table">'.
                                '<colgroup>'.
                                    implode( "\n", $_colgroup ).
                                '</colgroup>'.
                                '<thead>'.
                                    '<tr>'.
                                        implode( "\n", $_thead ).
                                        '<th>&nbsp;</th>'.
                                    '</tr>'.
                                '</thead>'.
                                '<tbody>'.
                                    implode( "\n", $_tr ).
                                '</tbody>'.
                                '<style type="text/css">'.
                                '/* add SVG background image support for retina devices -------------------------------*/'.
                                'img.add_list_item {background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiPjxnIGlkPSJpY29tb29uLWlnbm9yZSI+PC9nPjxwYXRoIGQ9Ik0yNTYgNTEyYy0xNDEuMzc1IDAtMjU2LTExNC42MDktMjU2LTI1NnMxMTQuNjI1LTI1NiAyNTYtMjU2YzE0MS4zOTEgMCAyNTYgMTE0LjYwOSAyNTYgMjU2cy0xMTQuNjA5IDI1Ni0yNTYgMjU2ek0yNTYgNjRjLTEwNi4wMzEgMC0xOTIgODUuOTY5LTE5MiAxOTJzODUuOTY5IDE5MiAxOTIgMTkyYzEwNi4wNDcgMCAxOTItODUuOTY5IDE5Mi0xOTJzLTg1Ljk1My0xOTItMTkyLTE5MnpNMjg4IDM4NGgtNjR2LTk2aC05NnYtNjRoOTZ2LTk2aDY0djk2aDk2djY0aC05NnY5NnoiPjwvcGF0aD48L3N2Zz4=);}'.
                                'img.delete_list_item {background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiPjxnIGlkPSJpY29tb29uLWlnbm9yZSI+PC9nPjxwYXRoIGQ9Ik0yNTYgMGMtMTQxLjM3NSAwLTI1NiAxMTQuNjI1LTI1NiAyNTYgMCAxNDEuMzkxIDExNC42MjUgMjU2IDI1NiAyNTYgMTQxLjM5MSAwIDI1Ni0xMTQuNjA5IDI1Ni0yNTYgMC0xNDEuMzc1LTExNC42MDktMjU2LTI1Ni0yNTZ6TTI1NiA0NDhjLTEwNi4wMzEgMC0xOTItODUuOTY5LTE5Mi0xOTJzODUuOTY5LTE5MiAxOTItMTkyYzEwNi4wNDcgMCAxOTIgODUuOTY5IDE5MiAxOTJzLTg1Ljk1MyAxOTItMTkyIDE5MnpNMTI4IDI4OGgyNTZ2LTY0aC0yNTZ2NjR6Ij48L3BhdGg+PC9zdmc+);}'.
                                'img.add_list_item,'.
                                'img.delete_list_item {width: 1em;height: 1em;background-size: 1em 1em;opacity: 0.5;}'.
                                'img.add_list_item:hover,img.add_list_item:active,img.delete_list_item:hover,img.delete_list_item:active {opacity: 1.0;}'.
                                '</style>'.
                            '</table>';
                    }
                    else {
                        for ($i = 1; $i <= 2; $i++) { 
                            $_colgroup[] = '<col id="gfield_list_'.$field->id.'_col'.$i.'" class="gfield_list_col_'.($i % 2 == 0 ? 'even' : 'odd').'">';
                        }
                        if ( is_array($value) ) {
                            $_c = count( $value );
                            foreach ($value as $k => $v) {
                                $_m = $_l   = 1;
                                $_td        = array();
                                $_tr[ $k ]  = '<tr class="gfield_list_row_'.($_m % 2 == 0 ? 'even' : 'odd').'">';
                                foreach ($v as $w) {
                                    $_td[] = '<td class="gfield_list_cell table_'.$field->id.'_cell'.$_l.'"><input type="text" name="input_'.$field->id.'[]" value="'.$w.'" class="form-control" /></td>';
                                    $_l++;
                                }
                                $_td[] = '<td class="gfield_list_icons '.( empty( $field->addIconUrl ) ? 'icons-default' : '' ).'">
                                        <img '.
                                            'src="'.( ! empty( $field->addIconUrl ) ? $field->addIconUrl : plugins_url() . '/gravityforms/images/blankspace.png' ).'" '.
                                            'class="add_list_item" '.
                                            'title="Add another row" '.
                                            'alt="Add a row" '.
                                            'onclick="gformAddListItem(this, 0)" '.
                                            'style="cursor:pointer; margin:0 3px;" '.
                                            '>'.
                                        '<img '.
                                            'src="'.( ! empty( $field->deleteIconUrl ) ? $field->deleteIconUrl : plugins_url() . '/gravityforms/images/blankspace.png' ).'" '.
                                            'title="Remove this row" '.
                                            'alt="Remove this row" '.
                                            'class="delete_list_item" '.
                                            'style="cursor: pointer; '.($_c == 1 ? 'visibility: hidden;' : '').'" '.
                                            'onclick="gformDeleteListItem(this, 0)" '.
                                            '>'.
                                    '</td>';
                                $_tr[ $k ] .= implode( "\n", $_td );
                                $_tr[ $k ] .= '</tr>';
                                $_m++;
                            }
                        }
                        else {
                            $_m = $_l = $_c = 1;
                            $_td        = array();
                            $_tr = '<tr class="gfield_list_row_'.($_m % 2 == 0 ? 'even' : 'odd').'">';
                            $_td[] = '<td class="gfield_list_cell table_'.$field->id.'_cell'.$_l.'"><input type="text" name="input_'.$field->id.'[]" value="" class="form-control" /></td>';
                            $_td[] = '<td class="gfield_list_icons">
                                    <img '.
                                        'src="'.( ! empty( $field->addIconUrl ) ? $field->addIconUrl : plugins_url() . '/gravityforms/images/blankspace.png' ).'" '.
                                        'class="add_list_item" '.
                                        'title="Add another row" '.
                                        'alt="Add a row" '.
                                        'onclick="gformAddListItem(this, 0)" '.
                                        'style="cursor:pointer; margin:0 3px;" '.
                                        '>'.
                                    '<img '.
                                        'src="'.( ! empty( $field->deleteIconUrl ) ? $field->deleteIconUrl : plugins_url() . '/gravityforms/images/blankspace.png' ).'" '.
                                        'title="Remove this row" '.
                                        'alt="Remove this row" '.
                                        'class="delete_list_item" '.
                                        'style="cursor: pointer; '.($_c == 1 ? 'visibility: hidden;' : '').'" '.
                                        'onclick="gformDeleteListItem(this, 0)" '.
                                        '>'.
                                '</td>';
                            $_tr .= implode( "\n", $_td );
                            $_tr .= '</tr>';
                        }
                        $input = '<input type="text" id="input_'.$field->formId.'_'.$field->id.'_shim" style="position:absolute;left:-999em;" onfocus="jQuery( &quot;#field_'.$field->formId.'_'.$field->id.' table tr td:first-child input&quot; ).focus();">'.
                            '<table class="gfield_list table">'.
                                '<colgroup>'.
                                    implode( "\n", $_colgroup ).
                                '</colgroup>'.
                                '<tbody>'.
                                    $_tr.
                                '</tbody>'.
                                '<style type="text/css">'.
                                '/* add SVG background image support for retina devices -------------------------------*/'.
                                'img.add_list_item {background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiPjxnIGlkPSJpY29tb29uLWlnbm9yZSI+PC9nPjxwYXRoIGQ9Ik0yNTYgNTEyYy0xNDEuMzc1IDAtMjU2LTExNC42MDktMjU2LTI1NnMxMTQuNjI1LTI1NiAyNTYtMjU2YzE0MS4zOTEgMCAyNTYgMTE0LjYwOSAyNTYgMjU2cy0xMTQuNjA5IDI1Ni0yNTYgMjU2ek0yNTYgNjRjLTEwNi4wMzEgMC0xOTIgODUuOTY5LTE5MiAxOTJzODUuOTY5IDE5MiAxOTIgMTkyYzEwNi4wNDcgMCAxOTItODUuOTY5IDE5Mi0xOTJzLTg1Ljk1My0xOTItMTkyLTE5MnpNMjg4IDM4NGgtNjR2LTk2aC05NnYtNjRoOTZ2LTk2aDY0djk2aDk2djY0aC05NnY5NnoiPjwvcGF0aD48L3N2Zz4=);}'.
                                'img.delete_list_item {background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiPjxnIGlkPSJpY29tb29uLWlnbm9yZSI+PC9nPjxwYXRoIGQ9Ik0yNTYgMGMtMTQxLjM3NSAwLTI1NiAxMTQuNjI1LTI1NiAyNTYgMCAxNDEuMzkxIDExNC42MjUgMjU2IDI1NiAyNTYgMTQxLjM5MSAwIDI1Ni0xMTQuNjA5IDI1Ni0yNTYgMC0xNDEuMzc1LTExNC42MDktMjU2LTI1Ni0yNTZ6TTI1NiA0NDhjLTEwNi4wMzEgMC0xOTItODUuOTY5LTE5Mi0xOTJzODUuOTY5LTE5MiAxOTItMTkyYzEwNi4wNDcgMCAxOTIgODUuOTY5IDE5MiAxOTJzLTg1Ljk1MyAxOTItMTkyIDE5MnpNMTI4IDI4OGgyNTZ2LTY0aC0yNTZ2NjR6Ij48L3BhdGg+PC9zdmc+);}'.
                                'img.add_list_item,'.
                                'img.delete_list_item {width: 1em;height: 1em;background-size: 1em 1em;opacity: 0.5;}'.
                                'img.add_list_item:hover,img.add_list_item:active,img.delete_list_item:hover,img.delete_list_item:active {opacity: 1.0;}'.
                                '</style>'.
                            '</table>';
                    }
                    break;
                
                /**
                 * Multi-Select
                 *
                 * @return string
                 */
                case 'multiselect':
                    $_input_type = false;
                    $is_conditional = ( is_array( $field->conditionalLogicFields ) && ! empty( $field->conditionalLogicFields ) 
                        ? 'onchange="gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . ']);" '.
                            'onkeyup="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . '])&quot;, 300);" ' 
                        : ' ' );
                    $input = '<select multiple="multiple" '.
                        'name="input_' . $field->id . '" '.
                        'id="input_' . $field->formId . '_' . $field->id . '" '.
                        $is_conditional.
                        'class="form-control ' . $field->size . '">';
                    foreach ($field->choices as $k => $v) {
                        $input .= '<option value="' . $v['value'] . '" ' . ( ( ! empty( $value ) && $v['value'] == $value ) ? 'selected="selected"' : ( empty( $value ) && $v['isSelected'] == 1 ? 'selected="selected"' : '' ) ) . '>' . $v['text'] . '</option>';
                    }
                    $input .= '</select>';
                    break;
                
                /**
                 * Name
                 *
                 * @return string
                 */
                case 'name':
                    $_input_type = false;
                    $input_this = array();
                    
                    /**
                     * Count Columns based on visible inputs
                     *
                     * Available value / options
                     * p = prefix, f = first name, m = middle name, l = last name, s = suffix
                     * 0 - none
                     * 1 - p
                     * 2 - f
                     * 4 - m
                     * 8 - l
                     * 16 - s
                     *
                     * Outputs as binary: 10101, up to 5 digits/places
                     */
                    $_cols = 0;
                    foreach ($field->inputs as $k => $v) {
                        $_cols += ( isset( $v['isHidden'] ) && $v['isHidden'] == 1 ? 0 : pow( 2, $k ) );
                    }
                    // array of classes based on visible fields
                    $_cols_arr = array(
                        1 => array( 'col-sm-2', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg' ),
                        2 => array( 'hidden-sm hidden-md hidden-lg', 'col-sm-12', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg' ),
                        3 => array( 'col-sm-2', 'col-sm-10', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg' ),
                        4 => array( 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg' ),
                        5 => array( 'col-sm-2', 'hidden-sm hidden-md hidden-lg', 'col-sm-10', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg' ),
                        6 => array( 'hidden-sm hidden-md hidden-lg', 'col-sm-6', 'col-sm-6', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg' ),
                        7 => array( 'col-sm-2', 'col-sm-5', 'col-sm-5', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg' ),
                        8 => array( 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-12', 'hidden-sm hidden-md hidden-lg' ),
                        9 => array( 'col-sm-2', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-10', 'hidden-sm hidden-md hidden-lg' ),
                        10 => array( 'hidden-sm hidden-md hidden-lg', 'col-sm-6', 'hidden-sm hidden-md hidden-lg', 'col-sm-6', 'hidden-sm hidden-md hidden-lg' ),
                        11 => array( 'col-sm-2', 'col-sm-5', 'hidden-sm hidden-md hidden-lg', 'col-sm-5', 'hidden-sm hidden-md hidden-lg' ),
                        12 => array( 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-6', 'col-sm-6', 'hidden-sm hidden-md hidden-lg' ),
                        13 => array( 'col-sm-2', 'hidden-sm hidden-md hidden-lg', 'col-sm-5', 'col-sm-5', 'hidden-sm hidden-md hidden-lg' ),
                        14 => array( 'hidden-sm hidden-md hidden-lg', 'col-sm-4', 'col-sm-4', 'col-sm-4', 'hidden-sm hidden-md hidden-lg' ),
                        15 => array( 'col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-4', 'hidden-sm hidden-md hidden-lg' ),
                        16 => array( 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-2' ),
                        17 => array( 'col-sm-2', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-2' ),
                        18 => array( 'hidden-sm hidden-md hidden-lg', 'col-sm-10', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-2' ),
                        19 => array( 'col-sm-2', 'col-sm-8', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-2' ),
                        20 => array( 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-10', 'hidden-sm hidden-md hidden-lg', 'col-sm-2' ),
                        21 => array( 'col-sm-2', 'hidden-sm hidden-md hidden-lg', 'col-sm-8', 'hidden-sm hidden-md hidden-lg', 'col-sm-2' ),
                        22 => array( 'hidden-sm hidden-md hidden-lg', 'col-sm-5', 'col-sm-5', 'hidden-sm hidden-md hidden-lg', 'col-sm-2' ),
                        23 => array( 'col-sm-2', 'col-sm-4', 'col-sm-4', 'hidden-sm hidden-md hidden-lg', 'col-sm-2' ),
                        24 => array( 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-10', 'col-sm-2' ),
                        25 => array( 'col-sm-2', 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-8', 'col-sm-2' ),
                        26 => array( 'hidden-sm hidden-md hidden-lg', 'col-sm-5', 'hidden-sm hidden-md hidden-lg', 'col-sm-5', 'col-sm-2' ),
                        27 => array( 'col-sm-2', 'col-sm-4', 'hidden-sm hidden-md hidden-lg', 'col-sm-4', 'col-sm-2' ),
                        28 => array( 'hidden-sm hidden-md hidden-lg', 'hidden-sm hidden-md hidden-lg', 'col-sm-5', 'col-sm-5', 'col-sm-2' ),
                        29 => array( 'col-sm-2', 'hidden-sm hidden-md hidden-lg', 'col-sm-4', 'col-sm-4', 'col-sm-2' ),
                        30 => array( 'hidden-sm hidden-md hidden-lg', 'col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3' ),
                        31 => array( 'col-sm-2', 'col-sm-3', 'col-sm-2', 'col-sm-3', 'col-sm-2' )
                    );

                    foreach ($field->inputs as $k => $v) {
                        
                        if ( ! isset( $v['isHidden'] ) || $v['isHidden'] != 1 ) {
                            
                            $_input_id = explode( '.', $v['id'] );
                            switch ( $v['label'] ) {
                                
                                case 'Prefix':
                                    $_this_label = '<label for="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '">' . ( isset( $v['customLabel'] ) ? $v['customLabel'] : $v['label'] ) . '</label>';
                                    $_this_input = '<select name="input_' . $v['id'] . '" id="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '" class="form-control"><option value="' . ( isset($v['placeholder']) ? $v['placeholder'] : '' ) . '">' . ( isset($v['placeholder']) ? $v['placeholder'] : '' ) . '</option>';
                                    foreach ($v['choices'] as $kc => $vc) {
                                        $_this_input .= '<option 
                                            value="' . $vc['value'] . '" ' . 
                                            ( isset( $value[ $v['id'] ] ) && $value[ $v['id'] ] == $vc['value'] 
                                                ? 'selected="selected"' 
                                                : ( empty( $value[ $v['id'] ] ) && $v['defaultValue'] == $vc['value'] 
                                                    ? 'selected="selected"' 
                                                    : ( empty( $value[ (string) $v['id'] ] ) && ( ! isset( $v['defaultValue'] ) || $v['defaultValue'] != $vc['value'] ) && $vc['isSelected'] == 1 ? 'selected="selected"' : '' ) ) ) . 
                                            '>' . $vc['text'] . '</option>';
                                    }
                                    $_this_input .= '</select>';
                                    break;
                                
                                case 'First':
                                case 'Middle':
                                case 'Last':
                                case 'Suffix':
                                default:
                                    $_this_label = '<label for="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '">' . ( isset( $v['customLabel'] ) ? $v['customLabel'] : $v['label'] ) . '</label>';
                                    $_this_input = '<input type="text" name="input_' . $v['id'] . '" id="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '" class="form-control ' . $field->size . '" value="' . ( isset( $value[ $v['id'] ] ) ? $value[ $v['id'] ] : ( isset( $v['defaultValue'] ) ? $v['defaultValue'] : '' ) ) . '" placeholder="' . ( isset($v['placeholder']) ? $v['placeholder'] : '' ) . '">';
                                    break;
                            
                            }

                            if ( $field->subLabelPlacement == 'hidden_label' ) {
                                $input_this[] = '<div class="'.$_cols_arr[ $_cols ][ $k ].'">' . $_this_input . '</div>';
                            }
                            elseif ( $field->subLabelPlacement == 'above' ) {
                                $input_this[] = '<div class="'.$_cols_arr[ $_cols ][ $k ].'">' . $_this_label . $_this_input . '</div>';
                            }
                            else {
                                $input_this[] = '<div class="'.$_cols_arr[ $_cols ][ $k ].'">' . $_this_input . $_this_label . '</div>';
                            }
                        
                        }
                    }
                    $input = '<div class="row">' . implode('', $input_this) . '</div>';
                    break;
                
                /**
                 * Number
                 *
                 * @return string
                 */
                case 'number':
                    $input_array['type'] = 'text';
                    $input_array['step'] = 'any';
                    $input_array['min'] = $field->rangeMin;
                    $input_array['max'] = $field->rangeMax;
                    var_dump($field);
                    break;
                
                /**
                 * Phone
                 *
                 * @return string
                 */
                case 'phone':
                    $input_array['type'] = 'tel';
                    break;
                
                /**
                 * Radio
                 *
                 * @return string
                 */
                case 'radio':
                    $_input_type = false;
                    $is_conditional = ( is_array( $field->conditionalLogicFields ) && ! empty( $field->conditionalLogicFields ) 
                        ? 'onclick="gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . ']);" '
                        : ' ' );
                    foreach ($field->choices as $k => $v) {
                        $input .= '<div class="radio form-check form-check-inline" id="input_' . $field->formId . '_' . $field->id . '_' . ($k) . '">'.
                                '<label for="choice_' . $field->formId . '_' . $field->id . '_' . ($k) . '" id="label_' . $field->formId . '_' . $field->id . '_' . ($k) . '">'.
                                    '<input '.
                                        'name="input_' . $field->id . '" '.
                                        'type="radio" '.
                                        'value="' . $v['value'] . '" '.
                                        'id="choice_' . $field->formId . '_' . $field->id . '_' . ($k) . '" '.
                                        $is_conditional.
                                        ( ( ! empty( $value ) && $v['value'] == $value ) ? 'checked="checked"' : ( empty( $value ) && $v['isSelected'] == 1 ? 'checked="checked"' : '' ) ).
                                        '>'.
                                    $v['text'].
                                '</label>'.
                            '</div>';
                    }
                    break;
                
                /**
                 * Select
                 *
                 * @return string
                 */
                case 'select':
                    $_input_type = false;
                    $is_conditional = ( is_array( $field->conditionalLogicFields ) && ! empty( $field->conditionalLogicFields ) 
                        ? 'onchange="gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . ']);" '
                        : ' ' );
                    $input = '<select '.
                        'name="input_' . $field->id . '" '.
                        'id="input_' . $field->formId . '_' . $field->id . '" '.
                        $is_conditional.
                        'class="form-control ' . $field->size . '">';
                    foreach ($field->choices as $k => $v) {
                        $input .= '<option value="' . $v['value'] . '" ' . ( ( ! empty( $value ) && $v['value'] == $value ) ? 'selected="selected"' : ( empty( $value ) && $v['isSelected'] == 1 ? 'selected="selected"' : '' ) ) . '>' . $v['text'] . '</option>';
                    }
                    $input .= '</select>';
                    break;
                
                /**
                 * Text
                 *
                 * @return string
                 */
                case 'text':
                    $input_array['type'] = ( $field->enablePasswordInput == 1 ? 'password' : 'text' );
                    break;
                
                /**
                 * Textarea
                 *
                 * @return string
                 */
                case 'textarea':
                    $_input_type = 'textarea';
                    $input_array['type'] = null;
                    $input_array['value'] = null;
                    $input_array['rows'] = 10;
                    $input_array['cols'] = 50;
                    $input_array['class'] = 'textarea form-control ' . $field->size;
                    break;
                
                /**
                 * Time
                 *
                 * @return string
                 */
                case 'time':
                    $_input_type = false;
                    $_cols_arr = array(
                        array( 'col-sm-5', 'col-sm-5', 'col-sm-2' ),
                        array( 'col-sm-6', 'col-sm-6', 'hidden' )
                    );
                    $input_this = array();
                    foreach ($field->inputs as $k => $v) {

                        $_input_id = explode( '.', $v['id'] );

                        if ( $k == 2 && ( ! isset( $field->timeFormat ) || $field->timeFormat == 12 ) ) {
                            $input_this[] = '<div class="col-sm-2">'.
                                    '<select name="input_' . $field->id . '[]" id="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '" class="form-control">'.
                                        '<option value="am" ' . ( isset( $value[ $k ] ) && $value[ $k ] == 'am' ? 'selected="selected"' : ( isset( $value[ (string) $v['id'] ] ) && $value[ (string) $v['id'] ] == 'am' ? 'selected="selected"' : '' ) ) . '>AM</option>'.
                                        '<option value="pm" ' . ( isset( $value[ $k ] ) && $value[ $k ] == 'pm' ? 'selected="selected"' : ( isset( $value[ (string) $v['id'] ] ) && $value[ (string) $v['id'] ] == 'pm' ? 'selected="selected"' : '' ) ) . '>PM</option>'.
                                    '</select>'.
                                '</div>';
                        }
                        elseif ( $k < 2 ) {
                            $input_this[] = '<div class="' . ( ! isset( $field->timeFormat ) || $field->timeFormat == 12 ? 'col-sm-5' : 'col-sm-6' ) . '">'.
                                '<div class="input-group">'.
                                    '<input '.
                                        'type="text" '.
                                        'maxlength="2" '.
                                        'name="input_' . $field->id . '[]" '.
                                        'id="input_' . $field->formId . '_' . $field->id . '_' . $_input_id[1] . '" '.
                                        'value="' . ( isset( $value[ $k ] ) ? $value[ $k ] : ( isset( $value[ (string) $v['id'] ] ) ? $value[ (string) $v['id'] ] : '' ) ) . '" '.
                                        'class="form-control ' . $field->size . '" '.
                                        'placeholder="' . ( isset( $v['placeholder'] ) ? $v['placeholder'] : '' ) . '">'.
                                    '<div class="input-group-addon">' . ( isset( $v['customLabel'] ) ? $v['customLabel'] : $v['label'] ) . '</div>'.
                                '</div></div>';
                        }

                    }
                    $input = '<div class="row">' . implode('', $input_this) . '</div>';
                    break;
                
                /**
                 * Website
                 *
                 * @return string
                 */
                case 'website':
                    $input_array['type'] = 'text';
                    break;
                
                /**
                 * HTML
                 *
                 * HTML input type is incomplete and only renders basic content at the moment.
                 *
                 * @return string
                 */
                case 'html':
                    $_input_type = false;
                    $input = $field->content;
                    break;

                default:
                    break;
            }

            array_filter( $input_array ); // remove empty array values

            // Build final input string(s)
            if ( $_input_type == 'default' ) :

                if ( is_array( $field->conditionalLogicFields ) && ! empty( $field->conditionalLogicFields ) ) {
                    $input_array['onchange'] = 'gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . ']);';
                    $input_array['onkeyup'] = 'clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . '])&quot;, 300);';
                }
                
                $input = $input_before;
                $input .= '<input ';
                foreach ($input_array as $k => $v) {
                    $input .= $k . '="' . $v . '" ';
                }
                $input .= '/>';
                $input .= $input_after;
            
            elseif ( $_input_type == 'textarea' ) :

                if ( is_array( $field->conditionalLogicFields ) && ! empty( $field->conditionalLogicFields ) ) {
                    $input_array['onchange'] = 'gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . ']);';
                    $input_array['onkeyup'] = 'clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(' . $field->formId . ',[' . implode(',', $field->conditionalLogicFields) . '])&quot;, 300);';
                }

                $input = $input_before;
                $input .= '<textarea ';
                foreach ($input_array as $k => $v) {
                    $input .= $k . '="' . $v . '" ';
                }
                $input .= '>' . $value . '</textarea>';
                $input .= $input_after;

            endif;

            if ( $layout == 'horizontal' && ! empty( $input ) ) :
                $input = '<div class="col-sm-' . $col_r . '">' . $input . '</div>';
            endif;

            return $input;
        }

        /**
         * gform_field_content
         *
         * This filter is executed before creating the field's content, allowing users to completely modify the way the field is rendered. It can also be used to create custom field types.
         *
         */
        public function gform_bootstrapper_field_content( $content, $field, $value, $lead_id, $form_id ) {
            $form = GFAPI::get_form($form_id);
            $settings = $this->get_form_settings($form);
            $col_r = ( isset($form['bootstrap_form_columns']) ? $form['bootstrap_form_columns'] : 10 );
            $col_l = 12 - $col_r;
            $offset = ( in_array( $field['cssClass'], array( 'tsbplaceholder', 'gf-add-placeholder' ) ) ? 'col-sm-offset-'.$col_l : '' );

            // Basic / Default Forms:
            if ( ! isset( $form['bootstrap_form_layout'] ) || $form['bootstrap_form_layout'] == 'basic' ) {
                //$content = str_replace( '<div ', '<span ', $content );
                //$content = str_replace( '</div>', '</span>', $content );
            }

            // Inline Forms:
            if ( isset( $form['bootstrap_form_layout'] ) && $form['bootstrap_form_layout'] == 'inline' ) {
                $content = str_replace( '<div ', '<span ', $content );
                $content = str_replace( '</div>', '</span>', $content );
            }

            // Horizontal Forms:
            if ( isset( $form['bootstrap_form_layout'] ) && $form['bootstrap_form_layout'] == 'horizontal' ) {
                $content = str_replace( 'ginput_container', 'col-sm-'.$col_r.' ginput_container ' . $offset, $content );
                $content = str_replace( 'gfield_label', 'gfield_label col-sm-'.$col_l, $content );
                $content = str_replace( 'gfield_description', 'gfield_description col-sm-'.$col_r.' ' . $offset, $content );
                $content = str_replace( 'validation_message', 'validation_message col-sm-offset-'.$col_l.' ', $content );
                $content = str_replace( 'id="gform_preview_', 'class="col-sm-'.$col_r.' col-sm-offset-'.$col_l.'" id="gform_preview_', $content ); // File Upload fixes
            }
            else { // Non-Horizontal forms:
                $content = str_replace( 'id="gform_preview_', 'class="col-sm-12" id="gform_preview_', $content ); // File Upload fixes
            }

            // All Forms:
            $content = str_replace( 'gform_button_select_files', 'gform_button_select_files btn btn-default ', $content ); // File Upload fixes
            $content = str_replace( 'gfield_label', 'control-label gfield_label', $content );
            $content = str_replace( 'gfield_description', 'help-block gfield_description', $content );
            $content = str_replace( 'small', 'small input-sm', $content );
            $content = str_replace( 'medium', 'medium input-md', $content );
            $content = str_replace( 'large', 'large input-lg', $content );
            
            return $content;
        }

        /**
         * gform_get_form_filter
         *
         * to clean up & inject some stuff..
         */
        public function gform_bootstrapper_gravity_form_filter( $form_string, $form ) {
            $settings = $this->get_form_settings($form);
            $btnalign = ( isset( $form['button']['bootstrap_submit_alignment'] ) && ( ! isset( $form['bootstrap_form_layout'] ) || $form['bootstrap_form_layout'] == 'basic' ) ) ? $form['button']['bootstrap_submit_alignment'] : 'default';
            $align = '';
            switch ($btnalign) {
                case 'center':
                    $align = 'text-center';
                    break;
                case 'right':
                    $align = 'text-right';
                    break;
                case 'left':
                    $align = 'text-left';
                    break;
                case 'default':
                default:
                    # code...
                    break;
            }
            // replace <ul> and <li> with <div> 's
            $form_string = str_replace( '<ul ', '<div ', $form_string );
            $form_string = str_replace( '</ul>', '</div>', $form_string );
            $form_string = str_replace( '<li ', '<div ', $form_string );
            $form_string = str_replace( '</li>', '</div>', $form_string );
            // set body as form group, fix for inline forms
            if ( ! isset( $form['bootstrap_form_layout'] ) || $form['bootstrap_form_layout'] == 'basic' ) {
                // $form_string = str_replace( 'gform_body', 'gform-body row', $form_string );
                $form_string = str_replace( 'gfield ', 'gfield col-xs-12 col-12 ', $form_string );
            }
            if ( isset( $form['bootstrap_form_layout'] ) && $form['bootstrap_form_layout'] == 'inline' ) {
                $form_string = str_replace( 'gform_body', 'gform_body form-group', $form_string );
            }
            // set footer as form group
            $form_string = str_replace( 'gform_footer', 'gform_footer form-group '.$align, $form_string );
            $form_string = str_replace( 'gform_page_footer', 'gform_footer form-group '.$align, $form_string );
            // column fixes - two col
            $form_string = str_replace( 'gf_left_half', 'gf_left_half col-sm-6 pull-left', $form_string );
            $form_string = str_replace( 'gf_right_half', 'gf_right_half col-sm-6 pull-right', $form_string );
            // column fixes - three col
            $form_string = str_replace( 'gf_left_third', 'gf_left_third col-sm-4 pull-left', $form_string );
            $form_string = str_replace( 'gf_middle_third', 'gf_middle_third col-sm-4', $form_string );
            $form_string = str_replace( 'gf_right_third', 'gf_right_third col-sm-4 pull-right', $form_string );
            return $form_string;
        }

        /**
         *
         * gform_country_select()
         *
         * @return string
         *
         */
        public function gform_country_select( $field_id, $input_id, $form_id, $value ) {
            return '<select name="input_'.$field_id.'.'.$input_id.'" id="input_'.$form_id.'_'.$field_id.'_'.$input_id.'" class="form-control">'.
                '<option value=""></option>'.
                '<option value="Afghanistan">Afghanistan</option><option value="Albania">Albania</option><option value="Algeria">Algeria</option><option value="American Samoa">American Samoa</option><option value="Andorra">Andorra</option><option value="Angola">Angola</option><option value="Antigua and Barbuda">Antigua and Barbuda</option><option value="Argentina">Argentina</option><option value="Armenia">Armenia</option><option value="Australia">Australia</option><option value="Austria">Austria</option><option value="Azerbaijan">Azerbaijan</option><option value="Bahamas">Bahamas</option><option value="Bahrain">Bahrain</option><option value="Bangladesh">Bangladesh</option><option value="Barbados">Barbados</option><option value="Belarus">Belarus</option><option value="Belgium">Belgium</option><option value="Belize">Belize</option><option value="Benin">Benin</option><option value="Bermuda">Bermuda</option><option value="Bhutan">Bhutan</option><option value="Bolivia">Bolivia</option><option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option><option value="Botswana">Botswana</option><option value="Brazil">Brazil</option><option value="Brunei">Brunei</option><option value="Bulgaria">Bulgaria</option><option value="Burkina Faso">Burkina Faso</option><option value="Burundi">Burundi</option><option value="Cambodia">Cambodia</option><option value="Cameroon">Cameroon</option><option value="Canada">Canada</option><option value="Cape Verde">Cape Verde</option><option value="Cayman Islands">Cayman Islands</option><option value="Central African Republic">Central African Republic</option><option value="Chad">Chad</option><option value="Chile">Chile</option><option value="China">China</option><option value="Colombia">Colombia</option><option value="Comoros">Comoros</option><option value="Congo, Democratic Republic of the">Congo, Democratic Republic of the</option><option value="Congo, Republic of the">Congo, Republic of the</option><option value="Costa Rica">Costa Rica</option><option value="Côte d\'Ivoire">Côte d\'Ivoire</option><option value="Croatia">Croatia</option><option value="Cuba">Cuba</option><option value="Cyprus">Cyprus</option><option value="Czech Republic">Czech Republic</option><option value="Denmark">Denmark</option><option value="Djibouti">Djibouti</option><option value="Dominica">Dominica</option><option value="Dominican Republic">Dominican Republic</option><option value="East Timor">East Timor</option><option value="Ecuador">Ecuador</option><option value="Egypt">Egypt</option><option value="El Salvador">El Salvador</option><option value="Equatorial Guinea">Equatorial Guinea</option><option value="Eritrea">Eritrea</option><option value="Estonia">Estonia</option><option value="Ethiopia">Ethiopia</option><option value="Faroe Islands">Faroe Islands</option><option value="Fiji">Fiji</option><option value="Finland">Finland</option><option value="France">France</option><option value="French Polynesia">French Polynesia</option><option value="Gabon">Gabon</option><option value="Gambia">Gambia</option><option value="Georgia">Georgia</option><option value="Germany">Germany</option><option value="Ghana">Ghana</option><option value="Greece">Greece</option><option value="Greenland">Greenland</option><option value="Grenada">Grenada</option><option value="Guam">Guam</option><option value="Guatemala">Guatemala</option><option value="Guinea">Guinea</option><option value="Guinea-Bissau">Guinea-Bissau</option><option value="Guyana">Guyana</option><option value="Haiti">Haiti</option><option value="Honduras">Honduras</option><option value="Hong Kong">Hong Kong</option><option value="Hungary">Hungary</option><option value="Iceland">Iceland</option><option value="India">India</option><option value="Indonesia">Indonesia</option><option value="Iran">Iran</option><option value="Iraq">Iraq</option><option value="Ireland">Ireland</option><option value="Israel">Israel</option><option value="Italy">Italy</option><option value="Jamaica">Jamaica</option><option value="Japan">Japan</option><option value="Jordan">Jordan</option><option value="Kazakhstan">Kazakhstan</option><option value="Kenya">Kenya</option><option value="Kiribati">Kiribati</option><option value="North Korea">North Korea</option><option value="South Korea">South Korea</option><option value="Kosovo">Kosovo</option><option value="Kuwait">Kuwait</option><option value="Kyrgyzstan">Kyrgyzstan</option><option value="Laos">Laos</option><option value="Latvia">Latvia</option><option value="Lebanon">Lebanon</option><option value="Lesotho">Lesotho</option><option value="Liberia">Liberia</option><option value="Libya">Libya</option><option value="Liechtenstein">Liechtenstein</option><option value="Lithuania">Lithuania</option><option value="Luxembourg">Luxembourg</option><option value="Macedonia">Macedonia</option><option value="Madagascar">Madagascar</option><option value="Malawi">Malawi</option><option value="Malaysia">Malaysia</option><option value="Maldives">Maldives</option><option value="Mali">Mali</option><option value="Malta">Malta</option><option value="Marshall Islands">Marshall Islands</option><option value="Mauritania">Mauritania</option><option value="Mauritius">Mauritius</option><option value="Mexico">Mexico</option><option value="Micronesia">Micronesia</option><option value="Moldova">Moldova</option><option value="Monaco">Monaco</option><option value="Mongolia">Mongolia</option><option value="Montenegro">Montenegro</option><option value="Morocco">Morocco</option><option value="Mozambique">Mozambique</option><option value="Myanmar">Myanmar</option><option value="Namibia">Namibia</option><option value="Nauru">Nauru</option><option value="Nepal">Nepal</option><option value="Netherlands">Netherlands</option><option value="New Zealand">New Zealand</option><option value="Nicaragua">Nicaragua</option><option value="Niger">Niger</option><option value="Nigeria">Nigeria</option><option value="Northern Mariana Islands">Northern Mariana Islands</option><option value="Norway">Norway</option><option value="Oman">Oman</option><option value="Pakistan">Pakistan</option><option value="Palau">Palau</option><option value="Palestine, State of">Palestine, State of</option><option value="Panama">Panama</option><option value="Papua New Guinea">Papua New Guinea</option><option value="Paraguay">Paraguay</option><option value="Peru">Peru</option><option value="Philippines">Philippines</option><option value="Poland">Poland</option><option value="Portugal">Portugal</option><option value="Puerto Rico">Puerto Rico</option><option value="Qatar">Qatar</option><option value="Romania">Romania</option><option value="Russia">Russia</option><option value="Rwanda">Rwanda</option><option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option><option value="Saint Lucia">Saint Lucia</option><option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option><option value="Samoa">Samoa</option><option value="San Marino">San Marino</option><option value="Sao Tome and Principe">Sao Tome and Principe</option><option value="Saudi Arabia">Saudi Arabia</option><option value="Senegal">Senegal</option><option value="Serbia and Montenegro">Serbia and Montenegro</option><option value="Seychelles">Seychelles</option><option value="Sierra Leone">Sierra Leone</option><option value="Singapore">Singapore</option><option value="Sint Maarten">Sint Maarten</option><option value="Slovakia">Slovakia</option><option value="Slovenia">Slovenia</option><option value="Solomon Islands">Solomon Islands</option><option value="Somalia">Somalia</option><option value="South Africa">South Africa</option><option value="Spain">Spain</option><option value="Sri Lanka">Sri Lanka</option><option value="Sudan">Sudan</option><option value="Sudan, South">Sudan, South</option><option value="Suriname">Suriname</option><option value="Swaziland">Swaziland</option><option value="Sweden">Sweden</option><option value="Switzerland">Switzerland</option><option value="Syria">Syria</option><option value="Taiwan">Taiwan</option><option value="Tajikistan">Tajikistan</option><option value="Tanzania">Tanzania</option><option value="Thailand">Thailand</option><option value="Togo">Togo</option><option value="Tonga">Tonga</option><option value="Trinidad and Tobago">Trinidad and Tobago</option><option value="Tunisia">Tunisia</option><option value="Turkey">Turkey</option><option value="Turkmenistan">Turkmenistan</option><option value="Tuvalu">Tuvalu</option><option value="Uganda">Uganda</option><option value="Ukraine">Ukraine</option><option value="United Arab Emirates">United Arab Emirates</option><option value="United Kingdom">United Kingdom</option><option value="United States" selected="selected">United States</option><option value="Uruguay">Uruguay</option><option value="Uzbekistan">Uzbekistan</option><option value="Vanuatu">Vanuatu</option><option value="Vatican City">Vatican City</option><option value="Venezuela">Venezuela</option><option value="Vietnam">Vietnam</option><option value="Virgin Islands, British">Virgin Islands, British</option><option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option><option value="Yemen">Yemen</option><option value="Zambia">Zambia</option><option value="Zimbabwe">Zimbabwe</option>'.
            '</select>';
        }

        /**
         * New Bootstrap Form Settings
         *
         */
        function bootstrap_setting_form_layout( $settings, $form ) {
            $settings['Form Layout']['bootstrap_form_layout'] = '
                <tr>
                    <th><label for="bootstrap_form_layout">Bootstrap Layout <a href="#" onclick="return false;" class="gf_tooltip tooltip" title="<h6>Bootstrap Layout</h6>Choose between three available Bootstrap form layouts. Inline recommended for simple forms only."><i class="fa fa-question-circle"></i></a></label></th>
                    <td><select name="bootstrap_form_layout">
                        <option value="basic" ' . ( isset($form['bootstrap_form_layout']) && $form['bootstrap_form_layout'] == 'basic' ? 'selected="selected"' : '' ) . '">Basic</option>
                        <option value="inline" ' . ( isset($form['bootstrap_form_layout']) && $form['bootstrap_form_layout'] == 'inline' ? 'selected="selected"' : '' ) . '">Inline</option>
                        <option value="horizontal" ' . ( isset($form['bootstrap_form_layout']) && $form['bootstrap_form_layout'] == 'horizontal' ? 'selected="selected"' : '' ) . '">Horizontal</option>
                        </select>
                    </td>
                </tr>';
            return $settings;
        }
        function bootstrap_setting_form_columns( $settings, $form ) {
            $settings['Form Layout']['bootstrap_form_columns'] = '
                <tr>
                    <th><label for="bootstrap_form_columns">Bootstrap Column Widths <a href="#" onclick="return false;" class="gf_tooltip tooltip" title="<h6>Bootstrap Column Widths</h6>Set left and right column widths in twelfths. Only applies to horizontal form layout."><i class="fa fa-question-circle"></i></a></label></th>
                    <td><select name="bootstrap_form_columns">
                        <option value="10" ' . ( isset($form['bootstrap_form_columns']) && $form['bootstrap_form_columns'] == '10' ? 'selected="selected"' : '' ) . '">2 - 10 (default)</option>
                        <option value="9" ' . ( isset($form['bootstrap_form_columns']) && $form['bootstrap_form_columns'] == '9' ? 'selected="selected"' : '' ) . '">3 - 9</option>
                        <option value="8" ' . ( isset($form['bootstrap_form_columns']) && $form['bootstrap_form_columns'] == '8' ? 'selected="selected"' : '' ) . '">4 - 8</option>
                        <option value="6" ' . ( isset($form['bootstrap_form_columns']) && $form['bootstrap_form_columns'] == '6' ? 'selected="selected"' : '' ) . '">6 - 6</option>
                        </select>
                    </td>
                </tr>';
            return $settings;
        }
        function bootstrap_setting_submit_button_classes( $settings, $form ) {
            $settings['Form Button']['bootstrap_submit_classes'] = '
                <tr>
                    <th><label for="bootstrap_submit_classes">Button Class <a href="#" onclick="return false;" class="gf_tooltip tooltip" title="<h6>Submit Button Classes</h6>Add additional CSS classes to the submit button."><i class="fa fa-question-circle"></i></a></label></th>
                    <td><input type="text" name="bootstrap_submit_classes" value="' . ( isset($form['button']['bootstrap_submit_classes']) && ! empty( $form['button']['bootstrap_submit_classes'] ) ? $form['button']['bootstrap_submit_classes'] : '' ) . '" /></td>
                </tr>';
            return $settings;
        }
        function bootstrap_setting_submit_button_size( $settings, $form ) {
            $settings['Form Button']['bootstrap_submit_size'] = '
                <tr>
                    <th><label for="bootstrap_submit_size">Bootstrap Button Size <a href="#" onclick="return false;" class="gf_tooltip tooltip" title="<h6>Submit Button Size</h6>Adds bootstrap class to submit button for either regular, large, or small buttons."><i class="fa fa-question-circle"></i></a></label></th>
                    <td><select name="bootstrap_submit_size">
                        <option value="default" ' . ( isset($form['button']['bootstrap_submit_size']) && $form['button']['bootstrap_submit_size'] == 'default' ? 'selected="selected"' : '' ) . '">Default</option>
                        <option value="large" ' . ( isset($form['button']['bootstrap_submit_size']) && $form['button']['bootstrap_submit_size'] == 'large' ? 'selected="selected"' : '' ) . '">Large</option>
                        <option value="small" ' . ( isset($form['button']['bootstrap_submit_size']) && $form['button']['bootstrap_submit_size'] == 'small' ? 'selected="selected"' : '' ) . '">Small</option>
                        </select>
                    </td>
                </tr>';
            return $settings;
        }
        function bootstrap_setting_submit_button_alignment( $settings, $form ) {
            $settings['Form Button']['bootstrap_submit_alignment'] = '
                <tr>
                    <th><label for="bootstrap_submit_alignment">Button Alignment <a href="#" onclick="return false;" class="gf_tooltip tooltip" title="<h6>Submit Button Alignment</h6>Left, right, or center align the Submit button. Only applies to Basic form layout."><i class="fa fa-question-circle"></i></a></label></th>
                    <td><select name="bootstrap_submit_alignment">
                        <option value="default" ' . ( isset($form['button']['bootstrap_submit_alignment']) && $form['button']['bootstrap_submit_alignment'] == 'default' ? 'selected="selected"' : '' ) . '">Default</option>
                        <option value="left" ' . ( isset($form['button']['bootstrap_submit_alignment']) && $form['button']['bootstrap_submit_alignment'] == 'left' ? 'selected="selected"' : '' ) . '">Left</option>
                        <option value="center" ' . ( isset($form['button']['bootstrap_submit_alignment']) && $form['button']['bootstrap_submit_alignment'] == 'center' ? 'selected="selected"' : '' ) . '">Center</option>
                        <option value="right" ' . ( isset($form['button']['bootstrap_submit_alignment']) && $form['button']['bootstrap_submit_alignment'] == 'right' ? 'selected="selected"' : '' ) . '">Right</option>
                        </select>
                    </td>
                </tr>';
            return $settings;
        }
        function save_bootstrap_setting_form_layout($form) {
            $form['bootstrap_form_layout'] = rgpost( 'bootstrap_form_layout' );
            return $form;
        }
        function save_bootstrap_setting_form_columns($form) {
            $form['bootstrap_form_columns'] = rgpost( 'bootstrap_form_columns' );
            return $form;
        }
        function save_bootstrap_setting_submit_button_classes($form) {
            $form['button']['bootstrap_submit_classes'] = rgpost( 'bootstrap_submit_classes' );
            return $form;
        }
        function save_bootstrap_setting_submit_button_size($form) {
            $form['button']['bootstrap_submit_size'] = rgpost( 'bootstrap_submit_size' );
            return $form;
        }
        function save_bootstrap_setting_submit_button_alignment($form) {
            $form['button']['bootstrap_submit_alignment'] = rgpost( 'bootstrap_submit_alignment' );
            return $form;
        }

        /**
         *  Plugin Custom Scripts
         *
         *  Call scripts that we may want to run on form pages
         *
         **/
        public function gform_bootstrapper_scripts() {

            $script = array(
                "handle"    => "gforms_bootstrapper_js",
                "src"       => $this->get_base_url() . "/js/gforms_bootstrapper_js.js",
                "deps"      => 'jquery',
                "ver"       => $this->_version,
                "in_footer" => true,
            );

            // wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
        }

        /**
         *  Plugin Default Bootstrap Scripts
         *
         *  Call scripts that we may want to run on form pages
         *
         **/
        public function gform_default_bootstrap_scripts() {

            $script = array(
                "handle"    => "bootstrap_min_js",
                "src"       => $this->get_base_url() . "/js/bootstrap.min.js",
                "deps"      => 'jquery',
                "ver"       => $this->_version,
                "in_footer" => true,
            );

            // wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
        }

        /**
         *  Plugin Custom Styles
         *
         *  Call styles that we may want apply on form pages
         *
         **/
        public function gform_bootstrapper_styles() {

            $style = array(
                "handle"  => "gforms_bootstrapper_style",
                "src"     => $this->get_base_url() . "/css/gforms_bootstrapper_style.css",
                "version" => $this->_version,
            );

            wp_enqueue_style( $style['handle'], $style['src'] );

        }

        /**
         *  Plugin Default Bootstrap Styles
         *
         *  Call styles that we may want apply on form pages
         *
         **/
        public function gform_default_bootstrap_styles() {

            $style = array(
                "handle"  => "bootstrap_min_style",
                "src"     => $this->get_base_url() . "/css/bootstrap.min.css",
                "version" => $this->_version,
            );

            wp_enqueue_style( $style['handle'], $style['src'] );

        }

    }

    // Instantiate the class - this triggers everything, makes the magic happen
    $gfb = new GFBootstrapper();

}
