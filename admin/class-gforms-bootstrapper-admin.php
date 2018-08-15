<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link     https://github.com/monkishtypist
 * @since    2.0.0
 *
 * @package    Gforms_Bootstrapper
 * @subpackage Gforms_Bootstrapper/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gforms_Bootstrapper
 * @subpackage Gforms_Bootstrapper/admin
 * @author     Tim Spinks <tim@monkishtypist.com>
 */
class Gforms_Bootstrapper_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gforms_Bootstrapper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gforms_Bootstrapper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gforms-bootstrapper-admin.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gforms_Bootstrapper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gforms_Bootstrapper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gforms-bootstrapper-admin.min.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Custom tooltips.
	 *
	 * @since    2.0.5
	 */
	public function add_tooltips( $tooltips ) {
		$tooltips['submit_button_css'] = '<h6>Button CSS Classes</h6>Enter the CSS classes you would like to use in addition to the default styles for the Submit button. Try `btn-primary` or `btn-secondary` for example.';
		return $tooltips;
	}

	/**
	 * Custom Submit Button setting for CSS classes.
	 *
	 * @since    2.0.5
	 */
	public function custom_form_submit_button_classes_setting( $settings, $form ) {
		$settings[ __( 'Form Button', 'gravityforms' ) ]['submit_css_classes'] = '
			<tr>
				<th>
					<label for="submit_css_classes">' .
						__( 'CSS classes', 'gravityforms' ) . ' ' .
						gform_tooltip( 'submit_button_css', '', true ) .
					'</label>
				</th>
				<td>
					<input type="text" value="' . esc_attr( rgar( $form, 'submit_css_classes' ) ) . '" name="submit_css_classes">
				</td>
			</tr>';
		return $settings;
	}

	/**
	 * Save custom Submit Button setting for CSS classes.
	 *
	 * @since    2.0.5
	 */
	public function save_custom_form_submit_button_classes_setting($form) {
		$form['submit_css_classes'] = rgpost( 'submit_css_classes' );
		return $form;
	}


}
