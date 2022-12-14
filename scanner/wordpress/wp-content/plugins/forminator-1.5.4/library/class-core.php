<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Forminator_Core
 *
 * @since 1.0
 */
class Forminator_Core {

	/**
	 * @var Forminator_Admin
	 */
	public $admin;

	/**
	 * Store modules objects
	 *
	 * @var array
	 */
	public $modules = array();

	/**
	 * Store forms objects
	 *
	 * @var array
	 */
	public $forms = array();

	/**
	 * Store fields objects
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Plugin instance
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0
	 * @return Forminator
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Forminator_Core constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		// Include all necessary files
		$this->includes();

		//First check if upgrade of data is needed
		Forminator_Upgrade::init();

		if ( is_admin() ) {
			// Initialize admin core
			$this->admin = new Forminator_Admin();

			new Forminator_Shortcode_Generator();

			add_action( 'admin_enqueue_scripts', array( $this, 'embed_pointers' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'add_pointers' ) );
			add_action( 'wp_head', array( $this, 'localize_pointers' ) );
		}

		// Get enabled modules
		$modules       = new Forminator_Modules();
		$this->modules = $modules->get_modules();

		// Get enabled fields
		$fields       = new Forminator_Fields();
		$this->fields = $fields->get_fields();

		// HACK: Add settings and entries page at the end of the list
		if ( is_admin() ) {
			$this->admin->add_entries_page();
			if ( Forminator::is_addons_feature_enabled() ) {
				$this->admin->add_integrations_page();
			}
			$this->admin->add_settings_page();
		}

		//Protection management
		Forminator_Protection::get_instance();

		//Export management
		Forminator_Export::get_instance();

		//Post meta box
		add_action( 'init', array( &$this, 'post_field_meta_box' ) );
	}

	/**
	 * Includes
	 *
	 * @since 1.0
	 */
	private function includes() {
		// Abstracts
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/abstracts/abstract-class-field.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/abstracts/abstract-class-form-result.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/abstracts/abstract-class-form-template.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/abstracts/abstract-class-front-action.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/abstracts/abstract-class-mail.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/abstracts/abstract-class-payment-gateway.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/abstracts/abstract-class-spam-protection.php';

		// Classes
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-loader.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-modules.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-form-fields.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-database-tables.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-upgrade.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-geo.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-protection.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-shortcode-generator.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-export-result.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-export.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/render/class-render-form.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/gateways/class-paypal-express.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/render/class-widget.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/class-recaptcha.php';

		//Models
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/model/class-form-entry-model.php';

		// Helpers
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/helpers/helper-core.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/helpers/helper-modules.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/helpers/helper-forms.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/helpers/helper-fields.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/helpers/helper-google-fonts.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/helpers/helper-mail.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/helpers/helper-currency.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/helpers/helper-autofill.php';

		//Model
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/model/class-base-form-model.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/model/class-custom-form-model.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/model/class-form-field-model.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/model/class-poll-form-model.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/model/class-quiz-form-model.php';
		/* @noinspection PhpIncludeInspection */
		include_once forminator_plugin_dir() . 'library/model/class-form-views-model.php';
		if ( is_admin() ) {
			/* @noinspection PhpIncludeInspection */
			include_once forminator_plugin_dir() . 'admin/abstracts/class-admin-page.php';
			/* @noinspection PhpIncludeInspection */
			include_once forminator_plugin_dir() . 'admin/abstracts/class-admin-module.php';
			/* @noinspection PhpIncludeInspection */
			include_once forminator_plugin_dir() . 'admin/classes/class-admin.php';
			/* @noinspection PhpIncludeInspection */
			if ( ! class_exists( 'WP_List_Table' ) ) {
				/* @noinspection PhpIncludeInspection */
				require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';//added
				/* @noinspection PhpIncludeInspection */
				require_once ABSPATH . 'wp-admin/includes/screen.php';//added
				/* @noinspection PhpIncludeInspection */
				require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
				/* @noinspection PhpIncludeInspection */
				require_once ABSPATH . 'wp-admin/includes/template.php';
			}
			/* @noinspection PhpIncludeInspection */
			include_once forminator_plugin_dir() . 'library/model/class-form-entries-list-table.php';
		}
	}

	/**
	 * Start creating meta box for the posts
	 *
	 * @since 1.0
	 */
	public function post_field_meta_box() {
		add_action( 'add_meta_boxes_post', array( $this, 'setup_post_meta_box' ) );
	}

	/**
	 * Setup the meta box
	 *
	 * @since 1.0
	 */
	public function setup_post_meta_box( $post ) {
		if ( is_object( $post ) ) {
			$is_forminator_meta = get_post_meta( $post->ID, '_has_forminator_meta' );
			if ( $is_forminator_meta ) {
				add_meta_box(
					'forminator-post-meta-box',
					__( 'Post Custom Data', Forminator::DOMAIN ),
					array( $this, 'render_post_meta_box' ),
					'post',
					'normal',
					'default'
				);
			}
		}
	}

	public function localize_pointers() {
		?>
		<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' );  // WPCS: XSS ok. ?>';
		</script>
		<?php
	}

	/**
	 * Embed pointers
	 *
	 * @since 1.5
	 */
	public function embed_pointers() {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	/**
	 * Display pointers modal
	 *
	 * @since 1.5
	 */
	public function add_pointers() {
		// Get dismissed pointers
		$get_dismissed = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
		$dismissed = explode( ',', (string) $get_dismissed );

		// Check if Forminator pointer dismissed
		if( in_array( 'forminator-pointer', $dismissed, true ) ) return;

		$pointer_content = '<h3>' . esc_html__( 'Create Forms', Forminator::DOMAIN ) . '</h3>';
		$pointer_content .= '<p>' . esc_html__( 'Start adding new forms, polls and quizzes here.', Forminator::DOMAIN ) . '</p>';
		?>
		<script type="text/javascript">
			var wpPointerL10n = {
				'dismiss': '<?php esc_html_e( 'Dismiss', Forminator::DOMAIN ); ?>'
			};

			//<![CDATA[
			jQuery(document).ready( function($) {
				// jQuery selector to point to
				jQuery( '#toplevel_page_forminator' ).pointer({
					content: '<?php echo $pointer_content; // WPCS: XSS ok. ?>',
					position: 'left',
					close: $.proxy(function () {
						$.post(ajaxurl, this);
					}, {
						pointer: 'forminator-pointer',
						action: 'dismiss-wp-pointer'
					})
				}).pointer('open');
			});
			//]]>
		</script>
		<?php
	}

	/**
	 * Render Meta box
	 *
	 * @since 1.0
	 */
	public function render_post_meta_box( $post ) {
		$meta_values = get_post_custom( $post->ID );
		?>
        <table class="widefat">
            <tbody>
			<?php
			foreach ( $meta_values as $key => $value ) {
				if ( '_' === $key[0] ) {
					continue;
				}
				$value = maybe_unserialize( $value[0] );
				?>
                <tr>
                    <th><?php echo esc_html( $value['label'] ); ?></th>
                    <td><?php echo esc_html( $value['value'] ); ?></td>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
		<?php
	}
}
