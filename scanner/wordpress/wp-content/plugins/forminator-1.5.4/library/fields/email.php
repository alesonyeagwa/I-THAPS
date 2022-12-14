<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Email
 *
 * @since 1.0
 */
class Forminator_Email extends Forminator_Field {

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $slug = 'email';

	/**
	 * @var int
	 */
	public $position = 2;

	 /**
     * @var string
     */
    public $type = 'email';

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
	 * Forminator_Email constructor.
	 *
	 * @since 1.0
	 */
    public function __construct() {
        parent::__construct();
        $this->name = __( 'Email', Forminator::DOMAIN );
    }

    /**
     * @since 1.0
	 * @param array $settings
	 *
	 * @return array
	 */
	public function load_settings( $settings = array() ) {
		return array(
			array(
				'id'         => 'required',
				'type'       => 'Toggle',
				'name'       => 'required',
				'size'       => 12,
				'className'  => 'required-field',
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
				'size'       => 12,
				'className'  => 'text-field',
			),

			array(
				'id'         => 'description',
				'type'       => 'Text',
				'name'       => 'description',
				'hide_label' => false,
				'label'      => __( 'Description', Forminator::DOMAIN ),
				'size'       => 12,
				'className'  => 'text-field',
			),

			array(
				'id'          => 'validation',
				'type'        => 'ToggleContainer',
				'name'        => 'validation',
				'hide_label'  => true,
				'has_content' => true,
				'values'      => array(
					array(
						'value'      => "true",
						'label'      => __( 'Enable Validation', Forminator::DOMAIN ),
						'labelSmall' => "true",
					),
				),
				'fields'      => array(
					array(
						'id'        => 'validation-text',
						'type'      => 'Text',
						'name'      => 'validation_text',
						'size'      => 12,
						'className' => 'text-field',
						'label'     => __( 'Custom validation error message', Forminator::DOMAIN ),
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
			'validation'  => false,
			'placeholder' => __( 'E.g. john@doe.com', Forminator::DOMAIN ),
			'field_label' => __( 'Email Address', Forminator::DOMAIN )
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
		$providers = apply_filters( 'forminator_field_' . $this->slug . '_autofill', array(), $this->slug );

		$autofill_settings = array(
			'email' => array(
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
		{[ if( ( field.validation == "true" ) && field.validation_text ) { ]}
			<span class="sui-error-message">{{ encodeHtmlEntity( field.validation_text ) }}</span>
		{[ } ]}
		{[ if( field.description ) { ]}
			<span class="sui-description">{{ encodeHtmlEntity( field.description ) }}</span>
		{[ } ]}';
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
		$this->field         = $field;
		$this->form_settings = $settings;

		$this->init_autofill($settings);

		$id          = self::get_property( 'element_id', $field );
		$name        = $id;
		$design      = $this->get_form_style( $settings );
		$ariaid      = $id;
		$id          = $id . '-field';
		$required    = self::get_property( 'required', $field, false );
		$placeholder = $this->sanitize_value( self::get_property( 'placeholder', $field ) );
		$value       = self::get_property( 'value', $field );

		$html = '';

		if ( 'material' === $design ) {
			$html .= '<div class="forminator-input--wrap">';
		}

		$email_attr = array(
			'id'              => $id,
			'name'            => $name,
			'placeholder'     => $placeholder,
			'data-required'   => $required,
			'class'           => 'forminator-email--field forminator-input',
			'type'            => 'email',
			'aria-labelledby' => 'forminator-label-' . $ariaid,
		);

		$autofill_markup = $this->get_element_autofill_markup_attr( self::get_property( 'element_id', $field ), $this->form_settings );

		$email_attr = array_merge( $email_attr, $autofill_markup );

		$html .= self::create_input($email_attr);

		if ( 'material' === $design ) {
			$html .= '</div>';
		}

		return apply_filters( 'forminator_field_email_markup', $html, $id, $required, $placeholder, $value );
	}

	/**
	 * Return field inline validation rules
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_validation_rules() {
		$field = $this->field;
		$rules = '"' . $this->get_id( $field )  .'": {' . "\n";
		if( $this->is_required( $field ) ) {
			$rules .= '"required": true,';
		}
		$rules .= '"emailWP": true,';
		$rules .= '},' . "\n";

		return $rules;
	}

	/**
	 * Return field inline validation errors
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_validation_messages() {
		$field        = $this->field;
		$id           = $this->get_id( $field );
		$custom_error = self::get_property( 'validation', $field );
		$error        = self::get_property( 'validation_text', $field );
		$error        = htmlentities( $error );

		$messages = '"' . $id  .'": {' . "\n";

		if( $this->is_required( $field ) ) {
			$default_required_error_message = apply_filters(
				'forminator_email_field_required_validation_message',
				__( 'This field is required. Please input a valid email', Forminator::DOMAIN ),
				$field,
				$id
			);
			$messages .= 'required: "' . $default_required_error_message . '",' . "\n";
		}

		$error_message = __( 'This is not a valid email', Forminator::DOMAIN );

		if( isset( $custom_error ) && $custom_error ) {
			if ( $error ) {
				$error_message = $error;
			}
		}

		$error_message = apply_filters(
			'forminator_email_field_custom_validation_message',
			$error_message,
			$id,
			$field,
			$custom_error
		);

		$messages .= 'emailWP: "' . $error_message . '",' . "\n";
		$messages .= 'email: "' . $error_message . '",' . "\n";
		$messages .= '},' . "\n";

		$messages = apply_filters(
			'forminator_email_field_validation_message',
			$messages,
			$id,
			$field,
			$custom_error
		);

		return $messages;
	}

	/**
	 * Field back-end validation
	 *
	 * @since 1.0
	 * @param array $field
	 * @param array|string $data
	 */
	public function validate( $field, $data ) {
		if ( $this->is_required( $field ) ) {
			$id           = self::get_property( 'element_id', $field );
			$custom_error = self::get_property( 'validation', $field );
			$error        = self::get_property( 'validation_text', $field );
			$error        = htmlentities( $error );

			if ( empty( $data ) ) {
				if( isset( $custom_error ) && $custom_error && $error ) {
					$this->validation_message[ $id ] = $error;
				} else {
					$this->validation_message[ $id ] = __( 'This field is required. Please enter the email', Forminator::DOMAIN );
				}
			} else {
				if ( !is_email( $data ) ) {
					if( isset( $custom_error ) && $custom_error && $error ) {
						$this->validation_message[ $id ] = $error;
					} else {
						$this->validation_message[ $id ] = __( 'Please enter a valid email address', Forminator::DOMAIN );
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
	 * @param array $field
	 * @param array|string $data - the data to be sanitized
	 *
	 * @return array|string $data - the data after sanitization
	 */
	public function sanitize( $field, $data ) {
		// Sanitize email
		$data = sanitize_email( $data );

		return apply_filters( 'forminator_field_email_sanitize', $data, $field );
	}
}
