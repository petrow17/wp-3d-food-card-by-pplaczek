<?php
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
require PP_3DFC_PATH.'model/food-card.php';

register_activation_hook(__FILE__, 'pp_3dfc_install');
register_deactivation_hook(__FILE__, 'pp_3dfc_uninstall');
add_action('admin_menu','pp_3dfc_add_ap_pages');



/*
 * Instalation function
*/
function pp_3dfc_install(){
    global $wpdb;
    $pp_3dfc_db_version = "1.1";
    
    if ($wpdb->get_var("SHOW TABLES LIKE '" . pp_3dfc_table_name() . "'") != pp_3dfc_table_name()) {
        $query = "CREATE TABLE " . pp_3dfc_table_name() . " ( 
        id int(9) NOT NULL AUTO_INCREMENT, 
        type varchar(250) NOT NULL,  
        title varchar(250) NOT NULL,  
        description varchar(250) NOT NULL,  
        amount varchar(250) NOT NULL, 
        price varchar(250) NOT NULL, 
        currency varchar(250) NOT NULL,
        PRIMARY KEY  (id)
        )";

        $wpdb->query($query);

        add_option("pp_3dfc_db_version", $pp_3dfc_db_version);
    }
}


/*
 * Uninstalation function
*/
function pp_3dfc_uninstall(){
    global $wpdb;    
    $query ='DROP TABLE '.pp_3dfc_table_name();
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
 * Adds pages to AP
*/
function pp_3dfc_add_ap_pages(){
    add_menu_page('3D Food Card','3D Food Card','administrator','food-card-ap-menu','pp_3dfc_display_main_ap_page','dashicons-carrot',26);
}

/*
 * Shows the plugin main admin page
*/
function pp_3dfc_display_main_ap_page(){
    if(isset($_POST['pp_3dfc'])){
        deleteAllItems();
        foreach($_POST['pp_3dfc'] as $item){
            addItem(array('type'=>$item['type'], 'title'=>$item['title'],'description'=>$item['description'],'amount'=>$item['amount'],'price'=>$item['price'],'currency'=>'PLN')); //$item['currency']
        }
    }
    $allItems = getAllItems("type");
    
    echo '<h1>3D Food Card by pplaczek</h1>';
    echo '<form action="?page=food-card-ap-menu" method="post">';
    echo '<table class="pp_3dfc_ap_table">
    <thead>
    <tr>
    <td>'.__('Type').'</td>
    <td>'.__('Title').'</td>
    <td>'.__('Description').'</td>
    <td>'.__('Amount').'</td>
    <td>'.__('Price').'</td>
    <td>'.__('Currency').'</td>
    <td>'.__('Delete').'</td>
    </tr>
    </thead>';
    echo '<tbody class="items">';
    
    $i=0;
    foreach($allItems as $item){
        echo '<tr>';
        echo '<td><input name="pp_3dfc['.$i.'][type]" type="text" value="' . $item['type'] . '" /></td>';
        echo '<td><input name="pp_3dfc['.$i.'][title]" type="text" value="' . $item['title'] . '" /></td>';
        echo '<td><input name="pp_3dfc['.$i.'][description]" type="text" value="' . $item['description'] . '" /></td>';
        echo '<td><input name="pp_3dfc['.$i.'][amount]" type="text" value="' . $item['amount'] . '" /></td>';
        echo '<td><input name="pp_3dfc['.$i.'][price]" type="text" value="' . $item['price'] . '" /></td>';
        echo '<td><input name="pp_3dfc['.$i.'][currency]" type="text" value="' . $item['currency'] . '" disabled="disabled" /></td>';
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
                    var code=\'<tr><td><input type="text" name="pp_3dfc[\'+count+\'][type]" /></td><td><input type="text" name="pp_3dfc[\'+count+\'][title]" /></td><td><input type="text" name="pp_3dfc[\'+count+\'][description]" /></td><td><input type="text" name="pp_3dfc[\'+count+\'][amount]" /></td><td><input type="text" name="pp_3dfc[\'+count+\'][price]" /></td><td><input type="text" name="pp_3dfc[\'+count+\'][currency]" value="PLN" disabled="disabled" /></td><td><a class="delete" href="">' . __('Delete') . '</a></td></tr>\';
                    $("tbody.items").append(code);
                    return false;
                });
            });
        </script>
        ';
        
}



function getAllItems($orderBy){
    global $wpdb;
    $query = "SELECT * FROM  " . pp_3dfc_table_name() . " ORDER BY ".$orderBy." ASC;";
    return $wpdb->get_results($query, ARRAY_A);
}
    
function addItem($data) {
    global $wpdb;
    $res = $wpdb->insert(pp_3dfc_table_name(), $data, array('%s','%s','%s','%s','%s','%s')); 
//    $wpdb->show_errors();
//    echo $wpdb->last_query;
}
    
function deleteAllItems() {
    global $wpdb;
    $sql = "TRUNCATE TABLE " . pp_3dfc_table_name();
    $wpdb->query($sql);
}
?>
