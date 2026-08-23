<?php
/**
 * Editable SEO metadata for the homepage, shop, and product categories.
 *
 * @package Oriente
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return the stored SEO settings with a predictable shape. */
function oriente_seo_get_settings() {
	$settings = get_option( 'oriente_seo_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();

	return wp_parse_args(
		$settings,
		array(
			'default_image_id' => 0,
			'home'             => array(),
			'shop'             => array(),
			'categories'       => array(),
		)
	);
}

/** Normalize a single page or category SEO record. */
function oriente_seo_normalize_record( $record ) {
	$record = is_array( $record ) ? $record : array();

	return wp_parse_args(
		$record,
		array(
			'title'       => '',
			'keywords'    => '',
			'description' => '',
			'image_id'    => 0,
		)
	);
}

/** Allow only image attachments to be used as SEO images. */
function oriente_seo_sanitize_image_id( $attachment_id ) {
	$attachment_id = absint( $attachment_id );

	return $attachment_id && wp_attachment_is_image( $attachment_id ) ? $attachment_id : 0;
}

/** Sanitize the complete SEO settings payload before it is saved. */
function oriente_seo_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$clean = array(
		'default_image_id' => oriente_seo_sanitize_image_id( isset( $input['default_image_id'] ) ? $input['default_image_id'] : 0 ),
		'home'             => array(),
		'shop'             => array(),
		'categories'       => array(),
	);

	foreach ( array( 'home', 'shop' ) as $context ) {
		$record            = isset( $input[ $context ] ) && is_array( $input[ $context ] ) ? $input[ $context ] : array();
		$clean[ $context ] = array(
			'title'       => sanitize_text_field( isset( $record['title'] ) ? $record['title'] : '' ),
			'keywords'    => sanitize_text_field( isset( $record['keywords'] ) ? $record['keywords'] : '' ),
			'description' => sanitize_textarea_field( isset( $record['description'] ) ? $record['description'] : '' ),
			'image_id'    => oriente_seo_sanitize_image_id( isset( $record['image_id'] ) ? $record['image_id'] : 0 ),
		);
	}

	if ( isset( $input['categories'] ) && is_array( $input['categories'] ) ) {
		foreach ( $input['categories'] as $term_id => $record ) {
			$term_id = absint( $term_id );
			$term    = $term_id ? get_term( $term_id, 'product_cat' ) : null;
			if ( ! $term instanceof WP_Term || ! is_array( $record ) ) {
				continue;
			}

			$clean['categories'][ $term_id ] = array(
				'title'       => sanitize_text_field( isset( $record['title'] ) ? $record['title'] : '' ),
				'keywords'    => sanitize_text_field( isset( $record['keywords'] ) ? $record['keywords'] : '' ),
				'description' => sanitize_textarea_field( isset( $record['description'] ) ? $record['description'] : '' ),
				'image_id'    => oriente_seo_sanitize_image_id( isset( $record['image_id'] ) ? $record['image_id'] : 0 ),
			);
		}
	}

	return $clean;
}

/** Register the option used by the SEO admin page. */
function oriente_seo_register_settings() {
	register_setting(
		'oriente_seo',
		'oriente_seo_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'oriente_seo_sanitize_settings',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'oriente_seo_register_settings' );

/** Add a dedicated SEO screen to the WordPress admin menu. */
function oriente_seo_admin_menu() {
	add_menu_page(
		__( 'Oriente SEO', 'oriente' ),
		__( 'SEO', 'oriente' ),
		'manage_options',
		'oriente-seo',
		'oriente_seo_render_admin_page',
		'dashicons-search',
		59
	);
}
add_action( 'admin_menu', 'oriente_seo_admin_menu' );

/** Load the media library only on the Oriente SEO screen. */
function oriente_seo_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_oriente-seo' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'oriente_seo_admin_assets' );

/** Render an image selector shared by global and page-level SEO settings. */
function oriente_seo_image_field( $name, $attachment_id, $description = '' ) {
	$attachment_id = absint( $attachment_id );
	$image_url      = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium' ) : '';
	?>
	<div class="oriente-seo-image-field" data-seo-image-field>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $attachment_id ); ?>" data-seo-image-id>
		<div class="oriente-seo-image-preview<?php echo $image_url ? '' : ' is-empty'; ?>" data-seo-image-preview>
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="">
			<?php else : ?>
				<span><?php esc_html_e( 'No image selected', 'oriente' ); ?></span>
			<?php endif; ?>
		</div>
		<p class="oriente-seo-image-actions">
			<button type="button" class="button" data-seo-image-select><?php esc_html_e( 'Choose image', 'oriente' ); ?></button>
			<button type="button" class="button-link-delete<?php echo $image_url ? '' : ' is-hidden'; ?>" data-seo-image-remove><?php esc_html_e( 'Remove', 'oriente' ); ?></button>
		</p>
		<?php if ( $description ) : ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/** Render a group of editable fields for one public SEO context. */
function oriente_seo_record_fields( $key, $record, $fallbacks = array() ) {
	$record    = oriente_seo_normalize_record( $record );
	$fallbacks = wp_parse_args(
		$fallbacks,
		array(
			'title'       => '',
			'description' => '',
			'image'       => '',
		)
	);
	$prefix    = 'oriente_seo_settings[' . $key . ']';
	?>
	<div class="oriente-seo-fields">
		<label>
			<span><?php esc_html_e( 'SEO title', 'oriente' ); ?></span>
			<input type="text" class="large-text" maxlength="70" name="<?php echo esc_attr( $prefix . '[title]' ); ?>" value="<?php echo esc_attr( $record['title'] ); ?>" placeholder="<?php echo esc_attr( $fallbacks['title'] ); ?>">
			<small><?php esc_html_e( 'Recommended: about 50–60 characters. Leave blank to use the automatic title.', 'oriente' ); ?></small>
		</label>

		<label>
			<span><?php esc_html_e( 'Keywords', 'oriente' ); ?></span>
			<input type="text" class="large-text" maxlength="255" name="<?php echo esc_attr( $prefix . '[keywords]' ); ?>" value="<?php echo esc_attr( $record['keywords'] ); ?>" placeholder="<?php esc_attr_e( 'tableware, home decor, handmade objects', 'oriente' ); ?>">
			<small><?php esc_html_e( 'Optional. Separate keywords with commas.', 'oriente' ); ?></small>
		</label>

		<label>
			<span><?php esc_html_e( 'Meta description', 'oriente' ); ?></span>
			<textarea class="large-text" rows="3" maxlength="320" name="<?php echo esc_attr( $prefix . '[description]' ); ?>" placeholder="<?php echo esc_attr( $fallbacks['description'] ); ?>"><?php echo esc_textarea( $record['description'] ); ?></textarea>
			<small><?php esc_html_e( 'Recommended: about 150–160 characters. Leave blank to use the page or category description.', 'oriente' ); ?></small>
		</label>

		<div>
			<span class="oriente-seo-field-label"><?php esc_html_e( 'Featured / social image', 'oriente' ); ?></span>
			<?php
			oriente_seo_image_field(
				$prefix . '[image_id]',
				$record['image_id'],
				$fallbacks['image']
			);
			?>
		</div>
	</div>
	<?php
}

/** Build an automatic title without invoking WordPress's title filters recursively. */
function oriente_seo_fallback_title( $label ) {
	$site_name = get_bloginfo( 'name' );

	return $site_name && $label !== $site_name ? $label . ' – ' . $site_name : $label;
}

/** Return the homepage hero as a final social-image fallback. */
function oriente_seo_theme_fallback_image_url() {
	$defaults = function_exists( 'oriente_homepage_defaults' ) ? oriente_homepage_defaults() : array();
	$fallback = isset( $defaults['oriente_hero_image'] ) ? $defaults['oriente_hero_image'] : '';

	return esc_url_raw( get_theme_mod( 'oriente_hero_image', $fallback ) );
}

/** Render the SEO management screen. */
function oriente_seo_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings        = oriente_seo_get_settings();
	$site_name       = get_bloginfo( 'name' );
	$site_description = get_bloginfo( 'description' );
	$home_title      = $site_description ? $site_name . ' – ' . $site_description : $site_name;
	$front_page_id   = absint( get_option( 'page_on_front' ) );
	$shop_page_id    = function_exists( 'wc_get_page_id' ) ? absint( wc_get_page_id( 'shop' ) ) : 0;
	$shop_title      = $shop_page_id ? get_the_title( $shop_page_id ) : __( 'Shop', 'oriente' );
	$shop_description = $shop_page_id ? wp_strip_all_tags( get_post_field( 'post_excerpt', $shop_page_id ) ) : '';
	$categories      = taxonomy_exists( 'product_cat' ) ? get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	) : array();
	$categories      = is_wp_error( $categories ) ? array() : $categories;
	?>
	<div class="wrap oriente-seo-admin">
		<h1><?php esc_html_e( 'Oriente SEO', 'oriente' ); ?></h1>
		<p class="oriente-seo-intro"><?php esc_html_e( 'Manage search-result metadata and social sharing images for the main storefront pages. Blank title and description fields use the shown automatic fallback.', 'oriente' ); ?></p>

		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'oriente_seo' ); ?>

			<section class="oriente-seo-card oriente-seo-card--global">
				<div>
					<p class="oriente-seo-eyebrow"><?php esc_html_e( 'Website default', 'oriente' ); ?></p>
					<h2><?php esc_html_e( 'Default featured image', 'oriente' ); ?></h2>
					<p><?php esc_html_e( 'Used by Open Graph and social platforms whenever the current page has no image override or native featured image.', 'oriente' ); ?></p>
				</div>
				<?php oriente_seo_image_field( 'oriente_seo_settings[default_image_id]', $settings['default_image_id'], __( 'Use a landscape image of at least 1200 × 630 pixels. If unset, the homepage hero is used.', 'oriente' ) ); ?>
			</section>

			<section class="oriente-seo-card">
				<div class="oriente-seo-card-heading">
					<div>
						<p class="oriente-seo-eyebrow"><?php esc_html_e( 'Page', 'oriente' ); ?></p>
						<h2><?php esc_html_e( 'Home page', 'oriente' ); ?></h2>
					</div>
					<?php if ( $front_page_id ) : ?>
						<a class="button" href="<?php echo esc_url( get_edit_post_link( $front_page_id ) ); ?>"><?php esc_html_e( 'Edit page', 'oriente' ); ?></a>
					<?php endif; ?>
				</div>
				<?php
				oriente_seo_record_fields(
					'home',
					$settings['home'],
					array(
						'title'       => $home_title,
						'description' => $site_description,
						'image'       => __( 'Falls back to the home page featured image, then the website default image.', 'oriente' ),
					)
				);
				?>
			</section>

			<section class="oriente-seo-card">
				<div class="oriente-seo-card-heading">
					<div>
						<p class="oriente-seo-eyebrow"><?php esc_html_e( 'Page', 'oriente' ); ?></p>
						<h2><?php esc_html_e( 'Shop', 'oriente' ); ?></h2>
					</div>
					<?php if ( $shop_page_id ) : ?>
						<a class="button" href="<?php echo esc_url( get_edit_post_link( $shop_page_id ) ); ?>"><?php esc_html_e( 'Edit page', 'oriente' ); ?></a>
					<?php endif; ?>
				</div>
				<?php
				oriente_seo_record_fields(
					'shop',
					$settings['shop'],
					array(
						'title'       => oriente_seo_fallback_title( $shop_title ),
						'description' => $shop_description ? $shop_description : $site_description,
						'image'       => __( 'Falls back to the Shop page featured image, then the website default image.', 'oriente' ),
					)
				);
				?>
			</section>

			<div class="oriente-seo-section-heading">
				<p class="oriente-seo-eyebrow"><?php esc_html_e( 'WooCommerce', 'oriente' ); ?></p>
				<h2><?php esc_html_e( 'Product categories', 'oriente' ); ?></h2>
				<p><?php esc_html_e( 'Each category can have unique metadata and its own social sharing image.', 'oriente' ); ?></p>
			</div>

			<?php if ( $categories ) : ?>
				<?php foreach ( $categories as $category ) : ?>
					<?php
					$category_key         = 'categories][' . $category->term_id;
					$category_settings    = isset( $settings['categories'][ $category->term_id ] ) ? $settings['categories'][ $category->term_id ] : array();
					$category_description = wp_strip_all_tags( term_description( $category, 'product_cat' ) );
					$edit_link            = get_edit_term_link( $category->term_id, 'product_cat' );
					?>
					<section class="oriente-seo-card">
						<div class="oriente-seo-card-heading">
							<div>
								<p class="oriente-seo-eyebrow"><?php echo esc_html( $category->slug ); ?></p>
								<h2><?php echo esc_html( $category->name ); ?></h2>
							</div>
							<?php if ( $edit_link ) : ?>
								<a class="button" href="<?php echo esc_url( $edit_link ); ?>"><?php esc_html_e( 'Edit category', 'oriente' ); ?></a>
							<?php endif; ?>
						</div>
						<?php
						oriente_seo_record_fields(
							$category_key,
							$category_settings,
							array(
								'title'       => oriente_seo_fallback_title( $category->name ),
								'description' => $category_description ? $category_description : $site_description,
								'image'       => __( 'Falls back to the WooCommerce category thumbnail, then the website default image.', 'oriente' ),
							)
						);
						?>
					</section>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="notice notice-info inline"><p><?php esc_html_e( 'No product categories are available yet.', 'oriente' ); ?></p></div>
			<?php endif; ?>

			<?php submit_button( __( 'Save SEO settings', 'oriente' ) ); ?>
		</form>
	</div>

	<style>
		.oriente-seo-admin { max-width: 1120px; }
		.oriente-seo-intro { max-width: 760px; margin-bottom: 24px; color: #50575e; font-size: 14px; }
		.oriente-seo-card { margin: 0 0 20px; padding: 24px; border: 1px solid #dcdcde; border-radius: 8px; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,.03); }
		.oriente-seo-card--global { display: grid; grid-template-columns: minmax(0,1fr) minmax(280px,420px); gap: 32px; align-items: start; }
		.oriente-seo-card h2, .oriente-seo-section-heading h2 { margin: 0; font-size: 20px; }
		.oriente-seo-card-heading { display: flex; align-items: start; justify-content: space-between; gap: 24px; margin-bottom: 24px; }
		.oriente-seo-eyebrow { margin: 0 0 5px; color: #646970; font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }
		.oriente-seo-section-heading { margin: 32px 0 16px; }
		.oriente-seo-section-heading p:last-child { margin-bottom: 0; color: #646970; }
		.oriente-seo-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 22px 28px; }
		.oriente-seo-fields > label, .oriente-seo-fields > div { display: block; }
		.oriente-seo-fields label > span, .oriente-seo-field-label { display: block; margin-bottom: 7px; font-weight: 600; }
		.oriente-seo-fields small { display: block; margin-top: 6px; color: #646970; }
		.oriente-seo-image-preview { display: flex; width: 220px; max-width: 100%; min-height: 112px; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #c3c4c7; border-radius: 4px; background: #f6f7f7; }
		.oriente-seo-image-preview img { display: block; width: 100%; height: 140px; object-fit: cover; }
		.oriente-seo-image-preview span { padding: 20px; color: #646970; }
		.oriente-seo-image-actions { display: flex; gap: 12px; align-items: center; margin: 9px 0 0; }
		.oriente-seo-image-actions .is-hidden { display: none; }
		@media (max-width: 782px) { .oriente-seo-card--global, .oriente-seo-fields { grid-template-columns: 1fr; } }
	</style>

	<script>
		(function ($) {
			'use strict';

			$(document).on('click', '[data-seo-image-select]', function (event) {
				event.preventDefault();
				var field = $(this).closest('[data-seo-image-field]');
				var frame = wp.media({
					title: <?php echo wp_json_encode( __( 'Choose an SEO image', 'oriente' ) ); ?>,
					button: { text: <?php echo wp_json_encode( __( 'Use this image', 'oriente' ) ); ?> },
					library: { type: 'image' },
					multiple: false
				});

				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					var imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
					field.find('[data-seo-image-id]').val(attachment.id);
					field.find('[data-seo-image-preview]').removeClass('is-empty').html($('<img>', { src: imageUrl, alt: '' }));
					field.find('[data-seo-image-remove]').removeClass('is-hidden');
				});

				frame.open();
			});

			$(document).on('click', '[data-seo-image-remove]', function (event) {
				event.preventDefault();
				var field = $(this).closest('[data-seo-image-field]');
				field.find('[data-seo-image-id]').val('');
				field.find('[data-seo-image-preview]').addClass('is-empty').html($('<span>').text(<?php echo wp_json_encode( __( 'No image selected', 'oriente' ) ); ?>));
				$(this).addClass('is-hidden');
			});
		})(jQuery);
	</script>
	<?php
}

/** Identify whether the current request is one of the managed public contexts. */
function oriente_seo_current_context() {
	if ( is_front_page() ) {
		return array(
			'type' => 'home',
			'id'   => 0,
		);
	}

	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return array(
			'type' => 'shop',
			'id'   => 0,
		);
	}

	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term && 'product_cat' === $term->taxonomy ) {
			return array(
				'type' => 'category',
				'id'   => (int) $term->term_id,
			);
		}
	}

	return null;
}

/** Resolve saved metadata and sensible fallbacks for the current public request. */
function oriente_seo_current_metadata() {
	$context = oriente_seo_current_context();
	if ( ! $context ) {
		return null;
	}

	$settings         = oriente_seo_get_settings();
	$site_name        = get_bloginfo( 'name' );
	$site_description = get_bloginfo( 'description' );
	$record           = array();
	$fallback_title   = '';
	$fallback_desc    = $site_description;
	$fallback_image   = 0;

	if ( 'home' === $context['type'] ) {
		$record         = $settings['home'];
		$fallback_title = $site_description ? $site_name . ' – ' . $site_description : $site_name;
		$front_page_id  = absint( get_option( 'page_on_front' ) );
		$fallback_image = $front_page_id ? get_post_thumbnail_id( $front_page_id ) : 0;
	} elseif ( 'shop' === $context['type'] ) {
		$record          = $settings['shop'];
		$shop_page_id    = function_exists( 'wc_get_page_id' ) ? absint( wc_get_page_id( 'shop' ) ) : 0;
		$shop_title      = $shop_page_id ? get_the_title( $shop_page_id ) : __( 'Shop', 'oriente' );
		$shop_excerpt    = $shop_page_id ? wp_strip_all_tags( get_post_field( 'post_excerpt', $shop_page_id ) ) : '';
		$fallback_title  = oriente_seo_fallback_title( $shop_title );
		$fallback_desc   = $shop_excerpt ? $shop_excerpt : $site_description;
		$fallback_image  = $shop_page_id ? get_post_thumbnail_id( $shop_page_id ) : 0;
	} else {
		$term             = get_term( $context['id'], 'product_cat' );
		$record           = isset( $settings['categories'][ $context['id'] ] ) ? $settings['categories'][ $context['id'] ] : array();
		$fallback_title   = $term instanceof WP_Term ? oriente_seo_fallback_title( $term->name ) : $site_name;
		$term_description = $term instanceof WP_Term ? wp_strip_all_tags( term_description( $term, 'product_cat' ) ) : '';
		$fallback_desc    = $term_description ? $term_description : $site_description;
		$fallback_image   = $term instanceof WP_Term ? absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) ) : 0;
	}

	$record   = oriente_seo_normalize_record( $record );
	$image_id = oriente_seo_sanitize_image_id( $record['image_id'] );
	if ( ! $image_id ) {
		$image_id = oriente_seo_sanitize_image_id( $fallback_image );
	}
	if ( ! $image_id ) {
		$image_id = oriente_seo_sanitize_image_id( $settings['default_image_id'] );
	}

	$title       = $record['title'] ? $record['title'] : $fallback_title;
	$description = $record['description'] ? $record['description'] : $fallback_desc;
	$paged       = max( 1, absint( get_query_var( 'paged' ) ) );
	if ( $paged > 1 ) {
		$title .= sprintf(
			/* translators: %d: current archive page number. */
			__( ' – Page %d', 'oriente' ),
			$paged
		);
	}

	return array(
		'title'       => trim( wp_strip_all_tags( $title ) ),
		'keywords'    => trim( wp_strip_all_tags( $record['keywords'] ) ),
		'description' => trim( wp_strip_all_tags( $description ) ),
		'image_id'    => $image_id,
		'image_url'   => $image_id ? '' : oriente_seo_theme_fallback_image_url(),
		'url'         => get_pagenum_link( $paged ),
	);
}

/** Replace the browser/search title on managed storefront pages. */
function oriente_seo_filter_document_title( $title ) {
	$metadata = oriente_seo_current_metadata();

	return $metadata && $metadata['title'] ? $metadata['title'] : $title;
}
add_filter( 'pre_get_document_title', 'oriente_seo_filter_document_title', 20 );

/** Print search and social metadata in the document head. */
function oriente_seo_output_meta_tags() {
	$metadata = oriente_seo_current_metadata();
	if ( ! $metadata ) {
		return;
	}

	$image     = $metadata['image_id'] ? wp_get_attachment_image_src( $metadata['image_id'], 'full' ) : false;
	$image_url = $image ? $image[0] : $metadata['image_url'];
	$image_alt = '';
	if ( $metadata['image_id'] ) {
		$image_alt = get_post_meta( $metadata['image_id'], '_wp_attachment_image_alt', true );
		$image_alt = $image_alt ? $image_alt : get_the_title( $metadata['image_id'] );
	} elseif ( $image_url ) {
		$image_alt = get_bloginfo( 'name' );
	}
	?>
	<link rel="canonical" href="<?php echo esc_url( $metadata['url'] ); ?>">
	<?php if ( $metadata['description'] ) : ?>
		<meta name="description" content="<?php echo esc_attr( $metadata['description'] ); ?>">
	<?php endif; ?>
	<?php if ( $metadata['keywords'] ) : ?>
		<meta name="keywords" content="<?php echo esc_attr( $metadata['keywords'] ); ?>">
	<?php endif; ?>
	<meta property="og:type" content="website">
	<meta property="og:locale" content="<?php echo esc_attr( get_locale() ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $metadata['title'] ); ?>">
	<?php if ( $metadata['description'] ) : ?>
		<meta property="og:description" content="<?php echo esc_attr( $metadata['description'] ); ?>">
	<?php endif; ?>
	<meta property="og:url" content="<?php echo esc_url( $metadata['url'] ); ?>">
	<meta name="twitter:card" content="<?php echo $image_url ? 'summary_large_image' : 'summary'; ?>">
	<meta name="twitter:title" content="<?php echo esc_attr( $metadata['title'] ); ?>">
	<?php if ( $metadata['description'] ) : ?>
		<meta name="twitter:description" content="<?php echo esc_attr( $metadata['description'] ); ?>">
	<?php endif; ?>
	<?php if ( $image_url ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $image_url ); ?>">
		<?php if ( $image ) : ?>
			<meta property="og:image:width" content="<?php echo esc_attr( $image[1] ); ?>">
			<meta property="og:image:height" content="<?php echo esc_attr( $image[2] ); ?>">
		<?php endif; ?>
		<meta name="twitter:image" content="<?php echo esc_url( $image_url ); ?>">
		<?php if ( $image_alt ) : ?>
			<meta property="og:image:alt" content="<?php echo esc_attr( $image_alt ); ?>">
			<meta name="twitter:image:alt" content="<?php echo esc_attr( $image_alt ); ?>">
		<?php endif; ?>
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'oriente_seo_output_meta_tags', 5 );
