<?php
/**
 * Provide a public-facing email template for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_With_Mautic
 * @subpackage Mwb_Gf_Integration_With_Mautic/public/partials
 */

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html charset=UTF-8" >
		<title><?php echo esc_html__( 'Error - ', 'mwb-gf-integration-with-mautic' ); ?><?php echo esc_html( ! empty( $data['title'] ) ? $data['title'] : '' ); ?></title>
	</head>
	<body>
		<table>
			<tr>
				<td id="error_email_heading">
				<?php echo esc_html__( 'Error - ', 'mwb-gf-integration-with-mautic' ); ?>
				<?php echo esc_html( $data['Title'] ); ?>
			</td>
			</tr>
			<tr>
				<td id="error_email_content">
					<table border="0" cellpadding="0" cellspacing="0" width="100%;">
						<tbody>    
							<?php foreach ( $data as $key => $value ) : ?>
								<?php if ( is_array( $value ) ) { ?>
									<?php foreach ( $value as $k => $v ) : ?>
										<?php if ( 'Logs' === $k ) : ?>
											<tr>
												<td class="email_subkey"><?php echo esc_html( $k ); ?></td>
												<td class="email_subvalue">
													<a href="<?php echo esc_url( $v ); ?>" target="_blank"><?php echo esc_html__( 'View Logs', 'mwb-gf-integration-with-mautic' ); ?></a>
												</td>
											</tr>
										<?php else : ?>
											<tr>
												<td class="email_subkey"><?php echo esc_html( $k ); ?></td>
												<td class="email_subvalue"><?php echo esc_html( $v ); ?></td>
											</tr>
										<?php endif; ?>   
									<?php endforeach; ?>	
								<?php } else { ?>
									<tr>
										<td class="email_subkey"><?php echo esc_html( $key ); ?></td>
										<td class="email_subvalue"><?php echo esc_html( $value ); ?></td>
									</tr> 
								<?php } ?>    
							<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>

