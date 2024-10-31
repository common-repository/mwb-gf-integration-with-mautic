<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the select object section of feeds.
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
$objects = isset( $params['objects'] ) ? $params['objects'] : array();

?>
<div class="mwb-feeds__content  mwb-content-wrap  mwb-feed__select-object">
	<a class="mwb-feeds__header-link active">
		<?php esc_html_e( 'Select Object', 'mwb-gf-integration-with-mautic' ); ?>
	</a>

	<div class="mwb-feeds__meta-box-main-wrapper">
		<div class="mwb-feeds__meta-box-wrap">
			<div class="mwb-form-wrapper">
				<select name="crm_object" id="mwb-feeds-<?php echo esc_attr( $params['crm_slug'] ); ?>-object" class="mwb-form__dropdown">
					<option value="-1"><?php esc_html_e( 'Select Object', 'mwb-gf-integration-with-mautic' ); ?></option>
					<?php foreach ( $objects as $key => $object ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $params['selected_object'], $key ); ?> >
							<?php echo esc_html( $object ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="mwb-form-wrapper">
				<a id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>-refresh-object" class="button refresh-object"><?php esc_html_e( 'Refresh Objects', 'mwb-gf-integration-with-mautic' ); ?></a>
				<a id="mwb-<?php echo esc_attr( $params['crm_slug'] ); ?>-refresh-fields" class="button refresh-fields"><?php esc_html_e( 'Refresh Fields', 'mwb-gf-integration-with-mautic' ); ?></a>
			</div>
		</div>
	</div>
</div>
