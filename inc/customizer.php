<?php
/** Customizer controls for the Oriente storefront. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return the complete set of editable homepage defaults. */
function oriente_homepage_defaults() {
	$images      = get_template_directory_uri() . '/assets/images/';
	$shop_url    = function_exists( 'oriente_shop_url' ) ? oriente_shop_url() : home_url( '/shop/' );
	$kitchen_url = function_exists( 'oriente_category_url' ) ? oriente_category_url( 'kitchen' ) : $shop_url;
	$decor_url   = function_exists( 'oriente_category_url' ) ? oriente_category_url( 'home-decoration' ) : $shop_url;

	return array(
		'oriente_announcement'             => __( 'Complimentary European delivery on orders over €250', 'oriente' ),
		'oriente_instagram_url'            => 'https://www.instagram.com/',
		'oriente_facebook_url'             => 'https://www.facebook.com/',
		'oriente_tiktok_url'               => 'https://www.tiktok.com/',
		'oriente_hero_image'               => $images . 'hero-kitchen-luxury.png',
		'oriente_hero_title'               => __( 'Objects for a life beautifully lived.', 'oriente' ),
		'oriente_hero_text'                => __( 'Collectible kitchenware and quiet objects, selected for daily rituals and generous tables.', 'oriente' ),
		'oriente_hero_primary_label'       => __( 'Shop the kitchen', 'oriente' ),
		'oriente_hero_primary_url'         => $kitchen_url,
		'oriente_hero_secondary_label'     => __( 'Discover home decor', 'oriente' ),
		'oriente_hero_secondary_url'       => $decor_url,
		'oriente_hero_scroll_label'        => __( 'Explore the edit', 'oriente' ),
		'oriente_collections_title'        => __( 'Orienté collections', 'oriente' ),
		'oriente_collections_text'         => '',
		'oriente_collections_link_label'   => __( 'View all collections', 'oriente' ),
		'oriente_collections_link_url'     => $shop_url,
		'oriente_collection_1_image'       => $images . 'hero-still-life.webp',
		'oriente_collection_1_title'       => __( 'The Kitchen', 'oriente' ),
		'oriente_collection_1_text'        => __( 'Tools, tableware and serving pieces selected for generous everyday use.', 'oriente' ),
		'oriente_collection_1_url'         => $kitchen_url,
		'oriente_collection_2_image'       => $images . 'linen-cups.webp',
		'oriente_collection_2_title'       => __( 'The Quiet Table', 'oriente' ),
		'oriente_collection_2_text'        => __( 'Layered neutrals and tactile materials for slower mornings and long lunches.', 'oriente' ),
		'oriente_collection_2_url'         => $kitchen_url,
		'oriente_collection_3_image'       => $images . 'vase-interior.webp',
		'oriente_collection_3_title'       => __( 'Home Decoration', 'oriente' ),
		'oriente_collection_3_text'        => __( 'Vessels and small objects that create atmosphere without filling the room.', 'oriente' ),
		'oriente_collection_3_url'         => $decor_url,
		'oriente_collection_4_image'       => $images . 'candleholder.webp',
		'oriente_collection_4_title'       => __( 'The Evening Edit', 'oriente' ),
		'oriente_collection_4_text'        => __( 'Warm glass, candlelight and reflective surfaces for the close of day.', 'oriente' ),
		'oriente_collection_4_url'         => $decor_url,
		'oriente_featured_title'           => __( 'Featured products', 'oriente' ),
		'oriente_featured_text'            => '',
		'oriente_featured_link_label'      => __( 'View all objects', 'oriente' ),
		'oriente_featured_link_url'        => '',
		'oriente_featured_category'        => '',
		'oriente_story_image'              => $images . 'story-living-table.png',
		'oriente_story_title'              => __( 'A memorable room is made for living.', 'oriente' ),
		'oriente_story_text'               => __( 'Mix clear glass, warm metal and tactile cloth. The most inviting tables feel collected, never over-styled.', 'oriente' ),
		'oriente_story_link_label'         => __( 'Explore the evening edit', 'oriente' ),
		'oriente_story_link_url'           => $decor_url,
		'oriente_kitchen_title'            => __( 'The Kitchen Edit', 'oriente' ),
		'oriente_kitchen_text'             => '',
		'oriente_kitchen_link_label'       => __( 'View all kitchen', 'oriente' ),
		'oriente_kitchen_link_url'         => '',
		'oriente_kitchen_category'         => 'kitchen',
		'oriente_closing_image'            => $images . 'hero-table.webp',
		'oriente_closing_title'            => __( 'Make room for everyday occasions.', 'oriente' ),
		'oriente_closing_text'             => '',
		'oriente_closing_link_label'       => __( 'Shop the collection', 'oriente' ),
		'oriente_closing_link_url'         => $shop_url,
	);
}

/** Return available WooCommerce product categories for slider controls. */
function oriente_product_category_choices() {
	$choices = array( '' => __( 'All product categories', 'oriente' ) );
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

/** Only allow an existing product-category slug or the all-products option. */
function oriente_sanitize_product_category( $value ) {
	$value = sanitize_title( $value );
	if ( '' === $value ) {
		return '';
	}

	$term = taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $value, 'product_cat' ) : false;
	return $term && ! is_wp_error( $term ) ? $value : '';
}

/** Register the homepage content controls. */
function oriente_customize_register( $wp_customize ) {
	$defaults = oriente_homepage_defaults();

	$wp_customize->add_panel(
		'oriente_homepage_panel',
		array(
			'title'       => __( 'Oriente homepage', 'oriente' ),
			'description' => __( 'Edit homepage images, copy, links, and product-slider categories.', 'oriente' ),
			'priority'    => 30,
		)
	);
	$wp_customize->add_section(
		'oriente_mobile_navigation',
		array(
			'title'       => __( 'Mobile navigation', 'oriente' ),
			'description' => __( 'Set the social profiles shown below the mobile menu links.', 'oriente' ),
			'priority'    => 31,
		)
	);

	$sections = array(
		'oriente_home_global'      => array( __( 'Header announcement', 'oriente' ), 10 ),
		'oriente_home_hero'        => array( __( 'Hero section', 'oriente' ), 20 ),
		'oriente_home_collections' => array( __( 'Orienté collections', 'oriente' ), 30 ),
		'oriente_home_featured'    => array( __( 'Featured products slider', 'oriente' ), 40 ),
		'oriente_home_story'       => array( __( 'Editorial story', 'oriente' ), 50 ),
		'oriente_home_kitchen'     => array( __( 'Kitchen Edit slider', 'oriente' ), 60 ),
		'oriente_home_closing'     => array( __( 'Make room section', 'oriente' ), 70 ),
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

	$text_controls = array(
		'oriente_announcement'           => array( __( 'Announcement text', 'oriente' ), 'oriente_home_global', 'text' ),
		'oriente_instagram_url'          => array( __( 'Instagram URL', 'oriente' ), 'oriente_mobile_navigation', 'url' ),
		'oriente_facebook_url'           => array( __( 'Facebook URL', 'oriente' ), 'oriente_mobile_navigation', 'url' ),
		'oriente_tiktok_url'             => array( __( 'TikTok URL', 'oriente' ), 'oriente_mobile_navigation', 'url' ),
		'oriente_hero_title'             => array( __( 'Title', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_text'              => array( __( 'Description', 'oriente' ), 'oriente_home_hero', 'textarea' ),
		'oriente_hero_primary_label'     => array( __( 'Primary button label', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_primary_url'       => array( __( 'Primary button link', 'oriente' ), 'oriente_home_hero', 'url' ),
		'oriente_hero_secondary_label'   => array( __( 'Secondary link label', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_hero_secondary_url'     => array( __( 'Secondary link URL', 'oriente' ), 'oriente_home_hero', 'url' ),
		'oriente_hero_scroll_label'      => array( __( 'Scroll link label', 'oriente' ), 'oriente_home_hero', 'text' ),
		'oriente_collections_title'      => array( __( 'Section title', 'oriente' ), 'oriente_home_collections', 'text' ),
		'oriente_collections_text'       => array( __( 'Optional description', 'oriente' ), 'oriente_home_collections', 'textarea' ),
		'oriente_collections_link_label' => array( __( 'View-all label', 'oriente' ), 'oriente_home_collections', 'text' ),
		'oriente_collections_link_url'   => array( __( 'View-all link', 'oriente' ), 'oriente_home_collections', 'url' ),
		'oriente_featured_title'         => array( __( 'Section title', 'oriente' ), 'oriente_home_featured', 'text' ),
		'oriente_featured_text'          => array( __( 'Optional description', 'oriente' ), 'oriente_home_featured', 'textarea' ),
		'oriente_featured_link_label'    => array( __( 'View-all label', 'oriente' ), 'oriente_home_featured', 'text' ),
		'oriente_featured_link_url'      => array( __( 'Custom view-all link', 'oriente' ), 'oriente_home_featured', 'url', __( 'Leave blank to use the selected category archive.', 'oriente' ) ),
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

	for ( $index = 1; $index <= 4; ++$index ) {
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
		'oriente_hero_image'         => array( __( 'Background image', 'oriente' ), 'oriente_home_hero' ),
		'oriente_collection_1_image' => array( __( 'Collection 1 image', 'oriente' ), 'oriente_home_collections' ),
		'oriente_collection_2_image' => array( __( 'Collection 2 image', 'oriente' ), 'oriente_home_collections' ),
		'oriente_collection_3_image' => array( __( 'Collection 3 image', 'oriente' ), 'oriente_home_collections' ),
		'oriente_collection_4_image' => array( __( 'Collection 4 image', 'oriente' ), 'oriente_home_collections' ),
		'oriente_story_image'        => array( __( 'Editorial image', 'oriente' ), 'oriente_home_story' ),
		'oriente_closing_image'      => array( __( 'Background image', 'oriente' ), 'oriente_home_closing' ),
	);
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

	$category_choices = oriente_product_category_choices();
	$category_controls = array(
		'oriente_featured_category' => array( __( 'Product category', 'oriente' ), 'oriente_home_featured' ),
		'oriente_kitchen_category'  => array( __( 'Product category', 'oriente' ), 'oriente_home_kitchen' ),
	);
	foreach ( $category_controls as $setting_id => $control ) {
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
				'description' => __( 'Choose which products appear in this slider.', 'oriente' ),
				'section'     => $control[1],
				'type'        => 'select',
				'choices'     => $category_choices,
			)
		);
	}

	$control_priorities = array(
		'oriente_announcement'           => 10,
		'oriente_instagram_url'          => 10,
		'oriente_facebook_url'           => 20,
		'oriente_tiktok_url'             => 30,
		'oriente_hero_image'             => 10,
		'oriente_hero_title'             => 20,
		'oriente_hero_text'              => 30,
		'oriente_hero_primary_label'     => 40,
		'oriente_hero_primary_url'       => 50,
		'oriente_hero_secondary_label'   => 60,
		'oriente_hero_secondary_url'     => 70,
		'oriente_hero_scroll_label'      => 80,
		'oriente_collections_title'      => 10,
		'oriente_collections_text'       => 20,
		'oriente_collections_link_label' => 30,
		'oriente_collections_link_url'   => 40,
		'oriente_featured_title'         => 10,
		'oriente_featured_text'          => 20,
		'oriente_featured_category'      => 30,
		'oriente_featured_link_label'    => 40,
		'oriente_featured_link_url'      => 50,
		'oriente_story_image'            => 10,
		'oriente_story_title'            => 20,
		'oriente_story_text'             => 30,
		'oriente_story_link_label'       => 40,
		'oriente_story_link_url'         => 50,
		'oriente_kitchen_title'          => 10,
		'oriente_kitchen_text'           => 20,
		'oriente_kitchen_category'       => 30,
		'oriente_kitchen_link_label'     => 40,
		'oriente_kitchen_link_url'       => 50,
		'oriente_closing_image'          => 10,
		'oriente_closing_title'          => 20,
		'oriente_closing_text'           => 30,
		'oriente_closing_link_label'     => 40,
		'oriente_closing_link_url'       => 50,
	);
	for ( $index = 1; $index <= 4; ++$index ) {
		$base_priority = 50 + ( ( $index - 1 ) * 40 );
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
