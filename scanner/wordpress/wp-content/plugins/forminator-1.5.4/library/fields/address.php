<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Address
 *
 * @since 1.0
 */
class Forminator_Address extends Forminator_Field {

	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var string
	 */
	public $slug = 'address';

	/**
	 * @var int
	 */
	public $position = 5;

	/**
	 * @var string
	 */
	public $type = 'address';

	/**
	 * @var array
	 */
	public $options = array();

	/**
	 * @var string
	 */
	public $category = 'standard';

	/**
	 * Forminator_Address constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		parent::__construct();
		$this->name = __( 'Address', Forminator::DOMAIN );
	}

	/**
	 * @param array $settings
	 *
	 * @since 1.0
	 * @return array
	 */
	public function load_settings( $settings = array() ) {
		return array(
			array(
				'id'         => 'required',
				'type'       => 'Toggle',
				'name'       => 'required',
				'hide_label' => true,
				'values'     => array(
					array(
						'value' => "true",
						'label' => __( 'Required', Forminator::DOMAIN ),
					),
				),
			),

			array(
				'id'         => 'street-address',
				'type'       => 'MultiName',
				'name'       => 'street_address',
				'hide_label' => true,
				'values'     => array(
					array(
						'value' => "true",
						'label' => __( 'Street Address', Forminator::DOMAIN ),
					),
				),
				'fields'     => array(
					array(
						'id'    => 'street-address-label',
						'type'  => 'Text',
						'name'  => 'street_address_label',
						'label' => __( 'Label', Forminator::DOMAIN ),
					),
					array(
						'id'    => 'street-address-placeholder',
						'type'  => 'Text',
						'name'  => 'street_address_placeholder',
						'label' => __( 'Placeholder', Forminator::DOMAIN ),
					),
				),
			),

			array(
				'id'         => 'address-line',
				'type'       => 'MultiName',
				'name'       => 'address_line',
				'hide_label' => true,
				'values'     => array(
					array(
						'value' => "true",
						'label' => __( 'Address Line 2', Forminator::DOMAIN ),
					),
				),
				'fields'     => array(
					array(
						'id'    => 'address-line-label',
						'type'  => 'Text',
						'name'  => 'address_line_label',
						'label' => __( 'Label', Forminator::DOMAIN ),
					),
					array(
						'id'    => 'address-line-placeholder',
						'type'  => 'Text',
						'name'  => 'address_line_placeholder',
						'label' => __( 'Placeholder', Forminator::DOMAIN ),
					),
				),
			),

			array(
				'id'         => 'address-city',
				'type'       => 'MultiName',
				'name'       => 'address_city',
				'hide_label' => true,
				'values'     => array(
					array(
						'value' => "true",
						'label' => __( 'City', Forminator::DOMAIN ),
					),
				),
				'fields'     => array(
					array(
						'id'    => 'address-city-label',
						'type'  => 'Text',
						'name'  => 'address_city_label',
						'label' => __( 'Label', Forminator::DOMAIN ),
					),
					array(
						'id'    => 'address-city-placeholder',
						'type'  => 'Text',
						'name'  => 'address_city_placeholder',
						'label' => __( 'Placeholder', Forminator::DOMAIN ),
					),
				),
			),

			array(
				'id'         => 'address-state',
				'type'       => 'MultiName',
				'name'       => 'address_state',
				'hide_label' => true,
				'values'     => array(
					array(
						'value' => "true",
						'label' => __( 'State / Province', Forminator::DOMAIN ),
					),
				),
				'fields'     => array(
					array(
						'id'        => 'address-state-label',
						'type'      => 'Text',
						'name'      => 'address_state_label',
						'className' => 'text-field',
						'label'     => __( 'Label', Forminator::DOMAIN ),
					),
					array(
						'id'        => 'address-state-placeholder',
						'type'      => 'Text',
						'name'      => 'address_state_placeholder',
						'className' => 'text-field',
						'label'     => __( 'Placeholder', Forminator::DOMAIN ),
					),
				),
			),

			array(
				'id'         => 'address-zip',
				'type'       => 'MultiName',
				'name'       => 'address_zip',
				'hide_label' => true,
				'values'     => array(
					array(
						'value' => "true",
						'label' => __( 'ZIP / Postal Code', Forminator::DOMAIN ),
					),
				),
				'fields'     => array(
					array(
						'id'    => 'address-zip-label',
						'type'  => 'Text',
						'name'  => 'address_zip_label',
						'label' => __( 'Label', Forminator::DOMAIN ),
					),
					array(
						'id'    => 'address-zip-placeholder',
						'type'  => 'Text',
						'name'  => 'address_zip_placeholder',
						'label' => __( 'Placeholder', Forminator::DOMAIN ),
					),
				),
			),

			array(
				'id'         => 'address-country',
				'type'       => 'MultiName',
				'name'       => 'address_country',
				'hide_label' => true,
				'values'     => array(
					array(
						'value' => "true",
						'label' => __( 'Country', Forminator::DOMAIN ),
					),
				),
				'fields'     => array(
					array(
						'id'    => 'address-country-label',
						'type'  => 'Text',
						'name'  => 'address_country_label',
						'label' => __( 'Label', Forminator::DOMAIN ),
					),
				),
			),
		);

	}

	/**
	 * Field defaults
	 *
	 * @since 1.0
	 * @return array
	 */
	public function defaults() {
		return array(
			'street_address'             => "true",
			'address_city'               => "true",
			'address_state'              => "true",
			'address_zip'                => "true",
			'address_country'            => "true",
			'street_address_label'       => __( 'Street Address', Forminator::DOMAIN ),
			'street_address_placeholder' => __( 'E.g. 42 Wallaby Way', Forminator::DOMAIN ),
			'address_city_label'         => __( 'City', Forminator::DOMAIN ),
			'address_city_placeholder'   => __( 'E.g. Sydney', Forminator::DOMAIN ),
			'address_state_label'        => __( 'State/Province', Forminator::DOMAIN ),
			'address_state_placeholder'  => __( 'E.g. New South Wales', Forminator::DOMAIN ),
			'address_zip_label'          => __( 'ZIP / Postal Code', Forminator::DOMAIN ),
			'address_zip_placeholder'    => __( 'E.g. 2000', Forminator::DOMAIN ),
			'address_country_label'      => __( 'Country', Forminator::DOMAIN ),
		);
	}

	/**
	 * Autofill Setting
	 *
	 * @since 1.0.5
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function autofill_settings( $settings = array() ) {
		$street_address_providers = apply_filters( 'forminator_field_' . $this->slug . '_street_address_autofill', array(), $this->slug . '_street_address' );
		$address_line_providers   = apply_filters( 'forminator_field_' . $this->slug . '_address_line_autofill', array(), $this->slug . '_address_line' );
		$city_providers           = apply_filters( 'forminator_field_' . $this->slug . '_city_autofill', array(), $this->slug . '_city' );
		$state_providers          = apply_filters( 'forminator_field_' . $this->slug . '_state_autofill', array(), $this->slug . '_state' );
		$zip_providers            = apply_filters( 'forminator_field_' . $this->slug . '_zip_autofill', array(), $this->slug . '_zip' );

		$autofill_settings = array(
			'address-street_address' => array(
				'values' => forminator_build_autofill_providers( $street_address_providers ),
			),
			'address-address_line'   => array(
				'values' => forminator_build_autofill_providers( $address_line_providers ),
			),
			'address-city'           => array(
				'values' => forminator_build_autofill_providers( $city_providers ),
			),
			'address-state'          => array(
				'values' => forminator_build_autofill_providers( $state_providers ),
			),
			'address-zip'            => array(
				'values' => forminator_build_autofill_providers( $zip_providers ),
			),
		);

		return $autofill_settings;
	}

	/**
	 * Field admin markup
	 *
	 * @since 1.0
	 * @return string
	 */
	public function admin_html() {
		return '{[ if( field.street_address == "true" ) { ]}
			<div class="sui-row">
				<div class="sui-col">
					{[ if( field.street_address_label !== "" ) { ]}
						<label class="sui-label">{{ encodeHtmlEntity( field.street_address_label ) }}{[ if( field.required == "true" ) { ]} *{[ } ]}</label>
					{[ } ]}
					<input type="text" class="sui-form-control" placeholder="{{ encodeHtmlEntity( field.street_address_placeholder ) }}" {{ field.required ? "required" : "" }}>
				</div>
			</div>
		{[ } ]}
		{[ if( field.address_line == "true" ) { ]}
			<div class="sui-row">
				<div class="sui-col">
					{[ if( field.address_line_label !== "" ) { ]}
						<label class="sui-label">{{ encodeHtmlEntity( field.address_line_label ) }}{[ if( field.required == "true" ) { ]} *{[ } ]}</label>
					{[ } ]}
					<input type="text" class="sui-form-control" placeholder="{{ encodeHtmlEntity( field.address_line_placeholder ) }}" {{ field.required ? "required" : "" }}>
				</div>
			</div>
		{[ } ]}
		{[ if( field.address_city == "true" || field.address_state == "true" ) { ]}
			<div class="sui-row">
				{[ if( field.address_city == "true" ) { ]}
					{[ if( field.address_state == "true" ) { ]}
					<div class="sui-col-md-6">
					{[ } else { ]}
					<div class="sui-col">
					{[ } ]}
						{[ if( field.address_city_label !== "" ) { ]}
							<label class="sui-label">{{ encodeHtmlEntity( field.address_city_label ) }}{[ if( field.required == "true" ) { ]} *{[ } ]}</label>
						{[ } ]}
						<input type="text" class="sui-form-control" placeholder="{{ encodeHtmlEntity( field.address_city_placeholder ) }}" {{ field.required ? "required" : "" }}>
					</div>
				{[ } ]}
				{[ if( field.address_state == "true" ) { ]}
					{[ if( field.address_city == "true" ) { ]}
					<div class="sui-col-md-6">
					{[ } else { ]}
					<div class="sui-col">
					{[ } ]}
						{[ if( field.address_state_label !== "" ) { ]}
							<label class="sui-label">{{ encodeHtmlEntity( field.address_state_label ) }}{[ if( field.required == "true" ) { ]} *{[ } ]}</label>
						{[ } ]}
						<input type="text" class="sui-form-control" placeholder="{{ encodeHtmlEntity( field.address_state_placeholder ) }}" {{ field.required ? "required" : "" }}>
					</div>
				{[ } ]}
			</div>
		{[ } ]}
		{[ if( field.address_zip == "true" || field.address_country == "true" ) { ]}
			<div class="sui-row">
				{[ if( field.address_zip == "true" ) { ]}
					{[ if( field.address_country == "true" ) { ]}
					<div class="sui-col-md-6">
					{[ } else { ]}
					<div class="sui-col">
					{[ } ]}
						{[ if( field.address_zip_label !== "" ) { ]}
							<label class="sui-label">{{ encodeHtmlEntity( field.address_zip_label ) }}{[ if( field.required == "true" ) { ]} *{[ } ]}</label>
						{[ } ]}
						<input type="number" class="sui-form-control" placeholder="{{ encodeHtmlEntity( field.address_zip_placeholder ) }}" {{ field.required ? "required" : "" }}>
					</div>
				{[ } ]}
				{[ if( field.address_country == "true" ) { ]}
					{[ if( field.address_zip == "true" ) { ]}
					<div class="sui-col-md-6">
					{[ } else { ]}
					<div class="sui-col">
					{[ } ]}
						{[ if( field.address_country_label !== "" ) { ]}
							<label class="sui-label">{{ encodeHtmlEntity( field.address_country_label ) }}{[ if( field.required == "true" ) { ]} *{[ } ]}</label>
						{[ } ]}
						<select {{ field.required ? "required" : "" }}>
							{[ _.each( field.options, function( value, key ){ ]}
								<option>{{ value.label }}</option>
							{[ }) ]}
						</select>
					</div>
				{[ } ]}
			</div>
		{[ } ]}';
	}

	/**
	 * Field front-end markup
	 *
	 * @since 1.0
	 *
	 * @param $field
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function markup( $field, $settings = array() ) {
		$this->field         = $field;
		$this->form_settings = $settings;

		$design = $this->get_form_style( $settings );

		// Address
		$html = $this->get_address( $field, 'street_address', $design );

		// Second Address
		$html .= $this->get_address( $field, 'address_line', $design );

		// City & State fields
		$html .= $this->get_city_state( $field, $design );

		// ZIP & Country fields
		$html .= $this->get_zip_country( $field, $design );

		return apply_filters( 'forminator_field_address_markup', $html, $field );
	}

	/**
	 * Return address input markup
	 *
	 * @since 1.0
	 *
	 * @param $field
	 * @param $slug
	 *
	 * @return string
	 */
	public function get_address( $field, $slug, $design ) {
		$cols     = 12;
		$html     = '';
		$id       = self::get_property( 'element_id', $field );
		$name     = $id;
		$required = self::get_property( 'required', $field, false );
		$enabled  = self::get_property( $slug, $field );

		if ( ! $enabled ) {
			return '';
		}

		/**
		 * Create address field
		 */
		$address = array(
			'type'        => 'text',
			'class'       => 'forminator-input',
			'name'        => $name . '-' . $slug,
			'id'          => $name . '-' . $slug,
			'placeholder' => $this->sanitize_value( self::get_property( $slug . '_placeholder', $field ) ),
		);

		// Address field markup
		$html .= '<div class="forminator-row forminator-row--inner">';
		$html .= sprintf( '<div class="forminator-col forminator-col-%s">', $cols );
		$html .= '<div class="forminator-field forminator-field--inner">';

		$html .= self::create_input( $address, self::get_property( $slug . '_label', $field ), '', $required, $design );

		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Return City and State fields markup
	 *
	 * @since 1.0
	 *
	 * @param $field
	 *
	 * @return string
	 */
	public function get_city_state( $field, $design ) {
		$cols     = 12;
		$html     = '';
		$id       = self::get_property( 'element_id', $field );
		$required = self::get_property( 'required', $field, false );
		$city     = self::get_property( 'address_city', $field, false );
		$state    = self::get_property( 'address_state', $field, false );

		// If both prefix & first name are disabled, return
		if ( ! $city && ! $state ) {
			return '';
		}

		// If both prefix & first name are enabled, change cols
		if ( $city && $state ) {
			$cols = 6;
		}

		if ( $city ) {
			/**
			 * Create city field
			 */
			$city_data = array(
				'type'        => 'text',
				'class'       => 'forminator-input',
				'name'        => $id . '-city',
				'id'          => $id . '-city',
				'placeholder' => $this->sanitize_value( self::get_property( 'address_city_placeholder', $field ) ),
			);

			// City markup
			$html .= '<div class="forminator-row forminator-row--inner">';
			$html .= sprintf( '<div class="forminator-col forminator-col-%s">', $cols );
			$html .= '<div class="forminator-field forminator-field--inner">';

			$html .= self::create_input( $city_data, self::get_property( 'address_city_label', $field ), '', $required, $design );

			$html .= '</div>';
			$html .= '</div>';

			if ( ! $state ) {
				$html .= '</div>';
			}
		}

		if ( $state ) {
			/**
			 * Create state field
			 */
			$state_data = array(
				'type'        => 'text',
				'class'       => 'forminator-input',
				'name'        => $id . '-state',
				'id'          => $id . '-state',
				'placeholder' => $this->sanitize_value( self::get_property( 'address_state_placeholder', $field ) ),
			);

			if ( ! $city ) {
				$html .= '<div class="forminator-row forminator-row--inner">';
			}

			// State markup
			$html .= sprintf( '<div class="forminator-col forminator-col-%s">', $cols );
			$html .= '<div class="forminator-field forminator-field--inner">';

			$html .= self::create_input( $state_data, self::get_property( 'address_state_label', $field ), '', $required, $design );

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Return Zip and County inputs
	 *
	 * @since 1.0
	 *
	 * @param $field
	 *
	 * @return string
	 */
	public function get_zip_country( $field, $design ) {
		$cols            = 12;
		$html            = '';
		$id              = self::get_property( 'element_id', $field );
		$required        = self::get_property( 'required', $field, false );
		$address_zip     = self::get_property( 'address_zip', $field, false );
		$address_country = self::get_property( 'address_country', $field, false );

		// If both prefix & first name are disabled, return
		if ( ! $address_zip && ! $address_country ) {
			return '';
		}

		// If both prefix & first name are enabled, change cols
		if ( $address_zip && $address_country ) {
			$cols = 6;
		}

		if ( $address_zip ) {
			/**
			 * Create first name field
			 */
			$zip_data = array(
				'type'        => 'text',
				'class'       => 'forminator-input',
				'name'        => $id . '-zip',
				'id'          => $id . '-zip',
				'placeholder' => $this->sanitize_value( self::get_property( 'address_zip_placeholder', $field ) ),
			);

			$html .= '<div class="forminator-row forminator-row--inner">';
			$html .= sprintf( '<div class="forminator-col forminator-col-%s">', $cols );
			$html .= '<div class="forminator-field forminator-field--inner">';

			$html .= self::create_input( $zip_data, self::get_property( 'address_zip_label', $field ), '', $required, $design );

			$html .= '</div>';
			$html .= '</div>';

			if ( ! $address_country ) {
				$html .= '</div>';
			}
		}

		if ( $address_country ) {
			/**
			 * Create prefix field
			 */
			$country_data = array(
				'class' => 'forminator-select',
				'name'  => $id . '-country',
				'id'    => $id . '-country',
			);

			if ( ! $address_zip ) {
				$html .= '<div class="forminator-row">';
			}

			$countries = array(
				array(
					'value' => '',
					'label' => __( "Select country", Forminator::DOMAIN ),
				),
			);

			$options   = forminator_to_field_array( forminator_get_countries_list(), true );
			$countries = array_merge( $countries, $options );

			/**
			 * Filter countries for <options> on <select> field
			 *
			 * Default format
			 * [
			 *  {
			 *      label:"label",
			 *      value:"value"
			 *  },
			 *  {
			 *      label:"label",
			 *      value:"value"
			 *  }
			 * ]
			 *
			 * Nested format
			 * [
			 *  {
			 *      label:"label",
			 *      value:[
			 *              {
			 *                  label:"sub-label",
			 *                  value:"sub-value",
			 *              },
			 *              {
			 *                  label:"sub-label",
			 *                  value:"sub-value",
			 *              }
			 *          ]
			 *  },
			 *  {
			 *      label:"label",
			 *      value:"value"
			 *  }
			 * ]
			 *
			 * @since 1.5.2
			 *
			 * @param array $countries
			 */
			$countries = apply_filters( 'forminator_countries_field', $countries );

			$html .= sprintf( '<div class="forminator-col forminator-col-%s">', $cols );
			$html .= '<div class="forminator-field forminator-field--inner">';

			if ( $required ) {
				$label    = self::get_property( 'address_country_label', $field );
				$asterisk = '<i class="wpdui-icon wpdui-icon-asterisk" aria-hidden="true"></i>';
				if ( ! empty( $label ) ) {
					$html .= '<div class="forminator-field--label">';
					$html .= '<label class="forminator-label">' . $label . ' ' . $asterisk . '</label>';
					$html .= '</div>';
				}
				$html .= self::create_simple_select( $country_data, $countries, self::get_property( 'address_country_placeholder', $field ) );
			} else {
				$html .= self::create_select( $country_data, self::get_property( 'address_country_label', $field ), $countries, self::get_property( 'address_country_placeholder', $field ) );
			}

			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Return field inline validation rules
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_validation_rules() {
		$field    = $this->field;
		$multiple = self::get_property( 'multiple_name', $field, false );
		$rules    = '';

		if ( $this->is_required( $field ) ) {
			$rules .= '"' . $this->get_id( $field ) . '-street_address": "required",';
			$rules .= '"' . $this->get_id( $field ) . '-city": "required",';
			$rules .= '"' . $this->get_id( $field ) . '-state": "required",';
			$rules .= '"' . $this->get_id( $field ) . '-zip": "required",';
			$rules .= '"' . $this->get_id( $field ) . '-country": "required",';
		}

		return $rules;
	}

	/**
	 * Return field inline validation errors
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_validation_messages() {
		$field    = $this->field;
		$id       = $this->get_id( $field );
		$messages = '';

		if ( $this->is_required( $field ) ) {
			// Street required validation
			$street_required_message = __( 'This field is required. Please enter the street address', Forminator::DOMAIN );
			$street_error_message    = apply_filters( 'forminator_address_field_street_validation_message', $street_required_message, $id, $field );
			$messages                .= '"' . $this->get_id( $field ) . '-street_address": "' . $street_error_message . '",' . "\n";

			// City required validation
			$city_required_message = __( 'This field is required. Please enter the city', Forminator::DOMAIN );
			$city_error_message    = apply_filters( 'forminator_address_field_city_validation_message', $city_required_message, $id, $field );
			$messages              .= '"' . $this->get_id( $field ) . '-city": "' . $city_error_message . '",' . "\n";

			// State required validation
			$state_required_message = __( 'This field is required. Please enter the state', Forminator::DOMAIN );
			$state_error_message    = apply_filters( 'forminator_address_field_state_validation_message', $state_required_message, $id, $field );
			$messages               .= '"' . $this->get_id( $field ) . '-state": "' . $state_error_message . '",' . "\n";

			// ZIP required validation
			$zip_required_message = __( 'This field is required. Please enter the zip code', Forminator::DOMAIN );
			$zip_error_message    = apply_filters( 'forminator_address_field_zip_validation_message', $zip_required_message, $id, $field );
			$messages             .= '"' . $this->get_id( $field ) . '-zip": "' . $zip_error_message . '",' . "\n";

			// Country required validation
			$country_required_message = __( 'This field is required. Please select the country', Forminator::DOMAIN );
			$country_error_message    = apply_filters( 'forminator_address_field_country_validation_message', $country_required_message, $id, $field );
			$messages                 .= '"' . $this->get_id( $field ) . '-country": "' . $country_error_message . '",' . "\n";
		}

		return $messages;
	}

	/**
	 * Field back-end validation
	 *
	 * @since 1.0
	 *
	 * @param array        $field
	 * @param array|string $data
	 */
	public function validate( $field, $data ) {
		if ( $this->is_required( $field ) ) {
			$id = self::get_property( 'element_id', $field );
			if ( empty( $data ) ) {
				$this->validation_message[ $id ] = apply_filters(
					'forminator_address_field_validation_message',
					__( 'This field is required. Please enter the address', Forminator::DOMAIN ),
					$id,
					$field
				);
			} else {
				if ( is_array( $data ) ) {
					//add street address
					$address_street  = self::get_property( 'street_address', $field, false );
					$address_zip     = self::get_property( 'address_zip', $field, false );
					$address_country = self::get_property( 'address_country', $field, false );
					$address_city    = self::get_property( 'address_city', $field, false );
					$address_state   = self::get_property( 'address_state', $field, false );
					$street          = isset( $data['street_address'] ) ? $data['street_address'] : '';
					$zip             = isset( $data['zip'] ) ? $data['zip'] : '';
					$country         = isset( $data['country'] ) ? $data['country'] : '';
					$city            = isset( $data['city'] ) ? $data['city'] : '';
					$state           = isset( $data['state'] ) ? $data['state'] : '';
					if ( $address_street && empty( $street ) ) {
						$street_required_message                             = __( 'This field is required. Please enter the street address', Forminator::DOMAIN );
						$street_error_message                                = apply_filters( 'forminator_address_field_street_validation_message', $street_required_message, $id, $field );
						$this->validation_message[ $id . '-street_address' ] = $street_error_message;
					}
					if ( $address_zip && empty( $zip ) ) {
						$zip_required_message                     = __( 'This field is required. Please enter the zip code', Forminator::DOMAIN );
						$zip_error_message                        = apply_filters( 'forminator_address_field_zip_validation_message', $zip_required_message, $id, $field );
						$this->validation_message[ $id . '-zip' ] = $zip_error_message;
					}
					if ( $address_country && empty( $country ) && '0' !== $country ) {
						$country_required_message                     = __( 'This field is required. Please select the country', Forminator::DOMAIN );
						$country_error_message                        = apply_filters( 'forminator_address_field_country_validation_message', $country_required_message, $id, $field );
						$this->validation_message[ $id . '-country' ] = $country_error_message;
					}
					if ( $address_city && empty( $city ) ) {
						$city_required_message                     = __( 'This field is required. Please enter the city', Forminator::DOMAIN );
						$city_error_message                        = apply_filters( 'forminator_address_field_city_validation_message', $city_required_message, $id, $field );
						$this->validation_message[ $id . '-city' ] = $city_error_message;
					}
					if ( $address_state && empty( $state ) ) {
						$state_required_message                     = __( 'This field is required. Please enter the state', Forminator::DOMAIN );
						$state_error_message                        = apply_filters( 'forminator_address_field_state_validation_message', $state_required_message, $id, $field );
						$this->validation_message[ $id . '-state' ] = $state_error_message;
					}
				}
			}
		}
	}

	/**
	 * Sanitize data
	 *
	 * @since 1.0.2
	 *
	 * @param array        $field
	 * @param array|string $data - the data to be sanitized
	 *
	 * @return array|string $data - the data after sanitization
	 */
	public function sanitize( $field, $data ) {
		// Sanitize
		$data = forminator_sanitize_field( $data );

		return apply_filters( 'forminator_field_address_sanitize', $data, $field );
	}
}
