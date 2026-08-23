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
				'description' => __( 'Wine glasses, tumblers and glassware for everyday use and special occasions.', 'oriente' ),
			),
			array(
				'name'        => __( 'Dinnerware', 'oriente' ),
				'slug'        => 'dinnerware',
				'description' => __( 'Dinnerware sets, plates and bowls for everyday meals and gatherings.', 'oriente' ),
			),
			array(
				'name'        => __( 'Cutlery', 'oriente' ),
				'slug'        => 'cutlery',
				'description' => __( 'Spoons, forks and knives chosen for comfort, balance and daily use.', 'oriente' ),
			),
			array(
				'name'        => __( 'Coffee & Tea Cups', 'oriente' ),
				'slug'        => 'coffee-tea-cups',
				'description' => __( 'Cups and mugs for coffee, tea and everyday use.', 'oriente' ),
			),
			array(
				'name'        => __( 'Decorative Bowls & Bonbonnières', 'oriente' ),
				'slug'        => 'decorative-bowls-bonbonnieres',
				'description' => __( 'Decorative bowls and bonbonnières for serving, storage or display.', 'oriente' ),
			),
		),
		'home-decoration' => array(
			array(
				'name'        => __( 'Decorative Vases', 'oriente' ),
				'slug'        => 'decorative-vases',
				'description' => __( 'Decorative vases and sculptural vessels for shelves, tables and consoles.', 'oriente' ),
			),
			array(
				'name'        => __( 'Home Fragrance', 'oriente' ),
				'slug'        => 'home-fragrance',
				'description' => __( 'Candles and diffusers that add a simple, lasting scent to your home.', 'oriente' ),
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

/** Return all direct product-category children for an automatic menu hierarchy. */
function oriente_product_category_children( $parent_term_id ) {
	static $children_by_parent = array();

	$parent_term_id = (int) $parent_term_id;
	if ( ! $parent_term_id || ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}
	if ( isset( $children_by_parent[ $parent_term_id ] ) ) {
		return $children_by_parent[ $parent_term_id ];
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => $parent_term_id,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) ) {
		$children_by_parent[ $parent_term_id ] = array();
		return $children_by_parent[ $parent_term_id ];
	}

	$parent_term = get_term( $parent_term_id, 'product_cat' );
	$curated     = $parent_term && ! is_wp_error( $parent_term ) ? oriente_storefront_subcategories( $parent_term->slug ) : array();
	if ( $curated ) {
		$curated_ids = wp_list_pluck( $curated, 'term_id' );
		$extras      = array_filter(
			$terms,
			static function ( $term ) use ( $curated_ids ) {
				return ! in_array( $term->term_id, $curated_ids, true );
			}
		);
		$terms = array_merge( $curated, $extras );
	}

	$children_by_parent[ $parent_term_id ] = $terms;
	return $children_by_parent[ $parent_term_id ];
}

/** Return random visible products assigned to the mega menu's child categories. */
function oriente_mega_menu_products( $subcategories, $limit = 2 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$subcategory_slugs = array();
	foreach ( (array) $subcategories as $subcategory ) {
		if ( $subcategory instanceof WP_Term && 'product_cat' === $subcategory->taxonomy ) {
			$subcategory_slugs[] = $subcategory->slug;
		}
	}
	$subcategory_slugs = array_values( array_unique( array_filter( $subcategory_slugs ) ) );
	if ( empty( $subcategory_slugs ) ) {
		return array();
	}

	return wc_get_products(
		array(
			'status'     => 'publish',
			'limit'      => max( 1, absint( $limit ) ),
			'category'   => $subcategory_slugs,
			'orderby'    => 'rand',
			'visibility' => 'visible',
		)
	);
}

/** Render the reusable category mega-menu panel. */
function oriente_render_mega_menu_panel( $parent_term, $label, $panel_id, $subcategories = array() ) {
	if ( ! $parent_term instanceof WP_Term ) {
		return;
	}

	$subcategories = $subcategories ? $subcategories : oriente_product_category_children( $parent_term->term_id );
	$parent_url     = get_term_link( $parent_term );
	$parent_url     = is_wp_error( $parent_url ) ? oriente_shop_url() : $parent_url;
	$products       = oriente_mega_menu_products( $subcategories );
	?>
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
					<?php
					$gallery_image_ids = array_values( array_filter( $product->get_gallery_image_ids() ) );
					$mega_image_id      = $gallery_image_ids ? (int) $gallery_image_ids[0] : (int) $product->get_image_id();
					?>
					<a class="mega-product-card" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
						<span class="mega-product-image">
							<?php
							if ( $mega_image_id ) {
								echo wp_kses_post(
									wp_get_attachment_image(
										$mega_image_id,
										'oriente-product',
										false,
										array(
											'class'     => 'mega-product-image__asset',
											'loading' => 'lazy',
											'sizes'     => '(max-width: 1120px) 28vw, 360px',
											'draggable' => 'false',
										)
									)
								);
							} elseif ( function_exists( 'wc_placeholder_img' ) ) {
								echo wp_kses_post( wc_placeholder_img( 'oriente-product' ) );
							}
							?>
						</span>
						<span class="mega-product-title"><?php echo esc_html( $product->get_name() ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

/** Render the curated fallback desktop category mega menu. */
function oriente_render_desktop_mega_menu( $parent_slug, $label ) {
	$parent_term = get_term_by( 'slug', $parent_slug, 'product_cat' );
	$parent_url  = oriente_category_url( $parent_slug );
	if ( ! $parent_term || is_wp_error( $parent_term ) ) {
		printf( '<a href="%1$s">%2$s</a>', esc_url( $parent_url ), esc_html( $label ) );
		return;
	}

	$subcategories = oriente_product_category_children( $parent_term->term_id );
	if ( empty( $subcategories ) ) {
		printf( '<a href="%1$s">%2$s</a>', esc_url( $parent_url ), esc_html( $label ) );
		return;
	}

	$panel_id = 'mega-menu-' . sanitize_html_class( $parent_slug );
	?>
	<div class="primary-nav-item primary-nav-item--mega" data-mega-menu>
		<span class="mega-menu-trigger">
			<a href="<?php echo esc_url( $parent_url ); ?>"><?php echo esc_html( $label ); ?></a>
			<button class="mega-menu-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-mega-toggle>
				<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Open %s menu', 'oriente' ), $label ) ); ?></span>
				<svg viewBox="0 0 12 7" aria-hidden="true"><path d="m1 1 5 5 5-5" /></svg>
			</button>
		</span>
		<?php oriente_render_mega_menu_panel( $parent_term, $label, $panel_id, $subcategories ); ?>
	</div>
	<?php
}

/** Return automatic mega-menu data for a top-level product-category menu item. */
function oriente_primary_menu_category_data( $menu_item, $depth, $args ) {
	if (
		0 !== (int) $depth ||
		empty( $args->theme_location ) ||
		'primary' !== $args->theme_location ||
		! in_array( $args->menu_id, array( 'header-primary-menu', 'mobile-primary-menu' ), true ) ||
		'taxonomy' !== $menu_item->type ||
		'product_cat' !== $menu_item->object
	) {
		return false;
	}

	$parent_term = get_term( (int) $menu_item->object_id, 'product_cat' );
	if ( ! $parent_term || is_wp_error( $parent_term ) ) {
		return false;
	}

	$subcategories = oriente_product_category_children( $parent_term->term_id );
	if ( empty( $subcategories ) ) {
		return false;
	}

	return array(
		'parent'        => $parent_term,
		'subcategories' => $subcategories,
	);
}

/** Mark eligible Primary menu items for their desktop or mobile category treatment. */
function oriente_primary_menu_mega_classes( $classes, $menu_item, $args, $depth ) {
	$category_data = oriente_primary_menu_category_data( $menu_item, $depth, $args );
	if ( ! $category_data ) {
		return array_unique( $classes );
	}

	if ( 'header-primary-menu' === $args->menu_id ) {
		$classes[] = 'primary-nav-item';
		$classes[] = 'primary-nav-item--mega';
	} elseif ( 'mobile-primary-menu' === $args->menu_id ) {
		$classes[] = 'mobile-nav-group';
	}
	return array_unique( $classes );
}
add_filter( 'nav_menu_css_class', 'oriente_primary_menu_mega_classes', 10, 4 );

/** Add JavaScript hooks to automatic category menu items. */
function oriente_primary_menu_mega_attributes( $attributes, $menu_item, $args, $depth ) {
	$category_data = oriente_primary_menu_category_data( $menu_item, $depth, $args );
	if ( ! $category_data ) {
		return $attributes;
	}

	if ( 'header-primary-menu' === $args->menu_id ) {
		$attributes['data-mega-menu'] = 'true';
	} elseif ( 'mobile-primary-menu' === $args->menu_id ) {
		$attributes['data-mobile-category'] = 'true';
	}
	return $attributes;
}
add_filter( 'nav_menu_item_attributes', 'oriente_primary_menu_mega_attributes', 10, 4 );

/** Add automatic category mega panels on desktop and child-category lists on mobile. */
function oriente_primary_menu_category_output( $item_output, $menu_item, $depth, $args ) {
	$category_data = oriente_primary_menu_category_data( $menu_item, $depth, $args );
	if ( ! $category_data ) {
		return $item_output;
	}

	if ( 'mobile-primary-menu' === $args->menu_id ) {
		$label    = wp_strip_all_tags( $menu_item->title );
		$panel_id = 'mobile-menu-categories-' . (int) $menu_item->ID;
		ob_start();
		?>
		<div class="mobile-nav-heading">
			<?php echo $item_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Generated by the core menu walker. ?>
			<button class="mobile-category-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-mobile-category-toggle>
				<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Show %s categories', 'oriente' ), $label ) ); ?></span>
				<svg viewBox="0 0 16 9" aria-hidden="true"><path d="m1 1 7 7 7-7" /></svg>
			</button>
		</div>
		<ul id="<?php echo esc_attr( $panel_id ); ?>" class="sub-menu product-category-submenu mobile-subcategories" data-mobile-category-panel hidden>
			<?php foreach ( $category_data['subcategories'] as $subcategory ) : ?>
				<li class="menu-item menu-item-object-product_cat"><a href="<?php echo esc_url( get_term_link( $subcategory ) ); ?>"><?php echo esc_html( $subcategory->name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php
		return ob_get_clean();
	}

	$label    = wp_strip_all_tags( $menu_item->title );
	$panel_id = 'mega-menu-item-' . (int) $menu_item->ID;
	ob_start();
	?>
	<span class="mega-menu-trigger">
		<?php echo $item_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Generated by the core menu walker. ?>
		<button class="mega-menu-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-mega-toggle>
			<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Open %s menu', 'oriente' ), $label ) ); ?></span>
			<svg viewBox="0 0 12 7" aria-hidden="true"><path d="m1 1 5 5 5-5" /></svg>
		</button>
	</span>
	<?php oriente_render_mega_menu_panel( $category_data['parent'], $label, $panel_id, $category_data['subcategories'] ); ?>
	<?php
	return ob_get_clean();
}
add_filter( 'walker_nav_menu_start_el', 'oriente_primary_menu_category_output', 10, 4 );

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
