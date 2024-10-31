<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the header of feeds section.
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
?>

<div class="mwb_<?php echo esc_attr( $params['crm_slug'] ); ?>_gf__feeds-wrap">
	<div class="mwb-sf_gf__logo-wrap">
		<div class="mwb-sf_gf__logo-mautic">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/mautic-logo.png' ); ?>" alt="<?php esc_html_e( 'Mautic', 'mwb-gf-integration-with-mautic' ); ?>">
		</div>
		<div class="mwb-sf_gf__logo-contact">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'GF', 'mwb-gf-integration-with-mautic' ); ?>">
		</div>
	</div>
