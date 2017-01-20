<?php

session_start();
require_once'includes/functions.php';

#echo $object->getDataByKey("project_locations", "location_id", "1", "location_title");die;
//Code for remember Start Here
if (isset($_REQUEST['session_type'])) {
    $_SESSION[$_REQUEST['session_type']] = $_REQUEST; //Set Session for back implement and Remeber
    setcookie($_SESSION['ww_builder_id'] . '_' . $_REQUEST['session_type'], serialize($_REQUEST), time() + 864000);
}
//Check Request from report section
$isReqReport = isset($_REQUEST['reqReport']) && $_REQUEST['reqReport'] != '' ? $_REQUEST['reqReport'] : '';
//Code for remember End Here

/* check username for account */
$checkUser = isset($_REQUEST['checkUser']) ? $_REQUEST['checkUser'] : '';

if (isset($checkUser) && !empty($checkUser)) {
    CheckUsernameStatus($checkUser, $_REQUEST['type']);
}

function CheckUsernameStatus($checkUser, $type) {
    $obj = new DB_Class();
    $user = mysql_real_escape_string(trim($_REQUEST['checkUser']));
    // check if registered member?
    $tbl = COMPANIES;
    $w = "comp_userName='$user'";
    if ($type == 1) {
        $tbl = BUILDERS;
        $w = "manager_username='$user'";
    }if ($type == 2) {
        $tbl = OWNERS;
        $w = "user_name='$user'";
    }if ($type == 3) {
        $tbl = RESPONSIBLES;
        $w = "resp_user_name='$user'";
    }

    $q = "SELECT * FROM $tbl WHERE $w ";
    if ($obj->db_num_rows($obj->db_query($q)) > 0) {
        $mess = 'User name already exist!';
    } else {
        $mess = '';
    }
    if (!empty($mess)) {
        echo '<img src="images/remove.png" style=" float:right; margin-right:-30px; margin-top:-30px;" /><lable htmlfor="username" generated="true" class="error">
	      <div class="error-edit-profile">' . $mess . '</div></lable>';
        $mess = "";
    } else {
        echo '<img src="images/edit.png" style=" float:right; margin-right:-30px; margin-top:-30px;" />';
    }
}

/* check email for account */
$checkEmail = isset($_REQUEST['checkEmail']) ? $_REQUEST['checkEmail'] : '';
if (isset($checkEmail) && !empty($checkEmail)) {
    CheckEmailStatus($checkEmail, $_REQUEST['type']);
}

function CheckEmailStatus($checkEmail, $type) {
    $obj = new DB_Class();
    $email = mysql_real_escape_string(trim($_REQUEST['checkEmail']));
    // check if registered member?
    $tbl = COMPANIES;
    $w = "comp_email='$email'";
    if ($type == 1) {
        $tbl = BUILDERS;
        $w = "manager_email='$email'";
    }if ($type == 2) {
        $tbl = OWNERS;
        $w = "email='$email'";
    }if ($type == 3) {
        $tbl = RESPONSIBLES;
        $w = "resp_email='$email'";
    }

    if ($obj->isValidEmail($email) == false) {
        $mess = 'Invalid email format';
    } else {
        $q = "SELECT * FROM $tbl WHERE $w ";
        if ($obj->db_num_rows($obj->db_query($q)) > 0) {
            $mess = 'Email id already exist!';
        } else {
            $mess = '';
        }
    }
    if (!empty($mess)) {
        echo '<img src="images/remove.png" style=" float:right; margin-right:-30px; margin-top:-30px;" /><lable htmlfor="email" generated="true" class="error">
	      <div class="error-edit-profile" style="z-index:9999px; position:relative">' . $mess . '</div></lable>';
        $mess = "";
    } else {
        echo '<img src="images/edit.png" style=" float:right; margin-right:-30px; margin-top:-30px;" />';
    }
}

/* Inspections Search */
//$_SESSION['ww_owner_id']
$proID = isset($_REQUEST['proID']) ? $_REQUEST['proID'] : '';

if ($proID != '') {
    SearchInpection($proID, trim($_REQUEST['type']), $isReqReport);
} else {
    $type = trim($_REQUEST['type']);
    global $isReqReport;
#	$selectBox='<select name="projName" id="projName" class="select_box" onchange="startAjax(this.value);" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
    switch ($type) {
        case "projName" :
            $selectBox = '<select name="projName" id="projName" class="select_box" onchange="startAjax(this.value);" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "location": $selectBox = '<select name="location" id="location" class="select_box" style="width:220px; background-image:url(images/selectSpl.png); "><option value="">Select</option></select>';
            break;

        case "subLocation": $selectBox = '<select name="subLocation" id="subLocation" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "sub_subLocation": $selectBox = '<select name="sub_subLocation" id="sub_subLocation" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "sub_subLocation_qc": $selectBox = '<select name="subSubLocation" id="subSubLocation" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;
        
        case "sub_subLocation2": $selectBox = '<select name="subSubLocation2" id="subSubLocation2" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;
        
        case "sub_subLocation3": $selectBox = '<select name="subSubLocation3" id="subSubLocation3" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "inspecrBy": $selectBox = '<select name="inspectedBy" id="inspectedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "issuedToQC": $q = "select issue_to_name as issued_to_name from inspection_issue_to where project_id='$proID' and is_deleted = '0' AND issue_to_name != '' GROUP BY issue_to_name";
            $name = 'name="issuedTo" id="issuedTo"';
            SelectListWithoutID($q, $name, 'issuedTo', '', $isReqReport);
            break;


        case "issuedTo": $selectBox = '<select name="issuedTo" id="issuedTo" class="select_box" multiple="multiple" size="2" style = "width:220px; height:76px; background-image:url(images/multiple_select_box.png);"><option value="">Select</option></select>';
            break;

        case "issuedToPM": $selectBox = '<select name="issuedToPM" id="issuedToPM" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "subLocationPM": $selectBox = '<select name="subLocationPM" id="subLocationPM" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "subLocation_subPM": $selectBox = '<select name="subLocation_subPM" id="subLocation_subPM" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "locationPM": $selectBox = '<select name="location" id="location" class="select_box" style="width:220px; background-image:url(images/selectSpl.png); "><option value="">Select</option></select>';
            break;

        case "raisedBy": $selectBox = '<select name="raisedBy" id ="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png); "><option value="">Select</option></select>';
            break;


///QA Task Select box Dated 14-12-2012	
        case "locationQA": $selectBox = '<select name="locationQA" id="locationQA" class="select_box" style="width:220px; background-image:url(images/selectSpl.png); "><option value="">Select</option></select>';
            break;

        case "subLocationQA1": $selectBox = '<select name="subLocationQA1" id="subLocationQA1" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "subLocationQA2": $selectBox = '<select name="subLocationQA2" id="subLocationQA2" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "subLocationQA3": $selectBox = '<select name="subLocationQA3" id="subLocationQA3" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;


///Checklist Select box Dated 19-12-2012	
        case "locationCL": $selectBox = '<select name="locationCL" id="locationCL" class="select_box" style="width:220px; background-image:url(images/selectSpl.png); "><option value="">Select</option></select>';
            break;

        case "subLocationCL1": $selectBox = '<select name="subLocationCL" id="subLocationCL" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "subLocationCL2": $selectBox = '<select name="sub_subLocationCL" id="sub_subLocationCL" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;

        case "userComp": $selectBox = '<select name="user" id="user" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;
        case "issueToList": $selectBox = '<select name="issuedToList" id="issuedToList" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);"><option value="">Select</option></select>';
            break;
    }
    echo $selectBox;
}

function SearchInpection($proID, $type, $isReqReport) {
    switch ($type) {
        case "location": $q = "select location_id, location_title	from " . PROJECTLOCATION . " where project_id='$proID' and location_parent_id = '0' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="location" id ="location" onChange="subLocate(this.value);"';
            SelectListWithID($q, $name, 'location', $isReqReport);
            break;

        case "subLocation": $q = "select location_id, location_title	from " . PROJECTLOCATION . " where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subLocation" id ="subLocation" onChange="sub_subLocate(this.value);"';
            SelectListWithID($q, $name, 'subLocation', $isReqReport);
            break;

        //This for reports section
        case "sub_subLocation": $q = "select location_id, location_title	from " . PROJECTLOCATION . " where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="sub_subLocation" id ="sub_subLocation" onChange="sub_subLocate2(this.value);"';
            SelectListWithID($q, $name, 'sub_subLocation', $isReqReport);
            break;

        //This for inspection section
        case "sub_subLocation_qc": $q = "select location_id, location_title from " . PROJECTLOCATION . " where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subSubLocation" id="subSubLocation" onChange="sub_subLocate2(this.value);"';
            SelectListWithID($q, $name, 'subSubLocation', $isReqReport);
            break;
        
        case "sub_subLocation2": $q = "select location_id, location_title from " . PROJECTLOCATION . " where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subSubLocation" id="subSubLocation" onChange="sub_subLocate3(this.value);"';
            SelectListWithID($q, $name, 'subSubLocation', $isReqReport);
            break;
        
        case "sub_subLocation3": $q = "select location_id, location_title from " . PROJECTLOCATION . " where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subSubLocation3" id="subSubLocation3"';
            SelectListWithID($q, $name, 'subSubLocation', $isReqReport);
            break;

        case "inspecrBy": $q = "select inspection_inspected_by from project_inspections where project_id='$proID' and is_deleted = '0' AND inspection_inspected_by != '' GROUP BY inspection_inspected_by";
            $name = 'name="inspectedBy" id="inspectedBy"';
            SelectListWithoutID($q, $name, 'inspecrBy', '', $isReqReport);
            break;

        case "issuedToQC": $q = "select issue_to_name as issued_to_name from inspection_issue_to where project_id='$proID' and is_deleted = '0' AND issue_to_name != '' GROUP BY issue_to_name";
            $extraQuery = "select DISTINCT issued_to_name from `issued_to_for_inspections` where project_id='$proID' and is_deleted = '0' and issued_to_name not in (select issue_to_name from inspection_issue_to where project_id='$proID' and is_deleted=0 AND issue_to_name != '')";
            $name = 'name="issuedTo" id="issuedTo"';
            SelectListWithoutID($q, $name, 'issuedTo', $extraQuery, $isReqReport);
            break;

        case "issuedTo": $q = "select issue_to_name as issued_to_name from inspection_issue_to where project_id='$proID' and is_deleted = '0' AND issue_to_name != '' GROUP BY issue_to_name";
#			$extraQuery="select DISTINCT issued_to_name from `issued_to_for_inspections` where project_id='$proID' and is_deleted = '0' and issued_to_name not in (select issue_to_name from inspection_issue_to where project_id='$proID' and is_deleted=0 AND issue_to_name != '')";
            $extraQuery = "";
            $name = 'issuedTo';
            SelectListWithoutIDMuli($q, $name, 'issuedTo', $extraQuery);
            break;

        case "issuedToPM": $q = "select issued_to_name as issued_to_name from issued_to_for_progress_monitoring where project_id='$proID' and is_deleted = '0' GROUP BY issued_to_name";
            $name = 'name="issuedToPM" id="issuedToPM"';
            SelectListWithoutID($q, $name, 'issuedToPM', '', $isReqReport);
            break;

        case "locationPM": $q = "select location_id, location_title from project_monitoring_locations where project_id='$proID' and location_parent_id = '0' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="locationPM" id ="locationPM" onChange="resetIds();subLocate1(this.value);"';
            SelectListWithIDSP($q, $name, 'locationPM', $isReqReport);
            break;

        case "subLocationPM": $q = "select location_id, location_title from project_monitoring_locations where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subLocationPM" onChange="subLocate_sub(this.value);" id ="subLocationPM"';
            SelectListWithID($q, $name, 'subLocationPM', $isReqReport);
            break;

        case "subLocation_subPM": $q = "select location_id, location_title from project_monitoring_locations where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subLocation_subPM" id ="subLocation_subPM"';
            SelectListWithID($q, $name, 'subLocation_subPM', $isReqReport);
            break;

        case "sessions": $q = "SELECT permission_name, is_allow FROM  user_permission WHERE project_id = '$proID' and is_deleted = 0  AND user_id = '" . $_SESSION['ww_builder']['user_id'] . "' AND permission_name IN('web_edit_inspection', 'web_delete_inspection', 'web_close_inspection', 'web_checklist')";
            setSessions($q);
            break;

        case "listDropVal":
            $_SESSION['qc']['listDropVal'] = $proID;
            break;

        case "session":
            $_SESSION['idp'] = $proID;
            break;

        case "setSession":
            $_SESSION['projIdQA'] = $proID;
            break;

        case "userRole": $q = "SELECT user_role, issued_to FROM  user_projects WHERE project_id = '$proID' and is_deleted = 0  AND user_id = '" . $_SESSION['ww_builder']['user_id'] . "'";
            setSessions4Role($q);
            break;

        case "raisedBy": $q = "SELECT user_role FROM  user_projects WHERE project_id = '$proID' and is_deleted = 0  AND user_id = '" . $_SESSION['ww_builder']['user_id'] . "'";
            SelectListWithoutIDuserRole($q, 'name="raisedBy" id ="raisedBy"', 'raisedBy', $isReqReport);
            break;

///QA Task Select box Dated 14-12-2012	
        case "locationQA": $q = "select location_id, location_title from qa_task_locations where project_id = '$proID' and location_parent_id = '0' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="locationQA" id ="locationQA" onChange="resetIds();subLocate1QA(this.value);"';
            SelectListWithID($q, $name, 'locationQA', $isReqReport);
            break;

        case "subLocationQA1": $q = "select location_id, location_title from qa_task_locations where location_parent_id='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subLocationQA1" id ="subLocationQA1" onChange="resetIds();subLocate2QA(this.value);"';
            SelectListWithID($q, $name, 'subLocationQA1', $isReqReport);
            break;

        case "subLocationQA2": $q = "select location_id, location_title from qa_task_locations where location_parent_id='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subLocationQA2" id ="subLocationQA2" onChange="resetIds();subLocate3QA(this.value);"';
            SelectListWithID($q, $name, 'subLocationQA2', $isReqReport);
            break;

        case "subLocationQA3": $q = "select location_id, location_title from qa_task_locations where location_parent_id='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subLocationQA3" id ="subLocationQA3"';
            SelectListWithID($q, $name, 'subLocationQA3', $isReqReport);
            break;


///Checklist Select box Dated 19-12-2012	
        case "locationCL": $q = "select location_id, location_title	from " . PROJECTLOCATION . " where project_id='$proID' and location_parent_id = '0' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="locationCL" id ="locationCL" onChange="subLocate1CL(this.value);"';
            SelectListWithID($q, $name, 'locationCL', $isReqReport);
            break;

        case "subLocationCL1": $q = "select location_id, location_title	from " . PROJECTLOCATION . " where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="subLocationCL" id ="subLocationCL" onChange="subLocate2CL(this.value);"';
            SelectListWithID($q, $name, 'subLocationCL', $isReqReport);
            break;

        case "subLocationCL2": $q = "select location_id, location_title	from " . PROJECTLOCATION . " where location_parent_id ='$proID' and is_deleted = '0' GROUP BY location_title";
            $name = 'name="sub_subLocationCL" id ="sub_subLocationCL"';
            SelectListWithID($q, $name, 'sub_subLocationCL', $isReqReport);
            break;

        case "userComp":
            if ($proID == 0) {
                $q = "SELECT user_id, user_name FROM user WHERE is_deleted = 0 ORDER BY user_name";
            } else {
                $q = "SELECT u.user_id, u.user_name FROM user AS u, user_projects AS up WHERE u.user_id = up.user_id AND u.is_deleted = 0 AND up.is_deleted IN (0, 2) AND project_id = '$proID' ORDER BY u.user_name";
            }
            $name = 'name="user" id ="user"';
            SelectListWithID($q, $name, 'user', $isReqReport);
            break;
        case "issueToList":
            $q = "SELECT issue_to_name, company_name FROM inspection_issue_to WHERE is_deleted = 0 AND project_id = '" . $proID . "' AND issue_to_name != '' ORDER BY issue_to_name";
            $name = 'name="issuedToList" id ="issuedToList"';
            SelectListWithSelectBox($q, $name, 'issuedToList', '');
            break;
    }
}

function SelectListWithoutID($q, $name, $sessionType, $extraQuery, $isReqReport = 'N') {
    $obj = new DB_Class();

    $r = $obj->db_query($q);
    $data = '<select ' . $name . ' class="select_box"';
    $data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
    $data .= '>
		  <option value="">Select</option>';
    while ($row = mysql_fetch_array($r)) {
        $data.='<option value="' . urlencode($row[0]) . '"';
        if (isset($_SESSION['qc']) || isset($_SESSION['pmr']) || isset($_SESSION['ir'])) {
            if ($sessionType == 'inspecrBy') {
                if ($_SESSION['qc']['inspectedBy'] == $row[0] || ($_SESSION['ir']['inspectedBy'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'issuedTo') {
                if ($_SESSION['qc']['issuedTo'] == $row[0] || ($_SESSION['ir']['issuedTo'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'issuedToPM') {
                if ($_SESSION['qc']['issuedToPM'] == $row[0] || ($_SESSION['pmr']['issuedToPM'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'raisedBy') {
                if ($_SESSION['qc']['raisedBy'] == $row[0] || ($_SESSION['ir']['raisedBy'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
        }
        $data.='>' . $row[0] . '</option>';
    }
    /* 	if (!empty($extraQuery)){
      $obj = new DB_Class();
      $r=$obj->db_query($extraQuery);
      while ($row=mysql_fetch_array($r)) {
      $data.='<option value="'.urlencode($row[0]).'"';
      if(isset($_SESSION['qc']) || isset($_SESSION['ir'])){
      if($sessionType == 'inspecrBy'){
      if($_SESSION['qc']['inspectedBy'] == $row[0]){
      $data.='selected="selected"';
      }
      if($_SESSION['ir']['inspectedBy'] == $row[0] && $isReqReport == 'Y'){
      $data.='selected="selected"';
      }
      }
      if($sessionType == 'issuedTo'){
      if($_SESSION['qc']['issuedTo'] == $row[0]){
      $data.='selected="selected"';
      }
      }
      if($sessionType == 'issuedToPM'){
      if($_SESSION['qc']['issuedToPM'] == $row[0]){
      $data.='selected="selected"';
      }
      }
      if($sessionType == 'raisedBy'){
      if($_SESSION['qc']['raisedBy'] == $row[0] || ($_SESSION['ir']['raisedBy'] == $row[0] && $isReqReport == 'Y')){
      $data.='selected="selected"';
      }
      }
      }
      $data.='>'.$row[0].'</option>';
      }
      } */
    echo $data.='</select>';
}

function SelectListWithID($q, $name, $sessionType, $isReqReport = 'N') {
    $obj = new DB_Class();
    $r = $obj->db_query($q);
    $data = '<select ' . $name . ' class="select_box"';
    $data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
    $data .= '><option value="">Select</option>';
    while ($row = mysql_fetch_array($r)) {
        $data.='<option value="' . urlencode($row[0]) . '"';
        if (isset($_SESSION['qc']) || isset($_SESSION['qa']) || isset($_SESSION['pm']) || isset($_SESSION['ir']) || isset($_SESSION['pmr']) || isset($_SESSION['qar']) || isset($_SESSION['clr'])) {
            if ($sessionType == 'location') {
                if ($_SESSION['qc']['location'] == $row[0] || ($_SESSION['ir']['location'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'subLocation') {
                if ($_SESSION['qc']['subLocation'] == $row[0] || ($_SESSION['ir']['subLocation'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'subSubLocation') {
                if ($_SESSION['qc']['subSubLocation'] == $row[0]) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'sub_subLocation') {
                if ($_SESSION['qc']['sub_subLocation'] == $row[0] || ($_SESSION['ir']['sub_subLocation'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'subLocationPM') {
                if ($_SESSION['qc']['subLocationPM'] == $row[0] || ($_SESSION['ir']['subLocationPM'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'raisedBy') {
                if ($_SESSION['qc']['raisedBy'] == $row[0] || ($_SESSION['ir']['raisedBy'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'subLocation_subPM') {
                if ($_SESSION['pmr']['subLocation_sub'] == $row[0]) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'locationQA') {
                if ($_SESSION['qa']['locationQA'] == $row[0] || ($_SESSION['qar']['locationQA'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'subLocationQA1') {
                if ($_SESSION['qa']['subLocationQA1'] == $row[0] || ($_SESSION['qar']['subLocationQA1'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'subLocationQA2') {
                if ($_SESSION['qa']['subLocationQA2'] == $row[0] || ($_SESSION['qar']['subLocationQA2'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'subLocationQA3') {
                if ($_SESSION['qa']['subLocationQA3'] == $row[0] || ($_SESSION['qar']['subLocationQA3'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'locationCL') {
                if ($_SESSION['clr']['locationCL'] == $row[0]) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'subLocationCL') {
                if ($_SESSION['clr']['subLocationCL'] == $row[0]) {
                    $data.='selected="selected"';
                }
            }
            if ($sessionType == 'sub_subLocationCL') {
                if ($_SESSION['clr']['sub_subLocationCL'] == $row[0]) {
                    $data.='selected="selected"';
                }
            }
        }
        $data.='>' . $row[1] . '</option>';
    }
    $data.='</select>';
    echo $data;
}

function SelectListWithIDSP($q, $name, $sessionType, $isReqReport = 'N') {
    $obj = new DB_Class();
    $r = $obj->db_query($q);
    $data = '<select ' . $name . ' class="select_box"';
    $data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
    $data .= '>
		<option value="">Select</option>';
    while ($row = mysql_fetch_array($r)) {
        $data.='<option value="' . $row[0] . '"';
        if (isset($_SESSION['qc']) || isset($_SESSION['pmr'])) {
            if ($sessionType == 'locationPM') {
                if ($_SESSION['qc']['locationPM'] == $row[0] || ($_SESSION['pmr']['location'] == $row[0] && $isReqReport == 'Y')) {
                    $data.='selected="selected"';
                }
            }
        }
        $data.='>' . $row[1] . '</option>';
    }
    $data.='</select>';
    echo $data;
}

function setSessions($q) {
    $obj = new DB_Class();
    $r = $obj->db_query($q);
    if (mysql_num_rows($r) > 0) {
        while ($rowPermission = mysql_fetch_assoc($r)) {
            $_SESSION[$rowPermission['permission_name']] = $rowPermission['is_allow'];
        }
    }
}

function SelectListWithoutIDMuli($q, $name, $sessionType, $extraQuery) {
    $obj = new DB_Class();

    $r = $obj->db_query($q);
    $data = '<select name="' . $name . '[]" id="' . $name . '" onChange="resetIds();" class="select_box" multiple="multiple" size="2"';
    $data .= 'style="width:220px;height:76px;background-image:url(images/multiple_select_box.png);"';
    $data .= '>
		  <option value="">Select</option>';
    while ($row = mysql_fetch_array($r)) {
        $data.='<option value="' . urlencode($row[0]) . '"';
        if (isset($_SESSION['ir']['issuedTo'])) {
            $ses_issedToArr = explode('@@@', $_SESSION['ir']['issuedTo']);
            if (in_array($row[0], $ses_issedToArr)) {
                $data.='selected="selected"';
            }
        }
        $data.='>' . $row[0] . '</option>';
    }
    /* 	if (!empty($extraQuery)){
      $obj = new DB_Class();
      $r=$obj->db_query($extraQuery);
      while ($row=mysql_fetch_array($r)) {
      $data.='<option value="'.urlencode($row[0]).'"';
      if(isset($_SESSION['ir']['issuedTo'])){
      $ses_issedToArr = explode('@@@', $_SESSION['ir']['issuedTo']);
      if(in_array($row[0], $ses_issedToArr)){
      $data.='selected="selected"';
      }
      }
      $data.='>'.$row[0].'</option>';
      }
      } */
    echo $data.='</select>';
}

function setSessions4Role($q) {
    $obj = new DB_Class();

    $r = $obj->db_query($q);
    if (mysql_num_rows($r) > 0) {
        $userRole = mysql_fetch_object($r);
        $_SESSION['userRole'] = $userRole->user_role;
        $_SESSION['userIssueTo'] = $userRole->issued_to;
    }
}

function SelectListWithoutIDuserRole($q, $name, $sessionType, $isReqReport = 'N') {
    $obj = new DB_Class();

    $r = $obj->db_query($q);
    $data = '<select ' . $name . ' class="select_box"';
    $data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
    $data .= '>
		  <option value="">Select</option>';
    while ($row = mysql_fetch_array($r)) {
        if ($row[0] == 'All Defect') {
            $raiseArray = array('Builder', 'Architect', 'Structural Engineer', 'Services Engineer', 'Superintendant', 'General Consultant', 'Client', 'Purchaser');
            for ($i = 0; $i < sizeof($raiseArray); $i++) {
                $data.='<option value="' . $raiseArray[$i] . '" ';
                if (isset($_SESSION['qc']) || isset($_SESSION['ir'])) {
                    if ($sessionType == 'raisedBy') {
                        if ($_SESSION['qc']['raisedBy'] == $raiseArray[$i] || ($_SESSION['ir']['raisedBy'] == $raiseArray[$i] && $isReqReport == 'Y')) {
                            $data.='selected="selected"';
                        }
                    }
                }
                $data.='>' . $raiseArray[$i] . '</option>';
            }
        } else {
            $data.='<option value="' . urlencode($row[0]) . '"';
            if (isset($_SESSION['qc']) || isset($_SESSION['ir'])) {
                if ($sessionType == 'raisedBy') {
                    if ($_SESSION['qc']['raisedBy'] == $row[0] || ($_SESSION['ir']['raisedBy'] == $row[0] && $isReqReport == 'Y')) {
                        $data.='selected="selected"';
                    }
                }
            }
            $data.='>' . $row[0] . '</option>';
        }
    }
    echo $data.='</select>';
}

function SelectListWithSelectBox($q, $name, $sessionType, $extraQuery, $isReqReport = 'N') {
    $obj = new DB_Class();
    echo $q;
    $r = $obj->db_query($q);

    $data .= '<option value="">Select</option>';
    while ($row = mysql_fetch_array($r)) {
        if ($row[1] != "") {
            $data .= '<option value="' . urlencode($row[0] . ' (' . $row[1] . ') ') . '">' . $row[0] . ' (' . $row[1] . ') </option>';
        } else {
            $data .= '<option value="' . urlencode($row[0]) . '">' . $row[0] . '</option>';
        }
    }
    echo $data;
}
?>						
