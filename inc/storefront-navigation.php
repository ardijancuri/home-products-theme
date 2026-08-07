<?php
/**
 * Storefront category structure and header navigation.
 *
 * @package Oriente
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return the curated child categories shown in the storefront navigation. */
function oriente_storefront_category_groups() {
	return array(
		'kitchen'         => array(
			array(
				'name'        => __( 'Glassware', 'oriente' ),
				'slug'        => 'glassware',
				'description' => __( 'Wine glasses, tumblers and refined vessels for everyday and occasion-led tables.', 'oriente' ),
			),
			array(
				'name'        => __( 'Dinnerware', 'oriente' ),
				'slug'        => 'dinnerware',
				'description' => __( 'Dinnerware sets and individual plates for considered table settings.', 'oriente' ),
			),
			array(
				'name'        => __( 'Cutlery', 'oriente' ),
				'slug'        => 'cutlery',
				'description' => __( 'Spoons, forks and knives selected for balance, finish and daily use.', 'oriente' ),
			),
			array(
				'name'        => __( 'Coffee & Tea Cups', 'oriente' ),
				'slug'        => 'coffee-tea-cups',
				'description' => __( 'Cups and refined forms for coffee, tea and quiet daily rituals.', 'oriente' ),
			),
			array(
				'name'        => __( 'Decorative Bowls & Bonbonnières', 'oriente' ),
				'slug'        => 'decorative-bowls-bonbonnieres',
				'description' => __( 'Decorative bowls, nesting sets and bonbonnières for serving or display.', 'oriente' ),
			),
		),
		'home-decoration' => array(
			array(
				'name'        => __( 'Decorative Vases', 'oriente' ),
				'slug'        => 'decorative-vases',
				'description' => __( 'Sculptural vessels and decorative vases designed to give a room presence.', 'oriente' ),
			),
			array(
				'name'        => __( 'Home Fragrance', 'oriente' ),
				'slug'        => 'home-fragrance',
				'description' => __( 'Candles, diffusers and considered scents that shape the atmosphere of a home.', 'oriente' ),
			),
		),
	);
}

/** Create the curated category hierarchy once WooCommerce has registered its taxonomy. */
function oriente_ensure_storefront_subcategories() {
	if ( '1.0.0' === get_option( 'oriente_storefront_categories_version' ) || ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$complete = true;
	foreach ( oriente_storefront_category_groups() as $parent_slug => $categories ) {
		$parent = get_term_by( 'slug', $parent_slug, 'product_cat' );
		if ( ! $parent || is_wp_error( $parent ) ) {
			$complete = false;
			continue;
		}

		foreach ( $categories as $category ) {
			$existing = get_term_by( 'slug', $category['slug'], 'product_cat' );
			$args     = array(
				'name'        => $category['name'],
				'description' => $category['description'],
				'parent'      => (int) $parent->term_id,
			);

			if ( $existing && ! is_wp_error( $existing ) ) {
				$result = wp_update_term( $existing->term_id, 'product_cat', $args );
			} else {
				$args['slug'] = $category['slug'];
				$result       = wp_insert_term( $category['name'], 'product_cat', $args );
			}

			if ( is_wp_error( $result ) ) {
				$complete = false;
			}
		}
	}

	if ( $complete ) {
		update_option( 'oriente_storefront_categories_version', '1.0.0', false );
	}
}
add_action( 'init', 'oriente_ensure_storefront_subcategories', 45 );

/** Return child category terms in the same editorial order used in the navigation. */
function oriente_storefront_subcategories( $parent_slug ) {
	$groups = oriente_storefront_category_groups();
	if ( empty( $groups[ $parent_slug ] ) || ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}

	$terms = array();
	foreach ( $groups[ $parent_slug ] as $category ) {
		$term = get_term_by( 'slug', $category['slug'], 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$terms[] = $term;
		}
	}
	return $terms;
}

/** Return two current products for the visual side of a category mega menu. */
function oriente_mega_menu_products( $parent_slug ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	return wc_get_products(
		array(
			'status'     => 'publish',
			'limit'      => 2,
			'category'   => array( $parent_slug ),
			'orderby'    => 'date',
			'order'      => 'DESC',
			'visibility' => 'visible',
		)
	);
}

/** Render a desktop category mega menu with subcategories and product imagery. */
function oriente_render_desktop_mega_menu( $parent_slug, $label ) {
	$parent_url    = oriente_category_url( $parent_slug );
	$subcategories = oriente_storefront_subcategories( $parent_slug );
	$products      = oriente_mega_menu_products( $parent_slug );
	$panel_id      = 'mega-menu-' . sanitize_html_class( $parent_slug );
	?>
	<div class="primary-nav-item primary-nav-item--mega" data-mega-menu>
		<span class="mega-menu-trigger">
			<a href="<?php echo esc_url( $parent_url ); ?>"><?php echo esc_html( $label ); ?></a>
			<button class="mega-menu-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-mega-toggle>
				<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Open %s menu', 'oriente' ), $label ) ); ?></span>
				<svg viewBox="0 0 12 7" aria-hidden="true"><path d="m1 1 5 5 5-5" /></svg>
			</button>
		</span>
		<div id="<?php echo esc_attr( $panel_id ); ?>" class="mega-menu">
			<div class="mega-menu-inner">
				<div class="mega-menu-categories">
					<span class="mega-menu-kicker"><?php esc_html_e( 'Shop by category', 'oriente' ); ?></span>
					<div class="mega-menu-category-list">
						<?php foreach ( $subcategories as $subcategory ) : ?>
							<a class="mega-menu-category-link" href="<?php echo esc_url( get_term_link( $subcategory ) ); ?>"><?php echo esc_html( $subcategory->name ); ?></a>
						<?php endforeach; ?>
					</div>
					<a class="mega-menu-view-all" href="<?php echo esc_url( $parent_url ); ?>"><?php echo esc_html( sprintf( __( 'View all %s', 'oriente' ), $label ) ); ?></a>
				</div>
				<div class="mega-menu-products">
					<?php foreach ( $products as $product ) : ?>
						<a class="mega-product-card" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
							<span class="mega-product-image">
								<?php
								if ( $product->get_image_id() ) {
									echo wp_kses_post(
										wp_get_attachment_image(
											$product->get_image_id(),
											'woocommerce_thumbnail',
											false,
											array(
												'loading' => 'lazy',
												'sizes'   => '(max-width: 1120px) 28vw, 360px',
											)
										)
									);
								} elseif ( function_exists( 'wc_placeholder_img' ) ) {
									echo wp_kses_post( wc_placeholder_img( 'woocommerce_thumbnail' ) );
								}
								?>
							</span>
							<span class="mega-product-title"><?php echo esc_html( $product->get_name() ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/** Render an accessible mobile accordion for a parent storefront category. */
function oriente_render_mobile_category_group( $parent_slug, $label ) {
	$subcategories = oriente_storefront_subcategories( $parent_slug );
	$panel_id      = 'mobile-categories-' . sanitize_html_class( $parent_slug );
	?>
	<div class="mobile-nav-group" data-mobile-category>
		<div class="mobile-nav-heading">
			<a class="mobile-primary-link" href="<?php echo esc_url( oriente_category_url( $parent_slug ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<button class="mobile-category-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-mobile-category-toggle>
				<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Show %s categories', 'oriente' ), $label ) ); ?></span>
				<svg viewBox="0 0 16 9" aria-hidden="true"><path d="m1 1 7 7 7-7" /></svg>
			</button>
		</div>
		<div id="<?php echo esc_attr( $panel_id ); ?>" class="mobile-subcategories" data-mobile-category-panel hidden>
			<?php foreach ( $subcategories as $subcategory ) : ?>
				<a href="<?php echo esc_url( get_term_link( $subcategory ) ); ?>"><?php echo esc_html( $subcategory->name ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
