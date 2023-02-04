<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Bizworx
 */

get_header();

$layout = bizworx_blog_layout();

?>

	<div id="primary" class="content-area col-md-8 <?php echo esc_attr($layout); ?>">
		<main id="main" class="post-wrap" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h3 class="archive-title">', '</h3>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<div class="posts-layout">
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
						get_template_part( 'content', get_post_format() );
				?>

			<?php endwhile; ?>
			</div>
			
			<?php
				the_posts_pagination( array(
					'mid_size'  => 1,
				) );
			?>	

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php 
	if ( $layout == 'classic' ) :
	get_sidebar();
	endif;
?>
<?php get_footer(); ?>
