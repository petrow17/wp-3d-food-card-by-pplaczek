<?php
class FoodCard{
    private $wpdb;
    private $table_name;
    
    public function __construct(){
        global $wbdb;
        $prefix = $wpdb->prefix;
        $this->table_name = $prefix.'pp_3dfc';
        $this->wpdb = $wpdb;
    }
    
    public function getAllItems($orderBy){
        $query = "SELECT * FROM  " . $this->$table_name . " ORDER BY ".$orderBy." DESC;";
        return $this->wpdb->get_results($query, ARRAY_A);
    }
    
    public function addItem($data) {
        $this->wpdb->insert($this->$table_name, $data, array('%s', '%s', '%s', '%s', '%s'));
    }
    
    public function deleteAllItems() {
        $sql = "TRUNCATE TABLE " . $this->$table_name;
        $this->wpdb->query($sql);
    }
}
?>
