<?php
/*

* Plugin Name: Page Menu

* Plugin URI: http://www.buffernow.com

* Description: This plugin enables choosing different menus on specific pages ,posts ,custom post and taxonomy

* Version: 5.1.2

* Author: Rohit Kumar

* Author URI: http://buffernow.com/about-me

* License: GPL2+

*/

class pagemenu
{
    private  $pgm_option;
	
    private  $menus;
    private  $menusloc;
	
    function __construct(){
		
       
		
		add_action( 'admin_init', array($this,'admin_init'), 1 );       
		
        add_action('wp', array($this,'init'));	
		
    }
	
	function admin_init(){		
	
			$this->menus = get_terms('nav_menu', array(
									 'hide_empty' => false
									));    
									
			$this->menusloc = get_registered_nav_menus();
		
		
				
		
			add_action('wp_ajax_pgm_listitems', array(
                $this,
                'pgm_listitems'
            ));
			
            add_action('wp_ajax_nopriv_pgm_listitems', array(
                $this,
                'pgm_listitems'
            ));
			
			add_action('add_meta_boxes', array(
				$this,
				'pgm_metabox'
			));
		
			add_action('save_post', array(
				$this,
				'save_pgm_postdata'
			));
			
			$this->taxonomies_metabox();
			$this->pgm_set_nav_menu();
			
		
	}
	
	function set_option(){
		
		$pgm_data = get_post_meta(get_the_ID(), "_pgm_post_meta", 1);		
        $this->pgm_option = isset($pgm_data) ? $pgm_data : "";		
	}
	
	
    function init(){
		
		$this->set_option();
		
        add_filter('wp_nav_menu_args', array(
            $this,
            'pgm_menu_args'
        ), 10);
		
        add_filter('nav_menu_css_class', array(
            $this,
            'pgm_nav_class'
        ), 10, 3);
		
        add_filter('wp_footer', array(
            $this,
            'css_injector'
        ));
	
    }
  
	function pgm_metabox(){
		
        global $shortname;
		
        $screens    = array();
		
        $args       = array(
            'public' => true
        );
		
        $output     = 'names';
        $operator   = 'and';
        $post_types = get_post_types($args, $output, $operator);
		
        foreach ($post_types as $post_type){
            add_meta_box('pgm_sectionid', __('Page Menu', 'pgm_textdomain'), array(
                $this,
                'pgm_meta_box'
            ), $post_type);
        }
		
    }
	
    function pgm_meta_box(){
		
		$this->set_option();
		
        require_once "meta-template.php";		
    }
	
    function save_pgm_postdata($post_id){
		
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;
		
        if (isset($_POST['post_type'])){
			
            if ('page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id))
                    return $post_id;
            } else {
                if (!current_user_can('edit_post', $post_id))
                    return $post_id;
            }
			
            $pgm_data = $_POST['pgm_option'];
			if(isset($pgm_data)){
				update_post_meta($post_id, '_pgm_post_meta', $pgm_data);
			}
        }
		
    }
	
    function pgm_menu_args($args){
		
		//die("out");
        if (is_archive()){
			global $wp_query;
			$t_id = $wp_query->get_queried_object_id();
			$this->pgm_option = get_option( "taxonomy_$t_id" );
		}
		
        //else if (is_single() OR is_page()) {
			
			if (is_array($this->pgm_option)){
				
                extract($this->pgm_option);
                if ($pgm_location == $args['theme_location']){
                    if (isset($pgm_menu) AND $pgm_menu != "")
                        $args['menu'] = $pgm_menu;
                }
            }
        //}
		
        return $args;
    }
	
    function pgm_listitems(){		
        if ($_REQUEST['menuid'] != "") {
            echo wp_nav_menu(array(
                'walker' => new Pgm_Walker(),
                "menu" => $_REQUEST['menuid']
            ));
        }
        exit;
    }

    function pgm_nav_class($classes, $item, $args){
			
		
		if (is_array($this->pgm_option) AND !empty($this->pgm_option['pgm_menulist'])){
			if ($this->pgm_option['pgm_location'] == $args->theme_location) {
			   if (!in_array($item->ID, $this->pgm_option['pgm_menulist']))
					$classes[] = "pagemenu-hide";
			}
		}
		
        return $classes;
    }
	
    function css_injector(){
		
        echo "<style>.pagemenu-hide{display:none !important;}</style>";
    }
	
	function pgm_set_nav_menu(){
	
		$theme_locations = $this->menusloc ;
	
		foreach($theme_locations as $key=>$val){
			if(!has_nav_menu( $key) ){
				
				$name = "Page Menu";
					if(!is_nav_menu( $name))
						$menu_id = wp_create_nav_menu($name);
					else
						$menu_id = get_term_by( 'name', $name, 'nav_menu' )->term_id;
					
				$locations[$key] = $menu_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}
		}
	}

	function taxonomies_metabox(){
		$reg_tax = get_taxonomies();
			
		$exclude = array('nav_menu','link_category','post_format');
		
			foreach($reg_tax as $taxonomy){
				if(!in_array($taxonomy,$exclude)){
					
					add_action( $taxonomy."_add_form_fields", array($this,"taxonomy_meta_box"), 10 );
					add_action( $taxonomy."_edit_form_fields", array($this,'taxonomy_edit_meta_field'),10,2 );
					
					add_action( 'create_'.$taxonomy,array($this, 'save_taxonomy_meta_box'), 10, 2 );
					add_action( 'edited_'.$taxonomy, array($this,'save_taxonomy_meta_box'), 10, 2 );
				}				
			}

	}
	
	function save_taxonomy_meta_box($t_id){
	
		if ( isset( $_POST['pgm_option'] ) ) {	
			
			$term_meta = get_option( "taxonomy_$t_id" );
			$cat_keys = array_keys( $_POST['pgm_option'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['pgm_option'][$key] ) ) {					
					
					$term_meta[$key] = $_POST['pgm_option'][$key];
				}
			}
		
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
		}
	}
	
	function taxonomy_meta_box(){
?>
			<div class="form-field">
				<label for="term_meta[custom_term_meta]"><?php _e( 'Menu Assignment' ); ?></label>
				<?php  require_once "meta-template.php"; ?>
			</div>
<?php
	}
	
	function taxonomy_edit_meta_field($term){
		
		$t_id = $term->term_id;
		
		$this->pgm_option =  get_option( "taxonomy_$t_id" );		
?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[custom_term_meta]"><?php _e( 'Menu Assignment' ); ?></label></th>
			<td>
				<?php  require_once "meta-template.php"; ?>
			</td>
		</tr>
<?php
	}
	
}

class Pgm_Walker extends Walker_Nav_Menu
{

    function start_el(&$output, $item,$depth = 0, $args = array(), $id = 0)
    {
		
		//var_dump($item);die;
        global $wp_query;

        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';



        $class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

        $classes[] = 'menu-item-' . $item->ID;

		 $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

        $class_names = ' class="' . esc_attr( $class_names ) . '"';



        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );

        $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';



        $output .= $indent . '<li' . $id . $value . $class_names .'>';



        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';

        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';

        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';

        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$screen  = get_current_screen();

	
		
		if(is_admin()) {
				if(isset($screen->taxonomy) AND isset($_GET['tag_ID'])){
					$t_id = $_GET['tag_ID'];
					$pgm_postOption = get_option( "taxonomy_$t_id");			
				}
				else if(isset($screen->post_type)) 
				{		
					$pgm_postOption =get_post_meta(get_the_ID(), "_pgm_post_meta", 1 );						
				}
		}else {
			$pgm_postOption =get_post_meta(get_the_ID(), "_pgm_post_meta", 1 );
		}
		

		$checked ="";

		if(empty($pgm_postOption['pgm_menulist'])){		
			$checked ='checked="checked"';
		}

		else if(in_array($item->ID,$pgm_postOption['pgm_menulist'])){
			$checked ='checked="checked"';
		}

		
		$item_output = $args->before;
		
	if(isset($item->title)) //Empty menu
        $item_output .= '<input type="checkbox" '.$checked.' name="pgm_option[pgm_menulist][]" value="'.$item->ID.'">';

		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;


        $item_output .= '';

        $item_output .= $args->after;



        $output .= $item_output;

    }

}

new pagemenu();
?>