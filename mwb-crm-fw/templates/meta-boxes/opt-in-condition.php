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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$prefilled_indexes = isset( $params['condition'] ) ? count( $params['condition'] ) : '';
?>
<div id="mwb-condition-filter-section-wrapper"  class="mwb-feeds__content  mwb-content-wrap">
	<a class="mwb-feeds__header-link active">
		<?php esc_html_e( 'Condition Filter', 'mwb-gf-integration-with-mautic' ); ?>
	</a>

	<div class="mwb-feeds__meta-box-main-wrapper">
		<div class="mwb-feeds__meta-box-wrap">
			<div class="mwb-form-wrapper  mwb-form-filter-wrapper">
				<div class="mwb-initial-filter">
					<?php if ( ! empty( $params['condition'] ) && is_array( $params['condition'] ) ) : ?>
						<?php foreach ( $params['condition'] as $or_index => $and_conditions ) : ?>
							<div class="or-condition-filter" data-or-index="<?php echo esc_html( $or_index ); ?>">
								<div class="mwb-form-filter-row">
									<?php foreach ( $and_conditions as $and_index => $and_condition ) : ?>
										<?php
										$and_condition['form'] = $params['fields'];
										$template_manager      = new Mwb_Gf_Integration_Mautic_Template_Manager();
										$template_manager->render_and_conditon( $and_condition, $and_index, $or_index );
										?>
									<?php endforeach; ?>
									<button data-next-and-index="<?php echo esc_html( ++$and_index ); ?>" data-or-index="<?php echo esc_html( $or_index ); ?>" class="button condition-and-btn"><?php esc_html_e( 'Add "AND" filter', 'mwb-gf-integration-with-mautic' ); ?></button>
									<?php if ( 1 != $prefilled_indexes ) :  // phpcs:ignore ?>
										<img src="<?php echo esc_url( MWB_GF_INTEGRATION_WITH_MAUTIC_URL . 'admin/images/trash.svg' ); ?>" id="feed_section_trash_icon" class="dashicons-trash" alt="<?php esc_html_e( 'Trash', 'mwb-gf-integration-with-mautic' ); ?>">
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
					<button data-next-or-index="<?php echo esc_html( ++$prefilled_indexes ); ?>" class="button condition-or-btn"><?php esc_html_e( 'Add "OR" filter', 'mwb-gf-integration-with-mautic' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>


