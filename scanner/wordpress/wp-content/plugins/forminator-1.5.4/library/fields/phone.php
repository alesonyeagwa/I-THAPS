<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Phone
 *
 * @since 1.0
 */
class Forminator_Phone extends Forminator_Field {

	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var string
	 */
	public $slug = 'phone';

	/**
	 * @var int
	 */
	public $position = 3;

	/**
	 * @var string
	 */
	public $type = 'phone';

	/**
	 * @var array
	 */
	public $options = array();

	/**
	 * @var string
	 */
	public $category = 'standard';

	/**
	 * @var bool
	 */
	public $is_input = true;

	/**
	 * @var bool
	 */
	public $has_counter = true;

	/**
	 * Forminator_Phone constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		parent::__construct();

		$this->name = __( 'Phone', Forminator::DOMAIN );
	}

	/**
	 * @since 1.0
	 * @param array $settings
	 *
	 * @return array
	 */
	public function load_settings( $settings = array() ) {
		$country_options = forminator_to_field_array( forminator_get_countries_list(), false );
		return array(
			array(
				'id'         => 'required',
				'type'       => 'Toggle',
				'name'       => 'required',
				'hide_label' => true,
				'values'     => array(
					array(
						'value'      => "true",
						'label'      => __( 'Required', Forminator::DOMAIN ),
						'labelSmall' => "true",
					),
				),
			),

			array(
				'id'         => 'field-label',
				'type'       => 'Text',
				'name'       => 'field_label',
				'hide_label' => false,
				'label'      => __( 'Field Label', Forminator::DOMAIN ),
				'size'       => 12,
				'className'  => 'text-field',
			),

			array(
				'id'         => 'placeholder',
				'type'       => 'Text',
				'name'       => 'placeholder',
				'hide_label' => false,
				'label'      => __( 'Placeholder', Forminator::DOMAIN ),
				'className'  => 'text-field',
			),

			array(
				'id'         => 'field-description',
				'type'       => 'Text',
				'name'       => 'description',
				'hide_label' => false,
				'label'      => __( 'Description', Forminator::DOMAIN ),
				'className'  => 'text-field',
			),

			array(
				'id'          => 'phone-validation',
				'type'        => 'ToggleContainer',
				'name'        => 'phone_validation',
				'hide_label'  => true,
				'values'      => array(
					array(
						'value'      => "true",
						'label'      => __( 'Enable Validation', Forminator::DOMAIN ),
						'labelSmall' => "true",
					),
				),
				'fields'      => array(
					array(
						'id'           => 'field-phone_validation-type',
						'type'         => 'Select',
						'name'         => 'phone_validation_type',
						'className'    => 'number-field',
						'label_hidden' => true,
						'values'       => apply_filters(
							'forminator_phone_validation_type',
							array(
								array(
									'value' => "standard",
									'label' => __( 'National', Forminator::DOMAIN ),
								),
								array(
									'value' => "international",
									'label' => __( 'International', Forminator::DOMAIN ),
								),
								array(
									'value' => "character_limit",
									'label' => __( 'Limit characters', Forminator::DOMAIN ),
								),
							)
						),
					),
					array(
						'id'               => 'phone-field-limit',
						'type'             => 'Number',
						'name'             => 'limit',
						'setting_to_check' => 'phone_validation_type',
						'value_to_show'    => 'character_limit',
						'label'            => __( 'Limit to:', Forminator::DOMAIN ),
					),
					array(
						'id'           => 'field-phone_national_country',
						'type'         => 'Select',
						'name'         => 'phone_national_country',
						'setting_to_check' => 'phone_validation_type',
						'value_to_show'    => 'standard',
						'label'      => __( 'Select country', Forminator::DOMAIN ),
						'values'       => apply_filters( 'forminator_phone_national_country', $country_options ),
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
		return apply_filters( 'forminator_phone_defaults_settings', array(
			'required'              => false,
			'limit'                 => 10,
			'limit_type'            => 'characters',
			'phone_validation_type' => "standard",
			'field_label'           => __( 'Phone', Forminator::DOMAIN ),
			'placeholder'           => __( 'E.g. +1 300 400 5000', Forminator::DOMAIN ),
		) );
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
		$providers = apply_filters( 'forminator_field_' . $this->slug . '_autofill', array(), $this->slug );

		$autofill_settings = array(
			'phone' => array(
				'values' => forminator_build_autofill_providers( $providers ),
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
		return '{[ if( field.field_label !== "" ) { ]}
			<label class="sui-label">{{ encodeHtmlEntity( field.field_label ) }}{[ if( field.required == "true" ) { ]} *{[ } ]}</label>
		{[ } ]}
		<input type="{{ field.type }}" class="sui-form-control" placeholder="{{ encodeHtmlEntity( field.placeholder ) }}" {{ field.required ? "required" : "" }}>
		{[ if( field.description && ( field.phone_validation && field.phone_validation_type === "character_limit" && field.limit ) ) { ]}
			<div class="fui-extended-description">
		{[ } ]}
			{[ if( field.description ) { ]}
				<span class="sui-description">{{ encodeHtmlEntity( field.description ) }}</span>
			{[ } ]}
			{[ if( field.description && ( field.phone_validation && field.phone_validation_type === "character_limit" && field.limit ) ) { ]}
				<div class="sui-actions-right">
			{[ } ]}
			{[ if( field.phone_validation && field.phone_validation_type === "character_limit" && field.limit ) { ]}
				<span class="sui-description">0 / {{ field.limit }}</span>
			{[ } ]}
			{[ if( field.description && ( field.phone_validation && field.phone_validation_type === "character_limit" && field.limit ) ) { ]}
				</div>
			{[ } ]}
		{[ if( field.description && ( field.phone_validation && field.phone_validation_type === "character_limit" && field.limit ) ) { ]}
			</div>
		{[ } ]}';
	}

	/**
	 * Phone formats
	 *
	 * @since 1.0
	 * @since 1.5.1 add regex for international phone number
	 * @return array
	 */
	public function get_phone_formats() {
		$phone_formats = array(
			'standard'      => array(
				'label'       => '(###) ###-####',
				'mask'        => '(999) 999-9999',
				/**
				 * match jquery-validation phoneUS validation
				 * https://github.com/jquery-validation/jquery-validation/blob/1.17.0/src/additional/phoneUS.js#L20
				 */
				//'regex'       => '/^(\+?1-?)?(\([2-9]([02-9]\d|1[02-9])\)|[2-9]([02-9]\d|1[02-9]))-?[2-9]([02-9]\d|1[02-9])-?\d{4}$/',
				'regex'		  => '/^(\d|\s|\(|\)|\-){5,20}$/',
				'instruction' => __( 'Please make sure the number has a national format.', Forminator::DOMAIN ),
			),
			'international' => array(
				'label'       => __( 'International', Forminator::DOMAIN ),
				'mask'        => '(123) 456-789',
				/**
				 * allowed `+`, but only on first character
				 * allowed `{`, `)`, `_space_`, `-` and `digits`
				 * allowed 10-20 in total characters
				 */
				'regex'       => '/^(\+){0,1}(\d|\s|\(|\)|\-){10,20}$/',
				'instruction' => __( 'Please make sure the number has an international format.', Forminator::DOMAIN ),
			),
		);

		return apply_filters( 'forminator_phone_formats', $phone_formats );
	}

	/**
	 * Field front-end markup
	 *
	 * @since 1.0
	 * @param $field
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function markup( $field, $settings = array() ) {
		$this->field  = $field;
		$id           = self::get_property( 'element_id', $field );
		$name         = $id;
		$ariaid       = $id;
		$id           = $id . '-field';
		$required     = self::get_property( 'required', $field, false );
		$design       = $this->get_form_style( $settings );
		$placeholder  = $this->sanitize_value( self::get_property( 'placeholder', $field ) );
		$value        = self::get_property( 'value', $field );
		$format_check = self::get_property( 'phone_validation', $field, false );
		$phone_format = self::get_property( 'phone_validation_type', $field );
		$country	  = self::get_property( 'phone_national_country', $field, false );
		$limit        = self::get_property( 'limit', $field, 10 );

		$html = '';

		if ( 'material' === $design ) {
			$html .= '<div class="forminator-input--wrap">';
		}

		$phone_format_check = '';
		$country_data = '';
		if ( 'true' === $format_check ) {

			if ( 'character_limit' === $phone_format && 0 < $limit ) {
				$phone_format_check = sprintf( 'maxlength="%d"', $limit );

			} elseif ( 'standard' === $phone_format ) {
				$phone_format_check = 'data-national_mode="enabled"';
				if ( $country ) {
					$country_data = 'data-country="' . $country . '"';
				}

			} elseif ( 'international' === $phone_format ) {
				$phone_format_check = 'data-national_mode="disabled"';
			}
		}

		$html .= sprintf( '<input class="forminator-phone--field forminator-input" type="text" data-required="%s" name="%s" placeholder="%s" value="%s" id="%s" aria-labelledby="%s" %s %s />', $required, $name, $placeholder, $value, $id, $ariaid, $phone_format_check, $country_data );

		if ( 'material' === $design ) {
			$html .= '</div>';
		}

		return apply_filters( 'forminator_field_phone_markup', $html, $id, $required, $placeholder, $value );
	}

	/**
	 * Return field inline validation rules
	 *
	 * @since 1.0
	 * @since 1.5.1 add forminatorPhoneInternational for jQueryValidation
	 * @return string
	 */
	public function get_validation_rules() {
		$field        = $this->field;
		$format_check = self::get_property( 'phone_validation', $field, false );
		$phone_format = self::get_property( 'phone_validation_type', $field );
		$limit        = self::get_property( 'limit', $field, 10 );
		$rules        = '"' . $this->get_id( $field ) . '": {';

		if ( $this->is_required( $field ) ) {
			$rules .= '"required": true,';
			$rules .= '"trim": true,';
		}

		//standard means phoneUS
		if ( $format_check ) {
			if ( 'standard' === $phone_format ) {
				$rules .= '"forminatorPhoneNational": true,';
			} elseif ( 'character_limit' === $phone_format ) {
				$limit = isset( $field['limit'] ) ? intval( $field['limit'] ) : 10;
				$rules .= '"maxlength": ' . $limit . ',';
			} elseif ( 'international' === $phone_format ) {
				$rules .= '"forminatorPhoneInternational": true,';
			}
		}

		$rules .= '},';

		return $rules;
	}

	/**
	 * Return field inline validation errors
	 *
	 * @since 1.0
	 * @since 1.5.1 add `international` phone
	 * @return string
	 */
	public function get_validation_messages() {
		$field        = $this->field;
		$format_check = self::get_property( 'phone_validation', $field, false );
		$phone_format = self::get_property( 'phone_validation_type', $field );
		$messages     = '"' . $this->get_id( $field ) . '": {' . "\n";

		if ( $this->is_required( $field ) ) {
			$required_message = apply_filters(
				'forminator_field_phone_required_validation_message',
				__( 'This field is required. Please input a phone number', Forminator::DOMAIN ) . '",' . "\n",
				$field,
				$format_check,
				$phone_format,
				$this
			);
			$messages         .= 'required: "' . $required_message;
			$messages .= 'trim: "' . apply_filters(
				'forminator_field_phone_trim_validation_message',
				__( 'This field is required. Please input a phone number', Forminator::DOMAIN ) . '",' . "\n",
				$field,
				$format_check,
				$phone_format,
				$this
			);
		}

		if ( $format_check ) {
			if ( 'standard' === $phone_format ) {
				$messages .= 'forminatorPhoneNational: "' . apply_filters( // phpcs:ignore
					'forminator_field_phone_phoneUS_validation_message',
					__( 'Please input a valid phone number', Forminator::DOMAIN ) . '",',
					$field,
					$format_check,
					$phone_format,
					$this
				);
			} elseif ( 'character_limit' === $phone_format ) {
				$messages .= '"maxlength": "' . apply_filters(
					'forminator_field_phone_maxlength_validation_message',
					__( 'You exceeded the allowed amount of numbers. Please check again', Forminator::DOMAIN ) . '",' . "\n",
					$field,
					$format_check,
					$phone_format,
					$this
				);
			} elseif ( 'international' === $phone_format ) {
				$messages .= '"forminatorPhoneInternational": "' . apply_filters(
						'forminator_field_phone_internation_validation_message',
						__( 'Please input a valid international phone number', Forminator::DOMAIN ) . '",' . "\n",
						$field,
						$format_check,
						$phone_format,
						$this
					);
			}
		}

		$phone_message = apply_filters(
			'forminator_field_phone_invalid_validation_message',
			__( 'Please enter a valid phone number.', Forminator::DOMAIN ) . '",' . "\n",
			$field,
			$format_check,
			$phone_format,
			$this
		);
		$messages      .= '"phone": "' . $phone_message;

		$messages .= '},' . "\n";

		return $messages;

	}

	/**
	 * Field back-end validation
	 *
	 * @since 1.0
	 * @param array $field
	 * @param array|string $data
	 *
	 * @return bool
	 */
	public function validate( $field, $data ) {
		$id = self::get_property( 'element_id', $field );

		if ( $this->is_required( $field ) ) {
			if ( empty( $data ) ) {
				$this->validation_message[ $id ] = apply_filters(
					'forminator_field_phone_required_field_validation_message',
					__( 'This field is required. Please input a phone number', Forminator::DOMAIN ),
					$id,
					$field,
					$data,
					$this
				);

				return false;
			}
		}

		//if data is empty, no need to `$format_check`
		if ( empty( $data ) ) {
			return true;
		}

		//enable phone validation if `phone_validation` property enabled and data not empty, even the field is not required
		$format_check = self::get_property( 'phone_validation', $field, false );
		$phone_format = self::get_property( 'phone_validation_type', $field );
		if ( $format_check ) {
			if ( 'character_limit' === $phone_format ) {
				if ( strlen( $data ) > $field['limit'] ) {
					$this->validation_message[ $id ] = apply_filters(
						'forminator_field_phone_limit_validation_message',
						__( 'You exceeded the allowed amount of numbers. Please check again', Forminator::DOMAIN ),
						$id,
						$field,
						$data,
						$this
					);

					return false;
				}
			} else {
				$formats = $this->get_phone_formats();
				if ( isset( $formats[ $phone_format ] ) ) {
					$validation_type = $formats[ $phone_format ];
					if ( $validation_type['regex'] && ! preg_match( $validation_type['regex'], $data ) ) {
						$this->validation_message[ $id ] = sprintf(
							apply_filters(
								'forminator_field_phone_format_validation_message',
								__( 'Invalid phone number. %s', Forminator::DOMAIN )
							), $validation_type['instruction']
						);

						return false;
					}
				}
			}
		}

		if ( preg_match( '/[a-z]|[^\w-()+ ]|[-()+]{2,}/i', $data ) ) {
			$this->validation_message[ $id ] = apply_filters(
				'forminator_field_phone_invalid_validation_message',
				__( 'Please enter a valid phone number.', Forminator::DOMAIN ),
				$id,
				$field,
				$data,
				$this
			);
			return false;
		}

		return true;
	}

	/**
	 * Sanitize data
	 *
	 * @since 1.0.2
	 *
	 * @param array $field
	 * @param array|string $data - the data to be sanitized
	 *
	 * @return array|string $data - the data after sanitization
	 */
	public function sanitize( $field, $data ) {
		// Sanitize
		$data = forminator_sanitize_field( $data );

		return apply_filters( 'forminator_field_phone_sanitize', $data, $field );
	}
}
