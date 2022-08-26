<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Export
 *
 * Handle data exports
 *
 * @since 1.0
 */
class Forminator_Export {

	/**
	 * Plugin instance
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Holds fields to be exported
	 *
	 * @since 1.0.5
	 *
	 * @var array
	 */
	private $global_fields_to_export = array();

	/**
	 * @var array
	 */
	private static $connected_addons = array();

	/**
	 * @var Forminator_Addon_Abstract[]
	 */
	private static $registered_addons = array();

	/**
	 * Return the plugin instance
	 *
	 * @return Forminator_Export
	 *
	 * @since 1.0
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Main constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wp_loaded', array( &$this, 'listen_for_csv_export' ) );
		add_action( 'wp_loaded', array( &$this, 'listen_for_saving_export_schedule' ) );
		//schedule for check and send export
		add_action( 'init', array( &$this, 'schedule_entries_exporter' ) );

		add_action( 'forminator_send_export', array( &$this, 'maybe_send_export' ) );
	}

	/**
	 * Set up the schedule
	 *
	 * @since 1.0
	 */
	public function schedule_entries_exporter() {
		if ( ! wp_next_scheduled( 'forminator_send_export' ) ) {
			wp_schedule_single_event( time(), 'forminator_send_export' );
		}
	}

	/**
	 * Listen for export action
	 *
	 * @since 1.0
	 */
	public function listen_for_csv_export() {
		if ( ! isset( $_POST['forminator_export'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_forminator_nonce'], 'forminator_export' ) ) {
			return;
		}

		$form_id     = isset( $_POST['form_id'] ) ? $_POST['form_id'] : 0;
		$type        = isset( $_POST['form_type'] ) ? $_POST['form_type'] : null;
		$form_id     = intval( $form_id );
		$export_data = $this->_prepare_export_data( $form_id, $type );
		if ( ! $export_data instanceof Forminator_Export_Result ) {
			return;
		}

		$data  = $export_data->data;
		$model = $export_data->model;
		$count = $export_data->entries_count;
		//save the time for later uses
		$logs = get_option( 'forminator_exporter_log', array() );
		if ( ! isset( $logs[ $model->id ] ) ) {
			$logs[ $model->id ] = array();
		}
		$logs[ $model->id ][] = array(
			'time'  => current_time( 'timestamp' ),
			'count' => $count,
		);
		update_option( 'forminator_exporter_log', $logs );

		$fp = fopen( 'php://memory', 'w' ); // phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_fopen -- disable phpcs because it writes memory
		foreach ( $data as $fields ) {
			fputcsv( $fp, $fields );
		}
		$filename = 'forminator-' . sanitize_title( $model->name ) . '-' . date( 'ymdHis' ) . '.csv';
		fseek( $fp, 0 );

		header( 'Content-Encoding: UTF-8' );
		header( 'Content-type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
		// make php send the generated csv lines to the browser
		fpassthru( $fp );
		exit();
	}

	/**
	 * Listen for the POST request to store schedule data
	 *
	 * @since 1.0
	 */
	public function listen_for_saving_export_schedule() {
		$post_data = $_POST;// wpcs csrf ok.

		if ( isset( $post_data['action'] ) && 'forminator_export_entries' === $post_data['action'] ) {

			if ( isset( $_POST['_forminator_nonce'] ) && ! wp_verify_nonce( $_POST['_forminator_nonce'], 'forminator_export' ) ) {
				$redirect = add_query_arg(
					array(
						'err_msg' => rawurlencode( __( 'Invalid request, you are not allowed to do that action.', Forminator::DOMAIN ) ),
					)
				);

				wp_safe_redirect( $redirect );
				exit;
			}

			$data = $this->get_entries_export_schedule();

			$enabled = false;
			if ( isset( $_POST['enabled'] ) ) {
				$enabled = filter_var( $_POST['enabled'], FILTER_VALIDATE_BOOLEAN );
			}

			if ( ! isset( $post_data['form_id'] ) || empty( $post_data['form_id'] ) ) {
				$redirect = add_query_arg(
					array(
						'err_msg' => rawurlencode( __( 'Invalid form ID.', Forminator::DOMAIN ) ),
					)
				);

				wp_safe_redirect( $redirect );
				exit;
			}
			if ( ! isset( $post_data['form_type'] ) || empty( $post_data['form_type'] ) ) {
				$redirect = add_query_arg(
					array(
						'err_msg' => rawurlencode( __( 'Invalid form type.', Forminator::DOMAIN ) ),
					)
				);

				wp_safe_redirect( $redirect );
				exit;
			}

			if ( $enabled && ( ! isset( $post_data['email'] ) || empty( $post_data['email'] ) || ! is_email( $post_data['email'] ) ) ) {
				$redirect = add_query_arg(
					array(
						'err_msg' => rawurlencode( __( 'Invalid email.', Forminator::DOMAIN ) ),
					)
				);

				wp_safe_redirect( $redirect );
				exit;
			}

			$key                 = $post_data['form_id'] . $post_data['form_type'];
			$current_form_export = isset( $data[ $key ] ) ? $data[ $key ] : array();
			$last_sent           = current_time( 'timestamp' );

			if ( 'daily' === $post_data['interval'] ) {
				$last_sent = strtotime( '-24 hours', current_time( 'timestamp' ) );
			}

			$data[ $key ] = array(
				'enabled'          => $enabled,
				'form_id'          => $post_data['form_id'],
				'form_type'        => $post_data['form_type'],
				'email'            => $post_data['email'],
				'interval'         => $post_data['interval'],
				'month_day'        => $post_data['month_day'],
				'day'              => $post_data['day'],
				'hour'             => $post_data['hour'],
				'last_sent'        => $last_sent,
				'if_new'           => isset( $post_data['if_new'] ) ? filter_var( $post_data['if_new'], FILTER_VALIDATE_BOOLEAN ) : false,
				'last_sent_row_id' => isset( $current_form_export['last_sent_row_id'] ) ? $current_form_export['last_sent_row_id'] : 0,
			);

			forminator_maybe_log($data[$key]);

			update_option( 'forminator_entries_export_schedule', $data );
			$redirect = remove_query_arg( array( 'err_msg' ) );

			$referer = wp_get_referer();
			if ( empty( $referer ) ) {
				// on same request uri `wp_get_referer` return false
				$referer = wp_get_raw_referer();
			}
			if ( ! empty( $referer ) && ! headers_sent() ) {
				// probably header sent so skip this logic to avoid erro
				$referer_query = wp_parse_url( $referer, PHP_URL_QUERY );
				if ( ! empty( $referer_query ) ) {
					wp_parse_str( $referer_query, $query_strings );
					if ( ! empty( $query_strings ) && isset( $query_strings['page'] ) && 'forminator-entries' === $query_strings['page'] ) {

						// additional redirect parameter on global entries page

						$redirect = add_query_arg(
							array(
								'form_id' => $post_data['form_id'],
							),
							$redirect
						);
					}
				}
			}
			wp_safe_redirect( $redirect );
			exit;
		}
	}

	/**
	 * Try send export
	 *
	 * @since 1.0
	 * @since 1.5.4 add force param
	 *
	 * @param bool $force force send, ignore last_sent timestamp
	 */
	public function maybe_send_export( $force = false ) {
		$export_schedules = $this->get_entries_export_schedule();

		$receipts = array();
		foreach ( $export_schedules as $row ) {
			if ( ! isset( $row['enabled'] ) || ( isset( $row['enabled'] ) && 'false' === $row['enabled'] ) || ( isset( $row['email'] ) && empty( $row['email'] ) ) ) {
				continue;
			}
			$last_sent = $row['last_sent'];
			//check the next sent
			$next_sent = null;
			switch ( $row['interval'] ) {
				case 'daily':
					$next_sent = strtotime( '+24 hours', $last_sent );
					$next_sent = date( 'Y-m-d', $next_sent ) . ' ' . $row['hour'];
					break;
				case 'weekly':
					$next_sent = strtotime( '+7 days', $last_sent );
					$next_sent = date( 'Y-m-d', $next_sent ) . ' ' . $row['hour'];
					break;
				case 'monthly':
					$month_date = isset( $row['month_day'] ) ? $row['month_day'] : 1;
					$next_sent  = $this->get_monthly_export_date( $last_sent, $month_date );
					$next_sent  = date( 'Y-m-d', $next_sent ) . ' ' . $row['hour'];
					break;
			}

			$is_send = current_time( 'timestamp' ) > strtotime( $next_sent );
			if ( $force ) {
				$is_send = true;
			}
			if ( $is_send ) {
				$last_entry_id = isset( $row['last_sent_row_id'] ) ? intval( $row['last_sent_row_id'] ) : 0;
				//queue to prevent spam
				$info = $this->_prepare_attachment( $row['form_id'], $row['form_type'], $row['email'], $last_entry_id );

				if ( ! $info instanceof Forminator_Export_Result || empty( $info->file_path ) ) {
					continue;
				}

				if ( ! isset( $receipts[ $row['email'] ] ) ) {
					$receipts[ $row['email'] ] = array();
				}
				$receipts[ $row['email'] ][] = $info;
			}
		}

		//now start to send
		foreach ( $receipts as $email => $info ) {
			$files          = array();
			$export_results = array();
			foreach ( $info as $export_result ) {

				// files reference needed for future deletion
				$files[] = $export_result->file_path;

				/** @var Forminator_Export_Result $export_result */
				$schedule_key    = $export_result->model->id . $export_result->form_type;
				$export_schedule = $this->get_entries_export_schedule( $schedule_key );
				$last_row_id     = isset( $export_schedule['last_sent_row_id'] ) ? intval( $export_schedule['last_sent_row_id'] ) : 0;
				$if_new          = isset( $export_schedule['if_new'] ) ? filter_var( $export_schedule['if_new'], FILTER_VALIDATE_BOOLEAN ) : false;

				// update last sent,
				// this options need to updated so it marked as email sent, and scheduled for next time
				$export_schedules[ $schedule_key ]['last_sent']        = current_time( 'timestamp' );
				$export_schedules[ $schedule_key ]['last_sent_row_id'] = $export_result->latest_entry_id;

				// only send email when new entry avail
				if ( $if_new ) {
					// skip sending this email
					if ( $last_row_id >= $export_result->latest_entry_id ) {
						forminator_maybe_log(
							__METHOD__,
							sprintf(
								'Scheduled Export email for %s ID %s skipped, due to no new submissions last_sent_row_id: %s latest_entry_id: %s',
								$export_result->form_type,
								$export_result->model->id,
								$last_row_id,
								$export_result->latest_entry_id
							)
						);
						continue;
					}
				}

				$export_results[] = $export_result;
			}

			if ( ! empty( $export_results ) ) {
				$subject      = $this->get_mail_subject( $export_results );
				$mail_content = $this->get_mail_content( $export_results );
				$mail_headers = $this->get_mail_headers( $email, $export_results );
				wp_mail( $email, $subject, $mail_content, $mail_headers, $files );
			}

			foreach ( $files as $file ) {
				@unlink( $file ); // phpcs:ignore
			}
		}
		update_option( 'forminator_entries_export_schedule', $export_schedules );
	}

	/**
	 * Prepare export data
	 *
	 * @since 1.0
	 * @since 1.5
	 * @since 1.5.4 add `$latest_exported_entry_id` param to get new entries count
	 *
	 * @param int    $form_id
	 * @param string $type
	 * @param int    $latest_exported_entry_id
	 *
	 * @return Forminator_Export_Result
	 *
	 */
	private function _prepare_export_data( $form_id, $type, $latest_exported_entry_id = 0 ) {
		$model                    = null;
		$data                     = array();
		$entries                  = array();
		$export_result            = new Forminator_Export_Result();
		$export_result->form_type = $type;

		switch ( $type ) {
			case 'quiz':
				$model = Forminator_Quiz_Form_Model::model()->load( $form_id );
				if ( ! is_object( $model ) ) {
					return null;
				}

				$export_result->model = $model;

				$entries = Forminator_Form_Entry_Model::get_entries( $form_id );

				$headers = array(
					__( 'Date', Forminator::DOMAIN ),
					__( 'Question', Forminator::DOMAIN ),
					__( 'Answer', Forminator::DOMAIN ),
					__( 'Result', Forminator::DOMAIN ),
				);
				foreach ( $entries as $entry ) {
					if ( $entry->entry_id > $latest_exported_entry_id ) {
						$export_result->new_entries_count ++;
					}
					// nowrong
					if ( isset( $entry->meta_data['entry']['value'][0]['value'] ) ) {
						$meta = $entry->meta_data['entry']['value'][0]['value'];

						if ( isset( $meta['answers'] ) ) {
							foreach ( $meta['answers'] as $answer ) {
								$row    = array();
								$row[]  = $entry->date_created_sql;
								$row[]  = $answer['question'];
								$row[]  = $answer['answer'];
								$row[]  = $meta['result']['title'];
								$data[] = $row;
							}
						}
					} else {
						// knowledge
						$meta = $entry->meta_data['entry']['value'];
						foreach ( $meta as $answer ) {
							$row    = array();
							$row[]  = $entry->date_created_sql;
							$row[]  = $answer['question'];
							$row[]  = $answer['answer'];
							$row[]  = ( ( $answer['isCorrect'] ) ? __( 'Correct', Forminator::DOMAIN ) : __( 'Incorrect', Forminator::DOMAIN ) );
							$data[] = $row;
						}
					}
				}

				$data                = array_merge( array( $headers ), $data );
				$export_result->data = $data;
				break;
			case 'poll':
				$model = Forminator_Poll_Form_Model::model()->load( $form_id );
				if ( ! is_object( $model ) ) {
					return null;
				}

				$export_result->model = $model;

				$entries = Forminator_Form_Entry_Model::get_entries( $form_id );

				foreach ( $entries as $entry ) {
					if ( $entry->entry_id > $latest_exported_entry_id ) {
						$export_result->new_entries_count ++;
					}
				}

				$fields_array = $model->get_fields_as_array();
				$map_entries  = Forminator_Form_Entry_Model::map_polls_entries( $form_id, $fields_array );
				$fields       = $model->get_fields();
				$data         = array(
					array( __( 'Date', Forminator::DOMAIN ), __( 'Answer', Forminator::DOMAIN ), __( 'Total', Forminator::DOMAIN ) ),
				);
				$date         = '';
				if ( ! is_null( $fields ) ) {
					foreach ( $fields as $field ) {
						$label = $field->__get( 'field_label' );
						if ( ! $label ) {
							$label = $field->title;
						}
						if ( isset( $entries[0] ) ) {
							$date = $entries[0]->date_created_sql;
						}
						$slug          = isset( $field->slug ) ? $field->slug : sanitize_title( $label );
						$count_entries = 0;
						if ( in_array( $slug, array_keys( $map_entries ), true ) ) {
							$count_entries = $map_entries[ $slug ];
						}
						$data[] = array( $date, $label, $count_entries );
					}
				}
				$export_result->data = $data;
				break;
			case 'cform':
				$model = Forminator_Custom_Form_Model::model()->load( $form_id );
				if ( ! is_object( $model ) ) {
					return null;
				}
				$entries              = Forminator_Form_Entry_Model::get_entries( $form_id );
				$mappers              = $this->get_custom_form_export_mappers( $model );
				$addon_mappers        = $this->attach_addons_on_export_render_title_row( $form_id, $entries );
				$export_result->model = $model;

				$result = array();
				foreach ( $entries as $entry ) {
					if ( $entry->entry_id > $latest_exported_entry_id ) {
						$export_result->new_entries_count ++;
					}
					$data = array();
					// traverse from fields to be correctly mapped with updated form fields.
					foreach ( $mappers as $mapper ) {
						//its from model's property
						if ( isset( $mapper['property'] ) ) {
							if ( property_exists( $entry, $mapper['property'] ) ) {
								$property = $mapper['property'];
								// casting property to string
								$data[] = (string) $entry->$property;
							} else {
								$data[] = '';
							}
						} else {
							// meta_key based
							$meta_value = $entry->get_meta( $mapper['meta_key'], '' );
							if ( ! isset( $mapper['sub_metas'] ) ) {
								$data[] = Forminator_Form_Entry_Model::meta_value_to_string( $mapper['type'], $meta_value );
							} else {

								// sub_metas available
								foreach ( $mapper['sub_metas'] as $sub_meta ) {
									$sub_key = $sub_meta['key'];
									if ( isset( $meta_value[ $sub_key ] ) && ! empty( $meta_value[ $sub_key ] ) ) {
										$value      = $meta_value[ $sub_key ];
										$field_type = $mapper['type'] . '.' . $sub_key;
										$data[]     = Forminator_Form_Entry_Model::meta_value_to_string( $field_type, $value );
									} else {
										$data[] = '';
									}
								}
							}
						}

					}

					// Addon columns
					$addon_data = $this->attach_addons_on_export_render_entry_row( $form_id, $entry );

					foreach ( $addon_mappers as $mapper_id => $mapper ) {
						if ( isset( $addon_data[ $mapper_id ] ) ) {
							$data[] = $addon_data[ $mapper_id ];
						}
					}
					$result[ (string) $entry->entry_id ] = $data;
				}

				//flatten mappers to headers
				$headers = array();
				foreach ( $mappers as $mapper ) {
					if ( ! isset( $mapper['sub_metas'] ) ) {
						$headers[] = $mapper['label'];
					} else {
						foreach ( $mapper['sub_metas'] as $sub_meta ) {
							$headers[] = $sub_meta['label'];
						}
					}
				}

				//additional addon headers
				foreach ( $addon_mappers as $mapper ) {
					$headers[] = $mapper;
				}

				$data                = array_merge( array( 'headers' => $headers ), $result );
				$export_result->data = $data;
				break;
		}

		$export_result->entries_count = count( $entries );

		// DESC order, latest entry will be first
		if ( isset( $entries[0] ) && $entries[0] instanceof Forminator_Form_Entry_Model ) {
			$latest_entry                   = $entries[0];
			$export_result->latest_entry_id = $latest_entry->entry_id;
		}

		return $export_result;
	}

	/**
	 * Prepare mail attachment
	 *
	 * @since 1.0
	 * @since 1.5.4 add `$last_entry_id` to calculate new entries count
	 *
	 * @param int    $form_id
	 * @param string $type
	 * @param string $email
	 * @param int    $last_entry_id
	 *
	 * @return Forminator_Export_Result|boolean
	 */
	private function _prepare_attachment( $form_id, $type, $email, $last_entry_id = 0 ) {
		$export_result = $this->_prepare_export_data( $form_id, $type, $last_entry_id );
		if ( ! $export_result instanceof Forminator_Export_Result ) {
			return false;
		}

		$model = $export_result->model;
		$data  = $export_result->data;

		$upload_dirs = wp_upload_dir();
		//temp write to uploads
		$tmp_path = $upload_dirs['basedir'] . '/forminator/';

		require_once ABSPATH . 'wp-admin/includes/file.php';
		/** @var WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;
		WP_Filesystem();

		if ( ! $wp_filesystem->is_dir( $tmp_path ) ) {
			$wp_filesystem->mkdir( $tmp_path );
		}

		$filename = sanitize_title( $model->name ) . '-' . date( 'ymdHis' ) . '.csv';
		$tmp_path = $tmp_path . $filename;

		$mode = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : false;

		if ( ! $wp_filesystem->put_contents( $tmp_path, $this->csvstr( $data ), $mode ) ) {
			if ( is_wp_error( $wp_filesystem->errors ) ) {
				forminator_maybe_log( __METHOD__, $wp_filesystem->errors->get_error_message() );
			}

			return false;
		}

		$export_result->file_path = $tmp_path;

		return $export_result;
	}

	private function csvstr( $fields ) {

		if ( ! is_array( $fields ) ) {
			return false;
		}

		$output = array();

		foreach ( $fields as $value ) {
			$f = fopen( 'php://memory', 'r+' );

			if ( fputcsv( $f, $value ) === false ) {
				return false;
			}
			rewind( $f );
			$csv_line = stream_get_contents( $f );

			$output[] = rtrim( $csv_line );
		}

		return implode( PHP_EOL, $output );
	}

	private function get_monthly_export_date( $last_sent, $month_day ) {
		// Array [0] = year. [1] = month. [2] = day.
		$last_sent_array = explode( '-', date( 'Y-m-d', $last_sent ) );

		$next_sent_array    = array();
		$next_sent_array[0] = 12 === $last_sent_array[1] ? $last_sent_array[0] + 1 : $last_sent_array[0];
		$next_sent_array[1] = 12 === $last_sent_array[1] ? 1 : $last_sent_array[1] + 1;
		$next_sent_array[2] = $month_day;

		$is_valid_date = checkdate( $next_sent_array[1], $next_sent_array[2], $next_sent_array[0] );

		while ( ! $is_valid_date ) {
			$next_sent_array[2] --;
			$is_valid_date = checkdate( $next_sent_array[1], $next_sent_array[2], $next_sent_array[0] );
		}

		$next_sent = strtotime( implode( '-', $next_sent_array ) );

		return $next_sent;
	}


	/**
	 * Get data mappers for retrieving entries meta
	 *
	 * @example {
	 *  [
	 *      'meta_key'  => 'FIELD_ID',
	 *      'label'     => 'LABEL',
	 *      'type'      => 'TYPE',
	 *      'sub_metas'      => [
	 *          [
	 *              'key'   => 'SUFFIX',
	 *              'label'   => 'LABEL',
	 *          ]
	 *      ],
	 *  ]...
	 * }
	 *
	 * @since   1.0.5
	 *
	 * @param Forminator_Custom_Form_Model|Forminator_Base_Form_Model $model
	 *
	 * @return array
	 */
	private function get_custom_form_export_mappers( $model ) {
		/** @var  Forminator_Custom_Form_Model $model */
		$fields              = $model->get_fields();
		$ignored_field_types = Forminator_Form_Entry_Model::ignored_fields();

		/** @var  Forminator_Form_Field_Model $fields */
		$mappers = array(
			array(
				// read form model's property
				'property' => 'date_created', // must be on export
				'label'    => __( 'Submission Date', Forminator::DOMAIN ),
				'type'     => 'entry_date_created',
			),
		);

		foreach ( $fields as $field ) {
			$field_type = $field->__get( 'type' );

			if ( in_array( $field_type, $ignored_field_types, true ) ) {
				continue;
			}

			// base mapper for every field
			$mapper             = array();
			$mapper['meta_key'] = $field->slug;
			$mapper['label']    = $field->get_label_for_entry();
			$mapper['type']     = $field_type;


			// fields that should be displayed as multi column (sub_metas)
			if ( 'name' === $field_type ) {
				$is_multiple_name = filter_var( $field->__get( 'multiple_name' ), FILTER_VALIDATE_BOOLEAN );
				if ( $is_multiple_name ) {
					$prefix_enabled      = filter_var( $field->__get( 'prefix' ), FILTER_VALIDATE_BOOLEAN );
					$first_name_enabled  = filter_var( $field->__get( 'fname' ), FILTER_VALIDATE_BOOLEAN );
					$middle_name_enabled = filter_var( $field->__get( 'mname' ), FILTER_VALIDATE_BOOLEAN );
					$last_name_enabled   = filter_var( $field->__get( 'lname' ), FILTER_VALIDATE_BOOLEAN );
					// at least one sub field enabled
					if ( $prefix_enabled || $first_name_enabled || $middle_name_enabled || $last_name_enabled ) {
						// sub metas
						$mapper['sub_metas'] = array();
						if ( $prefix_enabled ) {
							$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'prefix' );
							$label                 = $field->__get( 'prefix_label' );
							$mapper['sub_metas'][] = array(
								'key'   => 'prefix',
								'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
							);
						}

						if ( $first_name_enabled ) {
							$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'first-name' );
							$label                 = $field->__get( 'fname_label' );
							$mapper['sub_metas'][] = array(
								'key'   => 'first-name',
								'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
							);
						}

						if ( $middle_name_enabled ) {
							$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'middle-name' );
							$label                 = $field->__get( 'mname_label' );
							$mapper['sub_metas'][] = array(
								'key'   => 'middle-name',
								'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
							);
						}
						if ( $last_name_enabled ) {
							$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'last-name' );
							$label                 = $field->__get( 'lname_label' );
							$mapper['sub_metas'][] = array(
								'key'   => 'last-name',
								'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
							);
						}

					} else {
						// if no subfield enabled when multiple name remove mapper (means dont show it on export)
						$mapper = array();
					}
				}

			} elseif ( 'address' === $field_type ) {
				$street_enabled  = filter_var( $field->__get( 'street_address' ), FILTER_VALIDATE_BOOLEAN );
				$line_enabled    = filter_var( $field->__get( 'address_line' ), FILTER_VALIDATE_BOOLEAN );
				$city_enabled    = filter_var( $field->__get( 'address_city' ), FILTER_VALIDATE_BOOLEAN );
				$state_enabled   = filter_var( $field->__get( 'address_state' ), FILTER_VALIDATE_BOOLEAN );
				$zip_enabled     = filter_var( $field->__get( 'address_zip' ), FILTER_VALIDATE_BOOLEAN );
				$country_enabled = filter_var( $field->__get( 'address_country' ), FILTER_VALIDATE_BOOLEAN );
				if ( $street_enabled || $line_enabled || $city_enabled || $state_enabled || $zip_enabled || $country_enabled ) {
					$mapper['sub_metas'] = array();
					if ( $street_enabled ) {
						$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'street_address' );
						$label                 = $field->__get( 'street_address_label' );
						$mapper['sub_metas'][] = array(
							'key'   => 'street_address',
							'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
						);
					}
					if ( $line_enabled ) {
						$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'address_line' );
						$label                 = $field->__get( 'address_line_label' );
						$mapper['sub_metas'][] = array(
							'key'   => 'address_line',
							'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
						);
					}
					if ( $city_enabled ) {
						$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'city' );
						$label                 = $field->__get( 'address_city_label' );
						$mapper['sub_metas'][] = array(
							'key'   => 'city',
							'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
						);
					}
					if ( $state_enabled ) {
						$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'state' );
						$label                 = $field->__get( 'address_state_label' );
						$mapper['sub_metas'][] = array(
							'key'   => 'state',
							'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
						);
					}
					if ( $zip_enabled ) {
						$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'zip' );
						$label                 = $field->__get( 'address_zip_label' );
						$mapper['sub_metas'][] = array(
							'key'   => 'zip',
							'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
						);
					}
					if ( $country_enabled ) {
						$default_label         = Forminator_Form_Entry_Model::translate_suffix( 'country' );
						$label                 = $field->__get( 'address_country_label' );
						$mapper['sub_metas'][] = array(
							'key'   => 'country',
							'label' => $mapper['label'] . ' - ' . ( $label ? $label : $default_label ),
						);
					}
				} else {
					// if no subfield enabled when multiple name remove mapper (means dont show it on export)
					$mapper = array();
				}
			}

			if ( ! empty( $mapper ) ) {
				$mappers[] = $mapper;
			}
		}

		return $mappers;
	}

	/**
	 * Additional Column on Title(first) Row of Export data from Addon
	 *
	 * @see   Forminator_Addon_Form_Hooks_Abstract::on_export_render_title_row()
	 *
	 * @since 1.1
	 * @since 1.5.3 add $entries param to find addons that probably is/was connected
	 *
	 * @param                               $form_id
	 * @param Forminator_Form_Entry_Model[] $entries
	 *
	 * @return array
	 */
	private function attach_addons_on_export_render_title_row( $form_id, $entries = array() ) {
		$additional_headers = array();
		//find all registered addons, so history can be shown even for deactivated addons
		$registered_addons = $this->get_registered_addons( $form_id, $entries );

		foreach ( $registered_addons as $registered_addon ) {
			try {
				$form_hooks         = $registered_addon->get_addon_form_hooks( $form_id );
				$addon_headers      = $form_hooks->on_export_render_title_row();
				$addon_headers      = $this->format_addon_additional_headers( $registered_addon, $addon_headers );
				$additional_headers = array_merge( $additional_headers, $addon_headers );
			} catch ( Exception $e ) {
				forminator_addon_maybe_log( $registered_addon->get_slug(), 'failed to on_export_render_title_row', $e->getMessage() );
			}

		}

		return $additional_headers;
	}

	/**
	 * Format additional header given by addon
	 * Format used is `forminator_addon_export_title_{$addon_slug}_{$title_id_data_from_addon}`
	 *
	 * @since 1.1
	 *
	 * @param Forminator_Addon_Abstract $addon
	 * @param                           $addon_headers
	 *
	 * @return array
	 */
	private function format_addon_additional_headers( Forminator_Addon_Abstract $addon, $addon_headers ) {
		$formatted_headers = array();
		if ( ! is_array( $addon_headers ) || empty( $addon_headers ) ) {
			return $formatted_headers;
		}

		foreach ( $addon_headers as $title_id => $title ) {
			if ( ! is_scalar( $title ) || empty( $title ) ) {
				continue; // skip on empty title
			}

			// avoid collistion with other addon ids
			$title_id = 'forminator_addon_export_title_' . $addon->get_slug() . '_' . $title_id;

			$formatted_headers[ $title_id ] = $title;
		}

		return $formatted_headers;
	}

	/**
	 * Add addons export render entry row
	 *
	 * @see   Forminator_Addon_Form_Hooks_Abstract::on_export_render_entry()
	 * @since 1.1
	 *
	 * @param                             $form_id
	 * @param Forminator_Form_Entry_Model $entry_model
	 *
	 * @return array
	 */
	private function attach_addons_on_export_render_entry_row( $form_id, Forminator_Form_Entry_Model $entry_model ) {
		$additional_data = array();
		//find all registered addons, so history can be shown even for deactivated addons
		$registered_addons = $this->get_registered_addons( $form_id );

		foreach ( $registered_addons as $registered_addon ) {
			try {
				$form_hooks      = $registered_addon->get_addon_form_hooks( $form_id );
				$meta_data       = forminator_find_addon_meta_data_from_entry_model( $registered_addon, $entry_model );
				$addon_data      = $form_hooks->on_export_render_entry( $entry_model, $meta_data );
				$addon_data      = $this->format_addon_additional_data( $registered_addon, $addon_data );
				$additional_data = array_merge( $additional_data, $addon_data );
			} catch ( Exception $e ) {
				forminator_addon_maybe_log( $registered_addon->get_slug(), 'failed to on_export_render_entry', $e->getMessage() );
			}

		}

		return $additional_data;
	}

	/**
	 * Format addional data form addons to match requirement of export
	 * Format used is `forminator_addon_export_title_{$addon_slug}_{$title_id_data_from_addon}`
	 *
	 * @since 1.1
	 *
	 * @param Forminator_Addon_Abstract $addon
	 * @param                           $addon_data
	 *
	 * @return array
	 */
	private function format_addon_additional_data( Forminator_Addon_Abstract $addon, $addon_data ) {
		$formatted_data = array();
		if ( ! is_array( $addon_data ) || empty( $addon_data ) ) {
			return $formatted_data;
		}

		foreach ( $addon_data as $title_id => $value ) {
			$value = Forminator_Form_Entry_Model::meta_value_to_string( 'addon_' . $addon->get_slug(), $value );

			// avoid collistion with other addon ids
			$title_id = 'forminator_addon_export_title_' . $addon->get_slug() . '_' . $title_id;

			$formatted_data[ $title_id ] = $value;
		}

		return $formatted_data;
	}

	/**
	 * Get Connected Addons for form_id, avoid overhead for checking connected addons many times
	 *
	 * @since 1.1
	 *
	 * @param $form_id
	 *
	 * @return array|Forminator_Addon_Abstract[]
	 */
	public function get_connected_addons( $form_id ) {
		if ( ! isset( self::$connected_addons[ $form_id ] ) ) {
			self::$connected_addons[ $form_id ] = array();

			$connected_addons = forminator_get_addons_instance_connected_with_form( $form_id );

			foreach ( $connected_addons as $connected_addon ) {
				try {
					$form_hooks = $connected_addon->get_addon_form_hooks( $form_id );
					if ( $form_hooks instanceof Forminator_Addon_Form_Hooks_Abstract ) {
						self::$connected_addons[ $form_id ][] = $connected_addon;
					}
				} catch ( Exception $e ) {
					forminator_addon_maybe_log( $connected_addon->get_slug(), 'failed to get_addon_form_hooks', $e->getMessage() );
				}
			}
		}

		return self::$connected_addons[ $form_id ];
	}

	/**
	 * Get Globally Registered Addons for form_id, avoid overhead for checking registerd addons many times
	 *
	 * @since 1.5.3
	 *
	 * @param                               $form_id
	 * @param Forminator_Form_Entry_Model[] $entries
	 *
	 * @return array|Forminator_Addon_Abstract[]
	 */
	public function get_registered_addons( $form_id, $entries = array() ) {
		if ( empty( self::$registered_addons ) ) {
			self::$registered_addons = array();

			$registered_addons = forminator_get_registered_addons();

			foreach ( $entries as $entry ) {

				// find registered addon by slug pattern
				$entry_addon_slugs = forminator_find_addon_slugs_from_entry_model( $entry );
				foreach ( $entry_addon_slugs as $entry_addon_slug ) {

					// check if this slug globally registered
					if ( in_array( $entry_addon_slug, array_keys( $registered_addons ), true ) ) {

						// check if already in static $registered_addons
						if ( ! in_array( $entry_addon_slug, array_keys( self::$registered_addons ), true ) ) {
							$addon = forminator_get_addon( $entry_addon_slug );
							if ( $addon instanceof Forminator_Addon_Abstract ) {
								try {
									$form_hooks = $addon->get_addon_form_hooks( $form_id );
									if ( $form_hooks instanceof Forminator_Addon_Form_Hooks_Abstract ) {
										self::$registered_addons[ $addon->get_slug() ] = $addon;
									}
								} catch ( Exception $e ) {
									forminator_addon_maybe_log( $addon->get_slug(), 'failed to get_addon_form_hooks one export', $e->getMessage() );
								}
							}
						}
					}
				}
			}
		}

		return self::$registered_addons;
	}

	/**
	 * Get Entries Export Schedule
	 *
	 * Basic checking for export schedule
	 *
	 * @since 1.1
	 * @since 1.5.4 add $schedule_key param
	 *
	 * @param null|string $schedule_key
	 *
	 * @return array
	 */
	public function get_entries_export_schedule( $schedule_key = null ) {
		$opt           = get_option( 'forminator_entries_export_schedule', array() );
		$validated_opt = $opt;

		foreach ( $validated_opt as $key => $value ) {
			if ( ! $value['form_id'] || ! $value['form_type'] ) {
				// unschedule no form id exist
				unset( $validated_opt[ $key ] );
			}
		}

		if ( $validated_opt !== $opt ) {
			update_option( 'forminator_entries_export_schedule', $validated_opt );
		}

		if ( ! empty( $schedule_key ) ) {
			if ( isset( $validated_opt[ $schedule_key ] ) && is_array( $validated_opt[ $schedule_key ] ) ) {
				return $validated_opt[ $schedule_key ];
			}

			return array();
		}

		return $validated_opt;
	}

	/**
	 * Get email headers
	 *
	 * @since 1.5.4
	 *
	 * @param string                     $email
	 * @param Forminator_Export_Result[] $export_results
	 *
	 * @return array
	 */
	public function get_mail_headers( $email = '', $export_results = array() ) {
		$from_address = get_global_sender_email_address();
		$from_name    = get_global_sender_name();
		$mail_headers = array(
			'From: ' . $from_name . ' <' . $from_address . '>',
			'Content-Type: text/html; charset=UTF-8',
		);

		/**
		 * Filter header for export mails
		 *
		 * @since 1.5.4
		 *
		 * @param array                      $mail_headers
		 * @param string                     $email          email address which export mail will be sent
		 * @param Forminator_Export_Result[] $export_results export results @see Forminator_Export_Result
		 */
		$mail_headers = apply_filters( 'forminator_export_email_headers', $mail_headers, $email, $export_results );

		return $mail_headers;
	}

	/**
	 * Get compiled mail subject for scheduled export
	 *
	 * @since 1.5.4
	 *
	 * @param Forminator_Export_Result[] $export_results
	 *
	 * @return string
	 */
	public function get_mail_subject( $export_results ) {

		$form_names = array();
		foreach ( $export_results as $export_result ) {
			if ( isset( $export_result->model->settings['formName'] ) ) {
				$form_names[] = $export_result->model->settings['formName'];
			} else {
				$form_names[] = $export_result->model->name;
			}
		}

		$subject = sprintf( __( "Submissions data for %s", Forminator::DOMAIN ), implode( ', ', $form_names ) );

		/**
		 * Filter mail subject used for scheduled export email
		 *
		 * @since 1.5.4
		 *
		 * @param string                     $subject
		 * @param array                      $form_names     form names
		 * @param Forminator_Export_Result[] $export_results Export results @see Forminator_Export_Result
		 *
		 * @return string
		 */
		$subject = apply_filters( 'forminator_export_email_subject', $subject, $form_names, $export_results );

		return $subject;
	}

	/**
	 * Get compiled mail content for scheduled export
	 *
	 * @since 1.5.4
	 *
	 * @param Forminator_Export_Result[] $export_results
	 *
	 * @return string
	 */
	public function get_mail_content( $export_results ) {
		$submissions_link_format = admin_url( 'admin.php?page=forminator-entries&form_type=%1$s&form_id=%2$d' );

		$entries_counts     = array();
		$new_entries_counts = array();
		$form_names         = array();
		$submission_links   = array();
		foreach ( $export_results as $export_result ) {
			if ( isset( $export_result->model->settings['formName'] ) ) {
				$form_names[] = $export_result->model->settings['formName'];
			} else {
				$form_names[] = $export_result->model->name;
			}
			$entries_counts[]     = $export_result->entries_count;
			$new_entries_counts[] = $export_result->new_entries_count;
			$form_type            = 'forminator_forms';
			switch ( $export_result->form_type ) {
				case 'cform':
					$form_type = 'forminator_forms';
					break;
				case 'poll':
					$form_type = 'forminator_polls';
					break;
				case 'quiz':
					$form_type = 'forminator_quizzes';
					break;
			}
			$submission_links[] = sprintf( $submissions_link_format, $form_type, (int) $export_result->model->id );
		}

		$blog_name         = get_option( 'blogname' );
		$total_entries     = array_sum( $entries_counts );
		$total_new_entries = array_sum( $new_entries_counts );


		$mail_content = '<p>' . sprintf( __( 'Hi %s,', Forminator::DOMAIN ), $blog_name ) . '</p>' . PHP_EOL;

		$mail_content .= '<p>' . sprintf(
				__(
					'Your scheduled exports have arrived! Forminator has captured %1$s new submission(s) and packaged %2$s total submissions from %3$s since the last scheduled export sent.',
					Forminator::DOMAIN
				),
				'<strong>' . (int) $total_new_entries . '</strong>',
				'<strong>' . (int) $total_entries . '</strong>',
				implode( ', ', $form_names )
			) . '</p>' . PHP_EOL;

		$mail_content .= '<ul>' . PHP_EOL;
		foreach ( $submission_links as $key => $submission_link ) {
			$mail_content
				.= sprintf(
					   '<li><strong>%1$s</strong>: 
						<ul>
							<li>%2$s : %3$d</li>
							<li>%4$s : %5$d</li>
							<li><a href="%6$s">%7$s</a></li>
						</ul>
					</li>',
					   $form_names[ $key ],
					   __( 'New Submissions', Forminator::DOMAIN ),
					   (int) $new_entries_counts[ $key ],
					   __( 'Total Submissions', Forminator::DOMAIN ),
					   (int) $entries_counts[ $key ],
					   $submission_links[ $key ],
					   __( 'View Submissions', Forminator::DOMAIN )
				   ) . PHP_EOL;
		}
		$mail_content .= '</ul>' . PHP_EOL;


		$mail_content .= '<p>' . __( 'Cheers,', Forminator::DOMAIN ) . '</p>' . PHP_EOL;
		$mail_content .= '<p>' . __( 'Forminator', Forminator::DOMAIN ) . '</p>';

		/**
		 * Filter mail content used for scheduled export email
		 *
		 * @since 1.5.4
		 *
		 * @param string                     $mail_content   html formatted mail content
		 * @param array                      $form_names     form names
		 * @param Forminator_Export_Result[] $export_results Export results @see Forminator_Export_Result
		 *
		 * @return string
		 */
		$mail_content = apply_filters( 'forminator_export_email_content', $mail_content, $form_names, $export_results );

		return $mail_content;
	}
}
