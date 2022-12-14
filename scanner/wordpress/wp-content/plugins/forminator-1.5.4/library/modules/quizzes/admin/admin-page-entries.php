<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Quizz_View_Page
 *
 * @since 1.0
 */
class Forminator_Quizz_View_Page extends Forminator_Admin_Page {

	/**
	 * Current model
	 *
	 * @var bool|Forminator_Quiz_Form_Model
	 */
	protected $model = false;

	/**
	 * Current form id
	 *
	 * @var int
	 */
	protected $form_id = 0;

	/**
	 * Fields
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Visible Fields
	 *
	 * @var array
	 */
	protected $visible_fields = array();

	/**
	 * Number of checked fields
	 *
	 * @var int
	 */
	protected $checked_fields = 0;

	/**
	 * Number of total fields
	 *
	 * @var int
	 */
	protected $total_fields = 0;

	/**
	 * Per page
	 *
	 * @var int
	 */
	protected $per_page = 10;

	/**
	 * Error message if avail
	 * @var string
	 */
	protected $error_message = '';

	/**
	 * Initialise variables
	 *
	 * @since 1.0
	 */
	public function before_render() {
		
		// This view is unused from 1.5.4 on, using "forminator-entries" instead.
		if ( 'forminator-quiz-view' === $this->page_slug ) {
			$url = '?page=forminator-entries&form_type=forminator_quizzes';
			if ( isset( $_REQUEST['form_id'] ) ) { // WPCS: CSRF OK
				$url .= '&form_id=' .  $_REQUEST['form_id']; // WPCS: CSRF OK
			}
			if ( wp_safe_redirect( $url ) ) {
				exit;
			}
		}

		if ( isset( $_REQUEST['form_id'] ) ) { // WPCS: CSRF OK
			$this->form_id = sanitize_text_field( $_REQUEST['form_id'] );
			$this->model   = Forminator_Quiz_Form_Model::model()->load( $this->form_id );
			if ( is_object( $this->model ) ) {
				$this->fields = $this->model->get_fields();
				if ( is_null( $this->fields ) ) {
					$this->fields = array();
				}
			} else {
				$this->model = false;
			}
			$this->per_page 	  = forminator_form_view_per_page( 'entries' );
			$this->total_fields   = count( $this->fields ) + 1;
			$this->checked_fields = $this->total_fields;
			$this->process_request();
		}
	}

	/**
	 * Process request
	 *
	 * @since 1.0
	 */
	public function process_request() {

		if ( isset( $_GET['err_msg'] ) ) {
			$this->error_message = $_GET['err_msg']; // WPCS: CSRF OK
		}

		if ( ! isset( $_POST['forminatorEntryNonce'] ) ) {
			return;
		}

		$nonce = $_POST['forminatorEntryNonce']; // WPCS: CSRF OK
		if ( wp_verify_nonce( $nonce, 'forminatorQuizEntries' ) ) {
			if ( isset( $_POST['field'] ) ) {
				$this->visible_fields = $_POST['field'];
				$this->checked_fields = count( $this->visible_fields );
			}

			return;
		}

		if ( wp_verify_nonce( $nonce, 'forminator_quiz_bulk_action' ) ) {
			if ( isset( $_POST['entries-action'] ) || isset( $_POST['entries-action-bottom'] ) ) {
				if ( isset( $_POST['entries-action'] ) && ! empty( $_POST['entries-action'] ) ) {
					$action = $_POST['entries-action'];
				} elseif ( isset( $_POST['entries-action-bottom'] ) ) {
					$action = $_POST['entries-action-bottom'];
				}

				switch ( $action ) {
					case 'delete-all' :
						if ( isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {
							$entries = implode( ",", $_POST['ids'] );
							Forminator_Form_Entry_Model::delete_by_entrys( $this->model->id, $entries );
							$url = add_query_arg( '', '' );
							wp_safe_redirect( $url );
							exit;
						}
						break;
				}
			}
		}
	}

	/**
	 * Register content boxes
	 *
	 * @since 1.0
	 */
	public function register_content_boxes() {
		$this->add_box(
			'custom-form/entries/popup/exports-list',
			__( 'Your Exports', Forminator::DOMAIN ),
			'entries-popup-exports-list',
			null,
			null,
			null
		);

		$this->add_box(
			'custom-form/entries/popup/schedule-export',
			__( 'Edit Schedule Export', Forminator::DOMAIN ),
			'entries-popup-schedule-export',
			null,
			null,
			null
		);
	}

	/**
	 * Get fields
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Visible fields
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_visible_fields() {
		return $this->visible_fields;
	}

	/**
	 * Checked field option
	 *
	 * @since 1.0
	 * @param string $slug - the field slug
	 *
	 * @return string
	 */
	public function checked_field( $slug ) {
		if ( ! empty( $this->visible_fields ) && is_array( $this->visible_fields ) ) {
			if ( in_array( $slug, $this->visible_fields, true ) ) {
				return checked( $slug, $slug );
			} else {
				return '';
			}
		}

		return checked( $slug, $slug );
	}

	/**
	 * Get model name
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_model_name() {
		if ( $this->model ) {
			return $this->model->name;
		}

		return '';
	}

	/**
	 * Fields header
	 *
	 * @since 1.0
	 * @return string
	 */
	public function fields_header() {
		echo esc_html( sprintf( __( 'Showing %$1s of %$2s fields', Forminator::DOMAIN ), $this->checked_fields, $this->total_fields ) );
	}

	/**
	 * Get fields table
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_table() {
		$per_page 	= $this->get_per_page();
		$entries 	= Forminator_Form_Entry_Model::list_entries( $this->form_id, $per_page, ( $this->get_paged() - 1 ) * $per_page );

		return $entries;
	}

	/**
	 * Get paged
	 *
	 * @since 1.0
	 * @return int
	 */
	public function get_paged() {
		$paged = isset( $_GET['paged'] ) ? $_GET['paged'] : 1; // WPCS: CSRF OK

		return intval( $paged );
	}

	/**
	 * Get the results per page
	 *
	 * @since 1.0.3
	 *
	 * @return int
	 */
	public function get_per_page() {
		return $this->per_page;
	}

	/**
	 * @since 1.0
	 * @return int
	 */
	public function get_total_entries() {
		$count = Forminator_Form_Entry_Model::count_entries( $this->form_id );

		return $count;
	}

	/**
	 * Get form type
	 *
	 * @since 1.0
	 * @return mixed
	 */
	public function get_form_type() {
		return $this->model->quiz_type;
	}

	/**
	 * Bulk actions
	 *
	 * @since 1.0
	 * @param string $position
	 */
	public function bulk_actions( $position = 'top' ) { ?>

		<select name="<?php echo ( 'top' === $position ) ? 'entries-action' : 'entries-action-bottom'; ?>"
			class="sui-select-sm sui-select-inline"
			style="min-width: 200px;">
			<option value=""><?php esc_html_e( "Bulk Actions", Forminator::DOMAIN ); ?></option>
			<option value="delete-all"><?php esc_html_e( "Delete Entries", Forminator::DOMAIN ); ?></option>
		</select>

		<button class="sui-button"><?php esc_html_e( "Apply", Forminator::DOMAIN ); ?></button>

	<?php
	}

	/**
	 * Pagination
	 *
	 * @since 1.1
	 */
	public function paginate() {
		$count = $this->get_total_entries();
		forminator_list_pagination( $count, 'entries' );
	}

	/**
	 * Get current error message
	 *
	 * @return string
	 *
	 * @since 1.5.2
	 */
	public function error_message() {
		return $this->error_message;
	}
}
