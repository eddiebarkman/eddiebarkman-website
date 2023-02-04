<?php
/**
 * Template Name: Custom Home
 */

get_header(); ?>

<?php do_action( 'vw_photography_before_slider' ); ?>

<?php if( get_theme_mod( 'vw_photography_slider_arrows') != '') { ?>

<section id="slider">
  <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel"> 
    <?php $pages = array();
      for ( $count = 1; $count <= 3; $count++ ) {
        $mod = intval( get_theme_mod( 'vw_photography_slider_page' . $count ));
        if ( 'page-none-selected' != $mod ) {
          $pages[] = $mod;
        }
      }
      if( !empty($pages) ) :
        $args = array(
          'post_type' => 'page',
          'post__in' => $pages,
          'orderby' => 'post__in'
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) :
          $i = 1;
    ?>     
    <div class="carousel-inner" role="listbox">
      <?php  while ( $query->have_posts() ) : $query->the_post(); ?>
        <div <?php if($i == 1){echo 'class="carousel-item active"';} else{ echo 'class="carousel-item"';}?>>
          <img src="<?php the_post_thumbnail_url('full'); ?>"/>
          <div class="carousel-caption">
            <div class="inner_carousel">
              <h2><?php the_title(); ?></h2>
              <p><?php the_excerpt(); ?></p>
              <a class="more-btn" href="<?php the_permalink(); ?>"><?php esc_html_e('READ MORE','vw-photography'); ?></a>
            </div>
          </div>
        </div>
      <?php $i++; endwhile; 
      wp_reset_postdata();?>
    </div>
    <?php else : ?>
        <div class="no-postfound"></div>
    <?php endif;
    endif;?>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
    </a>
  </div>  
  <div class="clearfix"></div>
</section>

<?php } ?>

<?php do_action( 'vw_photography_after_slider' ); ?>

<section id="photo-category">
  <div class="container">
    <div class="row m-0">
      <div class="col-lg-3 col-md-12">
        <?php $pages = array();
          $mod = absint( get_theme_mod( 'vw_photography_category_page','vw-photography'));
          if ( 'page-none-selected' != $mod ) {
            $pages[] = $mod;
          }
          if( !empty($pages) ) :
            $args = array(
              'post_type' => 'page',
              'post__in' => $pages,
              'orderby' => 'post__in'
            );
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) :
              $count = 0;
              while ( $query->have_posts() ) : $query->the_post(); ?>
                <div class="cat-page-box">
                  <i class="fas fa-camera-retro"></i><h3><?php the_title(); ?></h3>
                  <p><?php $excerpt = get_the_excerpt();echo esc_html( vw_photography_string_limit_words( $excerpt, 30 ) ); ?></p>
                  <a href="<?php the_permalink(); ?>"><?php esc_html_e('View all Categories','vw-photography'); ?></a>
                </div>
              <?php $count++; endwhile; ?>
            <?php else : ?>
              <div class="no-postfound"></div>
            <?php endif;
          endif;
          wp_reset_postdata()
        ?>
      </div>
      <div class="col-lg-9 col-md-12 p-0">
        <div class="row">
          <?php 
            for ( $m = 1; $m <= 3; $m++ ) {
              $args = array( 'name' => get_theme_mod('vw_photography_post_category'.$m));
                $query = new WP_Query( $args );
                if ( $query->have_posts() ) :
                  while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="cat_content  col-lg-4 col-md-4 p-0">
                      <div class="cat-posts">
                        <img src="<?php the_post_thumbnail_url('full'); ?>"/>
                        <div class="cat_body">
                          <h4><?php the_title(); ?></h4>
                          <span class="entry-date"><?php the_date(); ?></span>
                          <p><?php $excerpt = get_the_excerpt();echo esc_html( vw_photography_string_limit_words( $excerpt,25 ) ); ?></p>
                          <a class="theme_button" href="<?php the_permalink(); ?>"><?php esc_html_e('Read More','vw-photography'); ?></a>
                        </div>
                      </div>
                      <h3 class="title-btn"><?php the_title(); ?></h3>
                    </div>
                  <?php endwhile;
                  wp_reset_postdata();?>
                  <?php else : ?>
                    <div class="no-postfound"></div>
                  <?php
              endif; 
          } ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php do_action( 'vw_photography_after_articles' ); ?>

<div id="content-vw">
  <div class="container">
    <?php while ( have_posts() ) : the_post(); ?>
      <?php the_content(); ?>
    <?php endwhile; // end of the loop. ?>
  </div>
</div>

<?php get_footer(); ?>