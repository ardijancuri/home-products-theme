<?php
/** Site header. @package Oriente */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url    = oriente_shop_url();
$kitchen_url = oriente_category_url( 'kitchen' );
$decor_url   = oriente_category_url( 'home-decoration' );
$home_anchor = is_front_page() ? '' : home_url( '/' );
$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
$cart_count  = oriente_cart_count();
$cart_label  = sprintf(
	/* translators: %d: number of items in the shopping bag. */
	_n( 'Shopping bag, %d item', 'Shopping bag, %d items', $cart_count, 'oriente' ),
	$cart_count
);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'oriente' ); ?></a>

<div class="announcement-bar">
	<?php echo esc_html( get_theme_mod( 'oriente_announcement', __( 'Complimentary European delivery on orders over €250', 'oriente' ) ) ); ?>
</div>

<header class="site-header" data-site-header>
	<div class="header-inner">
		<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'oriente' ); ?>">
			<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( $kitchen_url ); ?>"><?php esc_html_e( 'Kitchen', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( $decor_url ); ?>"><?php esc_html_e( 'Home Décor', 'oriente' ); ?></a>
			<a href="<?php echo esc_url( $home_anchor . '#story' ); ?>"><?php esc_html_e( 'The Edit', 'oriente' ); ?></a>
		</nav>

		<button class="mobile-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-mobile-open>
			<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'oriente' ); ?></span>
			<span class="mobile-toggle-lines" aria-hidden="true"></span>
		</button>

		<div class="site-branding"><?php oriente_the_logo(); ?></div>

		<div class="header-actions">
			<button class="header-action search-action" type="button" aria-expanded="false" aria-controls="search-overlay" data-search-open><?php esc_html_e( 'Search', 'oriente' ); ?></button>
			<a class="header-action account-action" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'Account', 'oriente' ); ?></a>
			<a class="header-action cart-action" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php echo esc_attr( $cart_label ); ?>">
				<svg class="cart-icon" viewBox="0 0 24 26" aria-hidden="true" focusable="false">
					<path d="M5.25 8.75h13.5l-.82 13H6.07l-.82-13Z" />
					<path d="M8.45 8.75V6.6a3.55 3.55 0 0 1 7.1 0v2.15" />
				</svg>
				<span class="cart-count" aria-hidden="true"><?php echo esc_html( $cart_count ); ?></span>
			</a>
		</div>
	</div>
</header>

<div id="search-overlay" class="search-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search the shop', 'oriente' ); ?>" data-search-overlay>
	<button class="overlay-close" type="button" data-search-close><?php esc_html_e( 'Close', 'oriente' ); ?></button>
	<div class="search-inner">
		<span class="search-kicker"><?php esc_html_e( 'Discover the collection', 'oriente' ); ?></span>
		<h2><?php esc_html_e( 'What are you looking for?', 'oriente' ); ?></h2>
		<?php get_product_search_form(); ?>
	</div>
</div>

<div id="mobile-menu" class="mobile-panel" aria-hidden="true" data-mobile-panel>
	<button class="overlay-close" type="button" data-mobile-close><?php esc_html_e( 'Close', 'oriente' ); ?></button>
	<img class="mobile-panel-logo" src="<?php echo esc_url( oriente_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<nav class="mobile-panel-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'oriente' ); ?>">
		<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop all', 'oriente' ); ?></a>
		<a href="<?php echo esc_url( $kitchen_url ); ?>"><?php esc_html_e( 'Kitchen', 'oriente' ); ?></a>
		<a href="<?php echo esc_url( $decor_url ); ?>"><?php esc_html_e( 'Home décor', 'oriente' ); ?></a>
		<a href="<?php echo esc_url( $home_anchor . '#story' ); ?>"><?php esc_html_e( 'The edit', 'oriente' ); ?></a>
	</nav>
	<div class="mobile-panel-meta">
		<a href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'Account', 'oriente' ); ?></a>
		<button class="header-action" type="button" data-mobile-search><?php esc_html_e( 'Search', 'oriente' ); ?></button>
	</div>
</div>
