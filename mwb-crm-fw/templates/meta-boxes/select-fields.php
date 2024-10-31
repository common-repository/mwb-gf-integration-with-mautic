<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the select fields section of feeds.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/templates/meta-boxes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div id="mwb-fields-form-section-wrapper" class="mwb-feeds__content  mwb-content-wrap row-hide">
	<a class="mwb-feeds__header-link">
		<?php esc_html_e( 'Map Fields', 'mwb-gf-integration-with-mautic' ); ?>
	</a>
	<div id="mwb-fields-form-section" class="mwb-feeds__meta-box-main-wrapper">
	<?php
	$mapping_exists = ! empty( $params['mapping_data'] );
	if ( isset( $params['crm_fields'] ) ) {
		foreach ( $params['crm_fields'] as $key => $fields_data ) {

			$require_status_mapping    = false;
			$require_pricebook_mapping = false;
			$option_data               = $params['field_options'];

			$default_data = array(
				'field_type'  => 'standard_field',
				'field_value' => '',
			);

			if ( $mapping_exists ) {
				if ( ! array_key_exists( $fields_data['alias'], $params['mapping_data'] ) ) {
					continue;
				}
				$default_data = $params['mapping_data'][ $fields_data['alias'] ];

			} else {
				if ( ! $fields_data['mandatory'] ) {
					continue;
				}
			}

			Mwb_Gf_Integration_Mautic_Template_Manager::get_field_section_html(
				$option_data,
				$fields_data,
				$default_data,
				$require_status_mapping,
				$require_pricebook_mapping
			);
		}
	}
	?>
	</div>
</div>
