<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/extra-templates
 * @author     MakeWebBetter <https://makewebbetter.com>
 */

$screen   = get_current_screen();
$is_valid = in_array( $screen->id, apply_filters( 'mwb_helper_valid_frontend_screens', array() ) ); // phpcs:ignore
if ( ! $is_valid ) {
	return false;
}

$form_fields = apply_filters( 'mwb_on_boarding_form_fields', array() );

?>

<?php if ( ! empty( $form_fields ) ) : ?>
	<div class="mwb-onboarding-section">
		<div class="mwb-on-boarding-wrapper-background">
		<div class="mwb-on-boarding-wrapper">
			<div class="mwb-on-boarding-close-btn">
				<a href="#">
					<span class="close-form">x</span>
				</a>
			</div>
			<h3 class="mwb-on-boarding-heading"><?php esc_html_e( 'Welcome to MakeWebBetter', 'mwb-gf-integration-with-mautic' ); ?> </h3>
			<p class="mwb-on-boarding-desc"><?php esc_html_e( 'We love making new friends! Subscribe below and we promise to keep you up-to-date with our latest new plugins, updates, awesome deals and a few special offers.', 'mwb-gf-integration-with-mautic' ); ?></p>
			<form action="#" method="post" class="mwb-on-boarding-form">
				<?php foreach ( $form_fields as $key => $field_attr ) : ?>
					<?php $this->render_field_html( $field_attr ); ?>
				<?php endforeach; ?> 
				<div class="mwb-on-boarding-form-btn__wrapper">
					<div class="mwb-on-boarding-form-submit mwb-on-boarding-form-verify ">
					<input type="submit" class="mwb-on-boarding-submit mwb-on-boarding-verify " value="<?php esc_html_e( 'Send Us', 'mwb-gf-integration-with-mautic' ); ?>">
				</div>
				<div class="mwb-on-boarding-form-no_thanks">
					<a href="#" class="mwb-on-boarding-no_thanks"><?php esc_html_e( 'Skip For Now', 'mwb-gf-integration-with-mautic' ); ?></a>
				</div>
				</div>
			</form>
		</div>
	</div>
	</div>
<?php endif; ?>
