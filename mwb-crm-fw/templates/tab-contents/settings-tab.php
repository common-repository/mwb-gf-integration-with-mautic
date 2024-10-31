<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the settings page.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/mwb-crm-fw/templates/tab-contents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="mwb_sf_gf__account-wrap">

	<div class="mwb-sf_gf__logo-wrap">
		<div class="mwb-sf_gf__logo-mautic">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/mautic-logo.png' ); ?>" alt="<?php esc_html_e( 'Mautic', 'mwb-gf-integration-with-mautic' ); ?>">
		</div>
		<div class="mwb-sf_gf__logo-contact">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'GF', 'mwb-gf-integration-with-mautic' ); ?>">
		</div>
	</div>

	<?php
	if ( ! empty( $params['response'] ) && is_array( $params['response'] ) ) {

		if ( array_key_exists( 'email_error', $params['response'] ) ) {
			unset( $params['response']['db_response'] );
			$params['admin_class']::mwb_sf_gf_notices( $params['response']['email_error']['class'], $params['response']['email_error']['message'] );
		}

		if ( array_key_exists( 'log_delete_error', $params['response'] ) ) {
			unset( $params['response']['db_response'] );
			$params['admin_class']::mwb_sf_gf_notices( $params['response']['log_delete_error']['class'], $params['response']['log_delete_error']['message'] );
		}

		if ( array_key_exists( 'db_response', $params['response'] ) ) {
			$params['admin_class']::mwb_sf_gf_notices( $params['response']['db_response']['class'], $params['response']['db_response']['message'] );
		}

		if ( array_key_exists( 'error', $params['response'] ) ) {
			$params['admin_class']::mwb_sf_gf_notices( $params['response']['error']['class'], $params['response']['error']['message'] );
		}
		delete_option( 'mwb-' . $params['crm_slug'] . '-gf-settings-response' );
	}
	?>

	<form method="post" id="mwb_<?php echo esc_attr( $params['crm_slug'] ); ?>_gf_settings_form">
		<?php wp_nonce_field( 'mwb_' . $params['crm_slug'] . '_gf_setting', $params['crm_slug'] . '_gf_setting_nonce' ); ?>
		<div class="mwb_sf_gf_table_wrapper">
			<table class="mwb_sf_gf_table">
				<tbody>

					<!-- Enable logs start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Enable logs', 'mwb-gf-integration-with-mautic' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'Enable logging of all the form data to be sent over mautic', 'mwb-gf-integration-with-mautic' );
							echo esc_html( $params['admin_class']::mwb_sf_gf_tooltip( $desc ) );

							$enable_logs = ! empty( $params['option']['enable_logs'] ) ? sanitize_text_field( wp_unslash( $params['option']['enable_logs'] ) ) : '';
							?>
							<input type="checkbox" name="mwb_setting[enable_logs]" value="yes" <?php checked( 'yes', $enable_logs ); ?>>
						</td>
					</tr>
					<!-- Enable logs end-->

					<!-- Data delete start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Plugin Data', 'mwb-gf-integration-with-mautic' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'Enable to delete the plugin data after uninstallation of plugin', 'mwb-gf-integration-with-mautic' );
							echo esc_html( $params['admin_class']::mwb_sf_gf_tooltip( $desc ) );

							$data_delete = ! empty( $params['option']['data_delete'] ) ? sanitize_text_field( wp_unslash( $params['option']['data_delete'] ) ) : '';
							?>
							<input type="checkbox" name="mwb_setting[data_delete]" value="yes"  <?php checked( 'yes', $data_delete ); ?>>
						</td>
					</tr>
					<!-- data delete end -->

					<!-- Enable email notif start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Email notification', 'mwb-gf-integration-with-mautic' ); ?></label>
						</th>

						<td>
							<?php
								$desc = esc_html__( 'Enable email notification on errors', 'mwb-gf-integration-with-mautic' );
								echo esc_html( $params['admin_class']::mwb_sf_gf_tooltip( $desc ) );

								$enable_notif = ! empty( $params['option']['enable_notif'] ) ? sanitize_text_field( wp_unslash( $params['option']['enable_notif'] ) ) : '';
							?>
							<input type="checkbox" name="mwb_setting[enable_notif]" value="yes" <?php checked( 'yes', $enable_notif ); ?> >
						</td>
					</tr>
					<!-- Enable email notif end -->

					<!-- Email field start -->
					<tr >
						<th>
						</th>
						<td>
							<div id="mwb_<?php echo esc_attr( $params['crm_slug'] ); ?>_gf_email_notif" class="<?php echo esc_attr( ( 'yes' != $enable_notif ) ? 'is_hidden' : '' ); // phpcs:ignore ?>">
								<?php

									$email_notif = ! empty( $params['option']['email_notif'] ) ? sanitize_email( wp_unslash( $params['option']['email_notif'] ) ) : get_bloginfo( 'admin_email' );
								?>
								<input type="email" name="mwb_setting[email_notif]" value="<?php echo esc_html( $email_notif ); ?>" >
							</div>
						</td>
					</tr>	
					<!-- Email field end -->

					<!-- Delete logs start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Delete logs after N days', 'mwb-gf-integration-with-mautic' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'This will delete the logs data after N no. of days', 'mwb-gf-integration-with-mautic' );
							echo esc_html( $params['admin_class']::mwb_sf_gf_tooltip( $desc ) );

							$delete_logs = ! empty( $params['option']['delete_logs'] ) ? sanitize_text_field( wp_unslash( $params['option']['delete_logs'] ) ) : 7;
							?>
							<input type="number" name="mwb_setting[delete_logs]" min="7" maxlenght="4" step="1" pattern="[0-9]" value="<?php echo esc_html( $delete_logs ); ?>">
						</td>
					</tr>
					<!-- Delete logs end -->

					<?php do_action( 'mwb_' . $params['crm_slug'] . '_gf_add_settings' ); ?>

					<!-- Save settings start -->
					<tr>
						<th>
						</th>
						<td>
							<input type="submit" name="mwb_<?php echo esc_attr( $params['crm_slug'] ); ?>_gf_submit_setting" class="mwb_<?php echo esc_attr( $params['crm_slug'] ); ?>_gf_submit_setting" value="<?php esc_html_e( 'Save', 'mwb-gf-integration-with-mautic' ); ?>" >
						</td>
					</tr>
					<!-- Save settings end -->

				</tbody>
			</table>
		</div>
	</form>
</div>
