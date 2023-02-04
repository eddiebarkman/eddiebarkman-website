<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package VW Photography
 */

get_header(); ?>

<div id="content-vw">
	<div class="container">
        <div class="page-content">
        	<h1><?php printf( '<strong>%s</strong> %s', esc_html__( '404','vw-photography' ), esc_html__( 'Not Found', 'vw-photography' ) ) ?></h1>	
			<p class="text-404"><?php esc_html_e( 'Looks like you have taken a wrong turn&hellip', 'vw-photography' ); ?></p>
			<p class="text-404"><?php esc_html_e( 'Dont worry&hellip it happens to the best of us.', 'vw-photography' ); ?></p>
        	<a class="error-btn" href="<?php echo esc_url(home_url()); ?>"><?php esc_html_e( 'Return to the home page', 'vw-photography' ); ?></a>
			<div class="clearfix"></div>
        </div>
	</div>
</div>

<?php get_footer(); ?>