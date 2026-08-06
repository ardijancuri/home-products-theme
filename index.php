<?php get_header(); ?>
<main id="primary" class="site-main">
	<div class="content-shell">
		<header class="page-header"><h1 class="page-title"><?php single_post_title( '', true ); ?></h1></header>
		<div class="archive-posts">
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'archive-post' ); ?>>
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	</div>
</main>
<?php get_footer(); ?>
