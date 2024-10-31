<?php
/**
 * The complete management for the Mautic-GF feeds custom post type.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/framework
 * @author     MakeWebBetter <https://makewebbetter.com>
 */

/**
 * The complete management for the Mautic-GF feeds custom post type.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/framework
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_Mautic_Feed_Module {


	/**
	 * Current crm slug.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $crm_slug    The current crm slug.
	 */
	private $crm_slug;

	/**
	 * Current crm name.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @var       string   $crm_name    The current crm name.
	 */
	private $crm_name;

	/**
	 * Feed CPT name/slug.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $feed_name    Feeds CPT name/slug.
	 */
	private $feed_name;

	/**
	 * Instance of the current class.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var     object    $instance    Instance of the current class.
	 */
	protected static $_instance = null; // phpcs:ignore

	/**
	 * Connect manager class name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $connect   Name of the Connect manager class.
	 */
	private $connect;

	/**
	 * Instance of Connect manager class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $connect_manager  Instance of the Connect manager class.
	 */
	private $connect_manager;

	/**
	 * Instance of Admin class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $admin  Instance of the Admin class.
	 */
	private $admin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		self::$_instance = $this;

		// Initialise CRM name and slug.
		$this->crm_slug = get_current_crm_mautic( 'slug' );
		$this->crm_name = get_current_crm_mautic();

		// Initialise Feed CPT name/slug.
		$this->feed_name = 'mwb_' . $this->crm_slug . '_gf';

		// Initialise Connect manager class.
		$this->connect         = 'Mwb_Gf_Integration_Connect_' . $this->crm_name . '_Framework';
		$this->connect_manager = $this->connect::get_instance();

		// Initialise admin class.
		$this->admin = 'Mwb_Gf_Integration_With_' . $this->crm_name . '_Admin';

	}

	/**
	 * Main Mwb_Gf_Integration_Mautic_Feed_Module Instance.
	 *
	 * Ensures only one instance of Mwb_Gf_Integration_Mautic_Feed_Module is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @return Mwb_Gf_Integration_Mautic_Feed_Module - Main instance.
	 */
	public static function get_instance() {

		if ( null == self::$_instance ) { //phpcs:ignore

			self::$_instance = new self();
		}
		return self::$_instance;
	}



	/**
	 * Register custom post type for feeds.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function register_feeds_post_type() {

		// Set UI labels for Custom Post Type.
		$labels = array(
			'name'               => _x( 'Feeds', 'Post Type General Name', 'mwb-gf-integration-with-mautic' ),
			'singular_name'      => _x( 'Feed', 'Post Type Singular Name', 'mwb-gf-integration-with-mautic' ),
			'menu_name'          => _x( 'Crm Feeds', 'Admin menu name', 'mwb-gf-integration-with-mautic' ),
			'parent_item_colon'  => __( 'Parent Feed', 'mwb-gf-integration-with-mautic' ),
			'all_items'          => __( 'All Feeds', 'mwb-gf-integration-with-mautic' ),
			'view_item'          => __( 'View Feed', 'mwb-gf-integration-with-mautic' ),
			'add_new_item'       => __( 'Add New Feed', 'mwb-gf-integration-with-mautic' ),
			'add_new'            => __( 'Add New', 'mwb-gf-integration-with-mautic' ),
			'edit_item'          => __( 'Edit Feed', 'mwb-gf-integration-with-mautic' ),
			'update_item'        => __( 'Update Feed', 'mwb-gf-integration-with-mautic' ),
			'search_items'       => __( 'Search Feed', 'mwb-gf-integration-with-mautic' ),
			'not_found'          => __( 'Not Found', 'mwb-gf-integration-with-mautic' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'mwb-gf-integration-with-mautic' ),
		);

		$args = array(
			'label'                => __( 'Feeds', 'mwb-gf-integration-with-mautic' ),
			'description'          => __( 'Feeds for crm', 'mwb-gf-integration-with-mautic' ),
			'labels'               => $labels,
			'supports'             => array( 'title' ),
			'hierarchical'         => false,
			'public'               => true,
			'menu_position'        => 5,
			'can_export'           => true,
			'has_archive'          => true,
			'show_in_rest'         => true,
			'exclude_from_search'  => false,
			'publicly_queryable'   => false,
			'show_in_menu'         => false,
			'show_in_nav_menus'    => false,
			'show_in_admin_bar'    => false,
			'register_meta_box_cb' => array( $this, 'add_feed_meta_box' ),
		);

		register_post_type( $this->feed_name, $args );
	}

	/**
	 * Callback :: Add feeds metabox.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function add_feed_meta_box() {

		global $post;
		$metaboxes = array();

		$metaboxes[] = array(
			'slug'     => 'mwb_' . $this->crm_slug . '_gf_feeds_meta_box',
			'title'    => esc_html__( 'Feed details', 'mwb-gf-integration-with-mautic' ),
			'callback' => 'feeds_mb_render',
			'screen'   => $this->feed_name,
			'context'  => '',
			'priority' => '',
		);

		$metaboxes[] = array(
			'slug'     => 'mwb_' . $this->crm_slug . '_gf_feeds_condition_meta_box',
			'title'    => esc_html__( 'Conditional Statements', 'mwb-gf-integration-with-mautic' ),
			'callback' => 'feeds_mb_condition_render',
			'screen'   => $this->feed_name,
			'context'  => '',
			'priority' => '',
		);

		$metaboxes = apply_filters( 'mwb_' . $this->crm_slug . '_gf_feeds_pro_mb', $metaboxes );

		foreach ( $metaboxes as $key => $metabox ) {

			if ( class_exists( 'Gf_Integration_With_' . $this->crm_name . '_Admin' ) && ! method_exists( $this, $metabox['callback'] ) ) {
				$metabox_content = '';
			} else {
				$metabox_content = '';
			}

			add_meta_box(
				$metabox['slug'],
				$metabox['title'],
				! empty( $metabox_content ) ? $metabox_content : array( $this, $metabox['callback'] ),
				$metabox['screen'],
				! empty( $metabox['context'] ) ? $metabox['context'] : 'advanced',
				! empty( $metabox['priority'] ) ? $metabox['priority'] : 'default'
			);

		}

	}

	/**
	 * Callback :: Post type feeds mapping metabox.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function feeds_mb_render() {

		global $post;
		$params                    = array();
		$params['objects']         = $this->connect_manager->get_available_crm_objects();
		$params['forms']           = $this->connect_manager->get_available_gf_forms();
		$params['selected_form']   = $this->fetch_feed_data( $post->ID, 'mwb-' . $this->crm_slug . '-gf-form', '' );
		$params['selected_object'] = $this->fetch_feed_data( $post->ID, 'mwb-' . $this->crm_slug . '-gf-object', '' );

		$this->render_mb_data( 'header' );
		$this->render_mb_data( 'select-form', $params );
		$this->render_mb_data( 'select-object', $params );
		$this->render_mb_data( 'footer' );
	}

	/**
	 * Callback :: Post type feeds conditional filter metabox.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function feeds_mb_condition_render() {
		global $post;
		$params  = array();
		$form_id = 0;
		$form_title          = $this->fetch_feed_data( $post->ID, 'mwb-' . $this->crm_slug . '-gf-form', '' );
		$params['condition'] = $this->fetch_feed_data( $post->ID, 'mwb-' . $this->crm_slug . '-gf-condtion-field', array() );
		$forms_list          = GFAPI::get_forms();
		if ( ! empty( $forms_list ) ) {
			foreach ( $forms_list as $k => $v ) {
				if ( ! empty( $v['title'] ) ) {
					if ( $form_title === $v['title'] ) {
						if ( ! empty( $v['fields'] ) ) {
							$form_id = $v['fields'][0]->formId;
							break;
						}
					}
				}
			}
		}
		$params['fields'] = $this->connect_manager->getMappingDataset( $form_id );
		$this->render_mb_data( 'opt-in-condition', $params );
	}

	/**
	 * Render html and data.
	 *
	 * @param     string $meta_box    Name of the meta box.
	 * @param     array  $params      An array of metabox params.
	 * @since     1.0.0
	 * @return    void
	 */
	private function render_mb_data( $meta_box = false, $params = array() ) {

		if ( empty( $meta_box ) ) {
			return;
		}
		$params['crm_slug']    = $this->crm_slug;
		$params['crm_name']    = $this->crm_name;
		$params['admin_class'] = 'Mwb_Gf_Integration_With_' . $this->crm_name . '_Admin';

		$path = 'templates/meta-boxes/' . $meta_box . '.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . $path;
	}

	/**
	 * Fetch feeds data.
	 *
	 * @param      int    $post_id          Feed ID.
	 * @param      string $key              Data key.
	 * @param      string $default          Default value.
	 * @param      mixed  $selected_object  Chosen value.
	 * @since      1.0.0
	 * @return     mixed
	 */
	public function fetch_feed_data( $post_id, $key, $default, $selected_object = '' ) {

		if ( ! $post_id ) {
			$method = 'get_default_' . $key;
			if ( method_exists( $this, $method ) && '' != $selected_object ) { // phpcs:ignore
				$default = $this->$method( $selected_object );
			}
			return $default;
		}
		$feed_data = get_post_meta( $post_id, $key, true );
		$feed_data = ! empty( $feed_data ) ? $feed_data : $default;
		return $feed_data;
	}




	/**
	 * Save feeds data.
	 *
	 * @param     int $post_id    Feed ID.
	 * @since     1.0.0
	 * @return    void
	 */
	public function save_feeds_data( $post_id ) {

		if ( ! isset( $_POST['_wpnonce'] ) ) {
			return;
		}

		if ( ! isset( $_POST['meta_box_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['meta_box_nonce'] ) ), 'meta_box_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['post_type'] ) && $this->feed_name == $_POST['post_type'] ) { // phpcs:ignore

			$crm_form         = isset( $_POST['crm_form'] ) ? sanitize_text_field( wp_unslash( $_POST['crm_form'] ) ) : '';
			$crm_object       = isset( $_POST['crm_object'] ) ? sanitize_text_field( wp_unslash( $_POST['crm_object'] ) ) : '';
			$crm_field_arr    = isset( $_POST['crm_field'] ) ? map_deep( wp_unslash( $_POST['crm_field'] ), 'sanitize_text_field' ) : array();
			$field_type_arr   = isset( $_POST['field_type'] ) ? map_deep( wp_unslash( $_POST['field_type'] ), 'sanitize_text_field' ) : array();
			$field_value_arr  = isset( $_POST['field_value'] ) ? map_deep( wp_unslash( $_POST['field_value'] ), 'sanitize_text_field' ) : array();
			$custom_value_arr = isset( $_POST['custom_value'] ) ? map_deep( wp_unslash( $_POST['custom_value'] ), 'sanitize_text_field' ) : array();
			$custom_field_arr = isset( $_POST['custom_field'] ) ? map_deep( wp_unslash( $_POST['custom_field'] ), 'sanitize_text_field' ) : array();
			$condition        = isset( $_POST['condition'] ) ? map_deep( wp_unslash( $_POST['condition'] ), 'sanitize_text_field' ) : array();
			$primary_field    = isset( $_POST['primary_field'] ) ? sanitize_text_field( wp_unslash( $_POST['primary_field'] ) ) : '';

			$manaul_sync    = isset( $_POST['manual-data-sync'] ) ? sanitize_text_field( wp_unslash( $_POST['manual-data-sync'] ) ) : '';
			$note_field     = isset( $_POST['add_feed_note'] ) ? sanitize_text_field( wp_unslash( $_POST['add_feed_note'] ) ) : '';
			$note_field_sel = isset( $_POST['add-note-select'] ) ? sanitize_text_field( wp_unslash( $_POST['add-note-select'] ) ) : '';
			$create_account = isset( $_POST['enable-create-account'] ) ? sanitize_text_field( wp_unslash( $_POST['enable-create-account'] ) ) : '';
			$add_tag        = isset( $_POST['add-feed-tag'] ) ? sanitize_text_field( wp_unslash( $_POST['add-feed-tag'] ) ) : '';
			$inc_attachment = isset( $_POST['include-attachment'] ) ? sanitize_text_field( wp_unslash( $_POST['include-attachment'] ) ) : '';
			$account_name   = isset( $_POST['create-account-name'] ) ? sanitize_text_field( wp_unslash( $_POST['create-account-name'] ) ) : '';
			$exists_account = isset( $_POST['associate-contact-exists'] ) ? sanitize_text_field( wp_unslash( $_POST['associate-contact-exists'] ) ) : '';
			$mapping_data = array();

			if ( ! empty( $crm_field_arr ) && is_array( $crm_field_arr ) ) {
				foreach ( $crm_field_arr as $key => $field ) {
					$mapping_data[ $field ] = array(
						'field_type'   => $field_type_arr[ $key ],
						'field_value'  => $field_value_arr[ $key ],
						'custom_value' => $custom_value_arr[ $key ],
						'custom_field' => $custom_field_arr[ $key ],
					);
				}
			}

			update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-form', $crm_form );
			update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-object', $crm_object );
			update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-mapping-data', $mapping_data );
			update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-primary-field', $primary_field );
			update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-condtion-field', $condition );

			if ( $this->admin::pro_dependency_check() ) {
				update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-manual-sync', $manaul_sync );
				update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-add-note', $note_field );
				update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-create-account', $create_account );
				update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-account-name', $account_name );
				update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-associate-exists-accnt', $exists_account );
				update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-include-attachment', $inc_attachment );
				update_post_meta( $post_id, 'mwb-' . $this->crm_slug . '-gf-add-tag', $add_tag );
			}
		}
	}

	/**
	 * Create meta box section html via ajax.
	 *
	 * @param  string $meta_box Meta box key.
	 * @param  array  $params   Required params for meta box.
	 * @return string           Meta box section html.
	 */
	public function do_ajax_render( $meta_box, $params ) {

		if ( '' == $meta_box ) { // phpcs:ignore
			return;
		}

		ob_start();
		$this->render_mb_data( $meta_box, $params );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Check if filter applicable.
	 *
	 * @param   array $filters     Filters array.
	 * @since   1.0.0
	 * @return  bool.
	 */
	public function if_filter_applied( $filters = array() ) {

		if ( ! is_array( $filters ) ) {
			return;
		}

		foreach ( $filters as $or => $orvalues ) {
			foreach ( $orvalues as $index => $val ) {
				if ( '-1' == $val['field'] || '-1' == $val['option'] ) { // phpcs:ignore
					return false;
				} else {
					return true;
				}
			}
		}
	}

}
