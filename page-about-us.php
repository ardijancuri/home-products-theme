<?php
/**
 * About Us page.
 *
 * @package Oriente
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$asset_uri = trailingslashit( get_template_directory_uri() ) . 'assets/images/';
$shop_url  = oriente_shop_url();
?>

<main id="primary" class="site-main about-page">
	<section class="about-hero" aria-labelledby="about-hero-title">
		<img
			class="about-hero__image"
			src="<?php echo esc_url( $asset_uri . 'about-hero-v1.webp' ); ?>"
			alt=""
			width="1774"
			height="887"
			fetchpriority="high"
			data-about-parallax
		>
		<div class="about-hero__shade" aria-hidden="true"></div>
		<div class="about-hero__inner">
			<div class="about-hero__copy" data-about-hero>
				<h1 id="about-hero-title">Welcome to <span>ORIENTÉ.</span></h1>
				<p>ORIENTÉ brings quality, thoughtful design and lasting value to the spaces where you live and work.</p>
				<a class="about-scroll-link" href="#experience">
					<span>Our story</span>
					<svg viewBox="0 0 18 22" aria-hidden="true"><path d="M9 1v18M2.5 13 9 19.5 15.5 13" /></svg>
				</a>
			</div>
		</div>
	</section>

	<section id="experience" class="about-foundation" aria-labelledby="about-foundation-title">
		<div class="about-foundation__stat" data-reveal>
			<strong>30<sup>+</sup></strong>
			<span>years of industry<br>experience</span>
		</div>
		<div class="about-foundation__copy" data-reveal>
			<h2 id="about-foundation-title">Experience that guides us.</h2>
			<p>ORIENTÉ is a new brand backed by more than 30 years of industry experience. Trusted partnerships and high standards guide every decision we make.</p>
		</div>
	</section>

	<section class="about-selection" aria-labelledby="about-selection-title">
		<figure class="about-selection__media" data-reveal>
			<div class="about-selection__image-wrap">
				<img
					src="<?php echo esc_url( $asset_uri . 'about-craft-v1.webp' ); ?>"
					alt="Careful inspection of a ceramic vessel."
					width="1122"
					height="1402"
					loading="lazy"
				>
			</div>
			<figcaption>Every detail matters.</figcaption>
		</figure>

		<div class="about-selection__copy" data-reveal>
			<h2 id="about-selection-title">Chosen with care.</h2>
			<p>We choose every product for its design, function and lasting value.</p>
			<ul class="about-selection__principles" aria-label="Selection principles">
				<li><span>01</span> Aesthetics</li>
				<li><span>02</span> Functionality</li>
				<li><span>03</span> Lasting value</li>
			</ul>
		</div>
	</section>

	<section class="about-belief" aria-labelledby="about-belief-title">
		<div class="about-belief__inner">
			<div class="about-belief__statement" data-reveal>
				<h2 id="about-belief-title">Spaces that inspire.</h2>
			</div>
			<p data-reveal>The spaces around us shape how we live. We select products that bring comfort, function and personal style to the home.</p>
		</div>
	</section>

	<section class="about-values" aria-labelledby="about-values-title">
		<header class="about-values__header" data-reveal>
			<div>
				<h2 id="about-values-title">Values that endure.</h2>
			</div>
			<p>We work with trust, integrity, quality and respect for every customer. Our goal is to build a reliable brand with high standards.</p>
		</header>

		<ol class="about-values__list">
			<li data-reveal><span>01</span><strong>Trust</strong></li>
			<li data-reveal><span>02</span><strong>Integrity</strong></li>
			<li data-reveal><span>03</span><strong>Quality</strong></li>
			<li data-reveal><span>04</span><strong>Respect for our customers</strong></li>
		</ol>
	</section>

	<section class="about-closing" aria-labelledby="about-closing-title">
		<img class="about-closing__mark" src="<?php echo esc_url( $asset_uri . 'oriente-favicon.svg' ); ?>" alt="" width="160" height="160" loading="lazy">
		<div class="about-closing__copy" data-reveal>
			<h2 id="about-closing-title">Where stories begin.</h2>
			<p>A home is where everyday life and the best stories begin.</p>
			<div class="about-signature" aria-label="ORIENTÉ — Inspired Living.">
				<strong>ORIENTÉ</strong>
				<span>Inspired Living.</span>
			</div>
			<a class="ori-button about-closing__action" href="<?php echo esc_url( $shop_url ); ?>">Explore the collections</a>
		</div>
	</section>
</main>

<?php get_footer(); ?>
