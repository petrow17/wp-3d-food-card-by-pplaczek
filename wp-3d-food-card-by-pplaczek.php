<?php
define( 'WP_DEBUG', true );
/*
	Plugin Name: Wp 3D Food Card by pplaczek
	Plugin URI: https://github.com/petrow17/wp-3d-food-card-by-pplaczek
	Description: Plugin creates 3D food card inspirated by https://tympanus.net/codrops/2012/09/25/3d-restaurant-menu-concept/
	Version: 1.0
	Author: Piotr Płaczek
	Author URI: http://www.petrow17.private.pl/piotr/
	License: MIT
*/

define( 'PP_3DFC_PATH', plugin_dir_path( __FILE__ ) );

register_activation_hook(__FILE__, 'pp_3dfc_install');
register_deactivation_hook(__FILE__, 'pp_3dfc_uninstall');
add_action('admin_menu','pp_3dfc_add_ap_pages');
add_action('init', 'pp_3dfc_init');
add_shortcode('pp_3dfc', 'pp_3dfc_show');

/*
 * Initialize function
 * http://webhelp.pl/artykuly/wordpress-jak-poprawnie-dodawac-skrypty-javascript-i-arkusze-stylow-css/
*/
function pp_3dfc_init(){
	wp_register_script('pp3dfc_script_menu', plugins_url('/js/menu.js', __FILE__)/*, array('jquery')*/);
    wp_enqueue_script('pp3dfc_script_menu');
	wp_register_style('pp3dfc_style_normalize', plugins_url('/css/normalize.css', __FILE__));
	wp_enqueue_style('pp3dfc_style_normalize');
	wp_register_style('pp3dfc_style_demo', plugins_url('/css/demo.css', __FILE__),array('pp3dfc_style_normalize'));
	wp_enqueue_style('pp3dfc_style_demo');
	wp_register_style('pp3dfc_style_menu', plugins_url('/css/style.css', __FILE__),array('pp3dfc_style_demo'));
    wp_enqueue_style('pp3dfc_style_menu');
    wp_register_style('pp3dfc_style_menu_custom', plugins_url('/css/custom.css', __FILE__),array('pp3dfc_style_menu'));
	wp_enqueue_style('pp3dfc_style_menu_custom');
}

/*
 * Instalation function
*/
function pp_3dfc_install(){
    global $wpdb;
    $pp_3dfc_db_version = "1.1";
    $pp_3dfc_cover_db_version = "1.0";
    
    if ($wpdb->get_var("SHOW TABLES LIKE '" . pp_3dfc_table_name() . "'") != pp_3dfc_table_name()) {
        $query = "CREATE TABLE " . pp_3dfc_table_name() . " ( 
        id int(9) NOT NULL AUTO_INCREMENT, 
        no int(9) NOT NULL,
        type varchar(250) NOT NULL,  
        title varchar(250) NOT NULL,  
        price varchar(250) NOT NULL, 
        PRIMARY KEY  (id)
        )";

        $wpdb->query($query);

        add_option("pp_3dfc_db_version", $pp_3dfc_db_version);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '" . pp_3dfc_cover_table_name() . "'") != pp_3dfc_cover_table_name()) {
        $query = "CREATE TABLE " . pp_3dfc_cover_table_name() . " ( 
        id int(9) NOT NULL AUTO_INCREMENT,
        param varchar(250) NOT NULL,  
        value varchar(250) NOT NULL,
        PRIMARY KEY  (id)
        )";

        $wpdb->query($query);

        addCoverData(array('param'=>'Title', 'value'=>'title'));
        addCoverData(array('param'=>'Catchword', 'value'=>'catchword'));
        addCoverData(array('param'=>'Addres line 1', 'value'=>'address'));
        addCoverData(array('param'=>'Addres line 2', 'value'=>'address'));
        addCoverData(array('param'=>'Phone line 1', 'value'=>'phone'));
        addCoverData(array('param'=>'Phone line 2', 'value'=>'phone'));
        

        add_option("pp_3dfc_cover_db_version", $pp_3dfc_cover_db_version);
    }
}


/*
 * Uninstalation function
*/
function pp_3dfc_uninstall(){
    global $wpdb;
    $query ='DROP TABLE '.pp_3dfc_table_name();
    $wpdb->query($query);
    $query ='DROP TABLE '.pp_3dfc_cover_table_name();
    $wpdb->query($query);
}

/*
 * Returns database table name
*/
function pp_3dfc_table_name(){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $pp_3dfc_tablename = $prefix . "pp_3dfc";
    return $pp_3dfc_tablename;
}

/*
 * Returns database additional table name (for cover)
*/
function pp_3dfc_cover_table_name(){
    global $wpdb;
    $prefix = $wpdb->prefix;
    $pp_3dfc_tablename = $prefix . "pp_3dfc_cover";
    return $pp_3dfc_tablename;
}

/*
 * Adds pages to AP
*/
function pp_3dfc_add_ap_pages(){
    add_menu_page('3D Food Card','3D Food Card','administrator','food-card-ap-menu-main','pp_3dfc_display_main_ap_page','dashicons-carrot',26);
    add_submenu_page('food-card-ap-menu-main','3D Food Card',__('Cover'),'administrator','food-card-ap-menu-cover','pp_3dfc_display_cover_ap_page');
    add_submenu_page('food-card-ap-menu-main','3D Food Card',__('Items'),'administrator','food-card-ap-menu-items','pp_3dfc_display_items_ap_page');
}

/*
 * Shows the plugin admin page - main (info)
*/
function pp_3dfc_display_main_ap_page(){
    echo '<h1>3D Food Card</h1>';
    echo '<h2>Wordpress plugin by pplaczek</h2>';
    echo '<hr>';
    echo 'This plugin adds <pre><b>[pp_3dfc]</b></pre> shortcode to yours wordpress engine.<br>';
    echo 'This shortcode shows the conceptual, flyer like, 3D restaurant menu.<br><br>';
    echo 'This project was inspired by: 
    <a href="https://tympanus.net/codrops/2012/09/25/3d-restaurant-menu-concept/" target="_blank">CODROPS</a> 
    <a href="https://github.com/codrops/3DRestaurantMenu" target="_blank">GIT</a>
    <br>';
    echo 'The git of this project you can find <a href="https://github.com/petrow17/wp-3d-food-card-by-pplaczek" target="_blank">here</a>.';

}

/*
 * Shows the plugin admin page - cover
*/
function pp_3dfc_display_cover_ap_page(){
    if(isset($_POST['pp_3dfc_cover'])){
        foreach($_POST['pp_3dfc_cover'] as $item){
            updateCoverData(array('param'=>$item['param'], 'value'=>$item['value']));
        }
    }
    $allItems = getAllCoverData();
    echo '<h1>3D Food Card by pplaczek</h1>';
    echo '<h2>Cover setup</h2>';
    echo '<form action="?page=food-card-ap-menu-cover" method="post">';
    echo '<table class="pp_3dfc_ap_cover_table">';
    $i = 0;
    foreach($allItems as $item){
        echo '<tr>';
        echo '<td><p>' . $item['param'] . '</p>
        <input type="hidden" name="pp_3dfc_cover['.$i.'][id]" value="'.$item['id'].'" />
        <input type="hidden" name="pp_3dfc_cover['.$i.'][param]" value="'.$item['param'].'" />
        </td>';
        echo '<td><input name="pp_3dfc_cover['.$i.'][value]" type="text" value="' . $item['value'] . '" /></td>';
        echo '</tr>';
        
        $i++;
    }
    echo '</table>';
    echo '<input type="submit" value="' . __('Save') . '" />';
    echo '</form>';
}

/*
 * Shows the plugin admin page - items
*/
function pp_3dfc_display_items_ap_page(){
    if(isset($_POST['pp_3dfc'])){
        deleteAllItems();
        foreach($_POST['pp_3dfc'] as $item){
            addItem(array('no'=>$item['no'],'type'=>$item['type'], 'title'=>$item['title'],'price'=>$item['price'])); //$item['currency']
        }
    }

    $allItems = getAllItems("no");
    
    echo '<h1>3D Food Card by pplaczek</h1>';
    echo '<form action="?page=food-card-ap-menu-items" method="post">';
    echo '<table class="pp_3dfc_ap_table">
    <thead>
    <tr>
    <td>'.__('Order').'</td>
    <td>'.__('Type').'</td>
    <td>'.__('Title').'</td>
    <td>'.__('Price').'</td>
    <td>'.__('Delete').'</td>
    </tr>
    </thead>';
    echo '<tbody class="items">';
    
    $i=0;
    foreach($allItems as $item){
        echo '<tr>';
        echo '<td><input name="pp_3dfc['.$i.'][no]" type="text" value="' . $item['no'] . '" /></td>';
        echo '<td><input name="pp_3dfc['.$i.'][type]" type="text" value="' . $item['type'] . '" /></td>';
        echo '<td><input name="pp_3dfc['.$i.'][title]" type="text" value="' . $item['title'] . '" /></td>';
        echo '<td><input name="pp_3dfc['.$i.'][price]" type="text" value="' . $item['price'] . '" /></td>';
        echo '<td><a class="delete" href="">' . __('Delete') . '</a></td>';
        echo '</tr>';
        
        $i++;
    }
    echo '</tbody>';
    echo '<tr><td colspan="4"><a class="add" href="">' . __('Add') . '</a></td></tr>';
    echo '<tr><td colspan="4"><input type="submit" value="' . __('Save') . '" /></td></tr>';
    echo '</table>';
    echo '</form>';
    
    echo '
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $("table .delete").click(function() {
                    $(this).parent().parent().remove();
                    return false;
                });
                $("table .add").click(function() {
                    var count = $("tbody.items tr").length+1;
                    var code=\'<tr><td><input type="text" name="pp_3dfc[\'+count+\'][no]" /></td><td><input type="text" name="pp_3dfc[\'+count+\'][type]" /></td><td><input type="text" name="pp_3dfc[\'+count+\'][title]" /></td><td><input type="text" name="pp_3dfc[\'+count+\'][price]" /></td><td><a class="delete" href="">' . __('Delete') . '</a></td></tr>\';
                    $("tbody.items").append(code);
                    return false;
                });
            });
        </script>
        ';
        
}

/*
 * Shows food card
*/
function pp_3dfc_show($atts){
	echo '<div id="rm-container" class="rm-container">';
	echo '<div class="rm-wrapper">';
	echo '<div class="rm-cover">';
	pp_3dfc_show_cover_front();
	pp_3dfc_show_cover_back();
	echo '</div>';
	pp_3dfc_show_middle_page();
	pp_3dfc_show_right_page();
	echo '</div><div>';
    pp_3dfc_menu_init_script();
}

/*
 * Shows food card (Cover front)
*/
function pp_3dfc_show_cover_front(){
    $items = getAllCoverData();
	echo '<div class="rm-front">';
	echo '<div class="rm-content">';
	echo '<div class="rm-logo"></div>';
	echo '<h2>'.$items[0]['value'].'</h2>';
	echo '<h3>'.$items[1]['value'].'</h3>';
	echo '<a href="#" class="rm-button-open">'.__('Otwórz').'</a>';
	echo '<div class="rm-info"><p>';
	echo '<strong>'.$items[0]['value'].'</strong>';
	echo '<br>';
	echo $items[2]['value'];
    echo '<br>';
    echo $items[3]['value'];
	echo '<br>';
	echo '<strong>'.__('Tel.').'</strong> '.$items[4]['value'];
	echo '<br>';
    echo '<strong>'.__('Tel.').'</strong> '.$items[5]['value'];
	echo '<br>';
	echo '</p></div></div></div>';
}

/*
 * Shows food card (Cover back)
*/
function pp_3dfc_show_cover_back(){
	echo '<div class="rm-back">';
	echo '<div class="rm-content">';
	echo pp_3dfc_get_page_content(0);
	echo '</div>';
	echo '<div class="rm-overlay"></div>';
	echo '</div>';
}

/*
 * Shows food card (Middle page)
*/
function pp_3dfc_show_middle_page(){
	echo '<div class="rm-middle">';
	echo '<div class="rm-inner">';
	echo '<div class="rm-content">';
	echo pp_3dfc_get_page_content(1);
	echo '</div>';
	echo '<div class="rm-overlay"></div>';
	echo '</div></div>';
}

/*
 * Shows food card  (Right page)
*/
function pp_3dfc_show_right_page(){
	echo '<div class="rm-right">';
	echo '<div class="rm-front"></div>';
	echo '<div class="rm-back">';
	echo '<span class="rm-close">'.__('Close').'</span>';
	echo '<div class="rm-content">';
	echo pp_3dfc_get_page_content(2);
	echo '</div></div></div>';
}

/*
 * Menu initialize
*/
function pp_3dfc_menu_init_script(){
    echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
    echo '<script type="text/javascript">$(function() {Menu.init();});</script>';
}

/*
 * Returns pageNo content
 * 0- cover back
 * 1- middle page
 * 2- right page (back)
*/
function pp_3dfc_get_page_content($pageNo){
	if($pageNo<0 || 2<$pageNo) return null;
	$items = getAllItems("no");
	$itemsCount = count($items);
	$itemsPerPage = $itemsCount/3;
	$currentItemType = 'none';
	$result = '';
	
	if($pageNo > 0){
		for($i = 0; $i < ($pageNo*$itemsPerPage); $i++){
			$item = $items[$i];
			if($item['type'] != $currentItemType){
				$currentItemType = $item['type'];
			}
		}
	}
	
	for($i = ($pageNo*$itemsPerPage); $i < ($pageNo*$itemsPerPage)+$itemsPerPage; $i++){
		$item = $items[$i];
		
		if($item['type'] != $currentItemType){
			if($currentItemType != 'none'){
				$result .= '</dl>';
			}
			$result .= '<h4>'.$item['type'].'</h4><dl>';
			$currentItemType = $item['type'];
        }
        else{
            $result .= '<dl>';
        }
		
		$result .= '<dt><div class="name">'.$item['no'].' <b>'.$item['title'].'</b></div>';

        $result .= '<div class="stretch">&nbsp;</div>';
        
        $result .= '<div class="price">'.$item['price'].' PLN</div></dt>';
	}
	
	return $result;
}

function getAllItems($orderBy){
    global $wpdb;
    $query = "SELECT * FROM  " . pp_3dfc_table_name() . " ORDER BY ".$orderBy." ASC;";
    return $wpdb->get_results($query, ARRAY_A);
}
    
function addItem($data) {
    global $wpdb;
    $res = $wpdb->insert(pp_3dfc_table_name(), $data, array('%s','%s','%s','%s')); 
//    $wpdb->show_errors();
//    echo $wpdb->last_query;
}
    
function deleteAllItems() {
    global $wpdb;
    $sql = "TRUNCATE TABLE " . pp_3dfc_table_name();
    $wpdb->query($sql);
}

function getAllCoverData(){
    global $wpdb;
    $query = "SELECT * FROM  " . pp_3dfc_cover_table_name() . " ORDER BY id ASC;";
    return $wpdb->get_results($query, ARRAY_A);
}
    
function addCoverData($data) {
    global $wpdb;
    $res = $wpdb->insert(pp_3dfc_cover_table_name(), $data); 
}
    
function deleteAllCoverData() {
    global $wpdb;
    $sql = "TRUNCATE TABLE " . pp_3dfc_cover_table_name();
    $wpdb->query($sql);
}

function updateCoverData($data){
    global $wpdb;
    $sql = "UPDATE " . pp_3dfc_cover_table_name() . ' SET value="'.$data['value'].'" WHERE param="'.$data['param'].'";';
    $wpdb->query($sql);
    // $wpdb->show_errors();
    // echo $wpdb->last_query.'<br>';
}
?>
