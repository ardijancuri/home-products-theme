<?php
/**
 * Oriente theme functions.
 *
 * @package Oriente
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ORIENTE_VERSION', '1.0.40' );
define( 'ORIENTE_WHATSAPP_NUMBER', '38975222542' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/demo-content.php';

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

	wp_enqueue_style( 'oriente-style', get_stylesheet_uri(), $style_dependencies, ORIENTE_VERSION );
	wp_enqueue_script( 'oriente-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), ORIENTE_VERSION, true );
	wp_localize_script(
		'oriente-theme',
		'orienteTheme',
		array(
			'newsletterSuccess' => __( 'Thank you. Your place at the table is reserved.', 'oriente' ),
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
				'number'       => ORIENTE_WHATSAPP_NUMBER,
				'buttonLabel'  => __( 'Order via WhatsApp', 'oriente' ),
				'introduction' => __( 'Здраво, сакам да направам нарачка:', 'oriente' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'oriente_assets', 99 );

/** Build a WhatsApp order link from the current classic WooCommerce cart. */
function oriente_cart_whatsapp_url() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '';
	}

	$lines    = array( __( 'Здраво, сакам да направам нарачка:', 'oriente' ) );
	$position = 0;

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$item_product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
		$quantity     = isset( $cart_item['quantity'] ) ? (int) $cart_item['quantity'] : 0;
		if ( ! $item_product instanceof WC_Product || $quantity < 1 ) {
			continue;
		}

		++$position;
		$sku     = $item_product->get_sku();
		$lines[] = sprintf(
			/* translators: 1: item number, 2: product name, 3: SKU, 4: quantity. */
			__( "%1\$d. Производ: %2\$s\nШифра: %3\$s\nКоличина: %4\$d", 'oriente' ),
			$position,
			wp_strip_all_tags( $item_product->get_name() ),
			$sku ? $sku : __( 'Не е достапна', 'oriente' ),
			$quantity
		);
	}

	if ( 0 === $position ) {
		return '';
	}

	return 'https://wa.me/' . ORIENTE_WHATSAPP_NUMBER . '?text=' . rawurlencode( implode( "\n\n", $lines ) );
}

/** Output the WhatsApp order action when a classic cart template is used. */
function oriente_whatsapp_order_button() {
	$whatsapp_url = oriente_cart_whatsapp_url();
	if ( ! $whatsapp_url ) {
		return;
	}

	printf(
		'<a href="%1$s" class="checkout-button button alt wc-forward oriente-whatsapp-order-button">%2$s</a>',
		esc_url( $whatsapp_url ),
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

/** Keep the header bag count in sync after AJAX adds. */
function oriente_cart_fragments( $fragments ) {
	ob_start();
	?>
	<span class="cart-count"><?php echo esc_html( oriente_cart_count() ); ?></span>
	<?php
	$fragments['.cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'oriente_cart_fragments' );

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

/** Add useful hierarchy and service details to the product summary. */
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

			$sku = $product instanceof WC_Product ? $product->get_sku() : '';
			?>
			<ul class="product-assurances" aria-label="<?php esc_attr_e( 'Order information', 'oriente' ); ?>">
				<li><strong><?php esc_html_e( 'Complimentary delivery', 'oriente' ); ?></strong><span><?php esc_html_e( 'On European orders over EUR 250', 'oriente' ); ?></span></li>
				<li><strong><?php esc_html_e( 'Considered dispatch', 'oriente' ); ?></strong><span><?php esc_html_e( 'Prepared within two to four working days', 'oriente' ); ?></span></li>
				<li><strong><?php esc_html_e( 'Secure checkout', 'oriente' ); ?></strong><span><?php esc_html_e( 'Protected payment and easy order support', 'oriente' ); ?></span></li>
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
