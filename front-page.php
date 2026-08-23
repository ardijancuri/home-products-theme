<?php
/** Editorial storefront home. @package Oriente */

get_header();

$home_defaults = oriente_homepage_defaults();
$home_mod      = static function ( $setting_id ) use ( $home_defaults ) {
	return get_theme_mod( $setting_id, isset( $home_defaults[ $setting_id ] ) ? $home_defaults[ $setting_id ] : '' );
};

$shop_url = oriente_shop_url();

$hero_slides = array();
for ( $hero_index = 1; $hero_index <= ORIENTE_HERO_SLIDES_MAX; ++$hero_index ) {
	$hero_prefix = 1 === $hero_index ? 'oriente_hero' : "oriente_hero_{$hero_index}";
	$hero_slide  = array(
		'image'         => $home_mod( "{$hero_prefix}_image" ),
		'title'         => $home_mod( "{$hero_prefix}_title" ),
		'text'          => $home_mod( "{$hero_prefix}_text" ),
		'primary_label' => $home_mod( "{$hero_prefix}_primary_label" ),
		'primary_url'   => $home_mod( "{$hero_prefix}_primary_url" ),
	);
	if ( $hero_slide['image'] ) {
		$hero_slides[] = $hero_slide;
	}
}

$collections_title      = $home_mod( 'oriente_collections_title' );
$collections_text       = $home_mod( 'oriente_collections_text' );
$collections_link_label = $home_mod( 'oriente_collections_link_label' );
$collections_link_url   = $home_mod( 'oriente_collections_link_url' );
$collections_count      = min( ORIENTE_COLLECTIONS_MAX, max( ORIENTE_COLLECTIONS_MIN, absint( $home_mod( 'oriente_collections_count' ) ) ) );
$collection_cards       = array();
for ( $index = 1; $index <= $collections_count; ++$index ) {
	$collection_card = array(
		'image' => $home_mod( "oriente_collection_{$index}_image" ),
		'title' => $home_mod( "oriente_collection_{$index}_title" ),
		'text'  => $home_mod( "oriente_collection_{$index}_text" ),
		'url'   => $home_mod( "oriente_collection_{$index}_url" ),
	);
	if ( $collection_card['image'] || $collection_card['title'] || $collection_card['text'] ) {
		$collection_cards[] = $collection_card;
	}
}

$featured_title      = $home_mod( 'oriente_featured_title' );
$featured_text       = $home_mod( 'oriente_featured_text' );
$featured_link_label = $home_mod( 'oriente_featured_link_label' );
$featured_product_ids = array_values( array_filter( array_map( 'absint', explode( ',', (string) $home_mod( 'oriente_featured_products' ) ) ) ) );
$featured_category   = sanitize_title( $home_mod( 'oriente_featured_category' ) );
$featured_by_status  = ORIENTE_FEATURED_PRODUCTS_CHOICE === $featured_category;
$featured_link_url   = $home_mod( 'oriente_featured_link_url' );
if ( ! $featured_link_url ) {
	$featured_link_url = $featured_product_ids || ! $featured_category || $featured_by_status ? $shop_url : oriente_category_url( $featured_category );
}
$featured_query_args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 9,
	'orderby'        => 'date',
	'order'          => 'ASC',
);
if ( $featured_by_status ) {
	$featured_query_args['tax_query'] = array(
		array(
			'taxonomy' => 'product_visibility',
			'field'    => 'slug',
			'terms'    => array( 'featured' ),
		),
	);
} elseif ( $featured_product_ids ) {
	$featured_query_args['posts_per_page'] = count( $featured_product_ids );
	$featured_query_args['post__in']       = $featured_product_ids;
	$featured_query_args['orderby']        = 'post__in';
} elseif ( $featured_category ) {
	$featured_query_args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $featured_category,
		),
	);
}

$follow_image        = $home_mod( 'oriente_follow_image' );
$follow_mobile_image = $home_mod( 'oriente_follow_mobile_image' );
$follow_title        = $home_mod( 'oriente_follow_title' );
$follow_text         = $home_mod( 'oriente_follow_text' );
$follow_profiles = array_filter(
	oriente_social_profiles(),
	static function ( $profile, $network ) {
		return 'whatsapp' !== $network && ! empty( $profile['url'] );
	},
	ARRAY_FILTER_USE_BOTH
);

$story_images     = array();
$story_initial_image = $home_mod( 'oriente_story_initial_image' );
if ( $story_initial_image ) {
	$story_images[] = $story_initial_image;
}
for ( $story_index = 1; $story_index <= 4; ++$story_index ) {
	$story_setting_id = 1 === $story_index ? 'oriente_story_image' : "oriente_story_image_{$story_index}";
	$story_image_url  = $home_mod( $story_setting_id );
	if ( $story_image_url ) {
		$story_images[] = $story_image_url;
	}
}
$story_title      = $home_mod( 'oriente_story_title' );
$story_text       = $home_mod( 'oriente_story_text' );
$story_link_label = $home_mod( 'oriente_story_link_label' );
$story_link_url   = $home_mod( 'oriente_story_link_url' );

$kitchen_title      = $home_mod( 'oriente_kitchen_title' );
$kitchen_text       = $home_mod( 'oriente_kitchen_text' );
$kitchen_link_label = $home_mod( 'oriente_kitchen_link_label' );
$kitchen_category   = sanitize_title( $home_mod( 'oriente_kitchen_category' ) );
$kitchen_link_url   = $home_mod( 'oriente_kitchen_link_url' );
if ( ! $kitchen_link_url ) {
	$kitchen_link_url = $kitchen_category ? oriente_category_url( $kitchen_category ) : $shop_url;
}
$kitchen_query_args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 8,
	'orderby'        => 'date',
	'order'          => 'ASC',
);
if ( $kitchen_category ) {
	$kitchen_query_args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $kitchen_category,
		),
	);
}

$closing_image        = $home_mod( 'oriente_closing_image' );
$closing_mobile_image = $home_mod( 'oriente_closing_mobile_image' );
$closing_title        = $home_mod( 'oriente_closing_title' );
$closing_text         = $home_mod( 'oriente_closing_text' );
$closing_link_label   = $home_mod( 'oriente_closing_link_label' );
$closing_link_url     = $home_mod( 'oriente_closing_link_url' );

$faq_whatsapp_url = oriente_whatsapp_support_url();
$faq_items        = array(
	array(
		'question' => __( 'How does the online store work?', 'oriente' ),
		'answer'   => __( 'Browse our products and categories, open an item to see its details, then add the pieces you want to your bag. The bag prepares your order for WhatsApp instead of taking payment online.', 'oriente' ),
	),
	array(
		'question' => __( 'How do I place an order through WhatsApp?', 'oriente' ),
		'answer'   => __( 'Open your bag and select “Order via WhatsApp.” A ready-made message with your chosen products and quantities will open in WhatsApp. Review it, add any note you would like us to see, and send it to our team.', 'oriente' ),
	),
	array(
		'question' => __( 'What happens after I send my order?', 'oriente' ),
		'answer'   => __( 'Our team checks availability, confirms the quantities and order total, and replies in the same WhatsApp conversation. Your order is only finalized after those details are confirmed with you.', 'oriente' ),
	),
	array(
		'question' => __( 'How do I pay for my order?', 'oriente' ),
		'answer'   => __( 'Payment is arranged with our team after your order is confirmed. We will explain the available option for your order before you approve it.', 'oriente' ),
	),
	array(
		'question' => __( 'How are delivery and timing arranged?', 'oriente' ),
		'answer'   => __( 'Share your location or delivery address in WhatsApp. Our team will confirm the delivery details and expected timing with you before the order is finalized.', 'oriente' ),
	),
);
?>

<main id="primary" class="site-main">
	<?php if ( $hero_slides ) : ?>
		<section class="hero hero--full home-hero-slider" data-home-hero-slider aria-label="<?php esc_attr_e( 'Featured stories', 'oriente' ); ?>" aria-roledescription="<?php esc_attr_e( 'carousel', 'oriente' ); ?>">
			<div class="home-hero-slider__slides">
				<?php foreach ( $hero_slides as $hero_position => $hero_slide ) : ?>
					<?php $hero_is_active = 0 === $hero_position; ?>
					<article class="home-hero-slide<?php echo $hero_is_active ? ' is-active' : ''; ?><?php echo $hero_slide['primary_url'] ? ' hero--clickable' : ''; ?>" data-home-hero-slide data-slide-title="<?php echo esc_attr( $hero_slide['title'] ); ?>" role="group" aria-roledescription="<?php esc_attr_e( 'slide', 'oriente' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%1$d of %2$d', 'oriente' ), $hero_position + 1, count( $hero_slides ) ) ); ?>" aria-hidden="<?php echo $hero_is_active ? 'false' : 'true'; ?>">
						<img class="hero-background" src="<?php echo esc_url( $hero_slide['image'] ); ?>" alt=""<?php echo $hero_is_active ? ' fetchpriority="high"' : ' loading="lazy"'; ?> decoding="async">
						<div class="hero-shade" aria-hidden="true"></div>
						<?php if ( $hero_slide['primary_url'] ) : ?>
							<a class="hero-click-target" href="<?php echo esc_url( $hero_slide['primary_url'] ); ?>" aria-label="<?php echo esc_attr( $hero_slide['primary_label'] ? $hero_slide['primary_label'] : $hero_slide['title'] ); ?>"></a>
						<?php endif; ?>
						<div class="hero-copy">
							<div class="home-hero-copy-content">
								<?php if ( $hero_is_active ) : ?>
									<h1 id="hero-title"><?php echo esc_html( $hero_slide['title'] ); ?></h1>
								<?php else : ?>
									<h2><?php echo esc_html( $hero_slide['title'] ); ?></h2>
								<?php endif; ?>
								<?php if ( $hero_slide['text'] ) : ?>
									<p class="hero-text"><?php echo esc_html( $hero_slide['text'] ); ?></p>
								<?php endif; ?>
								<?php if ( $hero_slide['primary_label'] && $hero_slide['primary_url'] ) : ?>
									<div class="hero-actions">
										<a class="hero-text-link" href="<?php echo esc_url( $hero_slide['primary_url'] ); ?>"><?php echo esc_html( $hero_slide['primary_label'] ); ?></a>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<?php if ( count( $hero_slides ) > 1 ) : ?>
				<p class="screen-reader-text" data-home-hero-status aria-live="polite"><?php echo esc_html( sprintf( __( 'Slide 1 of %1$d: %2$s', 'oriente' ), count( $hero_slides ), $hero_slides[0]['title'] ) ); ?></p>
			<?php endif; ?>
		</section>
	<?php endif; ?>

	<section id="collections" class="editorial-rail-section" aria-labelledby="collections-title">
		<header class="ori-section-header ori-section-header--mobile-full" data-reveal>
			<div>
				<h2 id="collections-title"><?php echo esc_html( $collections_title ); ?></h2>
				<?php if ( $collections_text ) : ?>
					<p class="ori-section-description"><?php echo esc_html( $collections_text ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $collections_link_label && $collections_link_url ) : ?>
				<div class="section-header-actions">
					<a class="text-link text-link--no-icon slider-view-all" href="<?php echo esc_url( $collections_link_url ); ?>"><?php echo esc_html( $collections_link_label ); ?></a>
				</div>
			<?php endif; ?>
		</header>

		<div class="ori-slider" data-slider>
			<div class="editorial-rail" data-slider-track tabindex="0" aria-label="<?php echo esc_attr( $collections_title ); ?>">
				<?php foreach ( $collection_cards as $card_index => $card ) : ?>
					<a class="editorial-slide" href="<?php echo esc_url( $card['url'] ? $card['url'] : $shop_url ); ?>">
						<span class="editorial-slide-media">
							<?php if ( $card['image'] ) : ?>
								<img src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" loading="lazy">
							<?php endif; ?>
						</span>
						<span class="editorial-slide-index"><?php echo esc_html( str_pad( (string) ( $card_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php if ( $card['title'] ) : ?>
							<h3><?php echo esc_html( $card['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( $card['text'] ) : ?>
							<p><?php echo esc_html( $card['text'] ); ?></p>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
			<div class="slider-progress" aria-hidden="true"><span data-slider-progress></span></div>
		</div>
	</section>

	<section class="products-editorial products-editorial--slider" aria-labelledby="objects-title">
		<header class="ori-section-header ori-section-header--mobile-full" data-reveal>
			<div>
				<h2 id="objects-title"><?php echo esc_html( $featured_title ); ?></h2>
				<?php if ( $featured_text ) : ?>
					<p class="ori-section-description"><?php echo esc_html( $featured_text ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $featured_link_label && $featured_link_url ) : ?>
				<div class="section-header-actions">
					<a class="text-link text-link--no-icon slider-view-all" href="<?php echo esc_url( $featured_link_url ); ?>"><?php echo esc_html( $featured_link_label ); ?></a>
				</div>
			<?php endif; ?>
		</header>

		<div class="ori-slider" data-slider>
			<div class="product-editorial-grid product-slider-track" data-slider-track tabindex="0" aria-label="<?php echo esc_attr( $featured_title ); ?>">
				<?php
				$product_query = new WP_Query( $featured_query_args );
				if ( $product_query->have_posts() ) :
					while ( $product_query->have_posts() ) :
						$product_query->the_post();
						$product = wc_get_product( get_the_ID() );
						if ( ! $product ) {
							continue;
						}
						$terms = get_the_terms( $product->get_id(), 'product_cat' );
						?>
						<article class="product-editorial-item" data-reveal>
							<div class="product-media">
								<span class="product-badge"><?php esc_html_e( 'New', 'oriente' ); ?></span>
								<a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
									<?php oriente_product_card_images( $product, 'oriente-product' ); ?>
								</a>
								<a class="quick-add add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" rel="nofollow"><?php esc_html_e( 'Add to bag', 'oriente' ); ?></a>
							</div>
							<div class="product-meta">
								<span class="product-category"><?php echo esc_html( $terms && ! is_wp_error( $terms ) ? $terms[0]->name : __( 'Oriente object', 'oriente' ) ); ?></span>
								<h3 class="product-title"><a href="<?php the_permalink(); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
								<span class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
								<a class="oriente-mobile-card-add add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to cart', 'oriente' ), $product->get_name() ) ); ?>" rel="nofollow"><?php esc_html_e( 'Add to cart', 'oriente' ); ?></a>
							</div>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
			<div class="slider-progress" aria-hidden="true"><span data-slider-progress></span></div>
		</div>
	</section>

	<section id="materials" class="products-editorial products-editorial--slider products-editorial--category" aria-labelledby="kitchen-products-title">
		<header class="ori-section-header ori-section-header--mobile-full" data-reveal>
			<div>
				<h2 id="kitchen-products-title"><?php echo esc_html( $kitchen_title ); ?></h2>
				<?php if ( $kitchen_text ) : ?>
					<p class="ori-section-description"><?php echo esc_html( $kitchen_text ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $kitchen_link_label && $kitchen_link_url ) : ?>
				<div class="section-header-actions">
					<a class="text-link text-link--no-icon slider-view-all" href="<?php echo esc_url( $kitchen_link_url ); ?>"><?php echo esc_html( $kitchen_link_label ); ?></a>
				</div>
			<?php endif; ?>
		</header>

		<div class="ori-slider" data-slider>
			<div class="product-editorial-grid product-slider-track" data-slider-track tabindex="0" aria-label="<?php echo esc_attr( $kitchen_title ); ?>">
				<?php
				$kitchen_product_query = new WP_Query( $kitchen_query_args );
				if ( $kitchen_product_query->have_posts() ) :
					while ( $kitchen_product_query->have_posts() ) :
						$kitchen_product_query->the_post();
						$product = wc_get_product( get_the_ID() );
						if ( ! $product ) {
							continue;
						}
						$terms = get_the_terms( $product->get_id(), 'product_cat' );
						?>
						<article class="product-editorial-item" data-reveal>
							<div class="product-media">
								<a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
									<?php oriente_product_card_images( $product, 'oriente-product' ); ?>
								</a>
								<a class="quick-add add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" rel="nofollow"><?php esc_html_e( 'Add to bag', 'oriente' ); ?></a>
							</div>
							<div class="product-meta">
								<span class="product-category"><?php echo esc_html( $terms && ! is_wp_error( $terms ) ? $terms[0]->name : __( 'Oriente object', 'oriente' ) ); ?></span>
								<h3 class="product-title"><a href="<?php the_permalink(); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
								<span class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
								<a class="oriente-mobile-card-add add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to cart', 'oriente' ), $product->get_name() ) ); ?>" rel="nofollow"><?php esc_html_e( 'Add to cart', 'oriente' ); ?></a>
							</div>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
			<div class="slider-progress" aria-hidden="true"><span data-slider-progress></span></div>
		</div>
	</section>

	<section class="closing-edit closing-edit--framed" aria-labelledby="closing-title">
		<?php if ( $closing_image || $closing_mobile_image ) : ?>
			<picture class="closing-edit-media<?php echo $closing_mobile_image ? ' has-mobile-source' : ''; ?>">
				<?php if ( $closing_mobile_image ) : ?>
					<source media="(max-width: 600px)" srcset="<?php echo esc_url( $closing_mobile_image ); ?>">
				<?php endif; ?>
				<img src="<?php echo esc_url( $closing_image ? $closing_image : $closing_mobile_image ); ?>" alt="" loading="lazy">
			</picture>
		<?php endif; ?>
		<div class="closing-copy" data-reveal>
			<h2 id="closing-title"><?php echo esc_html( $closing_title ); ?></h2>
			<?php if ( $closing_text ) : ?>
				<p><?php echo esc_html( $closing_text ); ?></p>
			<?php endif; ?>
			<?php if ( $closing_link_label && $closing_link_url ) : ?>
				<a class="text-link text-link--no-icon" href="<?php echo esc_url( $closing_link_url ); ?>"><?php echo esc_html( $closing_link_label ); ?></a>
			<?php endif; ?>
		</div>
	</section>

	<section class="home-follow" aria-labelledby="follow-title">
		<?php if ( $follow_image || $follow_mobile_image ) : ?>
			<picture class="home-follow-media<?php echo $follow_mobile_image ? ' has-mobile-source' : ''; ?>">
				<?php if ( $follow_mobile_image ) : ?>
					<source media="(max-width: 600px)" srcset="<?php echo esc_url( $follow_mobile_image ); ?>">
				<?php endif; ?>
				<img class="home-follow-image" src="<?php echo esc_url( $follow_image ? $follow_image : $follow_mobile_image ); ?>" alt="" loading="lazy">
			</picture>
		<?php endif; ?>
		<div class="home-follow-shade" aria-hidden="true"></div>
		<div class="home-follow-content" data-reveal>
			<h2 id="follow-title"><?php echo esc_html( $follow_title ); ?></h2>
			<?php if ( $follow_text ) : ?>
				<p><?php echo esc_html( $follow_text ); ?></p>
			<?php endif; ?>
			<?php if ( $follow_profiles ) : ?>
				<nav class="home-follow-links" aria-label="<?php esc_attr_e( 'Follow Oriente on social media', 'oriente' ); ?>">
					<?php foreach ( $follow_profiles as $profile ) : ?>
						<a href="<?php echo esc_url( $profile['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $profile['label'] ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
	</section>

	<section id="story" class="story-panel story-panel--slider" aria-labelledby="story-title">
		<div class="story-copy" data-reveal>
			<h2 id="story-title"><?php echo esc_html( $story_title ); ?></h2>
			<?php if ( $story_text ) : ?>
				<p><?php echo esc_html( $story_text ); ?></p>
			<?php endif; ?>
			<?php if ( $story_link_label && $story_link_url ) : ?>
				<a class="text-link text-link--no-icon" href="<?php echo esc_url( $story_link_url ); ?>"><?php echo esc_html( $story_link_label ); ?></a>
			<?php endif; ?>
			<?php if ( count( $story_images ) > 1 ) : ?>
				<div class="story-slider-meta">
					<div class="slider-controls story-slider-controls">
						<button class="slider-button" type="button" data-slider-prev aria-controls="story-slider-track" aria-label="<?php esc_attr_e( 'Previous story image', 'oriente' ); ?>"><span class="slider-chevron slider-chevron--left" aria-hidden="true"></span></button>
						<button class="slider-button" type="button" data-slider-next aria-controls="story-slider-track" aria-label="<?php esc_attr_e( 'Next story image', 'oriente' ); ?>"><span class="slider-chevron slider-chevron--right" aria-hidden="true"></span></button>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="story-slider" data-slider aria-roledescription="carousel" aria-label="<?php echo esc_attr( $story_title ); ?>">
			<div id="story-slider-track" class="story-slider-track" data-slider-track tabindex="0">
				<?php foreach ( $story_images as $story_image_index => $story_image ) : ?>
					<figure class="story-slide" aria-label="<?php echo esc_attr( sprintf( __( 'Story image %1$d of %2$d', 'oriente' ), $story_image_index + 1, count( $story_images ) ) ); ?>">
						<img src="<?php echo esc_url( $story_image ); ?>" alt="<?php echo esc_attr( sprintf( __( '%1$s, image %2$d', 'oriente' ), $story_title, $story_image_index + 1 ) ); ?>" loading="lazy">
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="home-faq" aria-labelledby="home-faq-title">
		<div class="home-faq__inner">
			<header class="home-faq__intro" data-reveal>
				<h2 id="home-faq-title"><?php esc_html_e( 'Questions, answered.', 'oriente' ); ?></h2>
				<p><?php esc_html_e( 'Browse at your own pace, then complete your order in a direct WhatsApp conversation with our team.', 'oriente' ); ?></p>
				<?php if ( $faq_whatsapp_url ) : ?>
					<a class="text-link text-link--no-icon" href="<?php echo esc_url( $faq_whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Ask us on WhatsApp', 'oriente' ); ?></a>
				<?php endif; ?>
			</header>

			<div class="home-faq__list" data-reveal>
				<?php foreach ( $faq_items as $faq_item ) : ?>
					<details class="home-faq__item">
						<summary>
							<span><?php echo esc_html( $faq_item['question'] ); ?></span>
							<span class="home-faq__toggle" aria-hidden="true"></span>
						</summary>
						<div class="home-faq__answer">
							<p><?php echo esc_html( $faq_item['answer'] ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
