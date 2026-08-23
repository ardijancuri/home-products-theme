<?php
/** Site header. @package Oriente */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url      = oriente_shop_url();
$kitchen_url   = oriente_category_url( 'kitchen' );
$decor_url     = oriente_category_url( 'home-decoration' );
$home_anchor   = is_front_page() ? '' : home_url( '/' );
$account_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$account_label = is_user_logged_in() ? __( 'My account', 'oriente' ) : __( 'Sign in / Register', 'oriente' );
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
		<div class="site-branding"><?php oriente_the_logo(); ?></div>

		<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'oriente' ); ?>">
			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'primary-nav-menu',
						'menu_id'        => 'header-primary-menu',
						'depth'          => 3,
						'fallback_cb'    => false,
						'item_spacing'   => 'discard',
					)
				);
				?>
			<?php else : ?>
				<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop', 'oriente' ); ?></a>
				<?php oriente_render_desktop_mega_menu( 'kitchen', __( 'Kitchen', 'oriente' ) ); ?>
				<?php oriente_render_desktop_mega_menu( 'home-decoration', __( 'Home Décor', 'oriente' ) ); ?>
				<a class="primary-nav-link--secondary" href="<?php echo esc_url( $home_anchor . '#story' ); ?>"><?php esc_html_e( 'The Edit', 'oriente' ); ?></a>
				<a class="primary-nav-link--secondary<?php echo is_page( 'about-us' ) ? ' is-current' : ''; ?>" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'oriente' ); ?></a>
			<?php endif; ?>
		</nav>

		<div class="header-actions">
			<button class="header-action search-action" type="button" aria-expanded="false" aria-controls="search-overlay" aria-label="<?php esc_attr_e( 'Search', 'oriente' ); ?>" data-search-open>
				<svg class="search-action-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5" /><path d="m15.4 15.4 5.1 5.1" /></svg>
				<span class="search-action-label"><?php esc_html_e( 'Search', 'oriente' ); ?></span>
			</button>
			<a class="header-action account-action" href="<?php echo esc_url( $account_url ); ?>" aria-label="<?php esc_attr_e( 'Account', 'oriente' ); ?>">
				<svg class="account-action-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<circle cx="12" cy="7.75" r="3.5" />
					<path d="M5.75 20c.35-4.15 2.43-6.25 6.25-6.25s5.9 2.1 6.25 6.25" />
				</svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Account', 'oriente' ); ?></span>
			</a>
			<?php oriente_render_cart_link(); ?>
			<button class="mobile-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e( 'Open menu', 'oriente' ); ?>" data-open-label="<?php esc_attr_e( 'Open menu', 'oriente' ); ?>" data-close-label="<?php esc_attr_e( 'Close menu', 'oriente' ); ?>" data-mobile-open>
				<span class="screen-reader-text mobile-toggle-label"><?php esc_html_e( 'Open menu', 'oriente' ); ?></span>
				<span class="mobile-toggle-lines" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</header>

<div id="search-overlay" class="search-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search the shop', 'oriente' ); ?>" data-search-overlay>
	<button class="overlay-close overlay-icon-close" type="button" aria-label="<?php esc_attr_e( 'Close search', 'oriente' ); ?>" data-search-close>
		<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5l14 14M19 5 5 19" /></svg>
	</button>
	<div class="search-inner">
		<span class="search-kicker"><?php esc_html_e( 'Discover the collection', 'oriente' ); ?></span>
		<h2><?php esc_html_e( 'What are you looking for?', 'oriente' ); ?></h2>
		<?php get_product_search_form(); ?>
	</div>
</div>

<div id="mobile-menu" class="mobile-panel" aria-hidden="true" data-mobile-panel>
	<nav class="mobile-panel-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'oriente' ); ?>">
		<a class="mobile-account-link" href="<?php echo esc_url( $account_url ); ?>">
			<svg class="mobile-account-link__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<circle cx="12" cy="7.75" r="3.5" />
				<path d="M5.75 20c.35-4.15 2.43-6.25 6.25-6.25s5.9 2.1 6.25 6.25" />
			</svg>
			<span><?php echo esc_html( $account_label ); ?></span>
			<svg class="mobile-account-link__arrow" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="m6 3.5 4.5 4.5L6 12.5" /></svg>
		</a>
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-primary-menu',
					'menu_id'        => 'mobile-primary-menu',
					'depth'          => 3,
					'fallback_cb'    => false,
					'item_spacing'   => 'discard',
				)
				);
				?>
		<?php else : ?>
			<a class="mobile-primary-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop all', 'oriente' ); ?></a>
			<?php oriente_render_mobile_category_group( 'kitchen', __( 'Kitchen', 'oriente' ) ); ?>
			<?php oriente_render_mobile_category_group( 'home-decoration', __( 'Home Décor', 'oriente' ) ); ?>
			<a class="mobile-primary-link" href="<?php echo esc_url( $home_anchor . '#story' ); ?>"><?php esc_html_e( 'The edit', 'oriente' ); ?></a>
			<a class="mobile-primary-link" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'oriente' ); ?></a>
		<?php endif; ?>
		<div class="mobile-panel-meta">
			<?php oriente_render_social_links( 'mobile' ); ?>
		</div>
	</nav>
</div>
