<?php 
include './wp-load.php';
global $wpdb;

$q = " SELECT * FROM  `links` " ;
        $target_row = $wpdb->get_results($q);
        foreach($target_row as $row){
                echo $row->target." | ".$row->clicks."<br/>";
        }