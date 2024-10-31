<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the select form section of feeds.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/templates/meta-boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$forms = isset( $params['forms'] ) ? $params['forms'] : array();

?>
<div class="mwb-feeds__content  mwb-content-wrap">
	<a class="mwb-feeds__header-link active">
		<?php esc_html_e( 'Select Form', 'mwb-gf-integration-with-mautic' ); ?>
	</a>

	<div class="mwb-feeds__meta-box-main-wrapper">
		<div class="mwb-feeds__meta-box-wrap">
			<div class="mwb-form-wrapper">
				<select name="crm_form" id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>-gf-select-form" class="mwb-form__dropdown">
					<option value="-1"><?php esc_html_e( 'Select Form', 'mwb-gf-integration-with-mautic' ); ?></option>
					<optgroup label="<?php esc_html_e( 'Gravity Form', 'mwb-gf-integration-with-mautic' ); ?>" ></optgroup>
					<?php if ( ! empty( $forms ) && is_array( $forms ) ) : ?>
						<?php foreach ( $forms as $key => $value ) : ?>
							<option value="<?php echo esc_html( $value['title'] ); ?>" <?php selected( $params['selected_form'], $value['title'] ); ?>><?php echo esc_html( $value['title'] ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
		</div>
	</div>
</div>
