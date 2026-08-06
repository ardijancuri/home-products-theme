<?php get_header(); ?>
<main id="primary" class="site-main">
	<div class="content-shell">
		<header class="page-header">
			<span class="section-kicker"><?php esc_html_e( 'Search results', 'oriente' ); ?></span>
			<h1 class="page-title"><?php echo esc_html( get_search_query() ); ?></h1>
		</header>
		<div class="archive-posts">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'archive-post' ); ?>><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
			<?php endwhile; else : ?>
				<p><?php esc_html_e( 'No objects matched your search. Try a material, room or product name.', 'oriente' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
