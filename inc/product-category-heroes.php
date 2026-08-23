<?php
/**
 * Editorial heroes and child-category product cards for product archives.
 *
 * @package Oriente
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ORIENTE_CATEGORY_HERO_META_KEY', 'oriente_category_hero_image_id' );

/** Render storefront archive breadcrumbs containing ancestors only. */
function oriente_archive_breadcrumbs( $args = array() ) {
	$args = wp_parse_args(
		$args,
		apply_filters(
			'woocommerce_breadcrumb_defaults',
			array(
				'delimiter'   => '<span class="breadcrumb-separator" aria-hidden="true">/</span>',
				'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'woocommerce' ) . '">',
				'wrap_after'  => '</nav>',
				'before'      => '',
				'after'       => '',
				'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
			)
		)
	);

	$crumbs = array();
	if ( ! empty( $args['home'] ) ) {
		$crumbs[] = array(
			$args['home'],
			apply_filters( 'woocommerce_breadcrumb_home_url', home_url() ),
		);
	}

	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$current_term = get_queried_object();
		if ( $current_term instanceof WP_Term ) {
			$ancestor_ids = array_reverse( get_ancestors( $current_term->term_id, 'product_cat', 'taxonomy' ) );
			foreach ( $ancestor_ids as $ancestor_id ) {
				$ancestor = get_term( $ancestor_id, 'product_cat' );
				if ( $ancestor instanceof WP_Term ) {
					$ancestor_url = get_term_link( $ancestor );
					if ( ! is_wp_error( $ancestor_url ) ) {
						$crumbs[] = array( $ancestor->name, $ancestor_url );
					}
				}
			}
		}
	}

	if ( empty( $crumbs ) ) {
		return;
	}

	echo wp_kses_post( $args['wrap_before'] );
	foreach ( $crumbs as $index => $crumb ) {
		echo wp_kses_post( $args['before'] );
		if ( ! empty( $crumb[1] ) ) {
			printf( '<a href="%1$s">%2$s</a>', esc_url( $crumb[1] ), esc_html( $crumb[0] ) );
		} else {
			echo esc_html( $crumb[0] );
		}
		echo wp_kses_post( $args['after'] );

		if ( $index < count( $crumbs ) - 1 ) {
			echo wp_kses_post( $args['delimiter'] );
		}
	}
	echo wp_kses_post( $args['wrap_after'] );
}

/** Return the direct children of a product category in storefront order. */
function oriente_category_hero_children( $term_id ) {
	if ( function_exists( 'oriente_product_category_children' ) ) {
		return oriente_product_category_children( $term_id );
	}

	$children = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => absint( $term_id ),
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	return is_wp_error( $children ) ? array() : $children;
}

/** Return the queried parent category and its children when it is a main archive. */
function oriente_main_product_category_context() {
	if ( ! function_exists( 'is_product_category' ) || ! is_product_category() ) {
		return null;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy || 0 !== (int) $term->parent ) {
		return null;
	}

	$children = oriente_category_hero_children( $term->term_id );
	if ( empty( $children ) ) {
		return null;
	}

	return array(
		'term'     => $term,
		'children' => $children,
	);
}

/** Return the generated theme fallback for a supported parent category. */
function oriente_default_category_hero_url( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$defaults = array(
		'kitchen'         => 'category-kitchen-hero-v1.webp',
		'home-decor'      => 'category-home-decor-hero-v1.webp',
		'home-decoration' => 'category-home-decor-hero-v1.webp',
	);

	if ( isset( $defaults[ $term->slug ] ) ) {
		return get_theme_file_uri( '/assets/images/' . $defaults[ $term->slug ] );
	}

	$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
	return $thumbnail_id ? (string) wp_get_attachment_image_url( $thumbnail_id, 'full' ) : '';
}

/** Return the custom category hero URL, falling back to the generated theme image. */
function oriente_category_hero_url( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$image_id = absint( get_term_meta( $term->term_id, ORIENTE_CATEGORY_HERO_META_KEY, true ) );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
	return $image_url ? $image_url : oriente_default_category_hero_url( $term );
}

/** Return the top-level product categories shown in the Shop hero rail. */
function oriente_shop_main_categories() {
	if ( ! function_exists( 'is_shop' ) || ! is_shop() || ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$categories = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $categories ) ) {
		return array();
	}

	$categories = array_values(
		array_filter(
			$categories,
			static function ( $category ) {
				return $category instanceof WP_Term && ( $category->count || oriente_category_hero_children( $category->term_id ) );
			}
		)
	);

	$preferred_order = array( 'kitchen', 'home-decor', 'home-decoration' );
	usort(
		$categories,
		static function ( $first, $second ) use ( $preferred_order ) {
			$first_position  = array_search( $first->slug, $preferred_order, true );
			$second_position = array_search( $second->slug, $preferred_order, true );
			$first_position  = false === $first_position ? PHP_INT_MAX : $first_position;
			$second_position = false === $second_position ? PHP_INT_MAX : $second_position;

			return $first_position === $second_position
				? strcasecmp( $first->name, $second->name )
				: $first_position <=> $second_position;
		}
	);

	return $categories;
}

/** Return the Shop page featured image or the theme's editorial fallback. */
function oriente_shop_hero_url() {
	$shop_page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
	$image_id     = $shop_page_id > 0 ? get_post_thumbnail_id( $shop_page_id ) : 0;
	$image_url    = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

	return $image_url ? $image_url : get_theme_file_uri( '/assets/images/shop-hero-living-v1.webp' );
}

/** Render one top-level category card in the Shop hero rail. */
function oriente_render_shop_category_card( $category ) {
	if ( ! $category instanceof WP_Term ) {
		return;
	}

	$category_url = get_term_link( $category );
	if ( is_wp_error( $category_url ) ) {
		return;
	}

	$product   = oriente_category_featured_product( $category );
	$image_id  = oriente_product_secondary_image_id( $product );
	$image_url = $image_id ? '' : oriente_category_hero_url( $category );
	?>
	<article class="product-editorial-item category-subcategory-card shop-main-category-card" data-reveal>
		<a class="category-subcategory-card__link" href="<?php echo esc_url( $category_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Explore %s', 'oriente' ), $category->name ) ); ?>">
			<div class="product-media category-subcategory-card__media">
				<?php if ( $image_id ) : ?>
					<?php
					echo wp_kses_post(
						wp_get_attachment_image(
							$image_id,
							'oriente-product',
							false,
							array(
								'class'     => 'category-subcategory-card__image',
								'alt'       => sprintf( __( '%s collection', 'oriente' ), $category->name ),
								'draggable' => 'false',
								'loading'   => 'lazy',
							)
						)
					);
					?>
				<?php elseif ( $image_url ) : ?>
					<img class="category-subcategory-card__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s collection', 'oriente' ), $category->name ) ); ?>" loading="lazy" decoding="async" draggable="false">
				<?php endif; ?>
			</div>
			<span class="product-category category-subcategory-card__label"><?php echo esc_html( $category->name ); ?></span>
		</a>
	</article>
	<?php
}

/** Render the Shop hero with a rail of top-level product categories. */
function oriente_render_shop_header() {
	if ( ! function_exists( 'is_shop' ) || ! is_shop() ) {
		return;
	}

	$shop_page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
	$title        = woocommerce_page_title( false );
	$description  = $shop_page_id > 0 ? trim( (string) get_post_field( 'post_excerpt', $shop_page_id ) ) : '';
	$description  = $description ? $description : __( 'Tableware and home accessories for everyday use.', 'oriente' );
	$categories   = oriente_shop_main_categories();
	$hero_url     = oriente_shop_hero_url();
	?>
	<section class="product-category-hero product-category-hero--shop" aria-labelledby="shop-hero-title">
		<?php if ( $hero_url ) : ?>
			<img class="product-category-hero__image" src="<?php echo esc_url( $hero_url ); ?>" alt="<?php esc_attr_e( 'Oriente objects for the table and home', 'oriente' ); ?>" fetchpriority="high" decoding="async">
		<?php endif; ?>
		<div class="product-category-hero__layout">
			<div class="product-category-hero__content">
				<?php
				oriente_archive_breadcrumbs(
					array(
						'wrap_before' => '<nav class="woocommerce-breadcrumb product-category-hero__breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'woocommerce' ) . '">',
						'wrap_after'  => '</nav>',
						'delimiter'   => '<span class="breadcrumb-separator" aria-hidden="true">/</span>',
					)
				);
				?>
				<h1 id="shop-hero-title"><?php echo esc_html( $title ); ?></h1>
				<div class="product-category-hero__description"><p><?php echo esc_html( $description ); ?></p></div>
			</div>
			<?php if ( $categories ) : ?>
				<div class="category-subcategory-products" role="region" aria-label="<?php esc_attr_e( 'Explore main categories', 'oriente' ); ?>" data-slider>
					<div class="category-subcategory-products__grid" data-slider-track tabindex="0">
						<?php foreach ( $categories as $category ) : ?>
							<?php oriente_render_shop_category_card( $category ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
add_action( 'woocommerce_archive_description', 'oriente_render_shop_header', 5 );

/** Return one random published product from a category, preferring products with gallery images. */
function oriente_category_featured_product( $category ) {
	if ( ! $category instanceof WP_Term || ! function_exists( 'wc_get_products' ) ) {
		return null;
	}

	$category_slugs = array( $category->slug );
	$descendant_ids = get_term_children( $category->term_id, 'product_cat' );
	if ( ! is_wp_error( $descendant_ids ) && $descendant_ids ) {
		foreach ( $descendant_ids as $descendant_id ) {
			$descendant = get_term( $descendant_id, 'product_cat' );
			if ( $descendant instanceof WP_Term ) {
				$category_slugs[] = $descendant->slug;
			}
		}
	}

	$products = wc_get_products(
		array(
			'status'   => 'publish',
			'limit'    => 24,
			'category' => array_values( array_unique( $category_slugs ) ),
			'orderby'  => 'rand',
			'return'   => 'objects',
		)
	);

	foreach ( $products as $product ) {
		if ( $product instanceof WC_Product && $product->get_gallery_image_ids() ) {
			return $product;
		}
	}

	return ! empty( $products ) && $products[0] instanceof WC_Product ? $products[0] : null;
}

/** Return the second product-page image, falling back to the featured image. */
function oriente_product_secondary_image_id( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return 0;
	}

	$gallery_ids = array_values( array_filter( $product->get_gallery_image_ids() ) );
	return $gallery_ids ? absint( $gallery_ids[0] ) : absint( $product->get_image_id() );
}

/** Return card data for each populated direct child category. */
function oriente_category_child_product_cards( $children ) {
	$cards = array();

	foreach ( (array) $children as $child ) {
		$product = oriente_category_featured_product( $child );
		if ( ! $product ) {
			continue;
		}

		$image_id = oriente_product_secondary_image_id( $product );
		if ( ! $image_id ) {
			continue;
		}

		$cards[] = array(
			'category' => $child,
			'product'  => $product,
			'image_id' => $image_id,
		);
	}

	return $cards;
}

/** Render one secondary-image tile linking to its child category. */
function oriente_render_category_child_product_card( $card ) {
	$child        = $card['category'];
	$product      = $card['product'];
	$category_url = get_term_link( $child );
	$category_url = is_wp_error( $category_url ) ? get_permalink( $product->get_id() ) : $category_url;
	?>
	<article class="product-editorial-item category-subcategory-card" data-reveal>
		<a class="category-subcategory-card__link" href="<?php echo esc_url( $category_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Explore %s', 'oriente' ), $child->name ) ); ?>">
			<div class="product-media category-subcategory-card__media">
				<?php
				echo wp_kses_post(
					wp_get_attachment_image(
						$card['image_id'],
						'oriente-product',
						false,
						array(
							'class'     => 'category-subcategory-card__image',
							'alt'       => sprintf( __( '%s collection', 'oriente' ), $child->name ),
							'draggable' => 'false',
							'loading'   => 'lazy',
						)
					)
				);
				?>
			</div>
			<span class="product-category category-subcategory-card__label"><?php echo esc_html( $child->name ); ?></span>
		</a>
	</article>
	<?php
}

/** Render the main category hero and one secondary-image card per populated child. */
function oriente_render_main_category_header() {
	$context = oriente_main_product_category_context();
	if ( ! $context ) {
		return;
	}

	$term        = $context['term'];
	$hero_url    = oriente_category_hero_url( $term );
	$description = term_description( $term, 'product_cat' );
	$cards       = oriente_category_child_product_cards( $context['children'] );
	?>
	<section class="product-category-hero product-category-hero--<?php echo esc_attr( $term->slug ); ?>" aria-labelledby="product-category-hero-title">
		<?php if ( $hero_url ) : ?>
			<img class="product-category-hero__image" src="<?php echo esc_url( $hero_url ); ?>" alt="<?php echo esc_attr( sprintf( __( '%s collection', 'oriente' ), $term->name ) ); ?>" fetchpriority="high" decoding="async">
		<?php endif; ?>
		<div class="product-category-hero__layout">
			<div class="product-category-hero__content">
				<?php
				if ( function_exists( 'oriente_archive_breadcrumbs' ) ) {
					oriente_archive_breadcrumbs(
						array(
							'wrap_before' => '<nav class="woocommerce-breadcrumb product-category-hero__breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'woocommerce' ) . '">',
							'wrap_after'  => '</nav>',
							'delimiter'   => '<span class="breadcrumb-separator" aria-hidden="true">/</span>',
						)
					);
				}
				?>
				<h1 id="product-category-hero-title"><?php echo esc_html( $term->name ); ?></h1>
				<?php if ( $description ) : ?>
					<div class="product-category-hero__description"><?php echo wp_kses_post( $description ); ?></div>
				<?php endif; ?>
			</div>
			<?php if ( $cards ) : ?>
				<div class="category-subcategory-products" role="region" aria-label="<?php esc_attr_e( 'Explore product subcategories', 'oriente' ); ?>" data-slider>
					<div class="category-subcategory-products__grid" data-slider-track tabindex="0">
						<?php foreach ( $cards as $card ) : ?>
							<?php oriente_render_category_child_product_card( $card ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
add_action( 'woocommerce_archive_description', 'oriente_render_main_category_header', 5 );

/** Suppress WooCommerce's duplicate title on custom hero archives. */
function oriente_main_category_show_page_title( $show ) {
	$is_shop = function_exists( 'is_shop' ) && is_shop();
	return oriente_main_product_category_context() || $is_shop ? false : $show;
}
add_filter( 'woocommerce_show_page_title', 'oriente_main_category_show_page_title' );

/** Remove the default term description after the custom parent-category hero is ready. */
function oriente_prepare_main_category_archive() {
	if ( oriente_main_product_category_context() ) {
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	}
}
add_action( 'wp', 'oriente_prepare_main_category_archive', 25 );

/** Add body classes used to release custom archive heroes to full width. */
function oriente_main_category_body_class( $classes ) {
	$is_shop = function_exists( 'is_shop' ) && is_shop();
	if ( oriente_main_product_category_context() || $is_shop ) {
		$classes[] = 'has-oriente-category-hero';
	}
	if ( $is_shop ) {
		$classes[] = 'has-oriente-shop-hero';
	}
	return $classes;
}
add_filter( 'body_class', 'oriente_main_category_body_class' );

/** Render the shared category-hero media picker. */
function oriente_category_hero_admin_control( $term = null ) {
	$image_id   = $term instanceof WP_Term ? absint( get_term_meta( $term->term_id, ORIENTE_CATEGORY_HERO_META_KEY, true ) ) : 0;
	$default_url = $term instanceof WP_Term ? oriente_default_category_hero_url( $term ) : '';
	$preview_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium_large' ) : $default_url;
	?>
	<div class="oriente-category-hero-control" data-default-url="<?php echo esc_url( $default_url ); ?>">
		<img class="oriente-category-hero-preview" src="<?php echo esc_url( $preview_url ); ?>" alt=""<?php echo $preview_url ? '' : ' hidden'; ?>>
		<input type="hidden" name="oriente_category_hero_image_id" value="<?php echo esc_attr( $image_id ); ?>" data-oriente-category-hero-id>
		<div class="oriente-category-hero-actions">
			<button class="button" type="button" data-oriente-category-hero-select data-media-title="<?php esc_attr_e( 'Choose category hero image', 'oriente' ); ?>" data-media-button="<?php esc_attr_e( 'Use as category hero', 'oriente' ); ?>"><?php esc_html_e( 'Choose image', 'oriente' ); ?></button>
			<button class="button-link-delete" type="button" data-oriente-category-hero-remove<?php echo $image_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Use default image', 'oriente' ); ?></button>
		</div>
	</div>
	<p class="description"><?php esc_html_e( 'Recommended: a wide landscape image. Parent categories use the generated Oriente image until you choose a replacement.', 'oriente' ); ?></p>
	<?php
}

/** Add the hero picker to the new product-category form. */
function oriente_category_hero_add_field() {
	wp_nonce_field( 'oriente_save_category_hero', 'oriente_category_hero_nonce' );
	?>
	<div class="form-field term-oriente-category-hero-wrap">
		<label><?php esc_html_e( 'Category hero image', 'oriente' ); ?></label>
		<?php oriente_category_hero_admin_control(); ?>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'oriente_category_hero_add_field' );

/** Add the hero picker to the product-category edit form. */
function oriente_category_hero_edit_field( $term ) {
	wp_nonce_field( 'oriente_save_category_hero', 'oriente_category_hero_nonce' );
	?>
	<tr class="form-field term-oriente-category-hero-wrap">
		<th scope="row"><label><?php esc_html_e( 'Category hero image', 'oriente' ); ?></label></th>
		<td><?php oriente_category_hero_admin_control( $term ); ?></td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'oriente_category_hero_edit_field' );

/** Save the category hero selected from the Media Library. */
function oriente_save_category_hero( $term_id ) {
	if ( ! isset( $_POST['oriente_category_hero_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['oriente_category_hero_nonce'] ) ), 'oriente_save_category_hero' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_product_terms' ) ) {
		return;
	}

	$image_id = isset( $_POST['oriente_category_hero_image_id'] ) ? absint( wp_unslash( $_POST['oriente_category_hero_image_id'] ) ) : 0;
	if ( $image_id && wp_attachment_is_image( $image_id ) ) {
		update_term_meta( $term_id, ORIENTE_CATEGORY_HERO_META_KEY, $image_id );
	} else {
		delete_term_meta( $term_id, ORIENTE_CATEGORY_HERO_META_KEY );
	}
}
add_action( 'created_product_cat', 'oriente_save_category_hero' );
add_action( 'edited_product_cat', 'oriente_save_category_hero' );

/** Load the Media Library picker only on product-category admin screens. */
function oriente_category_hero_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'product_cat' !== $screen->taxonomy ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'oriente-category-hero-admin', get_theme_file_uri( '/assets/css/category-hero-admin.css' ), array(), ORIENTE_VERSION );
	wp_enqueue_script( 'oriente-category-hero-admin', get_theme_file_uri( '/assets/js/category-hero-admin.js' ), array(), ORIENTE_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'oriente_category_hero_admin_assets' );
