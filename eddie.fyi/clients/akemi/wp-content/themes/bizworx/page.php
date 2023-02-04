<?php get_header(); ?>

			<?php 
				if ( have_posts() ) : while ( have_posts() ) : the_post();
  	
					get_template_part( 'content', 'front-page' );
  
				endwhile;
			?>
			
			<?php else : ?>

				<?php get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>

<?php get_footer(); ?>