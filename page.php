<?php get_header(); ?>
<main id="primary" class="site-main">
	<div class="content-shell">
		<?php while ( have_posts() ) : the_post(); ?>
			<header class="page-header"><h1 class="page-title"><?php the_title(); ?></h1></header>
			<article <?php post_class( 'entry-content' ); ?>><?php the_content(); ?></article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
