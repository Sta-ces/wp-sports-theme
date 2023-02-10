<?php
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

class NPTSDB{

    /**
     * @param $tablename
     */
    public static function install($tablename){
        global $wpdb;
        $tablen = $wpdb->prefix . $tablename;
        $r = dbDelta( "CREATE TABLE IF NOT EXISTS {$tablen} (
            id bigint(50) NOT NULL AUTO_INCREMENT,
            statut_name varchar(100) NOT NULL,
            statut_order smallint(50) DEFAULT 0,
            PRIMARY KEY (id)
        ) {$wpdb->get_charset_collate()};" );
    }

    /**
     * @param $tablename
     * @param $statut_name = ""
     * @param $statut_id = ""
     * @param $select = "*"
     * @param $order = "DESC"
     */
    public static function get($tablename, $statut_name = "", $statut_id = "", $select = "*", $order = "DESC"){
        global $wpdb;
        NPTSDB::install($tablename);
        $tablen = $wpdb->prefix . $tablename;

        $sql = "SELECT {$select} FROM {$tablen}";
        if(!empty($statut_id) || !empty($statut_name)){
            $sql .= " WHERE";
            if(!empty($statut_id)) $sql .= " id = {$statut_id}";
            if(!empty($statut_id) && !empty($statut_name)) $sql .= " AND";
            if(!empty($statut_name)) $sql .= " statut_name = '{$statut_name}'";
        }
        $sql .= " ORDER BY statut_order {$order}";
        
        return $wpdb->get_results( $sql );
    }

    /**
     * @param $tablename
     * @param $statut_name
     * @param $statut_order = 0
     */
    public static function insert($tablename, $statut_name, $statut_order = 0){
        global $wpdb;
        NPTSDB::install($tablename);
        $tablen = $wpdb->prefix . $tablename;
        if(count(NPTSDB::get($tablename, $statut_name)) <= 0)
            $wpdb->insert($tablen, ['statut_name' => $statut_name, 'statut_order' => $statut_order], ["%s", "%d"]);
        return NPTSDB::get($tablename, $statut_name);
    }

    /**
     * @param $tablename
     * @param $statut_id
     * @param $statut_name
     * @param $statut_order
     */
    public static function update($tablename, $statut_id, $statut_name, $statut_order){
        global $wpdb;
        NPTSDB::install($tablename);
        $tablen = $wpdb->prefix . $tablename;
        $wpdb->update($tablen, ['statut_name' => $statut_name, 'statut_order' => $statut_order], ['id' => $statut_id]);
        return NPTSDB::get($tablename, "", $statut_id);
    }

    /**
     * @param $tablename
     * @param $statut_id
     */
    public static function delete($tablename, $statut_id){
        global $wpdb;
        $tablen = $wpdb->prefix . $tablename;
        $wpdb->delete($tablen, ['statut_id' => $statut_id]);
    }
}