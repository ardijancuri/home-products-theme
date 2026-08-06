<?php
/** Site footer. @package Oriente */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url    = oriente_shop_url();
$kitchen_url = oriente_category_url( 'kitchen' );
$decor_url   = oriente_category_url( 'home-decoration' );
$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
?>

<section class="site-newsletter" aria-labelledby="newsletter-title">
	<div class="newsletter-copy">
		<h2 id="newsletter-title"><?php esc_html_e( 'A quieter way to live beautifully.', 'oriente' ); ?></h2>
		<p><?php esc_html_e( 'Seasonal edits, makers’ stories and considered objects—sent occasionally.', 'oriente' ); ?></p>
	</div>
	<div>
		<form class="newsletter-form" data-newsletter-form>
			<label class="screen-reader-text" for="oriente-email"><?php esc_html_e( 'Email address', 'oriente' ); ?></label>
			<input id="oriente-email" type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Your email address', 'oriente' ); ?>" required>
			<button type="submit"><?php esc_html_e( 'Join', 'oriente' ); ?></button>
		</form>
		<p class="newsletter-status" aria-live="polite" data-newsletter-status></p>
	</div>
</section>

<footer class="site-footer">
	<div class="footer-main">
		<div class="footer-brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( oriente_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"></a>
			<p><?php esc_html_e( 'Collectible kitchenware and quiet objects selected for daily rituals, generous tables and rooms with soul.', 'oriente' ); ?></p>
		</div>
		<div class="footer-column">
			<h3><?php esc_html_e( 'Collections', 'oriente' ); ?></h3>
			<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop all', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( $kitchen_url ); ?>"><?php esc_html_e( 'Kitchen', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( $decor_url ); ?>"><?php esc_html_e( 'Home decoration', 'oriente' ); ?></a>
		</div>
		<div class="footer-column">
			<h3><?php esc_html_e( 'Services', 'oriente' ); ?></h3>
			<a href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'My account', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( $cart_url ); ?>"><?php esc_html_e( 'Shopping bag', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'oriente' ); ?></a>
		</div>
		<div class="footer-column">
			<h3><?php esc_html_e( 'Oriente', 'oriente' ); ?></h3>
			<a href="<?php echo esc_url( home_url( '/#story' ) ); ?>"><?php esc_html_e( 'Our approach', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/#materials' ) ); ?>"><?php esc_html_e( 'Materials & makers', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'oriente' ); ?></a>
		</div>
	</div>
	<div class="footer-bottom">
		<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
		<span><?php esc_html_e( 'Designed for considered living', 'oriente' ); ?></span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
