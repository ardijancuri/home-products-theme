<?php get_header(); ?>
<main id="primary" class="not-found">
	<div>
		<span class="section-kicker"><?php esc_html_e( 'A quiet corner', 'oriente' ); ?></span>
		<h1>404</h1>
		<p><?php esc_html_e( 'The object or page you were looking for is no longer here.', 'oriente' ); ?></p>
		<a class="ori-button" href="<?php echo esc_url( oriente_shop_url() ); ?>"><?php esc_html_e( 'Return to the collection', 'oriente' ); ?></a>
	</div>
</main>
<?php get_footer(); ?>
