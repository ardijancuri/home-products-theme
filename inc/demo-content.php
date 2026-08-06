<?php
/**
 * One-time demo catalog installer for the local Oriente storefront.
 *
 * @package Oriente
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Import a bundled image into the WordPress media library once. */
function oriente_import_demo_image( $filename, $title, $credit = '' ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_key'       => '_oriente_demo_asset',
			'meta_value'     => $filename,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = get_template_directory() . '/assets/images/' . $filename;
	if ( ! file_exists( $source ) ) {
		return 0;
	}

	$uploads = wp_upload_dir();
	if ( ! empty( $uploads['error'] ) ) {
		return 0;
	}

	$destination_name = wp_unique_filename( $uploads['path'], 'oriente-' . $filename );
	$destination      = trailingslashit( $uploads['path'] ) . $destination_name;
	if ( ! copy( $source, $destination ) ) {
		return 0;
	}

	$filetype = wp_check_filetype( $destination_name, null );
	$attachment_id = wp_insert_attachment(
		array(
			'guid'           => trailingslashit( $uploads['url'] ) . $destination_name,
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title,
			'post_content'   => $credit,
			'post_excerpt'   => $credit,
			'post_status'    => 'inherit',
		),
		$destination
	);

	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attachment_id, $destination );
	wp_update_attachment_metadata( $attachment_id, $metadata );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );
	update_post_meta( $attachment_id, '_oriente_demo_asset', $filename );
	return (int) $attachment_id;
}

/** Return or create one of the two permitted storefront categories. */
function oriente_ensure_product_category( $name, $slug, $description ) {
	$existing = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $existing && ! is_wp_error( $existing ) ) {
		wp_update_term( $existing->term_id, 'product_cat', array( 'description' => $description ) );
		return (int) $existing->term_id;
	}

	$result = wp_insert_term(
		$name,
		'product_cat',
		array(
			'slug'        => $slug,
			'description' => $description,
		)
	);
	return is_wp_error( $result ) ? 0 : (int) $result['term_id'];
}

/** Create or refresh a simple WooCommerce demo product. */
function oriente_upsert_demo_product( $data, $category_id ) {
	$product_id = wc_get_product_id_by_sku( $data['sku'] );
	$product    = $product_id ? wc_get_product( $product_id ) : new WC_Product_Simple();
	if ( ! $product ) {
		return 0;
	}

	$product->set_name( $data['name'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_description( $data['description'] );
	$product->set_short_description( $data['short'] );
	$product->set_sku( $data['sku'] );
	$product->set_regular_price( $data['price'] );
	$product->set_price( $data['price'] );
	$product->set_category_ids( array( $category_id ) );
	$product->set_stock_status( 'instock' );
	$product->set_manage_stock( false );
	$product->set_weight( $data['weight'] );
	$product->set_virtual( false );

	$image_id = oriente_import_demo_image( $data['image'], $data['name'], $data['credit'] );
	if ( $image_id ) {
		$product->set_image_id( $image_id );
	}

	$gallery_ids = array();
	foreach ( $data['gallery'] ?? array() as $gallery_image ) {
		$gallery_id = oriente_import_demo_image( $gallery_image, $data['name'] . ' lifestyle view', $data['credit'] );
		if ( $gallery_id && $gallery_id !== $image_id ) {
			$gallery_ids[] = $gallery_id;
		}
	}
	$product->set_gallery_image_ids( array_values( array_unique( $gallery_ids ) ) );

	return (int) $product->save();
}

/** Create or refresh a small supporting page used by the storefront footer. */
function oriente_upsert_demo_page( $title, $slug, $content ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	$page_data = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
	);
	if ( $page ) {
		$page_data['ID'] = $page->ID;
	}
	return wp_insert_post( $page_data );
}

/** Seed the current clean local install with an original two-category catalog. */
function oriente_seed_demo_content() {
	if ( get_option( 'oriente_demo_content_version' ) === ORIENTE_VERSION ) {
		return;
	}
	if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product_Simple' ) || ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$kitchen_id = oriente_ensure_product_category(
		__( 'Kitchen', 'oriente' ),
		'kitchen',
		__( 'Sculptural tableware, serveware, glassware and purposeful tools selected for daily use.', 'oriente' )
	);
	$decor_id = oriente_ensure_product_category(
		__( 'Home Decoration', 'oriente' ),
		'home-decoration',
		__( 'Quiet vessels, candlelight, textiles and collectible objects that give a room its rhythm.', 'oriente' )
	);

	if ( ! $kitchen_id || ! $decor_id ) {
		return;
	}

	$products = array(
		array(
			'name' => 'Artisan Edge Serving Board', 'sku' => 'ORI-KIT-001', 'price' => '210', 'weight' => '2.4', 'category' => $kitchen_id,
			'image' => 'product-serving-board-white.png', 'gallery' => array( 'hero-still-life.webp', 'warm-table.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'A weighty olivewood board with a softly worked edge for bread, fruit and generous tables.',
			'description' => '<p>Shaped as a functional centrepiece, the Artisan Edge Serving Board celebrates the marks and colour shifts of natural wood. Each piece is intended to patinate with use.</p><p><strong>Material:</strong> Oiled olivewood<br><strong>Care:</strong> Wipe clean and oil occasionally<br><strong>Made in:</strong> Small European workshop</p>',
		),
		array(
			'name' => 'Alba Hand-thrown Bowl', 'sku' => 'ORI-KIT-002', 'price' => '95', 'weight' => '0.6', 'category' => $kitchen_id,
			'image' => 'product-alba-bowl-white.png', 'gallery' => array( 'porcelain-bowl.webp', 'hero-still-life.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'A softly irregular porcelain bowl with a luminous chalk glaze.',
			'description' => '<p>Alba is thrown and finished by hand, leaving subtle traces of the maker in its profile. Its quiet proportions work equally well for breakfast, sides or display.</p><p><strong>Material:</strong> High-fired porcelain<br><strong>Care:</strong> Dishwasher safe<br><strong>Finish:</strong> Chalk white glaze</p>',
		),
		array(
			'name' => 'Solstice Crystal Goblets', 'sku' => 'ORI-KIT-003', 'price' => '180', 'weight' => '0.8', 'category' => $kitchen_id,
			'image' => 'product-crystal-goblets-white.png', 'gallery' => array( 'hero-luxury.webp', 'warm-table.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'Four finely drawn crystal goblets, proportioned for wine, water and candlelit evenings.',
			'description' => '<p>A set of four clear goblets with slender stems and a light-catching silhouette. The simple form makes them easy to layer into both everyday and formal settings.</p><p><strong>Material:</strong> Lead-free crystal<br><strong>Set:</strong> Four goblets<br><strong>Care:</strong> Hand wash recommended</p>',
		),
		array(
			'name' => 'Cupola Porcelain Tea Pot', 'sku' => 'ORI-KIT-004', 'price' => '240', 'weight' => '1.1', 'category' => $kitchen_id,
			'image' => 'product-teapot-white.png', 'gallery' => array( 'hero-table.webp', 'linen-cups.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'An architectural white porcelain pot with a calm, domed silhouette.',
			'description' => '<p>Cupola balances a generous body with an unexpectedly precise spout. It brings a gallery-like presence to the familiar ritual of tea.</p><p><strong>Material:</strong> Glazed porcelain<br><strong>Capacity:</strong> 1.2 L<br><strong>Care:</strong> Hand wash</p>',
		),
		array(
			'name' => 'Atelier Porcelain Serving Bowl', 'sku' => 'ORI-KIT-005', 'price' => '135', 'weight' => '0.9', 'category' => $kitchen_id,
			'image' => 'product-serving-bowl-white.png', 'gallery' => array( 'white-bowl.webp', 'hero-still-life.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'A broad, low porcelain form designed to move from preparation to table.',
			'description' => '<p>Atelier is intentionally restrained: an open form, a fine rolled rim, and enough depth for salads, fruit and shared dishes.</p><p><strong>Material:</strong> Porcelain<br><strong>Diameter:</strong> 27 cm<br><strong>Care:</strong> Dishwasher safe</p>',
		),
		array(
			'name' => 'Aster Bronze Nesting Bowls', 'sku' => 'ORI-HOM-001', 'price' => '165', 'weight' => '1.8', 'category' => $decor_id,
			'image' => 'product-bronze-bowls-white.png', 'gallery' => array( 'hero-luxury.webp', 'warm-table.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'Two hand-finished bronze bowls with a mellow surface made to deepen over time.',
			'description' => '<p>Aster combines a softened profile with the warm irregularity of aged bronze. Use the pair as low centrepieces, catchalls or sculptural objects.</p><p><strong>Material:</strong> Patinated bronze<br><strong>Set:</strong> Two nesting bowls<br><strong>Care:</strong> Dust with a dry cloth</p>',
		),
		array(
			'name' => 'Selene Amber Candle Set', 'sku' => 'ORI-HOM-002', 'price' => '120', 'weight' => '0.9', 'category' => $decor_id,
			'image' => 'product-candle-set-white.png', 'gallery' => array( 'hero-luxury.webp', 'warm-table.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'Amber glass, natural wax and low evening light composed as one warm object.',
			'description' => '<p>Selene gathers three tonal glass forms around a clean-burning pillar candle. The arrangement is designed for consoles, bedside tables and intimate dinners.</p><p><strong>Material:</strong> Recycled glass and natural wax<br><strong>Set:</strong> Three pieces<br><strong>Care:</strong> Wipe with a soft cloth</p>',
		),
		array(
			'name' => 'Noma Ribbed Vessel', 'sku' => 'ORI-HOM-003', 'price' => '195', 'weight' => '2.2', 'category' => $decor_id,
			'image' => 'product-ribbed-vessel-white.png', 'gallery' => array( 'candleholder.webp', 'linen-cups.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'A deep smoke vessel with precise ribs and a generous, low profile.',
			'description' => '<p>Noma uses shadow as ornament. Its smoky surface shifts from near-black to translucent grey as the light changes.</p><p><strong>Material:</strong> Mouth-blown glass<br><strong>Height:</strong> 24 cm<br><strong>Care:</strong> Hand wash only</p>',
		),
		array(
			'name' => 'Orlis Washed Linen Set', 'sku' => 'ORI-HOM-004', 'price' => '88', 'weight' => '0.5', 'category' => $decor_id,
			'image' => 'product-linen-set-white.png', 'gallery' => array( 'hero-table.webp', 'warm-table.webp' ), 'credit' => 'Original studio image created for Oriente.',
			'short' => 'Four generously cut linen napkins in a tonal palette of flax, oat and warm grey.',
			'description' => '<p>Stone-washed for a soft hand from the first use, Orlis is made for relaxed tables and easy layering. The subtle colour variation is intentional.</p><p><strong>Material:</strong> European flax linen<br><strong>Set:</strong> Four napkins<br><strong>Care:</strong> Machine wash cool</p>',
		),
	);

	$created_ids = array();
	foreach ( $products as $product_data ) {
		$created_ids[] = oriente_upsert_demo_product( $product_data, $product_data['category'] );
	}

	$kitchen_thumb = oriente_import_demo_image( 'hero-still-life.webp', 'The Oriente kitchen', 'Demo photography: Magdalena Raczka / Unsplash.' );
	$decor_thumb   = oriente_import_demo_image( 'vase-interior.webp', 'Oriente home decoration', 'Demo photography: volant / Unsplash.' );
	if ( $kitchen_thumb ) {
		update_term_meta( $kitchen_id, 'thumbnail_id', $kitchen_thumb );
	}
	if ( $decor_thumb ) {
		update_term_meta( $decor_id, 'thumbnail_id', $decor_thumb );
	}

	$uncategorized = get_term_by( 'slug', 'uncategorized', 'product_cat' );
	update_option( 'default_product_cat', $kitchen_id );
	if ( $uncategorized && (int) $uncategorized->term_id !== $kitchen_id ) {
		wp_delete_term( $uncategorized->term_id, 'product_cat' );
	}

	update_option( 'woocommerce_currency', 'EUR' );
	update_option( 'woocommerce_currency_pos', 'right_space' );
	update_option( 'blogname', 'Oriente' );
	update_option( 'blogdescription', 'Inspired Living.' );

	oriente_upsert_demo_page(
		'Contact',
		'contact',
		'<h2>We would be pleased to help.</h2><p>For product questions, trade enquiries and order support, write to <a href="mailto:hello@oriente.studio">hello@oriente.studio</a>.</p><p>Our studio replies Monday to Friday, 09:00–17:00 CET.</p>'
	);
	oriente_upsert_demo_page(
		'Privacy Policy',
		'privacy-policy',
		'<h2>Your privacy</h2><p>This demonstration storefront collects only the information required to process an order or respond to an enquiry. Before launch, replace this page with the privacy, cookie, payment and retention terms that apply to your business and region.</p><h2>Store data</h2><p>WooCommerce may store account, cart and order information. Configure your production analytics, payment providers and email platform here before accepting live orders.</p>'
	);
	update_option( 'oriente_demo_content_version', ORIENTE_VERSION );
}
add_action( 'init', 'oriente_seed_demo_content', 40 );
