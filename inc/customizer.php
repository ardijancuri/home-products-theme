<?php
/** Customizer controls for the Oriente storefront. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Load custom control classes after WordPress has loaded the Customizer API. */
function oriente_load_customize_control_classes() {
	require_once get_template_directory() . '/inc/class-oriente-customize-product-checkbox-control.php';
}
add_action( 'customize_register', 'oriente_load_customize_control_classes', 5 );

/** Return the complete set of editable homepage defaults. */
function oriente_homepage_defaults() {
	$images      = get_template_directory_uri() . '/assets/images/';
	$shop_url    = function_exists( 'oriente_shop_url' ) ? oriente_shop_url() : home_url( '/shop/' );
	$kitchen_url = function_exists( 'oriente_category_url' ) ? oriente_category_url( 'kitchen' ) : $shop_url;
	$decor_url   = function_exists( 'oriente_category_url' ) ? oriente_category_url( 'home-decoration' ) : $shop_url;
	$about_page  = get_page_by_path( 'about-us' );
	$about_url   = $about_page instanceof WP_Post ? get_permalink( $about_page ) : home_url( '/about-us/' );

	return array(
		'oriente_announcement'             => __( 'Complimentary European delivery on orders over €250', 'oriente' ),
		'oriente_instagram_url'            => 'https://www.instagram.com/',
		'oriente_facebook_url'             => 'https://www.facebook.com/',
		'oriente_tiktok_url'               => 'https://www.tiktok.com/',
		'oriente_whatsapp_number'          => ORIENTE_WHATSAPP_NUMBER,
		'oriente_hero_image'               => $images . 'hero-ambient-noyer-sunroom-v3.webp',
		'oriente_hero_title'               => __( 'Objects for a life beautifully lived.', 'oriente' ),
		'oriente_hero_text'                => __( 'Kitchenware and home objects chosen for everyday use.', 'oriente' ),
		'oriente_hero_primary_label'       => __( 'Shop the kitchen', 'oriente' ),
		'oriente_hero_primary_url'         => $kitchen_url,
		'oriente_hero_2_image'             => $images . 'home-hero-about-story-v2.webp',
		'oriente_hero_2_title'             => __( 'A story shaped by experience.', 'oriente' ),
		'oriente_hero_2_text'              => __( 'Backed by more than 30 years of experience, ORIENTÉ brings quality and thoughtful design to every home.', 'oriente' ),
		'oriente_hero_2_primary_label'     => __( 'About us', 'oriente' ),
		'oriente_hero_2_primary_url'       => $about_url,
		'oriente_hero_3_image'             => '',
		'oriente_hero_3_title'             => '',
		'oriente_hero_3_text'              => '',
		'oriente_hero_3_primary_label'     => '',
		'oriente_hero_3_primary_url'       => '',
		'oriente_collections_title'        => __( 'Orienté collections', 'oriente' ),
		'oriente_collections_text'         => '',
		'oriente_collections_link_label'   => __( 'View all collections', 'oriente' ),
		'oriente_collections_link_url'     => $shop_url,
		'oriente_collections_count'        => ORIENTE_COLLECTIONS_MIN,
		'oriente_collection_1_image'       => $images . 'collection-kitchen-simple-v4.webp',
		'oriente_collection_1_title'       => __( 'The Kitchen', 'oriente' ),
		'oriente_collection_1_text'        => __( 'Kitchen tools, tableware and serving pieces for everyday use.', 'oriente' ),
		'oriente_collection_1_url'         => $kitchen_url,
		'oriente_collection_2_image'       => $images . 'collection-quiet-table-simple-v4.webp',
		'oriente_collection_2_title'       => __( 'The Quiet Table', 'oriente' ),
		'oriente_collection_2_text'        => __( 'Simple tableware in neutral colors and natural textures.', 'oriente' ),
		'oriente_collection_2_url'         => $kitchen_url,
		'oriente_collection_3_image'       => $images . 'collection-home-decoration-simple-v4.webp',
		'oriente_collection_3_title'       => __( 'Home Decoration', 'oriente' ),
		'oriente_collection_3_text'        => __( 'Vases, bowls and decorative objects for every room.', 'oriente' ),
		'oriente_collection_3_url'         => $decor_url,
		'oriente_collection_4_image'       => $images . 'collection-evening-edit-simple-v4.webp',
		'oriente_collection_4_title'       => __( 'The Evening Edit', 'oriente' ),
		'oriente_collection_4_text'        => __( 'Glassware, candles and home accents for evenings at home.', 'oriente' ),
		'oriente_collection_4_url'         => $decor_url,
		'oriente_collection_5_image'       => '',
		'oriente_collection_5_title'       => '',
		'oriente_collection_5_text'        => '',
		'oriente_collection_5_url'         => '',
		'oriente_collection_6_image'       => '',
		'oriente_collection_6_title'       => '',
		'oriente_collection_6_text'        => '',
		'oriente_collection_6_url'         => '',
		'oriente_collection_7_image'       => '',
		'oriente_collection_7_title'       => '',
		'oriente_collection_7_text'        => '',
		'oriente_collection_7_url'         => '',
		'oriente_collection_8_image'       => '',
		'oriente_collection_8_title'       => '',
		'oriente_collection_8_text'        => '',
		'oriente_collection_8_url'         => '',
		'oriente_featured_title'           => __( 'Featured products', 'oriente' ),
		'oriente_featured_text'            => '',
		'oriente_featured_link_label'      => __( 'View all objects', 'oriente' ),
		'oriente_featured_link_url'        => '',
		'oriente_featured_products'        => '',
		'oriente_featured_category'        => '',
		'oriente_follow_image'             => $images . 'follow-us-lattice-veranda-v2-cup.webp',
		'oriente_follow_mobile_image'      => $images . 'follow-us-mobile-v1.webp',
		'oriente_follow_title'             => __( 'Follow us', 'oriente' ),
		'oriente_follow_text'              => __( 'See new collections, home ideas and updates from ORIENTÉ.', 'oriente' ),
		'oriente_story_initial_image'      => $images . 'story-living-table-ambient-v3-light.webp',
		'oriente_story_image'              => $images . 'story-slider-halo-reading-v1.webp',
		'oriente_story_image_2'            => $images . 'story-slider-verdure-pantry-v1.webp',
		'oriente_story_image_3'            => $images . 'story-slider-rose-kitchen-v1.webp',
		'oriente_story_image_4'            => $images . 'story-slider-crater-threshold-v1.webp',
		'oriente_story_title'              => __( 'A memorable room is made for living.', 'oriente' ),
		'oriente_story_text'               => __( 'Combine glass, metal and natural fabrics to create a warm, balanced table.', 'oriente' ),
		'oriente_story_link_label'         => __( 'Explore the evening edit', 'oriente' ),
		'oriente_story_link_url'           => $decor_url,
		'oriente_kitchen_title'            => __( 'The Kitchen Edit', 'oriente' ),
		'oriente_kitchen_text'             => '',
		'oriente_kitchen_link_label'       => __( 'View all kitchen', 'oriente' ),
		'oriente_kitchen_link_url'         => '',
		'oriente_kitchen_category'         => 'kitchen',
		'oriente_closing_image'            => $images . 'closing-everyday-ambient-v5-natural-nuts.webp',
		'oriente_closing_mobile_image'     => $images . 'closing-everyday-mobile-v1.webp',
		'oriente_closing_title'            => __( 'Make room for everyday occasions.', 'oriente' ),
		'oriente_closing_text'             => '',
		'oriente_closing_link_label'       => __( 'Shop the collection', 'oriente' ),
		'oriente_closing_link_url'         => $shop_url,
	);
}

/** Return available WooCommerce product categories for slider controls. */
function oriente_product_category_choices( $include_featured_products = false ) {
	$choices = array( '' => __( 'All product categories', 'oriente' ) );
	if ( $include_featured_products ) {
		$choices[ ORIENTE_FEATURED_PRODUCTS_CHOICE ] = __( 'Featured products', 'oriente' );
	}
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return $choices;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return $choices;
	}

	foreach ( $terms as $term ) {
		$choices[ $term->slug ] = $term->name;
	}

	return $choices;
}

/** Return published products for the Featured Products checklist. */
function oriente_product_choices() {
	$choices = array();
	if ( ! post_type_exists( 'product' ) ) {
		return $choices;
	}

	$products = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'order'          => 'ASC',
		)
	);

	foreach ( $products as $product ) {
		$choices[ $product->ID ] = $product->post_title;
	}

	return $choices;
}

/** Only allow an existing product-category slug or the all-products option. */
function oriente_sanitize_product_category( $value ) {
	$value = sanitize_title( $value );
	if ( '' === $value ) {
		return '';
	}
	if ( ORIENTE_FEATURED_PRODUCTS_CHOICE === $value ) {
		return $value;
	}

	$term = taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $value, 'product_cat' ) : false;
	return $term && ! is_wp_error( $term ) ? $value : '';
}

/** Sanitize and preserve the selected product order for the homepage slider. */
function oriente_sanitize_product_ids( $value ) {
	$raw_ids = is_array( $value ) ? $value : explode( ',', (string) $value );
	$ids     = array_slice( array_values( array_unique( array_filter( array_map( 'absint', $raw_ids ) ) ) ), 0, 24 );
	if ( empty( $ids ) || ! post_type_exists( 'product' ) ) {
		return '';
	}

	$published_ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post__in'       => $ids,
			'fields'         => 'ids',
		)
	);
	$published_ids = array_map( 'absint', $published_ids );
	$ids           = array_values(
		array_filter(
			$ids,
			static function ( $product_id ) use ( $published_ids ) {
				return in_array( $product_id, $published_ids, true );
			}
		)
	);

	return implode( ',', $ids );
}

/** Keep a WhatsApp phone number in the international digits-only format required by wa.me. */
function oriente_sanitize_whatsapp_number( $value ) {
	return preg_replace( '/\D+/', '', (string) $value );
}

/** Keep the collection slider count within the supported editable range. */
function oriente_sanitize_collections_count( $value ) {
	$value = absint( $value );
	return min( ORIENTE_COLLECTIONS_MAX, max( ORIENTE_COLLECTIONS_MIN, $value ) );
}

/** Register the homepage content controls. */
function oriente_customize_register( $wp_customize ) {
	$defaults = oriente_homepage_defaults();

	$wp_customize->add_panel(
		'oriente_homepage_panel',
		array(
			'title'       => __( 'Oriente homepage', 'oriente' ),
			'description' => __( 'Edit homepage images, copy, links, and product slider selections.', 'oriente' ),
			'priority'    => 30,
		)
	);
	$wp_customize->add_section(
		'oriente_mobile_navigation',
		array(
			'title'       => __( 'Social media links', 'oriente' ),
			'description' => __( 'Set the social profiles shown in the mobile menu and site footer. Leave a field blank to hide that icon.', 'oriente' ),
			'priority'    => 31,
		)
	);
	$wp_customize->add_section(
		'oriente_contact_options',
		array(
			'title'       => __( 'Contact & WhatsApp', 'oriente' ),
			'description' => __( 'Set the WhatsApp number used for customer support, contact details, and cart ordering.', 'oriente' ),
			'priority'    => 32,
		)
	);

	$sections = array(
		'oriente_home_global'      => array( __( 'Header announcement', 'oriente' ), 10 ),
		'oriente_home_hero'        => array( __( 'Hero section', 'oriente' ), 20 ),
		'oriente_home_collections' => array( __( 'Orienté collections', 'oriente' ), 30 ),
		'oriente_home_featured'    => array( __( 'Featured products slider', 'oriente' ), 40 ),
		'oriente_home_follow'      => array( __( 'Follow us section', 'oriente' ), 50 ),
		'oriente_home_story'       => array( __( 'Editorial story', 'oriente' ), 60 ),
		'oriente_home_kitchen'     => array( __( 'Kitchen Edit slider', 'oriente' ), 70 ),
		'oriente_home_closing'     => array( __( 'Make room section', 'oriente' ), 80 ),
	);
	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'    => $section[0],
				'panel'    => 'oriente_homepage_panel',
				'priority' => $section[1],
			)
		);
	}
	$hero_section = $wp_customize->get_section( 'oriente_home_hero' );
	if ( $hero_section ) {
		$hero_section->description = __( 'Configure up to three slides. A slide remains hidden until it has a background image.', 'oriente' );
	}

	$wp_customize->add_setting(
		'oriente_collections_count',
		array(
			'default'           => $defaults['oriente_collections_count'],
			'sanitize_callback' => 'oriente_sanitize_collections_count',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'oriente_collections_count',
		array(
			'label'       => __( 'Number of collection slides', 'oriente' ),
			'description' => __( 'Choose between 4 and 8 slides, then complete the matching collection fields below.', 'oriente' ),
			'settings'    => 'oriente_collections_count',
			'section'     => 'oriente_home_collections',
			'type'        => 'number',
			'priority'    => 50,
			'input_attrs' => array(
				'min'  => ORIENTE_COLLECTIONS_MIN,
				'max'  => ORIENTE_COLLECTIONS_MAX,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'oriente_featured_products',
		array(
			'default'           => $defaults['oriente_featured_products'],
			'sanitize_callback' => 'oriente_sanitize_product_ids',
			'transport'         => 'refresh',
		)
	);
	if ( class_exists( 'Oriente_Customize_Product_Checkbox_Control' ) ) {
		$wp_customize->add_control(
			new Oriente_Customize_Product_Checkbox_Control(
				$wp_customize,
				'oriente_featured_products',
				array(
					'label'       => __( 'Optional individual products', 'oriente' ),
					'description' => __( 'Only use this when you want an exact hand-picked set. Choosing Featured products in the dropdown above loads every WooCommerce product marked as Featured automatically.', 'oriente' ),
					'section'     => 'oriente_home_featured',
					'choices'     => oriente_product_choices(),
				)
			)
		);
	}

	$text_controls = array(
		'oriente_announcement'           => array( __( 'Announcement text', 'oriente' ), 'oriente_home_global', 'text' ),
		'oriente_instagram_url'          => array( __( 'Instagram URL', 'oriente' ), 'oriente_mobile_navigation', 'url' ),
		'oriente_facebook_url'           => array( __( 'Facebook URL', 'oriente' ), 'oriente_mobile_navigation', 'url' ),
		'oriente_tiktok_url'             => array( __( 'TikTok URL', 'oriente' ), 'oriente_mobile_navigation', 'url' ),
		'oriente_whatsapp_number'        => array( __( 'WhatsApp number', 'oriente' ), 'oriente_contact_options', 'text', __( 'Include the country code and number. Spaces and symbols are removed automatically.', 'oriente' ) ),
		'oriente_hero_title'             => array( __( 'Slide 1 title', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_text'              => array( __( 'Slide 1 description', 'oriente' ), 'oriente_home_hero', 'textarea' ),
		'oriente_hero_primary_label'     => array( __( 'Slide 1 CTA label', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_primary_url'       => array( __( 'Slide 1 CTA link', 'oriente' ), 'oriente_home_hero', 'url', __( 'The entire slide links to this URL.', 'oriente' ) ),
		'oriente_hero_2_title'           => array( __( 'Slide 2 title', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_2_text'            => array( __( 'Slide 2 description', 'oriente' ), 'oriente_home_hero', 'textarea' ),
		'oriente_hero_2_primary_label'   => array( __( 'Slide 2 CTA label', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_2_primary_url'     => array( __( 'Slide 2 CTA link', 'oriente' ), 'oriente_home_hero', 'url', __( 'The entire slide links to this URL.', 'oriente' ) ),
		'oriente_hero_3_title'           => array( __( 'Slide 3 title', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_3_text'            => array( __( 'Slide 3 description', 'oriente' ), 'oriente_home_hero', 'textarea' ),
		'oriente_hero_3_primary_label'   => array( __( 'Slide 3 CTA label', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_3_primary_url'     => array( __( 'Slide 3 CTA link', 'oriente' ), 'oriente_home_hero', 'url', __( 'The entire slide links to this URL.', 'oriente' ) ),
		'oriente_collections_title'      => array( __( 'Section title', 'oriente' ), 'oriente_home_collections', 'text' ),
		'oriente_collections_text'       => array( __( 'Optional description', 'oriente' ), 'oriente_home_collections', 'textarea' ),
		'oriente_collections_link_label' => array( __( 'View-all label', 'oriente' ), 'oriente_home_collections', 'text' ),
		'oriente_collections_link_url'   => array( __( 'View-all link', 'oriente' ), 'oriente_home_collections', 'url' ),
		'oriente_featured_title'         => array( __( 'Section title', 'oriente' ), 'oriente_home_featured', 'text' ),
		'oriente_featured_text'          => array( __( 'Optional description', 'oriente' ), 'oriente_home_featured', 'textarea' ),
		'oriente_featured_link_label'    => array( __( 'View-all label', 'oriente' ), 'oriente_home_featured', 'text' ),
		'oriente_featured_link_url'      => array( __( 'Custom view-all link', 'oriente' ), 'oriente_home_featured', 'url', __( 'Leave blank to use the selected category archive.', 'oriente' ) ),
		'oriente_follow_title'           => array( __( 'Title', 'oriente' ), 'oriente_home_follow', 'text' ),
		'oriente_follow_text'            => array( __( 'Description', 'oriente' ), 'oriente_home_follow', 'textarea', __( 'Social links use the URLs from the Social media links section.', 'oriente' ) ),
		'oriente_story_title'            => array( __( 'Title', 'oriente' ), 'oriente_home_story', 'text' ),
		'oriente_story_text'             => array( __( 'Description', 'oriente' ), 'oriente_home_story', 'textarea' ),
		'oriente_story_link_label'       => array( __( 'Link label', 'oriente' ), 'oriente_home_story', 'text' ),
		'oriente_story_link_url'         => array( __( 'Link URL', 'oriente' ), 'oriente_home_story', 'url' ),
		'oriente_kitchen_title'          => array( __( 'Section title', 'oriente' ), 'oriente_home_kitchen', 'text' ),
		'oriente_kitchen_text'           => array( __( 'Optional description', 'oriente' ), 'oriente_home_kitchen', 'textarea' ),
		'oriente_kitchen_link_label'     => array( __( 'View-all label', 'oriente' ), 'oriente_home_kitchen', 'text' ),
		'oriente_kitchen_link_url'       => array( __( 'Custom view-all link', 'oriente' ), 'oriente_home_kitchen', 'url', __( 'Leave blank to use the selected category archive.', 'oriente' ) ),
		'oriente_closing_title'          => array( __( 'Title', 'oriente' ), 'oriente_home_closing', 'text' ),
		'oriente_closing_text'           => array( __( 'Optional description', 'oriente' ), 'oriente_home_closing', 'textarea' ),
		'oriente_closing_link_label'     => array( __( 'Button label', 'oriente' ), 'oriente_home_closing', 'text' ),
		'oriente_closing_link_url'       => array( __( 'Button link', 'oriente' ), 'oriente_home_closing', 'url' ),
	);

	for ( $index = 1; $index <= ORIENTE_COLLECTIONS_MAX; ++$index ) {
		$text_controls[ "oriente_collection_{$index}_title" ] = array(
			sprintf( __( 'Collection %d title', 'oriente' ), $index ),
			'oriente_home_collections',
			'text',
		);
		$text_controls[ "oriente_collection_{$index}_text" ] = array(
			sprintf( __( 'Collection %d description', 'oriente' ), $index ),
			'oriente_home_collections',
			'textarea',
		);
		$text_controls[ "oriente_collection_{$index}_url" ] = array(
			sprintf( __( 'Collection %d link', 'oriente' ), $index ),
			'oriente_home_collections',
			'url',
		);
	}

	foreach ( $text_controls as $setting_id => $control ) {
		$type     = $control[2];
		$sanitize = 'sanitize_text_field';
		if ( 'textarea' === $type ) {
			$sanitize = 'sanitize_textarea_field';
		} elseif ( 'url' === $type ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'oriente_whatsapp_number' === $setting_id ) {
			$sanitize = 'oriente_sanitize_whatsapp_number';
		}

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $setting_id ],
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$control_args = array(
			'label'   => $control[0],
			'section' => $control[1],
			'type'    => $type,
		);
		if ( isset( $control[3] ) ) {
			$control_args['description'] = $control[3];
		}
		$wp_customize->add_control( $setting_id, $control_args );
	}

	$image_controls = array(
		'oriente_hero_image'           => array( __( 'Slide 1 background image', 'oriente' ), 'oriente_home_hero' ),
		'oriente_hero_2_image'         => array( __( 'Slide 2 background image', 'oriente' ), 'oriente_home_hero' ),
		'oriente_hero_3_image'         => array( __( 'Slide 3 background image', 'oriente' ), 'oriente_home_hero' ),
		'oriente_follow_image'         => array( __( 'Desktop background image', 'oriente' ), 'oriente_home_follow' ),
		'oriente_follow_mobile_image'  => array( __( 'Mobile background image', 'oriente' ), 'oriente_home_follow' ),
		'oriente_story_initial_image'  => array( __( 'Slider image 1 (original)', 'oriente' ), 'oriente_home_story' ),
		'oriente_story_image'          => array( __( 'Slider image 2', 'oriente' ), 'oriente_home_story' ),
		'oriente_story_image_2'        => array( __( 'Slider image 3', 'oriente' ), 'oriente_home_story' ),
		'oriente_story_image_3'        => array( __( 'Slider image 4', 'oriente' ), 'oriente_home_story' ),
		'oriente_story_image_4'        => array( __( 'Slider image 5', 'oriente' ), 'oriente_home_story' ),
		'oriente_closing_image'        => array( __( 'Desktop background image', 'oriente' ), 'oriente_home_closing' ),
		'oriente_closing_mobile_image' => array( __( 'Mobile background image', 'oriente' ), 'oriente_home_closing' ),
	);
	for ( $index = 1; $index <= ORIENTE_COLLECTIONS_MAX; ++$index ) {
		$image_controls[ "oriente_collection_{$index}_image" ] = array(
			sprintf( __( 'Collection %d image', 'oriente' ), $index ),
			'oriente_home_collections',
		);
	}
	foreach ( $image_controls as $setting_id => $control ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $setting_id ],
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $control[0],
					'section' => $control[1],
				)
			)
		);
	}

	$category_controls = array(
		'oriente_featured_category' => array( __( 'Product category', 'oriente' ), 'oriente_home_featured' ),
		'oriente_kitchen_category'  => array( __( 'Product category', 'oriente' ), 'oriente_home_kitchen' ),
	);
	foreach ( $category_controls as $setting_id => $control ) {
		$description = 'oriente_featured_category' === $setting_id
			? __( 'Choose a product category or Featured products. The Featured products option automatically uses products marked as Featured in WooCommerce and ignores any optional individual choices below.', 'oriente' )
			: __( 'Choose which products appear in this slider.', 'oriente' );
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $defaults[ $setting_id ],
				'sanitize_callback' => 'oriente_sanitize_product_category',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'       => $control[0],
				'description' => $description,
				'section'     => $control[1],
				'type'        => 'select',
				'choices'     => oriente_product_category_choices( 'oriente_featured_category' === $setting_id ),
			)
		);
	}

	$control_priorities = array(
		'oriente_announcement'           => 10,
		'oriente_instagram_url'          => 10,
		'oriente_facebook_url'           => 20,
		'oriente_tiktok_url'             => 30,
		'oriente_whatsapp_number'        => 10,
		'oriente_hero_image'             => 10,
		'oriente_hero_title'             => 20,
		'oriente_hero_text'              => 30,
		'oriente_hero_primary_label'     => 40,
		'oriente_hero_primary_url'       => 50,
		'oriente_hero_2_image'           => 60,
		'oriente_hero_2_title'           => 70,
		'oriente_hero_2_text'            => 80,
		'oriente_hero_2_primary_label'   => 90,
		'oriente_hero_2_primary_url'     => 100,
		'oriente_hero_3_image'           => 110,
		'oriente_hero_3_title'           => 120,
		'oriente_hero_3_text'            => 130,
		'oriente_hero_3_primary_label'   => 140,
		'oriente_hero_3_primary_url'     => 150,
		'oriente_collections_title'      => 10,
		'oriente_collections_text'       => 20,
		'oriente_collections_link_label' => 30,
		'oriente_collections_link_url'   => 40,
		'oriente_collections_count'      => 50,
		'oriente_featured_title'         => 10,
		'oriente_featured_text'          => 20,
		'oriente_featured_category'      => 30,
		'oriente_featured_products'      => 40,
		'oriente_featured_link_label'    => 50,
		'oriente_featured_link_url'      => 60,
		'oriente_follow_image'           => 10,
		'oriente_follow_mobile_image'    => 20,
		'oriente_follow_title'           => 30,
		'oriente_follow_text'            => 40,
		'oriente_story_initial_image'    => 10,
		'oriente_story_image'            => 20,
		'oriente_story_image_2'          => 30,
		'oriente_story_image_3'          => 40,
		'oriente_story_image_4'          => 50,
		'oriente_story_title'            => 60,
		'oriente_story_text'             => 70,
		'oriente_story_link_label'       => 80,
		'oriente_story_link_url'         => 90,
		'oriente_kitchen_title'          => 10,
		'oriente_kitchen_text'           => 20,
		'oriente_kitchen_category'       => 30,
		'oriente_kitchen_link_label'     => 40,
		'oriente_kitchen_link_url'       => 50,
		'oriente_closing_image'          => 10,
		'oriente_closing_mobile_image'   => 20,
		'oriente_closing_title'          => 30,
		'oriente_closing_text'           => 40,
		'oriente_closing_link_label'     => 50,
		'oriente_closing_link_url'       => 60,
	);
	for ( $index = 1; $index <= ORIENTE_COLLECTIONS_MAX; ++$index ) {
		$base_priority = 60 + ( ( $index - 1 ) * 40 );
		$control_priorities[ "oriente_collection_{$index}_image" ] = $base_priority;
		$control_priorities[ "oriente_collection_{$index}_title" ] = $base_priority + 10;
		$control_priorities[ "oriente_collection_{$index}_text" ]  = $base_priority + 20;
		$control_priorities[ "oriente_collection_{$index}_url" ]   = $base_priority + 30;
	}
	foreach ( $control_priorities as $control_id => $priority ) {
		$registered_control = $wp_customize->get_control( $control_id );
		if ( $registered_control ) {
			$registered_control->priority = $priority;
		}
	}
}
add_action( 'customize_register', 'oriente_customize_register' );

/** Load the small checklist helper used only by the Customizer controls screen. */
function oriente_customize_controls_assets() {
	wp_enqueue_style( 'oriente-customizer-controls', get_template_directory_uri() . '/assets/css/customizer-controls.css', array(), ORIENTE_VERSION );
	wp_enqueue_script( 'oriente-customizer-controls', get_template_directory_uri() . '/assets/js/customizer-controls.js', array( 'jquery', 'customize-controls' ), ORIENTE_VERSION, true );
}
add_action( 'customize_controls_enqueue_scripts', 'oriente_customize_controls_assets' );
