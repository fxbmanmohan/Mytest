<?php 

session_start();
require_once'includes/functions.php';

#echo $object->getDataByKey("project_locations", "location_id", "1", "location_title");die;
/* check username for account */
$checkUser = isset($_REQUEST['checkUser']) ? $_REQUEST['checkUser'] : '';

if(isset($checkUser) && !empty($checkUser)){
	CheckUsernameStatus($checkUser, $_REQUEST['type']);
}

function CheckUsernameStatus($checkUser,$type){
	$obj = new DB_Class();
	$user=mysql_real_escape_string(trim($_REQUEST['checkUser']));
	// check if registered member?
	$tbl=COMPANIES;
	$w="comp_userName='$user'";
	if($type==1){
		$tbl=BUILDERS; 
		$w="manager_username='$user'";
	}if($type==2){
		$tbl=OWNERS;
		$w="user_name='$user'";
	}if($type==3){
		$tbl=RESPONSIBLES;
		$w="resp_user_name='$user'";
	}

	$q="SELECT * FROM $tbl WHERE $w ";
	if($obj->db_num_rows($obj->db_query($q)) > 0){
		$mess='User name already exist!';
	}else{
		$mess='';
	}
	if(!empty($mess)){
		echo '<img src="images/remove.png" style=" float:right; margin-right:-30px; margin-top:-30px;" /><lable htmlfor="username" generated="true" class="error">
	      <div class="error-edit-profile">'.$mess.'</div></lable>';$mess="";
	}else{echo '<img src="images/edit.png" style=" float:right; margin-right:-30px; margin-top:-30px;" />';}
}


/* check email for account */
$checkEmail=isset($_REQUEST['checkEmail'])?$_REQUEST['checkEmail']:'';
if(isset($checkEmail) && !empty($checkEmail)){
	CheckEmailStatus($checkEmail, $_REQUEST['type']);
}
function CheckEmailStatus($checkEmail,$type){
	$obj = new DB_Class();
	$email=mysql_real_escape_string(trim($_REQUEST['checkEmail']));
	// check if registered member?
	$tbl=COMPANIES;
	$w="comp_email='$email'";
	if($type==1){
		$tbl=BUILDERS; 
		$w="manager_email='$email'";
	}if($type==2){
		$tbl=OWNERS;
		$w="email='$email'";
	}if($type==3){
		$tbl=RESPONSIBLES;
		$w="resp_email='$email'";
	}
		
	if($obj->isValidEmail($email)==false){
		$mess='Invalid email format';
	}else{
		$q="SELECT * FROM $tbl WHERE $w ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$mess='Email id already exist!';
		}else{
			$mess='';
		}
	}
	if(!empty($mess)){
		echo '<img src="images/remove.png" style=" float:right; margin-right:-30px; margin-top:-30px;" /><lable htmlfor="email" generated="true" class="error">
	      <div class="error-edit-profile" style="z-index:9999px; position:relative">'.$mess.'</div></lable>';$mess="";
	}else{echo '<img src="images/edit.png" style=" float:right; margin-right:-30px; margin-top:-30px;" />';}
}


/* Inspections Search */
//$_SESSION['ww_owner_id']
$proID=isset($_REQUEST['proID'])?$_REQUEST['proID']:'';
if(isset($proID) && !empty($proID)){
	SearchInpection($proID,trim($_REQUEST['type']));
}else{
	$type = trim($_REQUEST['type']);
	$selectBox='<select name="projName" id="projName" class="select_box" onchange="startAjax(this.value);" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
	switch($type){
  		case "location": $selectBox='<select name="location" id="location" class="select_box" style="width:220px; background-image:url(images/selectSpl.png); "><option value="">Select</option></select>';
		break;

	  	case "subLocation": $selectBox='<select name="subLocation" id="subLocation" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
		break;

		case "inspecrBy": $selectBox='<select name="inspectedBy" id="inspectedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
		break;

		case "issuedTo": $selectBox='<select name="issuedTo" id="issuedTo" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
		break;

		case "priority": $selectBox='<select name="priority" id="priority" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
		break;

		case "locationPM": $selectBox='<select name="location" id="location" class="select_box" style="width:220px; background-image:url(images/selectSpl.png); "><option value="">Select</option></select>';
		break;
	}
	echo $selectBox;
}

function SearchInpection($proID,$type){
	switch($type){
		case "location": $q="select location_id, location_title	from ".PROJECTLOCATION." where project_id='$proID' and location_parent_id = '0' and is_deleted = '0' GROUP BY location_title"; 
		$name='name="location" id ="location" onChange="subLocate(this.value);"'; SelectListWithID($q,$name);
		break;
		
		case "subLocation": $q="select location_id, location_title	from ".PROJECTLOCATION." where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title"; 
		$name='name="subLocation" id ="subLocation"'; SelectListWithID($q, $name);
		break;
		
		case "inspecrBy": $q="select inspection_inspected_by from ".DEFECTS." where project_id='$proID' and is_deleted = '0' GROUP BY inspection_inspected_by"; 
		$name='name="inspectedBy" id="inspectedBy"'; SelectListWithoutID($q,$name);
		break;		
		
		case "issuedTo":  $q="select issued_to_name from issued_to_for_inspections where project_id='$proID' and is_deleted = '0' GROUP BY issued_to_name";
		$name='name="issuedTo" id="issuedTo"'; SelectListWithoutID($q,$name);
		break;
		
		case "issuedToPM": $q="select issued_to_name from issued_to_for_inspections where project_id='$proID' and is_deleted = '0' GROUP BY issued_to_name";
		$name='name="issuedToPM" id="issuedToPM"'; SelectListWithoutID($q,$name);
		break;

		case "priority": $q="select inspection_priority from ".DEFECTS." where project_id='$proID' and is_deleted = '0' GROUP BY inspection_priority";
		$name='name="priority" id="priority"'; SelectListWithoutID($q,$name);
		break;
		
		case "locationPM": $q="select location_id, location_title	from ".PROJECTLOCATION." where project_id='$proID' and location_parent_id = '0' and is_deleted = '0' GROUP BY location_title"; 
		$name='name="locationPM" id ="locationPM" onChange="resetIds();subLocate1(this.value);"'; SelectListWithIDSP($q,$name);
		break;
		
		case "subLocationPM": $q="select location_id, location_title	from ".PROJECTLOCATION." where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title"; 
		$name='name="subLocationPM" id ="subLocationPM"'; SelectListWithID($q, $name);
		break;
	}
}

function SelectListWithoutID($q,$name){
	$obj = new DB_Class();
	
	$r=$obj->db_query($q);
	$data = '<select '.$name.' class="select_box"';
	
	if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0 || isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// inspector
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}
	else if(isset($_SESSION['ww_is_company'])){	// inspector
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
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
}

function SelectListWithIDSP($q,$name){
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
}

?>
