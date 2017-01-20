<?php session_start();
set_time_limit(3000);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

function get_cats($cat){
	$i=1;
	$remain = array();
	$all = array();
	$insertArray = array();
	$parentArray = array();
	
	$remain[0] = $cat;
	$all[0] = $cat;	
	while(sizeof($remain)>0){
		$curr = $remain[0];
		
		$res = mysql_query("SELECT location_id FROM project_locations WHERE location_parent_id = ".$curr." and is_deleted = 0");
		while($row = mysql_fetch_array($res)){
			$all[$i++]=$row['location_id'];
			$remain[sizeof($remain)]=$row['location_id'];
		}
		unset($remain[0]);
		$remain=array_values($remain);
	}
	return $all;
}
 
$all_categories=array();

$all_categories = get_cats($_GET['location_id']);
if(!empty($all_categories)){
	foreach($all_categories as $deleteId){
		$query = "UPDATE project_locations SET is_deleted = '1' WHERE location_id = '".$deleteId."'";
		$res = mysql_query($query) or die(mysql_error());
		if(mysql_affected_rows()>0){
			echo 'Location Deleted Successfully !';
		}else{
			echo 'Location Not Deleted !';
		}
	}
}



//$q="UPDATE project_locations SET is_deleted = '1' WHERE location_id = '".$_GET['location_id']."'";
//$res = mysql_query($q);
//echo 'Location Deleted Successfully !';


?>