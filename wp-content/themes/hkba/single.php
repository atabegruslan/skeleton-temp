<?php get_header(); ?>

<h2> <?php the_title(); ?> </h2>
<div> 
	<?php 
		$query   = get_post(get_the_ID()); 
		$content = apply_filters('the_content', $query->post_content);

		echo $content; 
	?> 
</div>

<?php get_footer(); ?>