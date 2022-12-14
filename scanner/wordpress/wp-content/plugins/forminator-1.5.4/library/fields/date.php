<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Date
 *
 * @since 1.0
 */
class Forminator_Date extends Forminator_Field {

	/**
	 * @var string
	 */
	public $name = '';

	/**
	 * @var string
	 */
	public $slug = 'date';

	/**
	 * @var int
	 */
	public $position = 12;

	/**
	 * @var string
	 */
	public $type = 'date';

	/**
	 * @var string
	 */
	public $options = array();

	/**
	 * @var string
	 */
	public $category = 'standard';

	/**
	 * Forminator_Date constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		parent::__construct();

		$this->name = __( 'Date', Forminator::DOMAIN );
	}

	/**
	 * @param array $settings
	 * @since 1.0
	 * @return array
	 */
	public function load_advanced_settings( $settings = array() ) {
		return array(
			array(
				'id'             => 'restrict',
				'type'           => 'ToggleContainer',
				'name'           => 'restrict',
				'className'      => 'restrict-field',
				'hide_label'     => true,
				'values'         => array(

					array(
						'value'      => "true",
						'label'      => __( 'Restrict date choices', Forminator::DOMAIN ),
						'labelSmall' => "true",
					),

				),
				'fields'         => array(
					array(
						'id'        => 'howto-restrict',
						'type'      => 'RadioContainer',
						'clean'		=> true,
						'name'      => 'howto-restrict',
						'className' => 'howto-restrict-field',
						'label'     => __( 'How to restrict', Forminator::DOMAIN ),
						'values'    => array(
							array(
								'value' => 'week',
								'label' => __( 'Days of week', Forminator::DOMAIN ),
							),
							array(
								'value' => 'custom',
								'label' => __( 'Custom dates', Forminator::DOMAIN ),
							),
						),
						'fields'    => array(
							array(
								'id'        => 'day-monday',
								'type'      => 'Toggle',
								'name'      => 'monday',
								'className' => 'required-field',
								'label'     => __( 'Don\'t allow user to select following days:', Forminator::DOMAIN ),
								'tab'       => 'week',
								'values'    => array(
									array(
										'value' => "true",
										'label' => __( 'Monday', Forminator::DOMAIN ),
									),
								),
							),
							array(
								'id'         => 'day-tuesday',
								'type'       => 'Toggle',
								'name'       => 'tuesday',
								'className'  => 'required-field',
								'hide_label' => true,
								'tab'        => 'week',
								'values'     => array(
									array(
										'value' => "true",
										'label' => __( 'Tuesday', Forminator::DOMAIN ),
									),
								),
							),
							array(
								'id'         => 'day-wednesday',
								'type'       => 'Toggle',
								'name'       => 'wednesday',
								'className'  => 'required-field',
								'hide_label' => true,
								'tab'        => 'week',
								'values'     => array(
									array(
										'value' => "true",
										'label' => __( 'Wednesday', Forminator::DOMAIN ),
									),
								),
							),
							array(
								'id'         => 'day-thursday',
								'type'       => 'Toggle',
								'name'       => 'thursday',
								'className'  => 'required-field',
								'hide_label' => true,
								'tab'        => 'week',
								'values'     => array(
									array(
										'value' => "true",
										'label' => __( 'Thursday', Forminator::DOMAIN ),
									),
								),
							),
							array(
								'id'         => 'day-friday',
								'type'       => 'Toggle',
								'name'       => 'friday',
								'className'  => 'required-field',
								'hide_label' => true,
								'tab'        => 'week',
								'values'     => array(
									array(
										'value' => "true",
										'label' => __( 'Friday', Forminator::DOMAIN ),
									),
								),
							),
							array(
								'id'         => 'day-saturday',
								'type'       => 'Toggle',
								'name'       => 'saturday',
								'className'  => 'required-field',
								'hide_label' => true,
								'tab'        => 'week',
								'values'     => array(
									array(
										'value' => "true",
										'label' => __( 'Saturday', Forminator::DOMAIN ),
									),
								),
							),
							array(
								'id'         => 'day-sunday',
								'type'       => 'Toggle',
								'name'       => 'sunday',
								'className'  => 'required-field',
								'hide_label' => true,
								'tab'        => 'week',
								'values'     => array(
									array(
										'value' => "true",
										'label' => __( 'Sunday', Forminator::DOMAIN ),
									),
								),
							),
							array(
								'id'         => 'date-multiple',
								'type'       => 'DateMultiple',
								'name'       => 'date_multiple',
								'className'  => 'required-field',
								'hide_label' => true,
								'tab'        => 'custom',
							),
						),
					),

				),
			),

			array(
				'id'         => 'year-range',
				'type'       => 'ToggleContainer',
				'name'       => 'year_range',
				'size'       => 12,
				'className'  => 'toggle-container',
				'hide_label' => true,
				'values'     => array(
					array(
						'value'      => "true",
						'label'      => __( 'Restrict Year Range', Forminator::DOMAIN ),
						'labelSmall' => "true",
					),
				),
				'fields'     => array(
					array(
						'id'        => 'min-year',
						'type'      => 'Text',
						'name'      => 'min_year',
						'className' => 'text-field',
						'label'     => __( 'Minimum Year', Forminator::DOMAIN ),
					),
					array(
						'id'        => 'max-year',
						'type'      => 'Text',
						'name'      => 'max_year',
						'className' => 'text-field',
						'label'     => __( 'Maximum year', Forminator::DOMAIN ),
					),
				),
			),
		);
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
				'className'  => 'required-field',
				'hide_label' => true,
				'values'     => array(
					array(
						'value' => "true",
						'label' => __( 'Required', Forminator::DOMAIN ),
					),
				),
			),

			array(
				'id'         => 'separator-1',
				'type'       => 'Separator',
				'name'       => 'separator',
				'hide_label' => true,
				'className'  => 'separator-field',
			),

			array(
				'id'         => 'field-label',
				'type'       => 'Text',
				'name'       => 'field_label',
				'hide_label' => false,
				'label'      => __( 'Field Label', Forminator::DOMAIN ),
				'className'  => 'text-field',
			),

			array(
				'id'         => 'field-placeholder',
				'type'       => 'Text',
				'name'       => 'placeholder',
				'hide_label' => false,
				'label'      => __( 'Placeholder', Forminator::DOMAIN ),
				'className'  => 'text-field',
			),

			array(
				'id'         => 'separator-2',
				'type'       => 'Separator',
				'name'       => 'separator',
				'hide_label' => true,
				'className'  => 'separator-field',
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
				'id'         => 'separator-3',
				'type'       => 'Separator',
				'name'       => 'separator',
				'hide_label' => true,
				'className'  => 'separator-field',
			),

			array(
				'id'        => 'date-format',
				'type'      => 'Select',
				'name'      => 'date_format',
				'className' => 'date-format-field',
				'label'     => __( 'Date format', Forminator::DOMAIN ),
				'values'    => array(
					array(
						'value' => "yy-mm-dd",
						'label' => __( 'Y-m-d', Forminator::DOMAIN ),
					),
					array(
						'value' => "mm/dd/yy",
						'label' => __( 'm/d/Y', Forminator::DOMAIN ),
					),
					array(
						'value' => "dd/mm/yy",
						'label' => __( 'd/m/Y', Forminator::DOMAIN ),
					),
				),
			),

			array(
				'id'        => 'type',
				'type'      => 'RadioContainer',
				'name'      => 'field_type',
				'label'     => __( "Field type", Forminator::DOMAIN ),
				'className' => 'type-field',
				'values'    => array(
					array(
						'value' => "picker",
						'label' => __( 'Date picker', Forminator::DOMAIN ),
					),
					array(
						'value' => "select",
						'label' => __( 'Drop downs', Forminator::DOMAIN ),
					),
					array(
						'value' => "input",
						'label' => __( 'Text inputs', Forminator::DOMAIN ),
					),
				),
				'fields'    => array(
					array(
						'id'            => 'icon',
						'type'          => 'Toggle',
						'name'          => 'icon',
						'className'     => 'icon-field',
						'tab'           => 'picker',
						'hide_label'    => true,
						'default_value' => "true",
						'values'        => array(
							array(
								'value' => "true",
								'label' => __( 'Use calendar icon', Forminator::DOMAIN ),
							),
						),
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
			'field_type'  => 'picker',
			'date_format' => 'mm/dd/yy',
			'field_label' => __( 'Date', Forminator::DOMAIN ),
			'placeholder' => __( 'Choose Date', Forminator::DOMAIN ),
			'icon'		  => "true"
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

		// TODO: support for multiple field date
		$autofill_settings = array(
			'date' => array(
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
		{[ if( field.field_type === "picker" ) { ]}
			{[ if( field.icon ) { ]}
				<div class="sui-date">
			{[ } ]}
			<input type="text" class="sui-form-control wpmudev-option--datepicker" placeholder="{{ encodeHtmlEntity( field.placeholder ) }}" {{ field.required ? "required" : "" }}>
			{[ if( field.icon ) { ]}
				<i class="sui-icon-calendar" aria-hidden="true"></i>
			{[ } ]}
			{[ if( field.icon ) { ]}
				</div>
			{[ } ]}
		{[ } ]}
		{[ if( field.field_type === "select" ) { ]}
			<div class="sui-row">
				{[ if( field.date_format === "mm/dd/yy" ) { ]}
				<div class="sui-col-md-4">
					<select>
						<option>Month</option>
					</select>
				</div>
				<div class="sui-col-md-4">
					<select>
						<option>Day</option>
					</select>
				</div>
				<div class="sui-col-md-4">
					<select>
						<option>Year</option>
					</select>
				</div>
				{[ } ]}
				{[ if( field.date_format === "dd/mm/yy" ) { ]}
				<div class="sui-col-md-4">
					<select>
						<option>Day</option>
					</select>
				</div>
				<div class="sui-col-md-4">
					<select>
						<option>Month</option>
					</select>
				</div>
				<div class="sui-col-md-4">
					<select>
						<option>Year</option>
					</select>
				</div>
				{[ } ]}
				{[ if( field.date_format === "yy-mm-dd" ) { ]}
				<div class="sui-col-md-4">
					<select>
						<option>Year</option>
					</select>
				</div>
				<div class="sui-col-md-4">
					<select>
						<option>Month</option>
					</select>
				</div>
				<div class="sui-col-md-4">
					<select>
						<option>Day</option>
					</select>
				</div>
				{[ } ]}
			</div>
		{[ } ]}
		{[ if( field.field_type === "input" ) { ]}
			<div class="sui-row">
				{[ if( field.date_format === "mm/dd/yy" ) { ]}
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-month" placeholder="' . current_time('n'). '">
					</div>
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-day" placeholder="' . current_time('j'). '">
					</div>
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-year" placeholder="' . current_time('Y'). '">
					</div>
				{[ } ]}
				{[ if( field.date_format === "dd/mm/yy" ) { ]}
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-day" placeholder="' . current_time('n'). '">
					</div>
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-month" placeholder="' . current_time('n'). '">
					</div>
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-year" placeholder="' . current_time('Y'). '">
					</div>
				{[ } ]}
				{[ if( field.date_format === "yy-mm-dd" ) { ]}
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-year" placeholder="' . current_time('Y'). '">
					</div>
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-month" placeholder="' . current_time('n'). '">
					</div>
					<div class="sui-col-md-4">
						<input type="text" class="sui-form-control wpmudev-form-field--date-day" placeholder="' . current_time('n'). '">
					</div>
				{[ } ]}
			</div>
		{[ } ]}
		{[ if( field.description ) { ]}
		<label class="sui-description">{{ encodeHtmlEntity( field.description ) }}</label>
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
		$this->field = $field;
		$html        = '';
		$icon        = '';
		$design      = $this->get_form_style( $settings );
		$id          = self::get_property( 'element_id', $field );
		$name        = $id;
		$required    = self::get_property( 'required', $field, false );
		$placeholder = $this->sanitize_value( self::get_property( 'placeholder', $field ) );
		$type        = trim(self::get_property( 'field_type', $field ));
		$has_icon    = self::get_property( 'icon', $field );
		$date_format = self::get_property( 'date_format', $field );
		$year_range  = self::get_property( 'year_range', $field, false );
		$min_year    = '';
		$max_year    = '';

		$sep     = false !== strpos( $date_format, '/' ) ? '/' : '-';
		$formats = explode( $sep, $date_format );

		if ( $year_range ) {
			$min_year = self::get_property( 'min_year', $field );
			$max_year = self::get_property( 'max_year', $field );
		}

		if ( $has_icon ) {
			$icon = 'forminator-has_icon';
		}

		// If field type == picker
		if ( "picker" === $type ) {
			$restrict      = array();
			$restrict_type = self::get_property( 'howto-restrict', $field );
			$post_value    = self::get_post_data( $name, false );

			if ( "week" === $restrict_type ) {
				$days = forminator_week_days();
				$i    = 0;
				foreach ( $days as $k => $day ) {
					if ( self::get_property( $k, $field ) ) {
						$restrict[] = $i;
					}
					$i ++;
				}
			} else {
				$dates = self::get_property( 'date_multiple', $field );

				if ( ! empty( $dates ) ) {
					foreach ( $dates as $k => $date ) {
						$restrict[] = $date['value'];
					}
				}
			}

			$html .= sprintf( '<div class="forminator-date %s">', $icon );

			if ( 'material' === $design ) {
				$html .= '<div class="forminator-input--wrap">';
			}

			$html .= sprintf(
				'<input type="text" class="forminator-input forminator-datepicker" data-required="%s" name="%s" size="1" placeholder="%s" aria-labelledby="%s" data-format="%s" data-restrict-type="%s" data-restrict="%s" data-start-year="%s" data-end-year="%s" %s>',
				$required,
				$name,
				$placeholder,
				'forminator-label-' . $name,
				$date_format,
				$restrict_type,
				implode( ",", $restrict ),
				$min_year,
				$max_year,
				( $post_value ? 'value= "' . $post_value . '"' : '' )
			);

			if ( 'material' === $design ) {
				$html .= '</div>';
			}

			$html .= '</div>';
		} elseif ( "select" === $type ) {
			// Start row
			$html .= '<div class="forminator-row forminator-row--inner">';

			foreach ( $formats as $format ) {
				switch ( $format ) {
					case 'dd':
						// Start field
						$html .= '<div class="forminator-col forminator-col-4">';
						$html .= '<div class="forminator-field forminator-field--inner">';

						$day_data = array(
							'class' => 'forminator-select',
							'name'  => $id . '-day',
							'id'    => $id . '-day',
						);

						$html .= self::create_select( $day_data, __( "Day", Forminator::DOMAIN ), $this->get_day() );

						// End field
						$html .= '</div>';
						$html .= '</div>';
						break;

					case 'mm':
						// Start field
						$html .= '<div class="forminator-col forminator-col-4">';
						$html .= '<div class="forminator-field forminator-field--inner">';

						$month_data = array(
							'class' => 'forminator-select',
							'name'  => $id . '-month',
							'id'    => $id . '-month',
						);

						$html .= self::create_select( $month_data, __( "Month", Forminator::DOMAIN ), $this->get_months() );

						// End field
						$html .= '</div>';
						$html .= '</div>';
						break;

					case 'yy':
						// Start field
						$html .= '<div class="forminator-col forminator-col-4">';
						$html .= '<div class="forminator-field forminator-field--inner">';

						$year_data = array(
							'class' => 'forminator-select',
							'name'  => $id . '-year',
							'id'    => $id . '-year',
						);

						$html .= self::create_select( $year_data, __( "Year", Forminator::DOMAIN ), $this->get_years( $min_year, $max_year ) );

						// End field
						$html .= '</div>';
						$html .= '</div>';
						break;
				}
			}

			// End row
			$html .= '</div>';

		} elseif ( "input" === $type ) {
			// Start row
			$html .= '<div class="forminator-row forminator-row--inner">';

			foreach ( $formats as $format ) {
				switch ( $format ) {
					case 'dd':
						// Start field
						$html .= '<div class="forminator-col forminator-col-4">';
						$html .= '<div class="forminator-field forminator-field--inner">';

						$day_data = array(
							'class'           => 'forminator-input',
							'name'            => $id . '-day',
							'id'              => $id . '-day',
							'aria-labelledby' => 'forminator-label-' . $id . '-day',
						);

						$description = '';
						$html        .= self::create_input( $day_data, __( "Day", Forminator::DOMAIN ), $description, $required, $design );

						// End field
						$html .= '</div>';
						$html .= '</div>';
						break;

					case 'mm':
						// Start field
						$html .= '<div class="forminator-col forminator-col-4">';
						$html .= '<div class="forminator-field forminator-field--inner">';

						$month_data = array(
							'class'           => 'forminator-input',
							'name'            => $id . '-month',
							'id'              => $id . '-month',
							'aria-labelledby' => 'forminator-label-' . $id . '-month',
						);

						$description = '';
						$html        .= self::create_input( $month_data, __( "Month", Forminator::DOMAIN ), $description, $required, $design );

						// End field
						$html .= '</div>';
						$html .= '</div>';
						break;

					case 'yy':
						// Start field
						$html .= '<div class="forminator-col forminator-col-4">';
						$html .= '<div class="forminator-field forminator-field--inner">';

						$year_data   = array(
							'class'           => 'forminator-input',
							'name'            => $id . '-year',
							'id'              => $id . '-year',
							'aria-labelledby' => 'forminator-label-' . $id . '-year',
						);
						$description = '';
//						if ( ! empty( $min_year ) && ! empty( $max_year ) ) {
//							//Not sure if we add this
//							//$description = sprintf( __( 'Between %s and %s ', Forminator::DOMAIN ), $min_year, $max_year );
//						}

						$html .= self::create_input( $year_data, __( "Year", Forminator::DOMAIN ), $description, $required, $design );

						// End field
						$html .= '</div>';
						$html .= '</div>';
						break;
				}
			}

			// End row
			$html .= '</div>';
		}

		return apply_filters( 'forminator_field_date_markup', $html, $field, $this );
	}

	/**
	 * Return all years between two dates
	 *
	 * @since 1.0
	 * @param string $min_year
	 * @param string $max_year
	 *
	 * @return array
	 */
	public function get_years( $min_year = '', $max_year = '' ) {
		$array 	= array();
		$year  	= intval( date( 'Y' ) );
		$end 	= empty( $min_year ) ? $year - 100 : intval( $min_year ) - 1;
		$start  = empty( $max_year ) ? $year + 1 : intval( $max_year );
		for ( $i = $start; $i > $end; $i -- ) {
			$array[] = array(
				'label' => $i,
				'value' => $i
			);
		}

		return apply_filters( 'forminator_field_date_get_years', $array, $min_year, $max_year, $year, $start, $end, $this );
	}

	/**
	 * Return monts
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_months() {
		$array = array();
		for ( $i = 1; $i < 13; $i ++ ) {
			$array[] = array(
				'label' => $i,
				'value' => $i
			);
		}

		return apply_filters( 'forminator_field_date_get_months', $array, $this );
	}

	/**
	 * Return days
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_day() {
		$array = array();
		for ( $i = 1; $i < 32; $i ++ ) {
			$array[] = array(
				'label' => $i,
				'value' => $i
			);
		}

		return apply_filters( 'forminator_field_date_get_day', $array, $this );
	}

	/**
	 * Parse date
	 *
	 * @since 1.0
	 * @param string|array $date - the date to be parsed
	 * @param string $format - the data format
	 *
	 * @return array
	 */
	public static function parse_date( $date, $format = 'yy-mm-dd' ) {
		$date_info = array(
			'year' => 0,
			'month' => 0,
			'day' => 0
		);

		$position = substr( $format, 0, 8 );

		if ( is_array( $date ) ) {

			switch ( $position ) {
				case 'mm/dd/yy' :
					$date_info['month'] = isset( $date['month'] ) ? $date['month'] : 0;
					$date_info['day']   = isset( $date['day'] ) ? $date['day'] : 0;
					$date_info['year']  = isset( $date['year'] ) ? $date['year'] : 0;
					break;

				case 'dd/mm/yy' :
					$date_info['day']   = isset( $date['day'] ) ? $date['day'] : 0;
					$date_info['month'] = isset( $date['month'] ) ? $date['month'] : 0;
					$date_info['year']  = isset( $date['year'] ) ? $date['year'] : 0;
					break;

				case 'yy-mm-dd' :
					$date_info['year']  = isset( $date['year'] ) ? $date['year'] : 0;
					$date_info['month'] = isset( $date['month'] ) ? $date['month'] : 0;
					$date_info['day']   = isset( $date['day'] ) ? $date['day'] : 0;
					break;
			}
			return apply_filters( 'forminator_field_date_parse_dates', $date_info, $date, $format );
		}

		$date = preg_replace( "|[/\.]|", '-', $date );
		if ( 'mm/dd/yy'  === $position ) {
			if ( preg_match( '/^(\d{1,2})-(\d{1,2})-(\d{1,4})$/', $date, $matches ) ) {
				$date_info['month'] = $matches[1];
				$date_info['day']   = $matches[2];
				$date_info['year']  = $matches[3];
			}
		} elseif ( 'dd/mm/yy' === $position ) {
			if ( preg_match( '/^(\d{1,2})-(\d{1,2})-(\d{1,4})$/', $date, $matches ) ) {
				$date_info['day']   = $matches[1];
				$date_info['month'] = $matches[2];
				$date_info['year']  = $matches[3];
			}
		} elseif ( 'yy-mm-dd' === $position ) {
			if ( preg_match( '/^(\d{1,4})-(\d{1,2})-(\d{1,2})$/', $date, $matches ) ) {
				$date_info['year']  = $matches[1];
				$date_info['month'] = $matches[2];
				$date_info['day']   = $matches[3];
			}
		}

		return apply_filters( 'forminator_field_date_parse_dates', $date_info, $date, $format );
	}

	/**
	 * Check data
	 *
	 * @since 1.0
	 * @param int $month - the month
	 * @param int $day - the day
	 * @param int $year - the year
	 *
	 * @return bool
	 */
	public function check_date( $month, $day, $year ) {
		if ( empty( $month ) || ! is_numeric( $month ) || empty( $day ) || ! is_numeric( $day )
		     || empty( $year ) || ! is_numeric( $year ) || 4 !== strlen( $year ) ) {
			return false;
		}

		return checkdate( $month, $day, $year );
	}

	/**
	 * Return field inline validation rules
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_validation_rules() {
		$field       = $this->field;
		$type        = trim( self::get_property( 'field_type', $field ) );
		$date_format = self::get_property( 'date_format', $field );
		$rules       = '';

		if( "picker" === $type ) {
			$rules .= '"' . $this->get_id( $field ) . '": {' . "\n";
			if ( $this->is_required( $field ) ) {
				$rules .= '"required": true,';
			}

			$rules .= '"dateformat": "' . $date_format . '",';
			$rules .= '},' . "\n";
		} else {
			if ( $this->is_required( $field ) ) {
				$rules .= '"' . $this->get_id( $field ) . '-day": "required",';
				$rules .= '"' . $this->get_id( $field ) . '-month": "required",';
				$rules .= '"' . $this->get_id( $field ) . '-year": "required",';
			}
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
		$field       = $this->field;
		$type        = trim( self::get_property( 'field_type', $field ) );
		$date_format = self::get_property( 'date_format', $field );

		if( "picker" === $type ) {
			$messages = '"' . $this->get_id( $field ) . '": {' . "\n";
			if ( $this->is_required( $field ) ) {
				$required_validation_message = apply_filters(
					'forminator_field_date_required_validation_message',
					__( 'This field is required. Please enter a valid date', Forminator::DOMAIN ),
					$field,
					$type,
					$date_format,
					$this
				);
				$messages .= 'required: "' . $required_validation_message . '",' . "\n";
			}

			$format_validation_message = apply_filters(
				'forminator_field_date_format_validation_message',
				__( 'Not valid date', Forminator::DOMAIN ),
				$field,
				$type,
				$date_format,
				$this
			);

			$messages .= 'dateformat: "' . $format_validation_message . '",' . "\n";
			$messages .= '},' . "\n";
		} else {
			$day_validation_message = apply_filters(
				'forminator_field_date_day_validation_message',
				__( 'This field is required. Please input a value', Forminator::DOMAIN ),
				$field,
				$type,
				$date_format,
				$this
			);
			$messages = '"' . $this->get_id( $field ) . '-day": "' . $day_validation_message . '",' . "\n";

			$month_validation_message = apply_filters(
				'forminator_field_date_month_validation_message',
				__( 'This field is required. Please input a value', Forminator::DOMAIN ),
				$field,
				$type,
				$date_format,
				$this
			);
			$messages .= '"' . $this->get_id( $field ) . '-month": "' . $month_validation_message . '",' . "\n";

			$year_validation_message = apply_filters(
				'forminator_field_date_year_validation_message',
				__( 'This field is required. Please input a value', Forminator::DOMAIN ),
				$field,
				$type,
				$date_format,
				$this
			);
			$messages .= '"' . $this->get_id( $field ) . '-year": "' . $year_validation_message . '",' . "\n";
		}

		return apply_filters( 'forminator_field_date_validation_message', $messages, $field, $type, $date_format, $this );
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
			$id 			= self::get_property( 'element_id', $field );
			$date_format  	= self::get_property( 'date_format', $field );
			if ( empty( $data ) ) {
				$this->validation_message[ $id ] = apply_filters(
					'forminator_field_date_required_field_validation_message',
					__( 'This field is required. Please enter the date', Forminator::DOMAIN ),
					$id,
					$data,
					$date_format,
					$this
				);
			} else {
				$date = self::parse_date( $data, $date_format );
				if ( empty( $date ) || ! $this->check_date( $date['month'], $date['day'], $date['year'] ) ) {
					$this->validation_message[ $id ] = apply_filters(
						'forminator_field_date_valid_date_validation_message',
						__( 'Please enter a valid date', Forminator::DOMAIN ),
						$id,
						$data,
						$date_format,
						$this
					);
				} else {
					$year_range 	= self::get_property( 'year_range', $field, false );
					if ( $year_range ) {
						$min_year 	= self::get_property( 'min_year', $field );
						$max_year 	= self::get_property( 'max_year', $field );
						$year 		= intval( $date['year'] );
						if ( !empty( $min_year ) && !empty( $max_year ) ) {
							if ( $year < $min_year || $year > $max_year ) {
								$this->validation_message[ $id ] = apply_filters(
									'forminator_field_date_valid_maxmin_year_validation_message',
									__( 'Please enter a valid year', Forminator::DOMAIN )
								);
							}
						} else {
							if ( !empty( $min_year ) ) {
								if ( $year < $min_year ) {
									$this->validation_message[ $id ] = apply_filters(
										'forminator_field_date_valid_maxmin_year_validation_message',
										__( 'Please enter a valid year', Forminator::DOMAIN )
									);
								}
							}
							if ( !empty( $max_year ) ) {
								if ( $year > $max_year ) {
									$this->validation_message[ $id ] = apply_filters(
										'forminator_field_date_valid_maxmin_year_validation_message',
										__( 'Please enter a valid year', Forminator::DOMAIN )
									);
								}
							}
						}
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
		// Sanitize
		$data = forminator_sanitize_field( $data );

		return apply_filters( 'forminator_field_date_sanitize', $data, $field );
	}
}
