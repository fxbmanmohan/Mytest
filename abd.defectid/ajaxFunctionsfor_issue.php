<?php 
#error_reporting(E_ALL);
#ini_set('display_errors', '1');

session_start();
require_once'includes/functions.php';
$proID = isset($_REQUEST['proID']) ? $_REQUEST['proID'] : '';

if(isset($proID) && !empty($proID)){
	SearchInpection($proID, trim($_REQUEST['type']));
}else{
	$type = trim($_REQUEST['type']);
	switch($type){
  		case "location": $selectBox='<select name="location" id="location" class="select_box" style="width:220px; background-image:url(images/selectSpl.png); "><option value="">Select</option></select>';
		break;

	  	case "subLocation": $selectBox='<select name="subLocation" id="subLocation" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
		break;

		case "issuedTo": $selectBox='<select id="issuedTo" class="select_box" style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;" name="issuedTo"><option value="">Select</option></select>';
		break;
	}
	echo $selectBox;
#	SearchInpection_all();
}

function SearchInpection($proID, $type){
	switch($type){
		case "issuedTo":
			$q = "SELECT i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id = '$proID' AND i.project_id = '$proID' AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issue_to_name";
			$name='name="issuedTo" id="issuedTo"'; SelectListWithoutID($q, $name);
		break;
	}
}

function SearchInpection_all(){
	$q="select issued_to_name from issued_to_for_inspections where created_by=".$_SESSION['ww_is_builder']." and is_deleted = '0' GROUP BY issued_to_name";
	$name='name="issuedTo" id="issuedTo"'; SelectListWithoutID($q,$name);
}


function SelectListWithoutID($q,$name){
	$obj = new DB_Class();
	$r=$obj->db_query($q);
	$data = '<select '.$name.' class="select_box"';
	
	if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0 || isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// inspector
		$data .= 'style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"';
	}
	else if(isset($_SESSION['ww_is_company'])){	// inspector
		$data .= 'style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"';
	}
	$data .= '>
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[0].'</option>'; 
	}
	echo $data.='</select>';
}

function SelectListWithID($q,$name){
	$obj = new DB_Class();
	$r=$obj->db_query($q);
	$data='<select '.$name.' class="select_box"';
	if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0 || isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// inspector
		$data .= 'style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"';
	}else if(isset($_SESSION['ww_is_company'])){	// inspector
		$data .= 'style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"';
	}
		$data .= '>
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
	}
	echo $data.='</select>';
}

function SelectListWithIDSP($q, $name){
	$obj = new DB_Class();
	$r=$obj->db_query($q);
	$data='<select '.$name.' class="select_box"';
	if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0 || isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// inspector
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}else if(isset($_SESSION['ww_is_company'])){	// inspector
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}
		$data .= '>
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
	}
	echo $data.='</select>';
}?>
