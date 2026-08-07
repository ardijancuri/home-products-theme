<?php
/** Editorial storefront home. @package Oriente */

get_header();

$home_defaults = oriente_homepage_defaults();
$home_mod      = static function ( $setting_id ) use ( $home_defaults ) {
	return get_theme_mod( $setting_id, isset( $home_defaults[ $setting_id ] ) ? $home_defaults[ $setting_id ] : '' );
};

$shop_url = oriente_shop_url();

$hero_image           = $home_mod( 'oriente_hero_image' );
$hero_title           = $home_mod( 'oriente_hero_title' );
$hero_text            = $home_mod( 'oriente_hero_text' );
$hero_primary_label   = $home_mod( 'oriente_hero_primary_label' );
$hero_primary_url     = $home_mod( 'oriente_hero_primary_url' );
$hero_secondary_label = $home_mod( 'oriente_hero_secondary_label' );
$hero_secondary_url   = $home_mod( 'oriente_hero_secondary_url' );
$hero_scroll_label    = $home_mod( 'oriente_hero_scroll_label' );

$collections_title      = $home_mod( 'oriente_collections_title' );
$collections_text       = $home_mod( 'oriente_collections_text' );
$collections_link_label = $home_mod( 'oriente_collections_link_label' );
$collections_link_url   = $home_mod( 'oriente_collections_link_url' );
$collection_cards       = array();
for ( $index = 1; $index <= 4; ++$index ) {
	$collection_cards[] = array(
		'image' => $home_mod( "oriente_collection_{$index}_image" ),
		'title' => $home_mod( "oriente_collection_{$index}_title" ),
		'text'  => $home_mod( "oriente_collection_{$index}_text" ),
		'url'   => $home_mod( "oriente_collection_{$index}_url" ),
	);
}

$featured_title      = $home_mod( 'oriente_featured_title' );
$featured_text       = $home_mod( 'oriente_featured_text' );
$featured_link_label = $home_mod( 'oriente_featured_link_label' );
$featured_category   = sanitize_title( $home_mod( 'oriente_featured_category' ) );
$featured_link_url   = $home_mod( 'oriente_featured_link_url' );
if ( ! $featured_link_url ) {
	$featured_link_url = $featured_category ? oriente_category_url( $featured_category ) : $shop_url;
}
$featured_query_args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => 9,
	'orderby'        => 'date',
	'order'          => 'ASC',
);
if ( $featured_category ) {
	$featured_query_args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $featured_category,
		),
	);
}

$story_image      = $home_mod( 'oriente_story_image' );
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

$closing_image      = $home_mod( 'oriente_closing_image' );
$closing_title      = $home_mod( 'oriente_closing_title' );
$closing_text       = $home_mod( 'oriente_closing_text' );
$closing_link_label = $home_mod( 'oriente_closing_link_label' );
$closing_link_url   = $home_mod( 'oriente_closing_link_url' );
?>

<main id="primary" class="site-main">
	<section class="hero hero--full" aria-labelledby="hero-title">
		<?php if ( $hero_image ) : ?>
			<img class="hero-background" src="<?php echo esc_url( $hero_image ); ?>" alt="" fetchpriority="high">
		<?php endif; ?>
		<div class="hero-shade" aria-hidden="true"></div>
		<div class="hero-copy">
			<div data-reveal>
				<h1 id="hero-title"><?php echo esc_html( $hero_title ); ?></h1>
				<?php if ( $hero_text ) : ?>
					<p class="hero-text"><?php echo esc_html( $hero_text ); ?></p>
				<?php endif; ?>
				<div class="hero-actions">
					<?php if ( $hero_primary_label && $hero_primary_url ) : ?>
						<a class="ori-button ori-button--light" href="<?php echo esc_url( $hero_primary_url ); ?>"><?php echo esc_html( $hero_primary_label ); ?></a>
					<?php endif; ?>
					<?php if ( $hero_secondary_label && $hero_secondary_url ) : ?>
						<a class="hero-text-link" href="<?php echo esc_url( $hero_secondary_url ); ?>"><?php echo esc_html( $hero_secondary_label ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php if ( $hero_scroll_label ) : ?>
			<a class="hero-scroll" href="#collections"><?php echo esc_html( $hero_scroll_label ); ?></a>
		<?php endif; ?>
	</section>

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

	<section id="story" class="story-panel story-panel--compact" aria-labelledby="story-title">
		<div class="story-image">
			<?php if ( $story_image ) : ?>
				<img src="<?php echo esc_url( $story_image ); ?>" alt="<?php echo esc_attr( $story_title ); ?>" loading="lazy">
			<?php endif; ?>
		</div>
		<div class="story-copy" data-reveal>
			<h2 id="story-title"><?php echo esc_html( $story_title ); ?></h2>
			<?php if ( $story_text ) : ?>
				<p><?php echo esc_html( $story_text ); ?></p>
			<?php endif; ?>
			<?php if ( $story_link_label && $story_link_url ) : ?>
				<a class="text-link" href="<?php echo esc_url( $story_link_url ); ?>"><?php echo esc_html( $story_link_label ); ?></a>
			<?php endif; ?>
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
		<?php if ( $closing_image ) : ?>
			<img src="<?php echo esc_url( $closing_image ); ?>" alt="" loading="lazy">
		<?php endif; ?>
		<div class="closing-copy" data-reveal>
			<h2 id="closing-title"><?php echo esc_html( $closing_title ); ?></h2>
			<?php if ( $closing_text ) : ?>
				<p><?php echo esc_html( $closing_text ); ?></p>
			<?php endif; ?>
			<?php if ( $closing_link_label && $closing_link_url ) : ?>
				<a class="ori-button" href="<?php echo esc_url( $closing_link_url ); ?>"><?php echo esc_html( $closing_link_label ); ?></a>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
