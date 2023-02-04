<?php 
if (is_array($this->pgm_option))
            extract($this->pgm_option);
		
$menus  = $this->menus;
$menusloc  = $this->menusloc;
?>

<p>
	<strong></strong>
</p>

<div class="misc-pub-section">
	<select id="pgm_location" name="pgm_option[pgm_location]">
		<option value="">Select location</option>
	
<?php

	foreach ( $menusloc as $location => $description ){
		
		$selected ="";
		
		if(is_array($this->pgm_option) && isset($pgm_location) && $pgm_location  == $location)				
		$selected	= 'selected="selected"' ;				
					
		echo "<option ".$selected." value='".$location."'>".
			$description."</option>";

	}
?>
	</select>		
</div>
	
<div class="misc-pub-section">
	<select id="pmenu_list" name="pgm_option[pgm_menu]">
		<option value="">Select Menu</option>
		
<?php		
	foreach($menus as $menu){				
		$selected ="";
		
		if(is_array($this->pgm_option) && isset($pgm_menu ) && $pgm_menu  == $menu->term_id)				
		$selected = "selected='selected'";
	
		echo "<option ".$selected." value='".$menu->term_id."'>".$menu->name."</option>";
	
	}
?>
	</select>
</div>
	
	
<div id="plist_menu" class="misc-pub-section">
	<?php
		if(is_array($this->pgm_option) AND $pgm_menu!=""){
			echo wp_nav_menu(array('walker' => new Pgm_Walker(),"menu"=>$pgm_menu));
		}
	?>
</div>


<style>
#plist_menu .menu li{  
	margin: 10px 8px;   
	position: relative;
}	 
#plist_menu .menu ul{
	padding-left:10px !important;
}
</style>
<script>
	jQuery(document).ready(function() {

    pgm_location = jQuery("#pgm_location");
    pmenu_list = jQuery("#pmenu_list");

    pmenu_list.change(function(evt) {
        jQuery.post(ajaxurl, {
            action: 'pgm_listitems',
            menuid: this.options[evt.target.selectedIndex].value,
            nonce: jQuery.trim(jQuery('#pgm-nonce').html())

        }, function(response) {
            // TODO response handler
            jQuery("#plist_menu").html(response);

        });
    });

    jQuery('.menu-item-has-children input[type=checkbox]').live('click', function() {
        if (this.checked) { // if checked - check all parent checkboxes
            jQuery(this).parents('li').children('input[type=checkbox]').prop('checked', true);
        }

        // children checkboxes depend on current checkbox
        jQuery(this).parent().find('input[type=checkbox]').prop('checked', this.checked);
    });

    if (pgm_location.val() == "") {
        pmenu_list.val("").trigger("change").prop("disabled", true);

    } else {
        pmenu_list.prop("disabled", false);
    }

    pgm_location.change(function() {

        if (jQuery(this).val() == "") {
            pmenu_list.val("").trigger("change").prop("disabled", true);

        } else {
            pmenu_list.prop("disabled", false);
        }
    });

});
</script>