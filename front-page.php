<?php
/** Editorial storefront home. @package Oriente */

get_header();

$images      = get_template_directory_uri() . '/assets/images/';
$shop_url    = oriente_shop_url();
$kitchen_url = oriente_category_url( 'kitchen' );
$decor_url   = oriente_category_url( 'home-decoration' );
$hero_title  = get_theme_mod( 'oriente_hero_title', __( 'Objects for a life beautifully lived.', 'oriente' ) );
$hero_text   = get_theme_mod( 'oriente_hero_text', __( 'Collectible kitchenware and quiet objects, selected for daily rituals and generous tables.', 'oriente' ) );
?>

<main id="primary" class="site-main">
	<section class="hero hero--full" aria-labelledby="hero-title">
		<img class="hero-background" src="<?php echo esc_url( $images . 'hero-kitchen-luxury.png' ); ?>" alt="" fetchpriority="high">
		<div class="hero-shade" aria-hidden="true"></div>
		<div class="hero-copy">
			<div data-reveal>
				<h1 id="hero-title"><?php echo esc_html( $hero_title ); ?></h1>
				<p class="hero-text"><?php echo esc_html( $hero_text ); ?></p>
				<div class="hero-actions">
					<a class="ori-button ori-button--light" href="<?php echo esc_url( $kitchen_url ); ?>"><?php esc_html_e( 'Shop the kitchen', 'oriente' ); ?></a>
					<a class="hero-text-link" href="<?php echo esc_url( $decor_url ); ?>"><?php esc_html_e( 'Discover home decor', 'oriente' ); ?></a>
				</div>
			</div>
		</div>
		<a class="hero-scroll" href="#collections"><?php esc_html_e( 'Explore the edit', 'oriente' ); ?></a>
	</section>

	<section id="collections" class="editorial-rail-section" aria-labelledby="collections-title">
		<header class="ori-section-header ori-section-header--mobile-full" data-reveal>
			<div>
				<h2 id="collections-title"><?php esc_html_e( 'Orienté collections', 'oriente' ); ?></h2>
			</div>
			<div class="section-header-actions">
				<a class="text-link text-link--no-icon slider-view-all" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'View all collections', 'oriente' ); ?></a>
			</div>
		</header>

		<div class="ori-slider" data-slider>
			<div class="editorial-rail" data-slider-track tabindex="0" aria-label="<?php esc_attr_e( 'Featured collections', 'oriente' ); ?>">
				<a class="editorial-slide" href="<?php echo esc_url( $kitchen_url ); ?>">
					<span class="editorial-slide-media"><img src="<?php echo esc_url( $images . 'hero-still-life.webp' ); ?>" alt="<?php esc_attr_e( 'Artisan ceramics and kitchen utensils', 'oriente' ); ?>" loading="lazy"></span>
					<span class="editorial-slide-index">01</span>
					<h3><?php esc_html_e( 'The Kitchen', 'oriente' ); ?></h3>
					<p><?php esc_html_e( 'Tools, tableware and serving pieces selected for generous everyday use.', 'oriente' ); ?></p>
				</a>
				<a class="editorial-slide" href="<?php echo esc_url( $kitchen_url ); ?>">
					<span class="editorial-slide-media"><img src="<?php echo esc_url( $images . 'linen-cups.webp' ); ?>" alt="<?php esc_attr_e( 'Linen and ceramic cups on a pale oak table', 'oriente' ); ?>" loading="lazy"></span>
					<span class="editorial-slide-index">02</span>
					<h3><?php esc_html_e( 'The Quiet Table', 'oriente' ); ?></h3>
					<p><?php esc_html_e( 'Layered neutrals and tactile materials for slower mornings and long lunches.', 'oriente' ); ?></p>
				</a>
				<a class="editorial-slide" href="<?php echo esc_url( $decor_url ); ?>">
					<span class="editorial-slide-media"><img src="<?php echo esc_url( $images . 'vase-interior.webp' ); ?>" alt="<?php esc_attr_e( 'Sculptural vessels in a calm interior', 'oriente' ); ?>" loading="lazy"></span>
					<span class="editorial-slide-index">03</span>
					<h3><?php esc_html_e( 'Home Decoration', 'oriente' ); ?></h3>
					<p><?php esc_html_e( 'Vessels and small objects that create atmosphere without filling the room.', 'oriente' ); ?></p>
				</a>
				<a class="editorial-slide" href="<?php echo esc_url( $decor_url ); ?>">
					<span class="editorial-slide-media"><img src="<?php echo esc_url( $images . 'candleholder.webp' ); ?>" alt="<?php esc_attr_e( 'Amber glass and candlelight', 'oriente' ); ?>" loading="lazy"></span>
					<span class="editorial-slide-index">04</span>
					<h3><?php esc_html_e( 'The Evening Edit', 'oriente' ); ?></h3>
					<p><?php esc_html_e( 'Warm glass, candlelight and reflective surfaces for the close of day.', 'oriente' ); ?></p>
				</a>
			</div>
			<div class="slider-progress" aria-hidden="true"><span data-slider-progress></span></div>
		</div>
	</section>

	<section class="products-editorial products-editorial--slider" aria-labelledby="objects-title">
		<header class="ori-section-header ori-section-header--mobile-full" data-reveal>
			<div>
				<h2 id="objects-title"><?php esc_html_e( 'Featured products', 'oriente' ); ?></h2>
			</div>
			<div class="section-header-actions">
				<a class="text-link text-link--no-icon slider-view-all" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'View all objects', 'oriente' ); ?></a>
			</div>
		</header>

		<div class="ori-slider" data-slider>
			<div class="product-editorial-grid product-slider-track" data-slider-track tabindex="0" aria-label="<?php esc_attr_e( 'Featured products', 'oriente' ); ?>">
				<?php
				$product_query = new WP_Query(
					array(
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'posts_per_page' => 9,
						'orderby'        => 'date',
						'order'          => 'ASC',
					)
				);
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
		<div class="story-image"><img src="<?php echo esc_url( $images . 'story-living-table.png' ); ?>" alt="<?php esc_attr_e( 'A warmly lit dining table set with ivory porcelain, clear glass and linen', 'oriente' ); ?>" loading="lazy"></div>
		<div class="story-copy" data-reveal>
			<h2 id="story-title"><?php esc_html_e( 'A memorable room is made for living.', 'oriente' ); ?></h2>
			<p><?php esc_html_e( 'Mix clear glass, warm metal and tactile cloth. The most inviting tables feel collected, never over-styled.', 'oriente' ); ?></p>
			<a class="text-link" href="<?php echo esc_url( $decor_url ); ?>"><?php esc_html_e( 'Explore the evening edit', 'oriente' ); ?></a>
		</div>
	</section>

	<section id="materials" class="products-editorial products-editorial--slider products-editorial--category" aria-labelledby="kitchen-products-title">
		<header class="ori-section-header ori-section-header--mobile-full" data-reveal>
			<div>
				<h2 id="kitchen-products-title"><?php esc_html_e( 'The Kitchen Edit', 'oriente' ); ?></h2>
			</div>
			<div class="section-header-actions">
				<a class="text-link text-link--no-icon slider-view-all" href="<?php echo esc_url( $kitchen_url ); ?>"><?php esc_html_e( 'View all kitchen', 'oriente' ); ?></a>
			</div>
		</header>

		<div class="ori-slider" data-slider>
			<div class="product-editorial-grid product-slider-track" data-slider-track tabindex="0" aria-label="<?php esc_attr_e( 'Kitchen products', 'oriente' ); ?>">
				<?php
				$kitchen_product_query = new WP_Query(
					array(
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'posts_per_page' => 8,
						'orderby'        => 'date',
						'order'          => 'ASC',
						'tax_query'      => array(
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'slug',
								'terms'    => 'kitchen',
							),
						),
					)
				);
				if ( $kitchen_product_query->have_posts() ) :
					while ( $kitchen_product_query->have_posts() ) :
						$kitchen_product_query->the_post();
						$product = wc_get_product( get_the_ID() );
						if ( ! $product ) {
							continue;
						}
						?>
						<article class="product-editorial-item" data-reveal>
							<div class="product-media">
								<a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
									<?php oriente_product_card_images( $product, 'oriente-product' ); ?>
								</a>
								<a class="quick-add add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" rel="nofollow"><?php esc_html_e( 'Add to bag', 'oriente' ); ?></a>
							</div>
							<div class="product-meta">
								<span class="product-category"><?php esc_html_e( 'Kitchen', 'oriente' ); ?></span>
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
		<img src="<?php echo esc_url( $images . 'hero-table.webp' ); ?>" alt="<?php esc_attr_e( 'A relaxed table set with white ceramics and clear glass', 'oriente' ); ?>" loading="lazy">
		<div class="closing-copy" data-reveal>
			<h2 id="closing-title"><?php esc_html_e( 'Make room for everyday occasions.', 'oriente' ); ?></h2>
			<a class="ori-button" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop the collection', 'oriente' ); ?></a>
		</div>
	</section>
</main>

<?php get_footer(); ?>
