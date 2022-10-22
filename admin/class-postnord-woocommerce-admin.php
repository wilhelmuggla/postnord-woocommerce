<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://owlhub.se
 * @since      1.0.0
 *
 * @package    Postnord_Woocommerce
 * @subpackage Postnord_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Postnord_Woocommerce
 * @subpackage Postnord_Woocommerce/admin
 * @author     OwlHub AB <wilhelm@owlhub.se>
 */
class Postnord_Woocommerce_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies();

		new PostNordWoocommerceMetabox($this->plugin_name);
	}
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-postnord-woocommerce-metabox.php';
	}

	public function admin_page()
	{

		add_menu_page($this->plugin_name, 'PostNord integration', 'administrator', $this->plugin_name, array($this, 'displayPluginAdminDashboard'), 'dashicons-chart-area', 26);
		add_action('admin_init', array($this, 'registerAndBuildFields'));
	}

	public function displayPluginAdminDashboard()
	{

		require_once 'partials/' . $this->plugin_name . '-admin-display.php';
	}



	public function registerAndBuildFields()
	{
		/**
		 * First, we add_settings_section. This is necessary since all future settings must belong to one.
		 * Second, add_settings_field
		 * Third, register_setting
		 */
		add_settings_section(
			// ID used to identify this section and with which to register options
			'postnord_woocommerce_general_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			array($this, 'postnord_woocommerce_display_general_account'),
			// Page on which to add this section of options
			'postnord_woocommerce_general_settings'
		);

		add_settings_section(
			// ID used to identify this section and with which to register options
			'postnord_woocommerce_address_section',
			// Title to be displayed on the administration page
			'Company Address',
			// Callback used to render the description of the section
			array($this, 'address_section_desctiption'),
			// Page on which to add this section of options
			'postnord_woocommerce_general_settings'
		);


		add_settings_section(
			// ID used to identify this section and with which to register options
			'postnord_woocommerce_autogenerate_section',
			// Title to be displayed on the administration page
			'Autogenerate labels',
			// Callback used to render the description of the section
			null,
			// Page on which to add this section of options
			'postnord_woocommerce_general_settings'
		);


		add_settings_section(
			// ID used to identify this section and with which to register options
			'postnord_woocommerce_printer_section',
			// Title to be displayed on the administration page
			'Printer settings',
			// Callback used to render the description of the section
			null,
			// Page on which to add this section of options
			'postnord_woocommerce_general_settings'
		);



		add_settings_field(
			'postnord_customer_number',
			'Postnord Company Number',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_general_section',
			array(
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'postnord_customer_number',
				'name'      => 'postnord_customer_number',
				'required' => 'true',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);

		add_settings_field(
			'postnord_api_key',
			'Postnord API Key',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_general_section',
			array(
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'postnord_api_key',
				'name'      => 'postnord_api_key',
				'required' => 'true',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);


		add_settings_field(
			'postnord_sandbox',
			'Sandbox',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_general_section',
			array(
				'type'      => 'input',
				'subtype'   => 'checkbox',
				'id'    => 'postnord_sandbox',
				'name'      => 'postnord_sandbox',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);


		add_settings_field(
			'postnord_company_name',
			'Company Name',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_address_section',
			array(
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'postnord_company_name',
				'name'      => 'postnord_company_name',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);

		add_settings_field(
			'postnord_address_street',
			'Adress',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_address_section',
			array(
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'postnord_address_street',
				'name'      => 'postnord_address_street',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);

		add_settings_field(
			'postnord_address_zipcode',
			'Zipcode',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_address_section',
			array(
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'postnord_address_zipcode',
				'name'      => 'postnord_address_zipcode',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);

		add_settings_field(
			'postnord_address_city',
			'City',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_address_section',
			array(
				'type'      => 'input',
				'subtype'   => 'text',
				'id'    => 'postnord_address_city',
				'name'      => 'postnord_address_city',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);

		add_settings_field(
			'postnord_autogenerate_labels',
			'Autogenerate Labels',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_autogenerate_section',
			array(
				'type'      => 'input',
				'subtype'   => 'checkbox',
				'id'    => 'postnord_autogenerate_labels',
				'name'      => 'postnord_autogenerate_labels',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);

		add_settings_field(
			'postnord_wc_status_name',
			'Autogenerate Label on Status',
			array($this, 'render_wc_status_name_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_autogenerate_section',
			array(
				'type'      => 'input',
				'subtype'   => 'select',
				'id'    => 'postnord_wc_status_name',
				'name'      => 'postnord_wc_status_name',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);

		add_settings_field(
			'postnord_wc_printer_size',
			'PDF page size for the label',
			array($this, 'render_wc_printer_size_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_printer_section',
			array(
				'type'      => 'input',
				'subtype'   => 'select',
				'id'    => 'postnord_wc_printer_size',
				'name'      => 'postnord_wc_printer_size',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option'
			)
		);

		add_settings_field(
			'postnord_wc_extra_weight',
			'Extra weight',
			array($this, 'render_settings_field'),
			'postnord_woocommerce_general_settings',
			'postnord_woocommerce_printer_section',
			array(
				'type'      => 'input',
				'subtype'   => 'number',
				'id'    => 'postnord_wc_extra_weight',
				'name'      => 'postnord_wc_extra_weight',
				'required' => 'false',
				'get_options_list' => '',
				'value_type' => 'normal',
				'wp_data' => 'option',
				'append' => 'grams'
			)
		);


		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_customer_number'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_api_key'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_sandbox'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_company_name'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_address_street'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_address_zipcode'
		);
		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_address_city'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_autogenerate_labels'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_wc_status_name'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_wc_printer_size'
		);

		register_setting(
			'postnord_woocommerce_general_settings',
			'postnord_wc_extra_weight'
		);
	}

	public function postnord_woocommerce_display_general_account()
	{
		echo '<p>These settings apply to all Plugin Name functionality.</p>';
	}

	public function address_section_desctiption()
	{
		echo '
		<p>From address on the labels.</p>';
	}

	public function allowedHTML()
	{
		return array(
			'input' => array(
				'type'      => array(),
				'name'      => array(),
				'value'     => array(),
				'checked'   => array(),
				'step' => array(),
				'min' => array(),
				'max' => array(),
				'required'  => array(),
				'size'  => array(),
				'disabled'=> 'disabled',
				'checked' => 'checked'

			),
			'option' => array(
				'value' => array(),
				'selected' => array()
			)
		);
	}


	public function render_settings_field($args)
	{

		if ($args['wp_data'] == 'option') {
			$wp_data_value = get_option($args['name']);
		} elseif ($args['wp_data'] == 'post_meta') {
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
		}

		switch ($args['type']) {

			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if ($args['subtype'] != 'checkbox') {
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
					$min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
					$max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
					if (isset($args['disabled'])) {
						// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo wp_kses($prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd, $this->allowedHTML());
					} else {
						echo wp_kses($prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd, $this->allowedHTML());
					}
					if (isset($args['append'])) {
						echo esc_attr($args['append']);
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/
				} else {
					$checked = ($value) ? 'checked' : '';
					echo wp_kses('<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />', $this->allowedHTML());
				}
				break;
			default:
				# code...
				break;
		}
	}

	public function render_wc_status_name_field($args)
	{
		echo '<select name="postnord_wc_status_name">';
		$order_statuses = wc_get_order_statuses();
		foreach ($order_statuses as $key => $value) {

			echo '<option value="' .esc_attr($key). '"' .esc_attr(selected(get_option('postnord_wc_status_name'), $key)). '">' .esc_attr($value). '</option>';
		}
		echo '</select>';
	}

	public function render_wc_printer_size_field($args)
	{
		//Valid values: A4, A5, LABEL
		echo '<select name="postnord_wc_printer_size">';
		echo '<option value="A4"' .esc_attr(selected(get_option('postnord_wc_printer_size'), 'A4')). '>A4</option>';
		echo '<option value="A5"' . esc_attr(selected(get_option('postnord_wc_printer_size'), 'A5')). '>A5</option>';
		echo '<option value="LABEL"' . esc_attr(selected(get_option('postnord_wc_printer_size'), 'LABEL')). '>Label</option>';
		echo '</select>';
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Postnord_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Postnord_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/postnord-woocommerce-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Postnord_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Postnord_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/postnord-woocommerce-admin.js', array('jquery'), $this->version, false);
	}
}
