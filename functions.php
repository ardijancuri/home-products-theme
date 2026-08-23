<?php
/**
 * Oriente theme functions.
 *
 * @package Oriente
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ORIENTE_VERSION', '1.0.217' );
define( 'ORIENTE_WHATSAPP_NUMBER', '38975222542' );
define( 'ORIENTE_CONTACT_EMAIL', 'info@oriente.mk' );
define( 'ORIENTE_CONTACT_ADDRESS', 'Metodija Andonov Cento broj 3 Madzari, Skopje, North Macedonia' );
define( 'ORIENTE_COLLECTIONS_MIN', 4 );
define( 'ORIENTE_COLLECTIONS_MAX', 8 );
define( 'ORIENTE_HERO_SLIDES_MAX', 3 );
define( 'ORIENTE_FEATURED_PRODUCTS_CHOICE', 'featured-products' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/demo-content.php';
require_once get_template_directory() . '/inc/storefront-navigation.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/product-category-heroes.php';

/** Return the Customizer-managed WhatsApp number in wa.me-compatible format. */
function oriente_whatsapp_number() {
	$number = get_theme_mod( 'oriente_whatsapp_number', ORIENTE_WHATSAPP_NUMBER );
	return preg_replace( '/\D+/', '', (string) $number );
}

/** Return the direct customer-support WhatsApp URL. */
function oriente_whatsapp_support_url() {
	$number = oriente_whatsapp_number();
	return $number ? 'https://wa.me/' . $number : '';
}

/** Configure theme defaults and WordPress features. */
function oriente_setup() {
	load_theme_textdomain( 'oriente', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 410,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 720,
			'single_image_width'    => 1100,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'max_rows'        => 8,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'oriente' ),
			'footer'  => __( 'Footer navigation', 'oriente' ),
		)
	);
	add_image_size( 'oriente-product', 900, 1125, true );
	add_image_size( 'oriente-editorial', 1600, 1900, true );
}
add_action( 'after_setup_theme', 'oriente_setup' );

/** Enqueue typography, styles, and interactions. */
function oriente_assets() {
	wp_enqueue_style(
		'oriente-fonts',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600&display=swap',
		array(),
		null
	);
	$style_dependencies = array( 'oriente-fonts' );
	$is_single_product   = function_exists( 'is_product' ) && is_product();
	$is_storefront_page  = function_exists( 'is_woocommerce' ) && is_woocommerce();
	$is_woocommerce_page = $is_storefront_page || ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_account_page' ) && is_account_page() );
	if ( $is_woocommerce_page && ! $is_single_product ) {
		foreach ( array( 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen' ) as $woocommerce_style ) {
			if ( wp_style_is( $woocommerce_style, 'registered' ) ) {
				$style_dependencies[] = $woocommerce_style;
			}
		}
	}
	if ( $is_single_product ) {
		foreach ( array( 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen' ) as $woocommerce_style ) {
			wp_dequeue_style( $woocommerce_style );
		}
	}

	$theme_script_dependencies = array();
	if ( class_exists( 'WooCommerce' ) ) {
		$theme_script_dependencies = array( 'jquery', 'wc-cart-fragments' );
	}

	wp_enqueue_style( 'oriente-style', get_stylesheet_uri(), $style_dependencies, ORIENTE_VERSION );
	wp_enqueue_script( 'oriente-theme', get_template_directory_uri() . '/assets/js/theme.js', $theme_script_dependencies, ORIENTE_VERSION, true );
	wp_localize_script(
		'oriente-theme',
		'orienteTheme',
		array(
			'newsletterSuccess' => __( 'Thank you. Your place at the table is reserved.', 'oriente' ),
			'cartDrawerVersion' => ORIENTE_VERSION,
		)
	);

	if ( function_exists( 'is_cart' ) && is_cart() ) {
		wp_enqueue_script(
			'oriente-whatsapp-cart',
			get_template_directory_uri() . '/assets/js/whatsapp-cart.js',
			array( 'wc-blocks-checkout' ),
			ORIENTE_VERSION,
			true
		);
		wp_localize_script(
			'oriente-whatsapp-cart',
			'orienteWhatsAppCart',
			array(
				'number'       => oriente_whatsapp_number(),
				'buttonLabel'  => __( 'Order via WhatsApp', 'oriente' ),
				'introduction' => __( 'Здраво, сакам да направам нарачка:', 'oriente' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'oriente_assets', 99 );

/** Output the theme favicon when no WordPress Site Icon has been selected. */
function oriente_favicon_links() {
	if ( has_site_icon() ) {
		return;
	}

	$asset_uri         = get_template_directory_uri() . '/assets/images/';
	$favicon_asset_url = static function ( $filename ) use ( $asset_uri ) {
		return add_query_arg( 'ver', ORIENTE_VERSION, $asset_uri . $filename );
	};
	?>
	<link rel="icon" href="<?php echo esc_url( $favicon_asset_url( 'oriente-favicon.svg' ) ); ?>" type="image/svg+xml">
	<link rel="icon" href="<?php echo esc_url( $favicon_asset_url( 'oriente-favicon-32.png' ) ); ?>" sizes="32x32" type="image/png">
	<link rel="shortcut icon" href="<?php echo esc_url( $favicon_asset_url( 'favicon.ico' ) ); ?>">
	<link rel="apple-touch-icon" href="<?php echo esc_url( $favicon_asset_url( 'oriente-apple-touch-icon.png' ) ); ?>" sizes="180x180">
	<link rel="manifest" href="<?php echo esc_url( $favicon_asset_url( 'oriente-site.webmanifest' ) ); ?>">
	<meta name="theme-color" content="#f7f0e7">
	<?php
}
add_action( 'wp_head', 'oriente_favicon_links', 2 );

/**
 * Build a WhatsApp order link from the current classic WooCommerce cart.
 *
 * The returned URL uses a fixed HTTPS host, a digits-only phone number, and a
 * fully encoded message. Escape it with esc_attr() at output time: esc_url()
 * deliberately removes the encoded line breaks that WhatsApp needs.
 */
function oriente_cart_whatsapp_url() {
	$whatsapp_number = oriente_whatsapp_number();
	if ( ! $whatsapp_number || ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '';
	}

	$line_break = "\r\n";
	$sections   = array( '*' . __( 'Здраво, сакам да направам нарачка:', 'oriente' ) . '*' );
	$position   = 0;

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$item_product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
		$quantity     = isset( $cart_item['quantity'] ) ? (int) $cart_item['quantity'] : 0;
		if ( ! $item_product instanceof WC_Product || $quantity < 1 ) {
			continue;
		}

		++$position;
		$sku        = $item_product->get_sku();
		$sections[] = sprintf(
			/* translators: 1: item number, 2: product name, 3: SKU, 4: quantity. */
			__( "*%1\$d. Производ:* %2\$s\r\n*Шифра:* %3\$s\r\n*Количина:* %4\$d", 'oriente' ),
			$position,
			wp_strip_all_tags( $item_product->get_name() ),
			$sku ? $sku : __( 'Не е достапна', 'oriente' ),
			$quantity
		);
	}

	if ( 0 === $position ) {
		return '';
	}

	return 'https://wa.me/' . $whatsapp_number . '?text=' . rawurlencode( implode( $line_break . $line_break, $sections ) );
}

/** Output the WhatsApp order action when a classic cart template is used. */
function oriente_whatsapp_order_button() {
	$whatsapp_url = oriente_cart_whatsapp_url();
	if ( ! $whatsapp_url ) {
		return;
	}

	printf(
		'<a href="%1$s" class="checkout-button button alt wc-forward oriente-whatsapp-order-button">%2$s</a>',
		esc_attr( $whatsapp_url ),
		esc_html__( 'Order via WhatsApp', 'oriente' )
	);
}

/** Replace the standard checkout action for classic cart templates. */
function oriente_replace_classic_cart_checkout() {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return;
	}

	remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
	add_action( 'woocommerce_proceed_to_checkout', 'oriente_whatsapp_order_button', 20 );
}
add_action( 'wp', 'oriente_replace_classic_cart_checkout' );

/** Replace the WooCommerce Blocks empty-cart markup with an Orienté editorial state. */
function oriente_render_empty_cart_block( $block_content, $block ) {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() || ! function_exists( 'WC' ) || ! WC()->cart || ! WC()->cart->is_empty() ) {
		return $block_content;
	}

	$kitchen_url = oriente_category_url( 'kitchen' );
	$decor_url   = oriente_category_url( 'home-decoration' );
	$shop_url    = oriente_shop_url();
	$mark_url    = get_template_directory_uri() . '/assets/images/oriente-favicon.svg';

	ob_start();
	?>
	<div class="oriente-empty-cart">
		<section class="oriente-empty-cart-state" aria-labelledby="oriente-empty-cart-title">
			<div class="oriente-empty-cart-copy">
				<h2 id="oriente-empty-cart-title"><?php esc_html_e( 'Your cart is empty.', 'oriente' ); ?></h2>
				<p><?php esc_html_e( 'Explore kitchenware and home accessories designed for everyday use.', 'oriente' ); ?></p>
				<div class="oriente-empty-cart-actions">
					<a class="ori-button" href="<?php echo esc_url( $kitchen_url ); ?>"><?php esc_html_e( 'Shop Kitchen', 'oriente' ); ?></a>
					<a class="text-link text-link--no-icon" href="<?php echo esc_url( $decor_url ); ?>"><?php esc_html_e( 'Explore Home Décor', 'oriente' ); ?></a>
				</div>
			</div>
			<div class="oriente-empty-cart-mark" aria-hidden="true">
				<img src="<?php echo esc_url( $mark_url ); ?>" width="160" height="160" alt="">
				<span><?php esc_html_e( 'Inspired Living.', 'oriente' ); ?></span>
			</div>
		</section>

		<section class="oriente-empty-cart-products" aria-labelledby="oriente-empty-cart-products-title">
			<header class="oriente-empty-cart-products-header">
				<h2 id="oriente-empty-cart-products-title"><?php esc_html_e( 'New in store', 'oriente' ); ?></h2>
				<p><?php esc_html_e( 'A selection of recently arrived pieces for the kitchen and home.', 'oriente' ); ?></p>
				<a class="text-link text-link--no-icon" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'View all products', 'oriente' ); ?></a>
			</header>
			<?php echo do_shortcode( '[products limit="4" columns="4" orderby="date" order="DESC" visibility="visible"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</section>
	</div>
	<?php
	return ob_get_clean();
}

/** Output the WhatsApp order action inside the cart drawer. */
function oriente_mini_cart_whatsapp_button() {
	$whatsapp_url = oriente_cart_whatsapp_url();
	if ( ! $whatsapp_url ) {
		return;
	}
	?>
	<a class="button oriente-cart-whatsapp-button" href="<?php echo esc_attr( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Order via WhatsApp', 'oriente' ); ?></a>
	<?php
}

/** Replace the mini-cart View cart and Checkout actions with WhatsApp ordering. */
function oriente_replace_mini_cart_actions() {
	remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
	remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
	add_action( 'woocommerce_widget_shopping_cart_buttons', 'oriente_mini_cart_whatsapp_button', 20 );
}
add_action( 'wp_loaded', 'oriente_replace_mini_cart_actions', 20 );
add_filter( 'render_block_woocommerce/cart', 'oriente_render_empty_cart_block', 20, 2 );

/** Print the global floating WhatsApp customer-support action. */
function oriente_whatsapp_support_button() {
	$whatsapp_url = oriente_whatsapp_support_url();
	if ( ! $whatsapp_url ) {
		return;
	}
	?>
	<a class="whatsapp-support-button" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">
		<span class="screen-reader-text"><?php esc_html_e( 'Chat with Oriente support on WhatsApp', 'oriente' ); ?></span>
		<svg class="social-icon social-icon--whatsapp" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
			<path d="M20.25 11.65a8.25 8.25 0 0 1-12.17 7.27L3.5 20.5 5 16.14a8.25 8.25 0 1 1 15.25-4.49Z" />
			<path fill="currentColor" stroke="none" transform="translate(1.2 1.2) scale(.9)" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
		</svg>
	</a>
	<?php
}
add_action( 'wp_footer', 'oriente_whatsapp_support_button', 5 );

/** Return the supplied Oriente wordmark URL. */
function oriente_logo_url() {
	return get_template_directory_uri() . '/assets/images/oriente-logo.svg';
}

/** Print a custom logo with the supplied wordmark as fallback. */
function oriente_the_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf(
		'<a class="site-logo-link" href="%1$s" rel="home"><img class="site-logo" src="%2$s" alt="%3$s"></a>',
		esc_url( home_url( '/' ) ),
		esc_url( oriente_logo_url() ),
		esc_attr( get_bloginfo( 'name' ) )
	);
}

/** Return the configured social profiles and their shared icons. */
function oriente_social_profiles() {
	$defaults = oriente_homepage_defaults();

	return array(
		'instagram' => array(
			'label' => __( 'Instagram', 'oriente' ),
			'url'   => get_theme_mod( 'oriente_instagram_url', $defaults['oriente_instagram_url'] ),
			'path'  => 'M7.03.084c-1.277.06-2.149.264-2.911.563-.789.308-1.458.72-2.123 1.388C1.332 2.703.922 3.372.617 4.162.322 4.926.121 5.799.065 7.076.009 8.354-.004 8.764.002 12.023c.006 3.259.021 3.667.083 4.947.061 1.277.264 2.149.564 2.911.308.789.72 1.457 1.388 2.123.668.665 1.337 1.074 2.129 1.38.763.295 1.636.496 2.913.552 1.277.056 1.688.069 4.946.063 3.258-.006 3.668-.021 4.948-.081 1.28-.061 2.147-.265 2.91-.564.789-.308 1.458-.72 2.123-1.388.665-.668 1.074-1.338 1.38-2.128.295-.763.496-1.636.552-2.912.056-1.281.069-1.69.063-4.948-.006-3.258-.021-3.667-.082-4.947-.061-1.28-.264-2.149-.563-2.912-.308-.789-.72-1.457-1.388-2.123C21.298 1.33 20.628.921 19.838.617 19.074.321 18.202.12 16.924.065 15.647.009 15.236-.005 11.977.001 8.718.008 8.31.022 7.03.084Zm.14 21.693c-1.17-.051-1.805-.245-2.229-.408-.561-.216-.96-.477-1.382-.895-.422-.418-.681-.819-.9-1.378-.164-.423-.362-1.058-.417-2.228-.059-1.265-.072-1.644-.079-4.848-.007-3.204.005-3.583.061-4.848.05-1.169.245-1.805.408-2.228.216-.561.476-.96.895-1.382.419-.422.818-.681 1.378-.9.423-.165 1.058-.361 2.227-.417 1.266-.06 1.645-.072 4.848-.079 3.203-.007 3.584.005 4.85.061 1.169.051 1.805.244 2.228.408.561.216.96.475 1.382.895.422.419.682.818.901 1.379.165.422.362 1.056.417 2.226.06 1.266.074 1.645.08 4.848.005 3.203-.006 3.584-.061 4.848-.051 1.17-.245 1.806-.408 2.23-.216.56-.476.96-.896 1.381-.419.422-.818.681-1.378.9-.422.165-1.058.362-2.226.417-1.266.06-1.645.072-4.85.079-3.204.007-3.582-.006-4.848-.061ZM16.953 5.586a1.44 1.44 0 1 0 1.437-1.442 1.44 1.44 0 0 0-1.437 1.442ZM5.839 12.012c.007 3.403 2.771 6.156 6.173 6.149 3.403-.006 6.157-2.77 6.151-6.173-.007-3.403-2.771-6.157-6.174-6.15-3.403.007-6.156 2.771-6.15 6.174ZM8 12.008A4 4 0 1 1 12.008 16 4 4 0 0 1 8 12.008Z',
		),
		'facebook'  => array(
			'label' => __( 'Facebook', 'oriente' ),
			'url'   => get_theme_mod( 'oriente_facebook_url', $defaults['oriente_facebook_url'] ),
			'path'  => 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z',
		),
		'tiktok'    => array(
			'label' => __( 'TikTok', 'oriente' ),
			'url'   => get_theme_mod( 'oriente_tiktok_url', $defaults['oriente_tiktok_url'] ),
			'path'  => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07Z',
		),
		'whatsapp'  => array(
			'label' => __( 'WhatsApp', 'oriente' ),
			'url'   => oriente_whatsapp_support_url(),
			'path'  => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z',
		),
	);
}

/** Print the configured social icons in a supported theme context. */
function oriente_render_social_links( $context = 'default' ) {
	$allowed_contexts = array( 'default', 'mobile', 'footer', 'contact' );
	$context          = in_array( $context, $allowed_contexts, true ) ? $context : 'default';
	$profiles         = array_filter(
		oriente_social_profiles(),
		static function ( $profile ) {
			return ! empty( $profile['url'] );
		}
	);

	if ( empty( $profiles ) ) {
		return;
	}
	?>
	<div class="social-links social-links--<?php echo esc_attr( $context ); ?>" aria-label="<?php esc_attr_e( 'Social media', 'oriente' ); ?>">
		<?php foreach ( $profiles as $network => $profile ) : ?>
			<a href="<?php echo esc_url( $profile['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $profile['label'] ); ?>">
				<svg class="social-icon social-icon--<?php echo esc_attr( $network ); ?>" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<?php if ( 'instagram' === $network ) : ?>
						<rect x="3.75" y="3.75" width="16.5" height="16.5" rx="4.25" />
						<circle cx="12" cy="12" r="3.75" />
						<circle class="social-icon-dot" cx="17.25" cy="6.75" r=".7" />
					<?php elseif ( 'facebook' === $network ) : ?>
						<path d="M13.65 21v-8h2.9l.45-3h-3.35V8.2c0-1.7.75-2.8 2.9-2.8H18V2.75c-.65-.1-1.45-.15-2.35-.15-3.15 0-5.05 1.9-5.05 5.3V10H8v3h2.6v8" />
					<?php elseif ( 'tiktok' === $network ) : ?>
						<path d="M14.6 3.25v11.2a4.45 4.45 0 1 1-3.9-4.42" />
						<path d="M14.6 3.25c.28 2.4 1.7 3.82 4.15 4.15" />
					<?php elseif ( 'whatsapp' === $network ) : ?>
						<path d="M20.25 11.65a8.25 8.25 0 0 1-12.17 7.27L3.5 20.5 5 16.14a8.25 8.25 0 1 1 15.25-4.49Z" />
						<path fill="currentColor" stroke="none" transform="translate(1.2 1.2) scale(.9)" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
					<?php endif; ?>
				</svg>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

/** Find a storefront category or fall back to the shop. */
function oriente_category_url( $slug ) {
	if ( taxonomy_exists( 'product_cat' ) ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				return $link;
			}
		}
	}
	return oriente_shop_url();
}

/** Resolve the WooCommerce shop URL safely. */
function oriente_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/** Return the current cart count. */
function oriente_cart_count() {
	return function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
}

/** Render the header cart control and its conditional item badge. */
function oriente_render_cart_link() {
	$cart_count = oriente_cart_count();
	$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
	$cart_label = sprintf(
		/* translators: %d: number of items in the shopping bag. */
		_n( 'Shopping bag, %d item', 'Shopping bag, %d items', $cart_count, 'oriente' ),
		$cart_count
	);
	?>
	<a class="header-action cart-action" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php echo esc_attr( $cart_label ); ?>" aria-controls="oriente-cart-drawer" aria-expanded="false" data-cart-open data-cart-label-singular="<?php echo esc_attr__( 'Shopping bag, %d item', 'oriente' ); ?>" data-cart-label-plural="<?php echo esc_attr__( 'Shopping bag, %d items', 'oriente' ); ?>">
		<svg class="cart-icon" viewBox="0 0 24 26" aria-hidden="true" focusable="false">
			<path d="M5.25 8.75h13.5l-.82 13H6.07l-.82-13Z" />
			<path d="M8.45 8.75V6.6a3.55 3.55 0 0 1 7.1 0v2.15" />
		</svg>
		<span class="cart-count<?php echo 0 < $cart_count && 10 > $cart_count ? ' cart-count--single' : ''; ?>" aria-hidden="true"<?php echo $cart_count < 1 ? ' hidden' : ''; ?>><?php echo $cart_count > 0 ? esc_html( $cart_count ) : ''; ?></span>
	</a>
	<?php
}

/** Render the fragment-replaceable contents of the cart drawer. */
function oriente_render_cart_drawer_content() {
	$cart_count = oriente_cart_count();
	?>
	<div class="oriente-cart-drawer-content">
		<p class="oriente-cart-drawer-count">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %d: number of items in the shopping bag. */
					_n( '%d item', '%d items', $cart_count, 'oriente' ),
					$cart_count
				)
			);
			?>
		</p>
		<?php if ( function_exists( 'woocommerce_mini_cart' ) ) : ?>
			<?php
			$was_rendering_cart_drawer                = ! empty( $GLOBALS['oriente_rendering_cart_drawer'] );
			$GLOBALS['oriente_rendering_cart_drawer'] = true;
			woocommerce_mini_cart();
			$GLOBALS['oriente_rendering_cart_drawer'] = $was_rendering_cart_drawer;
			?>
		<?php endif; ?>
	</div>
	<?php
}

/** Use an uncropped product image while WooCommerce renders the cart drawer. */
function oriente_cart_drawer_item_thumbnail( $thumbnail, $cart_item ) {
	if ( empty( $GLOBALS['oriente_rendering_cart_drawer'] ) || empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
		return $thumbnail;
	}

	return $cart_item['data']->get_image(
		'full',
		array(
			'class'     => 'oriente-cart-drawer-image',
			'decoding'  => 'async',
			'draggable' => 'false',
			'loading'   => 'lazy',
		)
	);
}
add_filter( 'woocommerce_cart_item_thumbnail', 'oriente_cart_drawer_item_thumbnail', 30, 2 );

/** Render the global off-canvas shopping bag. */
function oriente_render_cart_drawer() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	?>
	<div class="oriente-cart-drawer-layer" aria-hidden="true" data-cart-drawer>
		<button class="oriente-cart-drawer-backdrop" type="button" aria-label="<?php esc_attr_e( 'Close shopping bag', 'oriente' ); ?>" data-cart-close></button>
		<aside id="oriente-cart-drawer" class="oriente-cart-drawer" role="dialog" aria-modal="true" aria-labelledby="oriente-cart-drawer-title">
			<header class="oriente-cart-drawer-header">
				<h2 id="oriente-cart-drawer-title"><?php esc_html_e( 'Shopping bag', 'oriente' ); ?></h2>
				<button class="oriente-cart-drawer-close" type="button" aria-label="<?php esc_attr_e( 'Close shopping bag', 'oriente' ); ?>" data-cart-close>
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 5l14 14M19 5 5 19" /></svg>
				</button>
			</header>
			<?php oriente_render_cart_drawer_content(); ?>
		</aside>
	</div>
	<?php
}
add_action( 'wp_footer', 'oriente_render_cart_drawer', 4 );

/** Keep the header bag count in sync after AJAX adds. */
function oriente_cart_fragments( $fragments ) {
	ob_start();
	oriente_render_cart_link();
	$fragments['.cart-action'] = ob_get_clean();

	ob_start();
	oriente_render_cart_drawer_content();
	$fragments['.oriente-cart-drawer-content'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'oriente_cart_fragments' );

/** Remove WooCommerce's View cart link from add-to-cart notices. */
function oriente_remove_view_cart_from_notice( $message ) {
	return preg_replace( '#<a[^>]+class="[^"]*wc-forward[^"]*"[^>]*>.*?</a>\s*#i', '', $message, 1 );
}
add_filter( 'wc_add_to_cart_message_html', 'oriente_remove_view_cart_from_notice', 20 );

/** Render a product image pair for editorial cards and shop loops. */
function oriente_product_card_images( $product, $size = 'woocommerce_thumbnail' ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$primary_id  = $product->get_image_id();
	$gallery_ids = array_values( array_filter( $product->get_gallery_image_ids() ) );
	$alternate_id = $gallery_ids ? (int) $gallery_ids[0] : 0;

	echo '<span class="oriente-card-images">';
	if ( $primary_id ) {
		echo wp_kses_post(
			wp_get_attachment_image(
				$primary_id,
				$size,
				false,
				array(
					'class'     => 'oriente-card-image oriente-card-image--primary',
					'draggable' => 'false',
					'loading'   => 'lazy',
				)
			)
		);
	} else {
		echo wp_kses_post( wc_placeholder_img( $size, array( 'class' => 'oriente-card-image oriente-card-image--primary' ) ) );
	}

	if ( $alternate_id ) {
		echo wp_kses_post(
			wp_get_attachment_image(
				$alternate_id,
				$size,
				false,
				array(
					'class'       => 'oriente-card-image oriente-card-image--alternate',
					'alt'         => '',
					'aria-hidden' => 'true',
					'draggable'   => 'false',
					'loading'     => 'lazy',
				)
			)
		);
	}
	echo '</span>';
}

/** Replace WooCommerce's single loop thumbnail with the two-image card treatment. */
function oriente_replace_loop_product_thumbnail() {
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	add_action(
		'woocommerce_before_shop_loop_item_title',
		function () {
			global $product;
			oriente_product_card_images( $product, 'oriente-product' );
		},
		10
	);
}
add_action( 'wp', 'oriente_replace_loop_product_thumbnail' );

/** Add Materials and Dimensions beneath pricing in Product Data > General. */
function oriente_product_detail_fields() {
	woocommerce_wp_text_input(
		array(
			'id'          => '_oriente_material',
			'label'       => __( 'Materials', 'oriente' ),
			'placeholder' => __( 'e.g. Porcelain, wood, stainless steel', 'oriente' ),
			'description' => __( 'The primary materials and finishes used for this product.', 'oriente' ),
			'desc_tip'    => true,
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_oriente_dimensions',
			'label'       => __( 'Dimensions', 'oriente' ),
			'placeholder' => __( 'e.g. 30 × 20 × 8 cm', 'oriente' ),
			'description' => __( 'Enter the complete product dimensions, including the unit.', 'oriente' ),
			'desc_tip'    => true,
		)
	);
}
add_action( 'woocommerce_product_options_pricing', 'oriente_product_detail_fields' );

/** Save the custom product details with the WooCommerce product record. */
function oriente_save_product_detail_fields( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$fields = array( '_oriente_material', '_oriente_dimensions' );

	foreach ( $fields as $meta_key ) {
		if ( ! isset( $_POST[ $meta_key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce verifies its product-edit nonce before this hook runs.
			continue;
		}

		$value = sanitize_text_field( wp_unslash( $_POST[ $meta_key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' === $value ) {
			$product->delete_meta_data( $meta_key );
		} else {
			$product->update_meta_data( $meta_key, $value );
		}
	}
}
add_action( 'woocommerce_admin_process_product_object', 'oriente_save_product_detail_fields' );

/** Add useful hierarchy and product details to the product summary. */
function oriente_single_product_summary_details() {
	add_action(
		'woocommerce_single_product_summary',
		function () {
			global $product;
			if ( ! $product instanceof WC_Product ) {
				return;
			}
			$terms = get_the_terms( $product->get_id(), 'product_cat' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				printf(
					'<p class="single-product-category"><a href="%1$s">%2$s</a></p>',
					esc_url( get_term_link( $terms[0] ) ),
					esc_html( $terms[0]->name )
				);
			}
		},
		1
	);

	add_action(
		'woocommerce_single_product_summary',
		function () {
			global $product;

			if ( ! $product instanceof WC_Product ) {
				return;
			}

			$material   = trim( (string) $product->get_meta( '_oriente_material', true ) );
			$dimensions = trim( (string) $product->get_meta( '_oriente_dimensions', true ) );
			$sku        = $product->get_sku();
			?>
			<ul class="product-assurances" aria-label="<?php esc_attr_e( 'Product details and order information', 'oriente' ); ?>">
				<?php if ( $material ) : ?>
					<li><strong><?php esc_html_e( 'Materials', 'oriente' ); ?></strong><span><?php echo esc_html( $material ); ?></span></li>
				<?php endif; ?>
				<li><strong><?php esc_html_e( 'Considered dispatch', 'oriente' ); ?></strong><span><?php esc_html_e( '1 to 2 days', 'oriente' ); ?></span></li>
				<?php if ( $dimensions ) : ?>
					<li><strong><?php esc_html_e( 'Dimensions', 'oriente' ); ?></strong><span><?php echo esc_html( $dimensions ); ?></span></li>
				<?php endif; ?>
				<?php if ( $sku ) : ?>
					<li class="product-sku-row"><strong><?php esc_html_e( 'SKU', 'oriente' ); ?></strong><span><?php echo esc_html( $sku ); ?></span></li>
				<?php endif; ?>
			</ul>
			<?php
		},
		35
	);
}
add_action( 'wp', 'oriente_single_product_summary_details' );

/** Remove ratings and reviews from the product experience. */
function oriente_disable_product_reviews() {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
}
add_action( 'wp', 'oriente_disable_product_reviews' );

/** Remove the reviews tab if another extension attempts to add it. */
function oriente_remove_product_reviews_tab( $tabs ) {
	unset( $tabs['reviews'] );
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'oriente_remove_product_reviews_tab', 98 );

/** Keep product comments closed while leaving ordinary posts unchanged. */
function oriente_close_product_comments( $open, $post_id ) {
	return 'product' === get_post_type( $post_id ) ? false : $open;
}
add_filter( 'comments_open', 'oriente_close_product_comments', 10, 2 );

/** Use the full product image treatment for every single-product gallery view. */
function oriente_product_gallery_image_size( $size ) {
	return function_exists( 'is_product' ) && is_product() ? 'woocommerce_single' : $size;
}
add_filter( 'woocommerce_gallery_image_size', 'oriente_product_gallery_image_size', 20 );

/** Render product gallery images as static media instead of lightbox links. */
function oriente_disable_product_gallery_links( $html ) {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return $html;
	}

	$html = preg_replace( '/<a\b[^>]*>/i', '<span class="oriente-product-image-frame">', $html, 1 );
	return preg_replace( '/<\/a>/i', '</span>', $html, 1 );
}
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'oriente_disable_product_gallery_links', 99 );

/** Return the stable Oriente prefix used for an automatically generated SKU. */
function oriente_generated_sku_prefix( $product ) {
	if ( $product instanceof WC_Product_Variation ) {
		return 'ORI-VAR';
	}

	$category_slugs = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'slugs' ) );
	if ( ! is_wp_error( $category_slugs ) ) {
		if ( in_array( 'kitchen', $category_slugs, true ) ) {
			return 'ORI-KIT';
		}
		if ( in_array( 'home-decoration', $category_slugs, true ) ) {
			return 'ORI-HOM';
		}
	}

	return 'ORI-PRD';
}

/** Assign a unique SKU to a saved WooCommerce product when it has none. */
function oriente_assign_generated_product_sku( $product_id ) {
	static $processing = array();

	$product_id = absint( $product_id );
	if ( ! $product_id || isset( $processing[ $product_id ] ) || ! function_exists( 'wc_get_product' ) ) {
		return '';
	}

	$product = wc_get_product( $product_id );
	if ( ! $product || $product->get_sku( 'edit' ) || in_array( get_post_status( $product_id ), array( 'trash', 'auto-draft' ), true ) ) {
		return $product ? $product->get_sku( 'edit' ) : '';
	}

	$processing[ $product_id ] = true;
	$base_sku                  = sprintf( '%s-%03d', oriente_generated_sku_prefix( $product ), $product_id );
	$generated_sku             = $base_sku;
	$suffix                    = 2;

	while ( ( $owner_id = wc_get_product_id_by_sku( $generated_sku ) ) && (int) $owner_id !== $product_id ) {
		$generated_sku = $base_sku . '-' . $suffix;
		++$suffix;
	}

	try {
		$product->set_sku( $generated_sku );
		$product->save();
	} catch ( Throwable $error ) {
		$generated_sku = '';
	}

	unset( $processing[ $product_id ] );
	return $generated_sku;
}

/** Generate SKUs after manual creation, updates, variation saves, and imports. */
function oriente_generate_sku_after_product_save( $product_id ) {
	oriente_assign_generated_product_sku( $product_id );
}
add_action( 'woocommerce_new_product', 'oriente_generate_sku_after_product_save', 30 );
add_action( 'woocommerce_update_product', 'oriente_generate_sku_after_product_save', 30 );
add_action( 'woocommerce_new_product_variation', 'oriente_generate_sku_after_product_save', 30 );
add_action( 'woocommerce_update_product_variation', 'oriente_generate_sku_after_product_save', 30 );

/** Cover WooCommerce's native CSV importer after it has inserted a product. */
function oriente_generate_sku_after_product_import( $product ) {
	if ( $product instanceof WC_Product ) {
		oriente_assign_generated_product_sku( $product->get_id() );
	}
}
add_action( 'woocommerce_product_import_inserted_product_object', 'oriente_generate_sku_after_product_import', 30 );

/** Backfill any pre-existing products that were created without an SKU. */
function oriente_backfill_missing_product_skus() {
	if ( '1' === get_option( 'oriente_product_sku_backfill' ) || ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$product_ids = get_posts(
		array(
			'post_type'      => array( 'product', 'product_variation' ),
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	foreach ( $product_ids as $product_id ) {
		if ( '' === (string) get_post_meta( $product_id, '_sku', true ) ) {
			oriente_assign_generated_product_sku( $product_id );
		}
	}

	update_option( 'oriente_product_sku_backfill', '1', false );
}
add_action( 'init', 'oriente_backfill_missing_product_skus', 60 );

/** Keep storefront pages focused by removing WooCommerce's theme sidebar. */
function oriente_remove_woocommerce_sidebar() {
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'wp', 'oriente_remove_woocommerce_sidebar' );

/** Add visual context body classes. */
function oriente_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'oriente-home';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$classes[] = 'oriente-woocommerce-ready';
	}
	return $classes;
}
add_filter( 'body_class', 'oriente_body_classes' );
add_filter( 'loop_shop_columns', fn() => 4 );
add_filter( 'excerpt_length', fn() => 24, 99 );
