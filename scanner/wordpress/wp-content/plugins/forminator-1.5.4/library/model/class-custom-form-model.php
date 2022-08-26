<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Author: Hoang Ngo
 */
class Forminator_Custom_Form_Model extends Forminator_Base_Form_Model {

	protected $post_type = 'forminator_forms';

	/**
	 * @param int|string $class_name
	 *
	 * @since 1.0
	 * @return Forminator_Custom_Form_Model
	 */
	public static function model( $class_name = __CLASS__ ) { // phpcs:ignore
		return parent::model( $class_name );
	}

	/**
	 * Get field
	 *
	 * @since 1.0
	 *
	 * @param      $id
	 * @param bool $to_array
	 *
	 * @return array|null|Forminator_Form_Field_Model
	 */
	public function get_field( $id, $to_array = true ) {
		foreach ( $this->get_fields() as $field ) {
			if ( $field->slug === $id ) {
				if ( $to_array ) {
					return $field->to_array();
				} else {
					return $field;
				}
			}
		}

		return null;
	}

	/**
	 * Return fields as array
	 *
	 * @since 1.5
	 *
	 * @param string
	 *
	 * @return array
	 */
	public function get_fields_by_type( $type ) {
		$fields = array();

		if ( empty( $this->fields ) ) {
			return $fields;
		}

		foreach ( $this->fields as $field ) {
			$field_settings = $field->to_array();

			if ( isset( $field_settings['type'] ) && $field_settings['type'] === $type ) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Get wrapper
	 *
	 * @since 1.5
	 *
	 * @param $id
	 *
	 * @return array|null
	 */
	public function get_wrapper( $id ) {
		$position = 0;
		foreach ( $this->get_fields_grouped() as $wrapper ) {
			if ( $wrapper['wrapper_id'] === $id ) {
				$wrapper['position'] = $position;

				return $wrapper;
			}

			$position ++;
		}

		return null;
	}

	/**
	 * Delete form field by ID
	 *
	 * @since 1.5
	 *
	 * @param $id
	 *
	 * @return string|false
	 */
	public function delete_field( $id ) {
		$counter = 0;
		foreach ( $this->fields as $field ) {
			if ( $field->slug === $id ) {
				unset( $this->fields[ $counter ] );

				return $field->form_id;
			}

			$counter ++;
		}

		return false;
	}

	/**
	 * Update fields cols in specific wrapper
	 *
	 * @since 1.5
	 *
	 * @param $wrapper_id
	 *
	 * @return bool
	 */
	public function update_fields_by_wrapper( $wrapper_id ) {
		// Get wrapper
		$wrapper = $this->get_wrapper( $wrapper_id );

		// Check if any fields in the wrapper
		if ( ! isset( $wrapper['fields'] ) ) {
			return false;
		}

		// Get total fields in the wrapper
		$total = count( $wrapper['fields'] );

		if ( $total > 0 ) {
			$cols = 12 / $total;

			// Update fields
			foreach ( $wrapper['fields'] as $field ) {
				$field_object = $this->get_field( $field['element_id'], false );

				// Update field object
				$field_object->import(
					array(
						'cols' => $cols,
					) );
			}

			return true;
		}

		return false;
	}

	/**
	 * Load preview
	 *
	 * @since 1.0
	 *
	 * @param $id
	 * @param $data
	 *
	 * @return bool|Forminator_Custom_Form_Model
	 */
	public function load_preview( $id, $data ) {
		$form_model = $this->load( $id, true );

		// If bool, abort
		if ( is_bool( $form_model ) ) {
			return false;
		}

		$form_model->clear_fields();
		$form_model->set_var_in_array( 'name', 'formName', $data );

		//build the field
		$fields = array();
		if ( isset( $data['wrappers'] ) ) {
			$fields = $data['wrappers'];
			unset( $data['wrappers'] );
		}

		//build the settings
		if ( isset( $data['settings'] ) ) {
			$settings             = $data['settings'];
			$form_model->settings = $settings;
		}

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $row ) {
				foreach ( $row['fields'] as $f ) {
					$field          = new Forminator_Form_Field_Model();
					$field->form_id = $row['wrapper_id'];
					$field->slug    = $f['element_id'];
					$field->import( $f );
					$form_model->add_field( $field );
				}
			}
		}

		return $form_model;
	}

	/**
	 * Check if can show the form
	 *
	 * @since 1.0
	 * @return bool
	 */
	public function form_is_visible() {
		$form_settings = $this->settings;
		$can_show      = true;

		if ( isset( $form_settings['logged-users'] ) && ! empty( $form_settings['logged-users'] ) ) {
			if ( filter_var( $form_settings['logged-users'], FILTER_VALIDATE_BOOLEAN ) && ! is_user_logged_in() ) {
				$can_show = false;
			}
		}
		if ( $can_show ) {
			if ( isset( $form_settings['form-expire'] ) ) {
				if ( 'submits' === $form_settings['form-expire'] ) {
					if ( isset( $form_settings['expire_submits'] ) && ! empty( $form_settings['expire_submits'] ) ) {
						$submits       = intval( $form_settings['expire_submits'] );
						$total_entries = Forminator_Form_Entry_Model::count_entries( $this->id );
						if ( $total_entries >= $submits ) {
							$can_show = false;
						}
					}
				} elseif ( 'date' === $form_settings['form-expire'] ) {
					if ( isset( $form_settings['expire_date'] ) && ! empty( $form_settings['expire_date'] ) ) {
						$expire_date  = strtotime( $form_settings['expire_date'] );
						$current_date = strtotime( "now" );
						if ( $current_date > $expire_date ) {
							$can_show = false;
						}
					}
				}
			}
		}

		return apply_filters( 'forminator_cform_form_is_visible', $can_show, $this->id, $form_settings );
	}

	/**
	 * Export model
	 *
	 * Add filter to include `integrations`
	 *
	 * @since 1.4
	 * @return array
	 */
	public function to_exportable_data() {

		if ( ! Forminator::is_import_export_feature_enabled() ) {
			return array();
		}

		if ( Forminator::is_export_integrations_feature_enabled() ) {
			add_filter( 'forminator_form_model_to_exportable_data', array( $this, 'export_integrations_data' ), 1, 1 );
		}

		$exportable_data = parent::to_exportable_data();

		// avoid filter executed on next cycle
		remove_filter( 'forminator_form_model_to_exportable_data', array( $this, 'export_integrations_data' ), 1 );

		return $exportable_data;
	}

	/**
	 * Export integrations Form setting
	 *
	 * @since 1.4
	 *
	 * @param $exportable_data
	 *
	 * @return array
	 */
	public function export_integrations_data( $exportable_data ) {
		$model_id                = $this->id;
		$exportable_integrations = array();

		$connected_addons = forminator_get_addons_instance_connected_with_form( $model_id );

		foreach ( $connected_addons as $connected_addon ) {
			try {
				$form_settings = $connected_addon->get_addon_form_settings( $model_id );
				if ( $form_settings instanceof Forminator_Addon_Form_Settings_Abstract ) {
					$exportable_integrations[ $connected_addon->get_slug() ] = $form_settings->to_exportable_data();
				}
			} catch ( Exception $e ) {
				forminator_addon_maybe_log( $connected_addon->get_slug(), 'failed to get to_exportable_data', $e->getMessage() );
			}

		}

		/**
		 * Filter integrations data to export
		 *
		 * @since 1.4
		 *
		 * @param array $exportable_integrations
		 * @param array $exportable_data all exportable data from model, useful
		 */
		$exportable_integrations         = apply_filters( 'forminator_form_model_export_integrations_data', $exportable_integrations, $model_id );
		$exportable_data['integrations'] = $exportable_integrations;

		return $exportable_data;
	}

	/**
	 * Import Model
	 *
	 * add filter `forminator_import_model`
	 *
	 * @since 1.4
	 *
	 * @param        $import_data
	 * @param string $module
	 *
	 * @return Forminator_Base_Form_Model|WP_Error
	 */
	public static function create_from_import_data( $import_data, $module = __CLASS__ ) {
		if ( Forminator::is_import_integrations_feature_enabled() ) {
			add_filter( 'forminator_import_model', array( 'Forminator_Custom_Form_Model', 'import_integrations_data' ), 1, 3 );
		}

		$model = parent::create_from_import_data( $import_data, $module );

		// avoid filter executed on next cycle
		remove_filter( 'forminator_import_model', array( 'Forminator_Custom_Form_Model', 'import_integrations_data' ), 1 );

		return $model;
	}

	/**
	 * Import Integrations data model
	 *
	 * @since 1.4
	 *
	 * @param $model
	 * @param $import_data
	 * @param $module
	 *
	 * @return Forminator_Custom_Form_Model
	 */
	public static function import_integrations_data( $model, $import_data, $module ) {
		// return what it is
		if ( is_wp_error( $model ) ) {
			return $model;
		}

		// make sure its custom form
		if ( __CLASS__ !== $module ) {
			return $model;
		}

		if ( ! isset( $import_data['integrations'] ) || empty( $import_data['integrations'] ) || ! is_array( $import_data['integrations'] ) ) {
			return $model;
		}

		/** @var Forminator_Custom_Form_Model $model */

		$integrations_data = $import_data['integrations'];
		foreach ( $integrations_data as $slug => $integrations_datum ) {
			try {
				$addon = forminator_get_addon( $slug );
				if ( $addon instanceof Forminator_Addon_Abstract ) {
					$form_settings = $addon->get_addon_form_settings( $model->id );
					if ( $form_settings instanceof Forminator_Addon_Form_Settings_Abstract ) {
						$form_settings->import_data( $integrations_datum );
					}
				}
			} catch ( Exception $e ) {
				forminator_addon_maybe_log( $slug, 'failed to get import form settings', $e->getMessage() );
			}

		}

		return $model;

	}

	/**
	 * Get status of prevent_store
	 *
	 * @since 1.5
	 *
	 * @return boolean
	 */
	public function is_prevent_store() {
		$form_id       = (int) $this->id;
		$form_settings = $this->settings;

		// default is always store
		$is_prevent_store = false;

		$is_prevent_store = isset( $this->settings['store'] ) ? $this->settings['store'] : $is_prevent_store;
		$is_prevent_store = filter_var( $is_prevent_store, FILTER_VALIDATE_BOOLEAN );

		/**
		 * Filter is_prevent_store flag of a custom form
		 *
		 * @since 1.5
		 *
		 * @param bool  $is_prevent_store
		 * @param int   $form_id
		 * @param array $form_settings
		 */
		$is_prevent_store = apply_filters( 'forminator_custom_form_is_prevent_store', $is_prevent_store, $form_id, $form_settings );

		return $is_prevent_store;
	}

	/**
	 * Get first captcha field in form if available
	 *
	 * @since 1.5.3
	 *
	 * @return bool|Forminator_Form_Field_Model
	 */
	public function get_captcha_field() {
		$captcha_field = false;
		$form_id       = (int) $this->id;
		$fields        = $this->fields;
		foreach ( $fields as $field ) {
			$field_array = $field->to_formatted_array();
			if ( isset( $field_array['type'] ) && 'captcha' === $field_array['type'] ) {
				$captcha_field = $field;
				break;
			}
		}

		$captcha_field = apply_filters( 'forminator_custom_form_get_captcha_field', $captcha_field, $form_id, $fields );

		return $captcha_field;
	}
}
