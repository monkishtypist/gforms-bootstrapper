<?php
/*
Plugin Name: Gravity Forms Bootstrapper
Plugin URI: http://www.ninthlink.com
Description: A Gravity Forms add-on to add Bootstrap CSS classes to forms
Version: 1.0
Author: Tim @ Ninthlink
Author URI: http://www.ninthlink.com
Documentation: http://www.gravityhelp.com/documentation/page/GFAddOn

------------------------------------------------------------------------
Copyright 2014 Ninthlink, Inc.

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

- Completed: Add a column width option for horizontal forms?
*/

//exit if accessed directly
if(!defined('ABSPATH')) exit;

//------------------------------------------
if (class_exists("GFForms")) {
    GFForms::include_addon_framework();

    class GFBootstrapper extends GFAddOn {

        protected $_version = "1.0";
        protected $_min_gravityforms_version = "1.7.9999";
        protected $_slug = "gforms-bootstrapper";
        protected $_path = "gforms-bootstrapper/gforms-bootstrapper.php";
        protected $_full_path = __FILE__;
        protected $_title = "GForms Bootstrapper";
        protected $_short_title = "GF Bootstrapper";


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
        }

        public function init_frontend(){
            parent::init_frontend();
            // add tasks or filters here that you want to perform only in the front end
            add_action( 'wp_enqueue_scripts', array($this, 'bootstrapper_styles'), 10 );
            add_filter( 'gform_field_css_class', array($this, 'bootstrap_css_classes'), 10, 3);
            add_filter( 'gform_field_content', array($this, 'bootstrap_field_content'), 10, 5 );
            add_filter( 'gform_field_input' , array($this, 'bootstrap_field_input'), 10, 5 );
            add_filter( 'gform_get_form_filter', array($this, 'bootstrap_gravity_form_filter'), 10, 2 );
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
            if ( ! isset($settings['formlayout']) || $settings['formlayout'] == 'basic' ) {
                $form_tag = str_replace( '<form ', '<form class="form-basic form-bootstrapped" ', $form_tag );
            }
            else if ( $settings['formlayout'] == 'inline' ) {
                $form_tag = str_replace( '<form ', '<form class="form-inline form-bootstrapped" ', $form_tag );
            }
            else if ( $settings['formlayout'] == 'horizontal' ) {
                $form_tag = str_replace( '<form ', '<form class="form-horizontal form-bootstrapped" ', $form_tag );
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
            $col_r = ( isset($settings['colwidth']) ? $settings['colwidth'] : 10 );
            $col_l = 12 - $col_r;
            $classes = 'btn btn-primary ';
            if ( isset($settings['btnsize']) ) {
                if ( $settings['btnsize'] == 'large' )
                    $classes .= 'btn-lg';
                if ( $settings['btnsize'] == 'small' )
                    $classes .= 'btn-sm';
            }
            $button = str_replace( 'gform_button', $classes, $button );
            $button = str_replace( '>', 'data-loading-text="Processing..." >', $button );
            
            if ( isset( $settings['formlayout'] ) && $settings['formlayout'] == 'horizontal' ) {
                $button = '<div class="col-md-offset-'.$col_l.' col-md-'.$col_r.'">' . $button . '</div>';
            }
            return $button;
        }

        /**
         * gform_field_css_class
         *
         * This filter can be used to dynamically add/remove CSS classes to a field
         *
         */
        public function bootstrap_css_classes( $classes, $field, $form ){
            if ( $field['type'] == "name" ) {
                $classes .= " custom_name_class";
            }
            $settings = $this->get_form_settings($form);
            $has_error = ( $field['failed_validation'] == 1 ? 'has-error' : '' );
            $size = ( isset($settings['btnsize']) ? $settings['btnsize'] : '' );
            $classes .= " form-group " . $has_error;
            if ( isset( $settings['formlayout'] ) && $settings['formlayout'] == 'horizontal' ) {
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
        public function bootstrap_field_input( $input, $field, $value, $lead_id, $form_id ) {
            // Rewrite any inputs here...
            return $input;
        }

        /**
         * gform_field_content
         *
         * This filter is executed before creating the field's content, allowing users to completely modify the way the field is rendered. It can also be used to create custom field types.
         *
         */
        public function bootstrap_field_content( $content, $field, $value, $lead_id, $form_id ) {
            $form = GFAPI::get_form($form_id);
            $settings = $this->get_form_settings($form);
            $col_r = ( isset($settings['colwidth']) ? $settings['colwidth'] : 10 );
            $col_l = 12 - $col_r;
            $offset = ( in_array( $field['cssClass'], array( 'tsbplaceholder', 'gf-add-placeholder' ) ) ? 'col-md-offset-'.$col_l : '' );

            if ( $field['type'] == 'checkbox' ) {
                /* This regex is meant to modify the html for each checkbox placing the <input> inside the <label>
                 * However, for some unknown reason the regex is not working at all inside this API class
                 */
                //$regex = '/((((\<div class\=\"gchoice)(.*?)(?=\>)(\>))(\s*))((\<input)(.*?)(?=\>)(\>))(\s*)((\<label)(.*?)(?=\>)(\>))([\w\d\s]*)(\<\/label\>)(\s*)(\<\/div\>))/';
                //$replace = '$3$13$8$17$18$20';
                //$content = preg_replace($regex, $replace, $content, -1);
                //$content = str_replace('gfield_checkbox', 'gfield_checkbox checkbox', $content);
            }

            if ( ! isset( $settings['formlayout'] ) || $settings['formlayout'] == 'basic' ) {
                $content = str_replace( '<div ', '<span ', $content );
                $content = str_replace( '</div>', '</span>', $content );
            }
            else if ( $settings['formlayout'] == 'inline' ) {
                $content = str_replace( '<div ', '<span ', $content );
                $content = str_replace( '</div>', '</span>', $content );
            }
            else if ( $settings['formlayout'] == 'horizontal' ) {
                $content = str_replace( 'ginput_container', 'col-md-'.$col_r.' ginput_container ' . $offset, $content );
                $content = str_replace( 'gfield_label', 'col-md-'.$col_l.' control-label gfield_label', $content );
                $content = str_replace( 'gfield_description', 'gfield_description help-block col-md-'.$col_r.' ' . $offset, $content );
            }
            else {
            }
            $content = str_replace( 'small', 'small form-control input-sm', $content );
            $content = str_replace( 'medium', 'medium form-control', $content );
            $content = str_replace( 'large', 'large form-control input-lg', $content );
            
            return $content;
        }

        /**
         * gform_get_form_filter
         *
         * to clean up & inject some stuff..
         */
        public function bootstrap_gravity_form_filter( $form_string, $form ) {
            $settings = $this->get_form_settings($form);
            $btnalign = ( isset( $settings['btnalign'] ) && ( ! isset( $settings['formlayout'] ) || $settings['formlayout'] == 'basic' ) ) ? $settings['btnalign'] : 'default';
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
            // set footer as form group
            $form_string = str_replace( 'gform_footer', 'gform_footer form-group '.$align , $form_string );
            
            // set body as form group, fix for inline forms
            if ( ! isset( $settings['formlayout'] ) || $settings['formlayout'] == 'basic' ) {
                $form_string = str_replace( 'gform_body', 'gform-body row', $form_string );
                $form_string = str_replace( 'gfield ', 'gfield col-xs-12 ', $form_string );
            }
            if ( isset( $settings['formlayout'] ) && $settings['formlayout'] == 'inline' ) {
                $form_string = str_replace( 'gform_body', 'gform_body form-group', $form_string );
            }

            
            $form_string = str_replace( 'gf_left_half', 'gf_left_half col-md-6 pull-left', $form_string );
            $form_string = str_replace( 'gf_right_half', 'gf_right_half col-md-6 pull-right', $form_string );
            return $form_string;
        }

        /**
         * Settings Page to Bootstrapify each Form
         *
         */
        public function form_settings_fields($form) {
            return array(
                array(
                    "title"  => "Bootstrapper Form Settings",
                    "fields" => array(
                        array(
                            "label"   => "Form layout",
                            "type"    => "select",
                            "name"    => "formlayout",
                            "default_value" => "basic",
                            "choices" => array(
                                array(
                                    "label" => "Basic form",
                                    "value" => "basic"
                                ),
                                array(
                                    "label" => "Inline form",
                                    "value" => "inline"
                                ),
                                array(
                                    "label" => "Horizontal form",
                                    "value" => "horizontal"
                                )
                            ),
                        ),
                        array(
                            "label"   => "Horizontal Column Widths",
                            "type"    => "select",
                            "name"    => "colwidth",
                            "tooltip" => "Sets width of label (left) and field (right) columns as a ratio. Only applies to horizontal form layout.",
                            "dependency" => array( 'field' => 'formlayout', 'values' => array( 'horizontal' ) ),
                            "choices" => array(
                                array(
                                    "label" => "2 - 10",
                                    "value" => "10"
                                ),
                                array(
                                    "label" => "3 - 9",
                                    "value" => "9"
                                ),
                                array(
                                    "label" => "4 - 8",
                                    "value" => "8"
                                ),
                                array(
                                    "label" => "6 - 6",
                                    "value" => "6"
                                )
                            )
                        ),
                        array(
                            "label"   => "Submit Button Size",
                            "type"    => "select",
                            "name"    => "btnsize",
                            //"tooltip" => "Controls input field sizing",
                            "choices" => array(
                                array(
                                    "label" => "Default",
                                    "value" => "default"
                                ),
                                array(
                                    "label" => "Large",
                                    "value" => "large"
                                ),
                                array(
                                    "label" => "Small",
                                    "value" => "small"
                                )
                            )
                        ),
                        array(
                            "label"   => "Submit Button Alignment",
                            "type"    => "select",
                            "name"    => "btnalign",
                            "tooltip" => "Adds Bootstrap alignment class to Gravity Form footer",
                            "dependency" => array( 'field' => 'formlayout', 'values' => array( 'basic' ) ),
                            "choices" => array(
                                array(
                                    "label" => "Default",
                                    "value" => "default"
                                ),
                                array(
                                    "label" => "Left",
                                    "value" => "left"
                                ),
                                array(
                                    "label" => "Center",
                                    "value" => "center"
                                ),
                                array(
                                    "label" => "Right",
                                    "value" => "right"
                                )
                            )
                        ),
                    )
                )
            );
        }

        /**
         *  Plugin Styles
         *
         *  Call styles that we may want apply on form pages
         *
         **/
        public function bootstrapper_styles() {

            $style = array(
                "handle"  => "gforms_bootstrapper_style",
                "src"     => $this->get_base_url() . "/css/gforms_bootstrapper_style.css",
                "version" => $this->_version,
            );

            wp_enqueue_style( $style['handle'], $style['src'] );

        }

    }

    // Instantiate the class - this triggers everything, makes the magic happen
    $gfb = new GFBootstrapper();

}
