<?php
/** Customizer controls for the Oriente storefront. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function oriente_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'oriente_homepage',
		array(
			'title'       => __( 'Oriente homepage', 'oriente' ),
			'description' => __( 'Edit the storefront announcement and opening message.', 'oriente' ),
			'priority'    => 30,
		)
	);
	$controls = array(
		'oriente_announcement' => array(
			'label'   => __( 'Announcement', 'oriente' ),
			'default' => __( 'Complimentary European delivery on orders over €250', 'oriente' ),
		),
		'oriente_hero_kicker' => array(
			'label'   => __( 'Hero eyebrow', 'oriente' ),
			'default' => __( 'The art of gathering · Edition 01', 'oriente' ),
		),
		'oriente_hero_title' => array(
			'label'   => __( 'Hero title', 'oriente' ),
			'default' => __( 'Objects for a life beautifully lived.', 'oriente' ),
		),
		'oriente_hero_text' => array(
			'label'   => __( 'Hero supporting copy', 'oriente' ),
			'default' => __( 'Collectible kitchenware and quiet objects, selected for daily rituals and generous tables.', 'oriente' ),
		),
	);
	foreach ( $controls as $setting_id => $control ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $control['default'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $control['label'],
				'section' => 'oriente_homepage',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'oriente_customize_register' );
