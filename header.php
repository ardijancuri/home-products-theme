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
$instagram_url = get_theme_mod( 'oriente_instagram_url', 'https://www.instagram.com/' );
$facebook_url  = get_theme_mod( 'oriente_facebook_url', 'https://www.facebook.com/' );
$tiktok_url    = get_theme_mod( 'oriente_tiktok_url', 'https://www.tiktok.com/' );
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
			<?php oriente_render_desktop_mega_menu( 'kitchen', __( 'Kitchen', 'oriente' ) ); ?>
			<?php oriente_render_desktop_mega_menu( 'home-decoration', __( 'Home Décor', 'oriente' ) ); ?>
			<a class="primary-nav-link--secondary" href="<?php echo esc_url( $home_anchor . '#story' ); ?>"><?php esc_html_e( 'The Edit', 'oriente' ); ?></a>
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
	<button class="overlay-close mobile-panel-close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'oriente' ); ?>" data-mobile-close>
		<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5l14 14M19 5 5 19" /></svg>
	</button>
	<img class="mobile-panel-logo" src="<?php echo esc_url( oriente_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<nav class="mobile-panel-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'oriente' ); ?>">
		<a class="mobile-primary-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop all', 'oriente' ); ?></a>
		<?php oriente_render_mobile_category_group( 'kitchen', __( 'Kitchen', 'oriente' ) ); ?>
		<?php oriente_render_mobile_category_group( 'home-decoration', __( 'Home Décor', 'oriente' ) ); ?>
		<a class="mobile-primary-link" href="<?php echo esc_url( $home_anchor . '#story' ); ?>"><?php esc_html_e( 'The edit', 'oriente' ); ?></a>
		<div class="mobile-panel-meta">
			<div class="mobile-social-links" aria-label="<?php esc_attr_e( 'Social media', 'oriente' ); ?>">
				<?php if ( $instagram_url ) : ?>
					<a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Instagram', 'oriente' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.25" y="3.25" width="17.5" height="17.5" rx="5" /><circle cx="12" cy="12" r="4.1" /><circle class="social-icon-dot" cx="17.35" cy="6.75" r="1" /></svg>
					</a>
				<?php endif; ?>
				<?php if ( $facebook_url ) : ?>
					<a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Facebook', 'oriente' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.75 3.5h-1.6a3.4 3.4 0 0 0-3.4 3.4V21M7 11h9" /></svg>
					</a>
				<?php endif; ?>
				<?php if ( $tiktok_url ) : ?>
					<a href="<?php echo esc_url( $tiktok_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'TikTok', 'oriente' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 3.5v10.25a4.75 4.75 0 1 1-4.75-4.75M15 3.5c.5 2.4 1.85 3.8 4.35 4.15" /></svg>
					</a>
				<?php endif; ?>
			</div>
			<button class="mobile-search-action" type="button" aria-label="<?php esc_attr_e( 'Search', 'oriente' ); ?>" data-mobile-search>
				<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5" /><path d="m15.4 15.4 5.1 5.1" /></svg>
				<span><?php esc_html_e( 'Search', 'oriente' ); ?></span>
			</button>
		</div>
	</nav>
</div>
