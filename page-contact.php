<?php
/**
 * Contact page.
 *
 * @package Oriente
 */

get_header();

$whatsapp_number  = oriente_whatsapp_number();
$whatsapp_url     = oriente_whatsapp_support_url();
$whatsapp_display = $whatsapp_number ? '+' . $whatsapp_number : '';
$map_url           = 'https://www.google.com/maps?q=' . rawurlencode( ORIENTE_CONTACT_ADDRESS ) . '&output=embed';
?>

<main id="primary" class="site-main contact-page">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<section class="contact-intro" aria-labelledby="contact-title">
			<div class="contact-heading" data-reveal>
				<span class="contact-kicker"><?php the_title(); ?></span>
				<h1 id="contact-title"><?php esc_html_e( 'We would be pleased to help.', 'oriente' ); ?></h1>
				<p><?php esc_html_e( 'Contact us for product questions, order support or store visits.', 'oriente' ); ?></p>
			</div>

			<div class="contact-details" data-reveal>
				<div class="contact-detail contact-detail--address">
					<span class="contact-detail-label"><?php esc_html_e( 'Address', 'oriente' ); ?></span>
					<address><?php echo esc_html( ORIENTE_CONTACT_ADDRESS ); ?></address>
				</div>
				<div class="contact-detail">
					<span class="contact-detail-label"><?php esc_html_e( 'Email', 'oriente' ); ?></span>
					<a href="mailto:<?php echo esc_attr( ORIENTE_CONTACT_EMAIL ); ?>"><?php echo esc_html( ORIENTE_CONTACT_EMAIL ); ?></a>
				</div>
				<?php if ( $whatsapp_url ) : ?>
					<div class="contact-detail">
						<span class="contact-detail-label"><?php esc_html_e( 'WhatsApp', 'oriente' ); ?></span>
						<a href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $whatsapp_display ); ?></a>
					</div>
				<?php endif; ?>
				<div class="contact-detail contact-detail--social">
					<span class="contact-detail-label"><?php esc_html_e( 'Follow', 'oriente' ); ?></span>
					<?php oriente_render_social_links( 'contact' ); ?>
				</div>
			</div>
		</section>

		<section class="contact-map" aria-label="<?php esc_attr_e( 'Oriente location', 'oriente' ); ?>" data-reveal>
			<iframe
				title="<?php esc_attr_e( 'Oriente location on Google Maps', 'oriente' ); ?>"
				src="<?php echo esc_url( $map_url ); ?>"
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"
				allowfullscreen
			></iframe>
		</section>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
