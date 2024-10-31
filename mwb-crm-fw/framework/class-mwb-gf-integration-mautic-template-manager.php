<?php
/**
 * The core plugin templates are handled here.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/framework
 * @author     MakeWebBetter <https://makewebbetter.com>
 */

/**
 * Template manager class, handles plugin templates.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/framework
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_Mautic_Template_Manager {

	/**
	 * Current crm slug.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $crm_slug    The current crm slug.
	 */
	public $crm_slug;

	/**
	 * Current crm name.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string   $crm_name    The current crm name.
	 */
	public $crm_name;

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

		// Initialise CRM name and slug.
		$this->crm_slug = get_current_crm_mautic( 'slug' );
		$this->crm_name = get_current_crm_mautic();

		// Initialise Connect manager class.
		$this->connect         = 'Mwb_Gf_Integration_Connect_' . $this->crm_name . '_Framework';
		$this->connect_manager = $this->connect::get_instance();

		$this->admin = 'Mwb_Gf_Integration_With_' . $this->crm_name . '_Admin';
	}

	/**
	 * Add a header panel for all screens in plugin.
	 * Returns :: HTML
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function render_navigation_tab() {

		if ( $this->admin::pro_dependency_check() ) {
			$pro_main   = 'Gf_Integration_With_' . $this->crm_name;
			$license    = $pro_main::$mwb_gf_pro_license_cb;
			$ini_days   = $pro_main::$mwb_gf_pro_ini_license_cb;
			$days_count = $pro_main::$ini_days();

			if ( $pro_main::$license() || 0 <= $days_count ) {
				if ( ! $pro_main::$license() && 0 <= $days_count ) {

					$warning = floor( $days_count );

					/* translators: %s is replaced with "days remaining" */
					$day_string = sprintf( _n( '%s day', '%s days', $warning, 'mwb-gf-integration-with-mautic' ), number_format_i18n( $warning ) );
					?>
					<div id="mwb-sf-gf-thirty-days-notify" class="notice notice-warning mwb-notice">
						<p>
							<strong><a href="<?php echo esc_url( admin_url( 'admin.php' ) . '?page=mwb_' . $this->crm_slug . '_gf_page&tab=license' ); ?>">

							<!-- License warning. -->
							<?php esc_html_e( 'Activate', 'mwb-gf-integration-with-mautic' ); ?></a>
							<?php
							/* translators: %s is replaced with "days remaining" */
							printf( esc_html__( ' the license key before %s or you may risk losing data and the plugin will also become dysfunctional.', 'mwb-gf-integration-with-mautic' ), '<span id="mwb-sf-gf-day-count" >' . esc_html( $day_string ) . '</span>' );
							?>
							</strong>
						</p>
					</div>
					<?php
				}
			} else {
				?>
				<div id="mwb-sf-gf-thirty-days-notify" class="notice notice-warning mwb-notice">
					<p>
						<strong>
							<?php esc_html_e( 'Trail expried !! ', 'mwb-gf-integration-with-mautic' ); ?></a>
							<a href="<?php echo esc_url( admin_url( 'admin.php' ) . '?page=mwb_' . $this->crm_slug . '_gf_page&tab=license' ); ?>">

							<!-- License warning. -->
							<?php esc_html_e( 'Activate', 'mwb-gf-integration-with-mautic' ); ?></a>
							<?php
							/* translators: %s is replaced with "days remaining" */
							printf( esc_html__( ' your license and continue enjoying the pro version features.', 'mwb-gf-integration-with-mautic' ) );
							?>
						</strong>
					</p>
				</div>
				<?php
			}
			$this->get_nav_tabs();

		} else {
			$this->get_nav_tabs();
		}

	}

	/**
	 * Get navigaton tabs.
	 *
	 * @since     1.0.1
	 * @return    void
	 */
	public function get_nav_tabs() {

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'accounts'; // phpcs:ignore

		/* Tabs section start. */
		?>
		<nav class="mwb-sf-gf-navbar">
			<div class="mwb-sf-gf-nav-collapse">
				<ul class="mwb-sf-gf-nav mwb-sf-gf-nav-tabs" role="tablist">
					<?php $tabs = $this->retrieve_nav_tabs(); ?>
					<?php if ( ! empty( $tabs ) && is_array( $tabs ) ) : ?>
						<?php foreach ( $tabs as $href => $label ) : ?>
							<li class="mwb-sf-gf-nav-item">
								<a class="mwb-sf-gf-nav-link nav-tab <?php echo esc_html( $active_tab == $href ? 'nav-tab-active' : '' ); // phpcs:ignore ?>" href="?page=mwb_<?php echo esc_html( $this->crm_slug ); ?>_gf_page&tab=<?php echo esc_html( $href ); ?>"><?php echo esc_html( $label ); ?></a>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</nav>

		<?php
		/* Tabs section end */

		switch ( $active_tab ) {

			case 'accounts':
				$params = array();

				$params['is_auth']      = get_option( 'mwb-gf-' . $this->crm_slug . '-authorised' );
				$params['issue_time']   = $this->connect_manager->get_crm_token_details( 'issue_time' );
				$params['instance_url'] = $this->connect_manager->get_crm_token_details( 'instance_url' );
				$params['count']        = get_option( 'mwb-' . $this->crm_slug . '-gf-synced-forms-count' );
				$params['links']        = $this->add_plugin_links();
				$this->load_template( 'accounts', $params );
				break;

			case 'feeds':
				$params = array();

				$params['gf_edit_forms'] = $this->connect_manager->get_available_gf_forms();
				$params['feeds']         = $this->connect_manager->get_available_mautic_feeds();
				$params['feed_class']    = 'Mwb_Gf_Integration_' . $this->crm_name . '_Feed_Module';
				$this->load_template( 'feeds', $params );
				break;

			case 'logs':
				$params               = array();
				$params['log_enable'] = $this->connect_manager->get_settings_details( 'logs' );
				$this->load_template( 'logs', $params );
				break;

			case 'settings':
				$params = array();

				$params['option']   = get_option( 'mwb-' . $this->crm_slug . '-gf-setting', $this->connect_manager->get_plugin_default_settings() );
				$params['response'] = get_option( 'mwb-' . $this->crm_slug . '-gf-settings-response' );
				$this->load_template( 'settings', $params );
				break;

			case 'license':
				$params = array();
				$this->load_template( 'license', $params );
				break;

			default:
				'';

		}
	}

	/**
	 * Render authorisation screen.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function render_authorisation_screen() {
		$params         = array();
		$api_class      = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api_Base';
		$crm_api_module = $api_class::get_instance();
		$api_module     = $crm_api_module->get_crm_objects();

		$params['api_modules']     = implode( ', ', $api_module );
		$params['consumer_key']    = get_option( 'mwb-' . $this->crm_slug . '-gf-consumer-key' );
		$params['consumer_secret'] = get_option( 'mwb-' . $this->crm_slug . '-gf-consumer-secret' );
		$params['auth_type']       = get_option( 'mwb-' . $this->crm_slug . '-gf-auth_type' );
		$params['redirect_url']    = get_option( 'mwb-' . $this->crm_slug . '-gf-redirect-url' );
		$params['username']        = get_option( 'mwb-' . $this->crm_slug . '-gf-username' );
		$params['password']        = get_option( 'mwb-' . $this->crm_slug . '-gf-password' );
		$params['connection']      = get_option( 'mwb-' . $this->crm_slug . '-gf-connection-data' );
		$this->load_template( 'authorisation', $params );
	}

	/**
	 * Get all nav tabs of current screen.
	 *
	 * @since     1.0.0
	 * @return    array   An array of screen tabs.
	 */
	public function retrieve_nav_tabs() {

		$current_screen = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : false; // phpcs:ignore

		$tabs = '';

		switch ( $current_screen ) {

			case 'mwb_' . $this->crm_slug . '_gf_page':
				$tabs = array(
					'accounts' => esc_html__( 'Dashboard', 'mwb-gf-integration-with-mautic' ),
					'feeds'    => esc_html__( 'Feeds', 'mwb-gf-integration-with-mautic' ),
					'logs'     => esc_html__( 'Logs', 'mwb-gf-integration-with-mautic' ),
					'settings' => esc_html__( 'Settings', 'mwb-gf-integration-with-mautic' ),
				);
				break;
		}

		return apply_filters( $current_screen . '_tab', $tabs );
	}

	/**
	 * Loads plugin templates.
	 *
	 * @param     string $template     Name of the template.
	 * @param     array  $params       Parameters to pass to template.
	 * @since     1.0.0
	 * @return    void
	 */
	protected function load_template( $template = '', $params = array() ) {

		if ( empty( $template ) ) {
			return;
		}

		if ( 'license' == $template ) { // phpcs:ignore
			if ( $this->admin::pro_dependency_check() ) {
				require_once GF_INTEGRATION_WITH_MAUTIC_DIRPATH . 'admin/partials/templates/license-tab.php';
			}
		} else {
			$params['admin_class'] = $this->admin;
			$params['crm_slug']    = $this->crm_slug;
			$params['crm_name']    = $this->crm_name;

			$path = 'templates/tab-contents/' . $template . '-tab.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . $path;
		}
	}

	/**
	 * Get plugin important links.
	 *
	 * @since   1.0.0
	 * @return  array
	 */
	public function add_plugin_links() {

		$links = array(
			'doc'     => 'https://docs.makewebbetter.com/mwb-gf-integration-with-mautic/?utm_source=MWB-gfmautic-org&utm_medium=MWB-org-backend&utm_campaign=doc',
			'ticket'  => 'https://support.makewebbetter.com/wordpress-plugins-knowledge-base/category/mwb-gf-integration-with-mautic/?utm_source=MWB-gfmautic-org&utm_medium=MWB-org-backend&utm_campaign=support',
			'contact' => 'https://makewebbetter.com/contact-us/?utm_source=MWB-gfmautic-org&utm_medium=MWB-org-backend&utm_campaign=contactus',
		);

		return apply_filters( 'mwb_' . $this->crm_slug . '_gf_plugin_links', $links );
	}

	/**
	 * Get individual field mapping section.
	 *
	 * @param    array $field_options             GF field mapping options.
	 * @param    array $fields_data               CRM fields.
	 * @param    array $default_data              Default mapping data.
	 * @param    array $require_status_mapping    Status mapping.
	 * @param    array $require_pricebook_mapping Pricebook mapping.
	 * @since    1.0.0
	 * @return   void
	 */
	public static function get_field_section_html(
		$field_options,
		$fields_data,
		$default_data = array(),
		$require_status_mapping = false,
		$require_pricebook_mapping = false ) {

		if ( empty( $default_data ) ) {

			$default_data = array(
				'field_type'  => 'standard_field',
				'field_value' => '',
			);
		}
		$row_stndrd   = ( 'standard_field' === $default_data['field_type'] ) ? '' : 'row-hide';
		$row_custom   = ( 'custom_value' === $default_data['field_type'] ) ? '' : 'row-hide';
		$custom_value = ! empty( $default_data['custom_value'] ) ? $default_data['custom_value'] : '';
		$field_value  = ! empty( $default_data['field_value'] ) ? $default_data['field_value'] : '';

		$selected_pricebook = get_option( 'mwb_woo_mautic_selected_pricebook', '' );
		?>
		<div class="mwb-feeds__form-wrap mwb-fields-form-row">
				<div class="mwb-form-wrapper">
					<div class="mwb-fields-form-section-head">
						<span class="field-label-txt"><?php echo esc_html( $fields_data['label'] ); ?></span>
						<input type="hidden" class="crm-field-name" name="crm_field[]" value="<?php echo esc_html( $fields_data['alias'] ); ?>">
						<?php if ( ! $fields_data['mandatory'] ) : ?>
						<span class="field-delete dashicons">
							<img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/trash.svg' ); ?>" id="feed_section_trash_icon">
						</span>
						<?php elseif ( 'email' === $fields_data['alias'] ) : ?>
							<?php if ( ! empty( $default_data['field_value'] ) ) { ?>
								<span class="field-delete dashicons">
									<img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/trash.svg' ); ?>" id="feed_section_trash_icon">
								</span>
							<?php } ?>
						<?php endif; ?>
					</div>
					<div class="mwb-fields-form-section-meta">
						<span>
							<?php esc_html_e( 'Field Alias : ', 'mwb-gf-integration-with-mautic' ); ?><?php echo esc_html( $fields_data['alias'] ); ?>
						</span>
						<span>
							<?php esc_html_e( 'Type : ', 'mwb-gf-integration-with-mautic' ); ?><?php echo esc_html( $fields_data['type'] ); ?> 
						</span>
						<?php if ( ! empty( $fields_data['picklistValues'] ) ) : ?>
						<span>
							<?php esc_html_e( 'Picklist Values : ', 'mwb-gf-integration-with-mautic' ); ?><?php echo esc_html( implode( ', ', array_column( $fields_data['picklistValues'], 'label' ) ) ); ?> 
						</span>
						<?php endif; ?>	
					</div>
					<div class="mwb-fields-form-section-form">
						<div class="form-field-row row-field-type">
							<label><?php esc_html_e( 'Field Type', 'mwb-gf-integration-with-mautic' ); ?></label>
							<select class="field-type-select" name="field_type[]">
								<option value=""><?php esc_html_e( 'Select an Option', 'mwb-gf-integration-with-mautic' ); ?></option>
								<option value="standard_field" <?php echo esc_attr( selected( 'standard_field', $default_data['field_type'] ) ); ?> >
								<?php esc_html_e( 'Standard Value', 'mwb-gf-integration-with-mautic' ); ?>
								</option>
								<?php if ( ! $require_pricebook_mapping ) : ?>
								<option value="custom_value" <?php echo esc_attr( selected( 'custom_value', $default_data['field_type'] ) ); ?>>
									<?php esc_html_e( 'Custom Value', 'mwb-gf-integration-with-mautic' ); ?>		
								</option>
								<?php endif; ?>
							</select>
						</div>
						<div class="form-field-row row-field-value row-standard_field <?php echo esc_attr( $row_stndrd ); ?>">
							<label><?php esc_html_e( 'Field Value', 'mwb-gf-integration-with-mautic' ); ?></label>
							<select class="field-value-select" name="field_value[]">
								<option value=""><?php esc_html_e( 'Select an Option', 'mwb-gf-integration-with-mautic' ); ?></option>
								<?php if ( ! $require_pricebook_mapping ) : ?>
									<?php foreach ( $field_options as $k1 => $options ) : ?>
										<optgroup label="<?php echo esc_attr( ucfirst( str_replace( '_', ' ', $k1 ) ) ); ?>">
										<?php foreach ( $options as $k2 => $name ) : ?>
											<option value="<?php echo esc_attr( $k1 . '_' . $k2 ); ?>" <?php echo esc_attr( selected( $k1 . '_' . $k2, $field_value ) ); ?>>
												<?php echo esc_html( $name ); ?>
											</option>
										<?php endforeach; ?>
										</optgroup>
									<?php endforeach; ?>
								<?php else : ?>
									<?php foreach ( $field_options as $k2 => $name ) : ?>
										<option value="<?php echo esc_attr( $name['Id'] ); ?>" <?php echo esc_attr( selected( $selected_pricebook, $name['Id'] ) ); ?>>
											<?php echo esc_html( $name['Name'] ); ?>
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>
						<div class="form-field-row row-custom_value row-field-value <?php echo esc_attr( $row_custom ); ?>">
							<label>
							<?php
							esc_html_e( 'Custom Value', 'mwb-gf-integration-with-mautic' );
							?>
							</label>
							<input type="text" class="custom-value-input" name="custom_value[]" value="<?php echo esc_attr( $custom_value ); ?>">
							<select class="custom-value-select" name="custom_field[]">
								<option value=""><?php esc_html_e( 'Select an Option', 'mwb-gf-integration-with-mautic' ); ?></option>
								<?php foreach ( $field_options as $k1 => $options ) : ?>
									<optgroup label="<?php echo esc_attr( ucfirst( str_replace( '_', ' ', $k1 ) ) ); ?>">
									<?php foreach ( $options as $k2 => $name ) : ?>
										<option value="<?php echo esc_attr( $k1 . '_' . $k2 ); ?>">
											<?php echo esc_html( $name ); ?>
										</option>
									<?php endforeach; ?>
									</optgroup>
								<?php endforeach; ?>
							</select>
						</div>
						<?php if ( isset( $default_data['use_status_mapping'] ) && $require_status_mapping ) : ?>
							<div class="form-field-row">
								<label><?php esc_html_e( 'Map with woo order statuses', 'mwb-gf-integration-with-mautic' ); ?></label>
								<input <?php echo esc_attr( checked( 'yes', $default_data['use_status_mapping'] ) ); ?> type="checkbox" class="use-status-mapping-cb" value="yes" name="use_status_mapping[<?php echo esc_attr( $fields_data['name'] ); ?>]">
							</div>	
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Feeds Condtional html.
	 *
	 * @param     string $and_condition  The and condition of current html.
	 * @param     string $and_index      The and offset of current html.
	 * @param     string $or_index       The or offset of current html.
	 * @since     1.0.0
	 * @return    mixed
	 */
	public function render_and_conditon( $and_condition = array(), $and_index = '1', $or_index = '' ) {

		if ( empty( $and_index ) || empty( $and_condition ) || empty( $or_index ) ) {
			return;
		}

		?>
		<div class="and-condition-filter" data-and-index=<?php echo esc_attr( $and_index ); ?> >
			<select name="condition[<?php echo esc_html( $or_index ); ?>][<?php echo esc_html( $and_index ); ?>][field]"  class="condition-form-field">
				<option value="-1" ><?php esc_html_e( 'Select Field', 'mwb-gf-integration-with-mautic' ); ?></option>
				<?php foreach ( $and_condition['form'] as $key => $value ) : ?>
					<optgroup label="<?php echo esc_html( $key ); ?>" >
						<?php foreach ( $value as $index => $field ) : ?>
							<option value="<?php echo esc_html( $index ); ?>" <?php selected( $and_condition['field'], $index ); ?> ><?php echo esc_html( $field ); ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
			<select name="condition[<?php echo esc_html( $or_index ); ?>][<?php echo esc_html( $and_index ); ?>][option]" class="condition-option-field">
				<option value="-1"><?php esc_html_e( 'Select Condition', 'mwb-gf-integration-with-mautic' ); ?></option>
				<?php foreach ( $this->connect_manager->get_avialable_form_filters() as $key => $value ) : ?>
					<option value="<?php echo esc_html( $key ); ?>" <?php selected( $and_condition['option'], $key ); ?>><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="text" name="condition[<?php echo esc_html( $or_index ); ?>][<?php echo esc_html( $and_index ); ?>][value]" class="condition-value-field" value="<?php echo esc_html( ! empty( $and_condition['value'] ) ? $and_condition['value'] : '' ); ?>" placeholder="<?php esc_html_e( 'Enter value', 'mwb-gf-integration-with-mautic' ); ?>" >
			<?php if ( 1 != $and_index ) : // @codingStandardsIgnoreLine ?>
				<span class="dashicons dashicons-no"></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Get feeds filter html.
	 *
	 * @param     object $feed    Feed object.
	 * @since     1.0.0
	 * @return    mixed
	 */
	public function get_filter_section_html( $feed ) {

		$feed_class  = 'Mwb_Gf_Integration_' . $this->crm_name . '_Feed_Module';
		$feed_module = $feed_class::get_instance();

		if ( ! empty( $feed ) ) {
			$_status        = get_post_status( $feed->ID );
			$edit_link      = get_edit_post_link( $feed->ID );
			$gf_from        = $feed_module->fetch_feed_data( $feed->ID, 'mwb-' . $this->crm_slug . '-gf-form', '-' );
			$crm_object     = $feed_module->fetch_feed_data( $feed->ID, 'mwb-' . $this->crm_slug . '-gf-object', '-' );
			$primary_field  = $feed_module->fetch_feed_data( $feed->ID, 'mwb-' . $this->crm_slug . '-gf-primary-field', '-' );
			$checked        = 'publish' == $_status ? 'checked="checked"' : ''; // phpcs:ignore
			$filters        = $feed_module->fetch_feed_data( $feed->ID, 'mwb-' . $this->crm_slug . '-gf-condtion-field', '-' );
			$filter_applied = $feed_module->if_filter_applied( $filters );
			?>
			<li class="mwb-sf-gf__feed-row">
				<div class="mwb-sf-gf__left-col">
					<h3 class="mwb-about__list-item-heading"><?php echo esc_html( $feed->post_title ); ?></h3>
					<div class="mwb-feed-status__wrap">
						<p class="mwb-feed-status-text_<?php echo esc_attr( $feed->ID ); ?>" ><strong><?php echo esc_html( 'publish' === $_status ? 'Active' : 'Sandbox' ); ?></strong></p>
						<p><input type="checkbox" class="mwb-feed-status" value="publish" <?php echo esc_html( $checked ); ?> feed-id=<?php esc_attr( $feed->ID ); ?>></p>
					</div>
					<p>
						<span class="mwb-about__list-item-sub-heading"><?php esc_html_e( 'Form : ', 'mwb-gf-integration-with-mautic' ); ?></span>
						<span><?php echo esc_html( $gf_from ); ?></span>    
					</p>
					<p>
						<span class="mwb-about__list-item-sub-heading"><?php esc_html_e( 'Object : ', 'mwb-gf-integration-with-mautic' ); ?></span>
						<span><?php echo esc_html( $crm_object ); ?></span> 
					</p>
					<p>
						<span class="mwb-about__list-item-sub-heading"><?php esc_html_e( 'Conditions : ', 'mwb-gf-integration-with-mautic' ); ?></span>
						<span><?php echo esc_html( false == $filter_applied ? '-' : 'Applied' ); // phpcs:ignore ?></span> 
					</p>
				</div>
				<div class="mwb-sf-gf__right-col">
					<a href="<?php echo esc_url( $edit_link ); ?>"><img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/edit.svg' ); ?>" alt="<?php esc_html_e( 'Edit feed', 'mwb-gf-integration-with-mautic' ); ?>"></a>
					<div class="mwb-sf-gf__right-col1">
						<a href="javascript:void(0)" class="mwb_<?php echo esc_attr( $this->crm_slug ); ?>_gf__trash_feed" feed-id="<?php echo esc_html( $feed->ID ); ?>"><img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/trash.svg' ); ?>" alt="<?php esc_html_e( 'Trash feed', 'mwb-gf-integration-with-mautic' ); ?>"></span></a>
					</div>
				</div>
			</li>
			<?php
		}

	}

}

