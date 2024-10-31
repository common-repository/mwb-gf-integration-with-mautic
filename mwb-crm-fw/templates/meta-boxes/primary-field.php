<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the primary field of feeds section.
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
<div id="mwb-primary-field-section-wrapper"  class="mwb-feeds__content  mwb-content-wrap row-hide">
	<a class="mwb-feeds__header-link">
		<?php esc_html_e( 'Primary Field', 'mwb-gf-integration-with-mautic' ); ?>
	</a>
	<div class="mwb-feeds__meta-box-main-wrapper">
		<div class="mwb-feeds__meta-box-wrap">
			<div class="mwb-form-wrapper">
				<select id="primary-field-select" name="primary_field">
					<option value=""><?php esc_html_e( 'Select an Option', 'mwb-gf-integration-with-mautic' ); ?></option>
					<?php $mapping_exists = ! empty( $params['mapping_data'] ); ?>
					<?php
					if ( isset( $params['crm_fields'] ) ) :
						foreach ( $params['crm_fields'] as $key => $fields_data ) :
							?>
							<?php

							if ( $mapping_exists ) {
								if ( ! array_key_exists( $fields_data['alias'], $params['mapping_data'] ) ) {
									continue;
								}
							}
							?>
						<option <?php selected( $params['primary_field'], $fields_data['alias'] ); ?>  value="<?php echo esc_attr( $fields_data['alias'] ); ?>"><?php echo esc_html( $fields_data['label'] ); ?></option>	
							<?php
						endforeach;
					endif;
					?>
				</select>
				<p class="mwb-description">
					<?php
					esc_html_e(
						'Please select a field which should be used as "primary key" to update an existing record. 
						In case of duplicate records',
						'mwb-gf-integration-with-mautic'
					);
					?>
				</p>
			</div>
		</div>
	</div>
</div>

