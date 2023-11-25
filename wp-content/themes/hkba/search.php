<?php get_header(); ?>

<?php if ( have_posts() ) : ?>
	<h1>Results for: <?php echo get_search_query(); ?></h1>

	<?php while (have_posts()) : the_post(); ?>

	<?php endwhile; ?>

<?php else : ?>
	<h1>Nothing found</h1>
<?php endif; ?>

<?php get_footer(); ?>