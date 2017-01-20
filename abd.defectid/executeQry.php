<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();



if($_REQUEST['task'] == 'issueToData_sd'){
//Projec wise Standard Defect Data
	$projData = $object->selQRYMultiple('project_id, standard_defect_id, issued_to', 'standard_defects', 'is_deleted =0 AND issued_to != ""');
	$projwiseStData = array();
	$projidsArr = array();
#	print_r($projData);
	foreach($projData as $pData){
		if(is_array($projwiseStData[$pData['project_id']])){
			$projwiseStData[$pData['project_id']][$pData['standard_defect_id']] = $pData['issued_to'];
		}else{
			$projidsArr[] = $pData['project_id'];
			$projwiseStData[$pData['project_id']] = array();
			$projwiseStData[$pData['project_id']][$pData['standard_defect_id']] = $pData['issued_to'];
		}
	}
	
//Projec wise IssueTo Data
	$issueToData = $object->selQRYMultiple('project_id, issue_to_name, company_name', 'inspection_issue_to', 'company_name != ""');
	$projwiseIsData = array();
#print_r($issueToData);
	foreach($issueToData as $iToData){
		if(is_array($projwiseIsData[$iToData['project_id']])){
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];
		}else{
			$projwiseIsData[$iToData['project_id']] = array();
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];			
		}
	}
	
//Start itrations here
	foreach($projwiseStData as $key=>$stData){
		foreach($stData as $keySt=>$valSt){
			if(	array_key_exists($valSt, $projwiseIsData[$key]) ){
				echo $upQRY = "UPDATE standard_defects SET issued_to = '".$valSt." (".$projwiseIsData[$key][$valSt].")', last_modified_date = NOW() WHERE standard_defect_id = ".$keySt." AND project_id = ".$key;
				mysql_query($upQRY);
			}
		}
	}
}

if($_REQUEST['task'] == 'issueToData_checkList'){
//Projec wise Standard Defect Data
	$projData = $object->selQRYMultiple('project_id, check_list_items_id, issued_to', 'check_list_items', 'is_deleted =0 AND issued_to != ""');
	$projwiseStData = array();
#	print_r($projData);
	foreach($projData as $pData){
		if(is_array($projwiseStData[$pData['project_id']])){
			$projwiseStData[$pData['project_id']][$pData['check_list_items_id']] = $pData['issued_to'];
		}else{
			$projwiseStData[$pData['project_id']] = array();
			$projwiseStData[$pData['project_id']][$pData['check_list_items_id']] = $pData['issued_to'];
		}
	}
	
//Projec wise IssueTo Data
	$issueToData = $object->selQRYMultiple('project_id, issue_to_name, company_name', 'inspection_issue_to', 'company_name != ""');
	$projwiseIsData = array();
#print_r($issueToData);
	foreach($issueToData as $iToData){
		if(is_array($projwiseIsData[$iToData['project_id']])){
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];
		}else{
			$projwiseIsData[$iToData['project_id']] = array();
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];			
		}
	}
	
//Start itrations here
	foreach($projwiseStData as $key=>$stData){
		foreach($stData as $keySt=>$valSt){
			if(	array_key_exists($valSt, $projwiseIsData[$key]) ){
				echo $upQRY = "UPDATE check_list_items SET issued_to = '".$valSt." (".$projwiseIsData[$key][$valSt].")', last_modified_date = NOW() WHERE check_list_items_id = ".$keySt." AND project_id = ".$key;
				mysql_query($upQRY);
			}
		}
	}
}

if($_REQUEST['task'] == 'issueToData_insp'){
//Projec wise Standard Defect Data
	$projData = $object->selQRYMultiple('project_id, issued_to_inspections_id, issued_to_name', 'issued_to_for_inspections', 'is_deleted =0 AND issued_to_name != ""');
	$projwiseStData = array();
#	print_r($projData);
	foreach($projData as $pData){
		if(is_array($projwiseStData[$pData['project_id']])){
			$projwiseStData[$pData['project_id']][$pData['issued_to_inspections_id']] = $pData['issued_to_name'];
		}else{
			$projwiseStData[$pData['project_id']] = array();
			$projwiseStData[$pData['project_id']][$pData['issued_to_inspections_id']] = $pData['issued_to_name'];
		}
	}
	
//Projec wise IssueTo Data
	$issueToData = $object->selQRYMultiple('project_id, issue_to_name, company_name', 'inspection_issue_to', 'company_name != ""');
	$projwiseIsData = array();
#print_r($issueToData);
	foreach($issueToData as $iToData){
		if(is_array($projwiseIsData[$iToData['project_id']])){
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];
		}else{
			$projwiseIsData[$iToData['project_id']] = array();
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];			
		}
	}
#	echo '<pre>';print_r($projwiseStData);print_r($projwiseIsData);die;
//Start itrations here
	foreach($projwiseStData as $key=>$stData){
		foreach($stData as $keySt=>$valSt){
			if(	array_key_exists($valSt, $projwiseIsData[$key]) ){
				echo $upQRY = "UPDATE issued_to_for_inspections SET issued_to_name = '".$valSt." (".$projwiseIsData[$key][$valSt].")', last_modified_date = NOW() WHERE issued_to_inspections_id = ".$keySt." AND project_id = ".$key;
				mysql_query($upQRY);
			}
		}
	}
}

if($_REQUEST['task'] == 'issueToData'){
//Project Data from inspection_issue_to
	$projData = $object->selQRYMultiple('DISTINCT project_id', 'inspection_issue_to', 'is_deleted =0');
//Project Data from inspection_issue_to
	$masterIssueToArr = array();
	$masterIssueToData = $object->selQRYMultiple('id, issue_to_name', 'master_issue_to', 'is_deleted =0');
	foreach($masterIssueToData as $mData){
		$masterIssueToArr[$mData['id']] = $mData['issue_to_name'];
	}
//Project Data from inspection_issue_to
	$masterIssueToContactArr = array();
	$masterIssueToContactPersonArr = array();
	$masterIssueToContactData = $object->selQRYMultiple('contact_id, issue_to_name, company_name', 'master_issue_to_contact', 'is_deleted =0');
	foreach($masterIssueToContactData as $mData){
		$masterIssueToContactArr[$mData['contact_id']] = $mData['issue_to_name'];
		$masterIssueToContactPersonArr[$mData['contact_id']] = $mData['company_name'];
	}
	
	foreach($projData as $proData){
		echo '<h1>'.$proData['project_id'].'</h1>';
		$issueToData = $object->selQRYMultiple('issue_to_name, company_name, issue_to_phone, issue_to_email, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag, activity, cast(GROUP_CONCAT(issue_to_id) as CHAR) AS issueID, issue_to_name, GROUP_CONCAT(company_name) AS companyName ', 'inspection_issue_to', 'project_id = '.$proData['project_id'].' AND is_deleted = 0 GROUP BY issue_to_name');
		foreach($issueToData as $iss2Data){
			$issueIDArr = explode(",", $iss2Data['issueID']);
			$companyNameArr = explode(",", $iss2Data['companyName']);
			if(sizeof($issueIDArr) > 1){
				$g = 0;
				foreach($issueIDArr as $key=>$val){$g++;
					$isDefault = 0;
					if($g == 0){
						$isDefault = 1;
						if(!in_array($iss2Data['issue_to_name'], $masterIssueToArr)){
echo '<br />'.							$issueToMasterQry = "INSERT INTO master_issue_to SET 
									issue_to_name = '".addslashes($iss2Data['issue_to_name'])."',
									company_name = '".addslashes($iss2Data['company_name'])."',
									issue_to_phone = '".addslashes($iss2Data['issue_to_phone'])."',
									issue_to_email = '".addslashes($iss2Data['issue_to_email'])."',
									last_modified_date = NOW(),
									last_modified_by = '".$iss2Data['last_modified_by']."',
									created_date = NOW(),
									created_by = '".$iss2Data['created_by']."',
									resource_type = '".$iss2Data['resource_type']."',
									is_deleted = '".$iss2Data['is_deleted']."',
									tag = '".$iss2Data['tag']."',
									activity = '".$iss2Data['activity']."'";
							mysql_query($issueToMasterQry);
							$masterIssueID = mysql_insert_id();
						}else{
							$masterIssueID = array_search($iss2Data['issue_to_name'], $masterIssueToArr);
						}
					}
					if(!in_array($iss2Data['issue_to_name'], $masterIssueToContactArr) && $iss2Data['company_name'] != ""){	
echo '<br />'.						$issueToContactQry = "INSERT INTO master_issue_to_contact SET 
								master_issue_id = '".$masterIssueID."',
								issue_to_name = '".addslashes($iss2Data['issue_to_name'])."',
								company_name = '".addslashes($iss2Data['company_name'])."',
								issue_to_phone = '".addslashes($iss2Data['issue_to_phone'])."',
								issue_to_email = '".addslashes($iss2Data['issue_to_email'])."',
								last_modified_date = NOW(),
								last_modified_by = '".$iss2Data['last_modified_by']."',
								created_date = NOW(),
								created_by = '".$iss2Data['created_by']."',
								resource_type = '".$iss2Data['resource_type']."',
								is_deleted = '".$iss2Data['is_deleted']."',
								tag = '".$iss2Data['tag']."',
								activity = '".$iss2Data['activity']."',
								is_default = ".$isDefault;
						mysql_query($issueToContactQry);
						$masterIssueContactID = mysql_insert_id();
					}else{
						$masterIssueContactID = array_search($iss2Data['issue_to_name'], $masterIssueToContactArr);
					}	
	//Update Query Here for issueto update
echo '<br />'.					$issueToUpdateQry = "UPDATE inspection_issue_to SET 
							master_issue_id = '".$masterIssueID."',
							master_contact_id = '".$masterIssueContactID."',
							is_default = ".$isDefault."
						WHERE
							issue_to_id = ".$issueIDArr[0];
					mysql_query($issueToUpdateQry);
				}
			}else{
				if(!in_array($iss2Data['issue_to_name'], $masterIssueToArr)){
echo '<br />'.					$issueToMasterQry = "INSERT INTO master_issue_to SET 
							issue_to_name = '".addslashes($iss2Data['issue_to_name'])."',
							company_name = '".addslashes($iss2Data['company_name'])."',
							issue_to_phone = '".addslashes($iss2Data['issue_to_phone'])."',
							issue_to_email = '".addslashes($iss2Data['issue_to_email'])."',
							last_modified_date = NOW(),
							last_modified_by = '".$iss2Data['last_modified_by']."',
							created_date = NOW(),
							created_by = '".$iss2Data['created_by']."',
							resource_type = '".$iss2Data['resource_type']."',
							is_deleted = '".$iss2Data['is_deleted']."',
							tag = '".$iss2Data['tag']."',
							activity = '".$iss2Data['activity']."'";
					mysql_query($issueToMasterQry);
					$masterIssueID = mysql_insert_id();
				}else{
					$masterIssueID = array_search($iss2Data['issue_to_name'], $masterIssueToArr);
				}	
				if(!in_array($iss2Data['issue_to_name'], $masterIssueToContactArr)){
echo '<br />'.					$issueToContactQry = "INSERT INTO master_issue_to_contact SET 
							master_issue_id = '".$masterIssueID."',
							issue_to_name = '".addslashes($iss2Data['issue_to_name'])."',
							company_name = '".addslashes($iss2Data['company_name'])."',
							issue_to_phone = '".addslashes($iss2Data['issue_to_phone'])."',
							issue_to_email = '".addslashes($iss2Data['issue_to_email'])."',
							last_modified_date = NOW(),
							last_modified_by = '".$iss2Data['last_modified_by']."',
							created_date = NOW(),
							created_by = '".$iss2Data['created_by']."',
							resource_type = '".$iss2Data['resource_type']."',
							is_deleted = '".$iss2Data['is_deleted']."',
							tag = '".$iss2Data['tag']."',
							activity = '".$iss2Data['activity']."',
							is_default = 1";
					mysql_query($issueToContactQry);
					$masterIssueContactID = mysql_insert_id();
				}else{
					$masterIssueContactID = array_search($iss2Data['issue_to_name'], $masterIssueToContactArr);
				}	

//Update Query Here for issueto update
echo '<br />'.				$issueToUpdateQry = "UPDATE inspection_issue_to SET 
						master_issue_id = '".$masterIssueID."',
						master_contact_id = '".$masterIssueContactID."',
						is_default = 1
					WHERE
						issue_to_id = ".$issueIDArr[0];
				mysql_query($issueToUpdateQry);
			}
		}
		#print_r($issueToData);die;
	}
}die;

if($_REQUEST['task'] == 'location_tree'){
	$locData = $object->selQRYMultiple('location_id, location_title, location_parent_id', 'project_locations', 'is_deleted in (0, 1)');
	foreach($locData as $lData){
		$locIdTree = $object->subLocationsIDS($lData['location_id'], ' > ');
		$locNameTree = $object->subLocations($lData['location_id'], ' > ');
		echo $query = 'UPDATE project_locations SET location_id_tree = "'.$locIdTree.'", location_name_tree = "'.$locNameTree.'", last_modified_date = NOW() WHERE location_id = '.$lData['location_id'];
		mysql_query($query);
	}
}


if($_REQUEST['task'] == 'conCalender'){
	$table    = "public_holidays";
	$fileName = "csv/Project_Leave_2014.csv";
	$ignoreFirstRow = 1;
	$insertDataArr = array();
	if (($handle = fopen($fileName, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if($ignoreFirstRow != 1){
				$d = explode("/",$data[0]);
				$data[0] = $d[2]."-".$d[1]."-".$d[0];
				$insertDataArr[] = "('".$data[0]."', '".$data[1]."', NOW(), 0, 0, NOW(), NOW())";
			 }
			 $ignoreFirstRow++;
		}
		fclose($handle);
	}
	
	$sql = "INSERT INTO ".$table." (`date`, `leave_type`, `created_date`, `created_by`, `last_modified_by`, `last_modified_date`, `original_modified_date`) values ".join(", ", $insertDataArr);
	mysql_query($sql);
	
	$defaultLeave = $object->selQRYMultiple('date, leave_type, reason, is_leave', 'public_holidays', 'is_deleted = 0 and  `date` >= "2014-01-01"');
	
	$projectData = $object->selQRYMultiple('project_id, created_by', 'projects', 'is_deleted = 0');
	
	foreach($projectData as $projData){
		$insertDataArr = array();
		foreach($defaultLeave as $val){
			$insertDataArr[] = '('.$projData['project_id'].', "'.$val['date'].'", "'.$val['leave_type'].'", "'.$val['reason'].'", "'.$val['is_leave'].'", NOW(), "'.$projData['created_by'].'", NOW(), "'.$projData['created_by'].'", NOW())';
		}
		
echo		$insertQuery = "INSERT INTO project_leave (project_id, date, leave_type, reason, is_leave, created_date, created_by, last_modified_date, last_modified_by, original_modified_date) values ".join(", ", $insertDataArr);
		
		mysql_query($insertQuery);	
	}
	
	echo "Done";
}


die;


if ($_REQUEST['task'] == 'correct_location_breadcrumb'){
	$locations = $object->selQRYMultiple('location_id, inspection_id', 'project_inspections', 'is_deleted=0');

	if(!empty($locations)){
		foreach($locations as $row){
			$location_id = $row["location_id"];
			$inspection_id = $row["inspection_id"];
			$locations = $object->subLocations($location_id, ' > ');
			$query = "UPDATE project_inspections SET
							inspection_location = '".$locations."',
							last_modified_date = NOW()
						WHERE
							inspection_id = ".$inspection_id." AND
							location_id = ".$location_id;
			mysql_query ($query);
		}
	}
}

//Code for Set Permissions start here
if($_REQUEST['task'] == 'permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		//for($i=0;$i<sizeof($managerPermissionArray);$i++){
			echo $permissionQry = "INSERT INTO user_permission SET
										user_id = '".$id['user_id']."',
										permission_name = 'web_report_sub_contractor_report',
										is_allow = '1',
										created_by = '0',
										created_date = NOW(),
										created_by = '0',
										created_date = NOW()";
			mysql_query($permissionQry);
		//}
	}
	die;
	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
			echo $permissionQry = "INSERT INTO user_permission SET
										user_id = '".$id['user_id']."',
										permission_name = '".$keyInspectorPermissionArray[$i]."',
										is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
										created_by = '0',
										created_date = NOW(),
										last_modified_by = '0',
										last_modified_date = NOW()";
			mysql_query($permissionQry);
		}
	}
}

//Code for Insert row in projects table start here

if($_REQUEST['task'] == 'projects'){
	$projects = $object->selQRYMultiple('project_id, pro_code, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, created_date, created_by, resource_type, is_deleted', 'user_projects', 'is_deleted>=0');
	
#	print_r($projects);die;
	
	foreach($projects as $proj){
		echo $projectQry = "INSERT INTO projects SET
								pro_code = '".$proj['pro_code']."',
								project_id = '".$proj['project_id']."',
								project_name = '".$proj['project_name']."',
								project_type = '".$proj['project_type']."',
								project_address_line1 = '".$proj['project_address_line1']."',
								project_address_line2 = '".$proj['project_address_line2']."',
								project_suburb = '".$proj['project_suburb']."',
								project_state = '".$proj['project_state']."',
								project_postcode = '".$proj['project_postcode']."',
								project_country = '".$proj['project_country']."',
								is_deleted = '".$proj['is_deleted']."',
								created_date = NOW(),
								created_by = 0,
								last_modified_by = '0',
								last_modified_date = NOW()";
				mysql_query($projectQry);
	}
}

if($_REQUEST['task'] == 'default_issue_to'){
	$projects = $object->selQRYMultiple('project_id', 'projects', 'is_deleted>=0');
	
#	print_r($projects);die;
	
	foreach($projects as $proj){
		echo $projectQry = "INSERT INTO inspection_issue_to SET
								project_id = '".$proj['project_id']."',
								issue_to_name = 'NA',
								last_modified_date = NOW(),
								last_modified_by = 0,
								created_date = NOW(),
								is_deleted=0,
								created_by = 0";
				mysql_query($projectQry);
	}
}

if($_REQUEST['task'] == 'set_project_permision'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		foreach($project_ids as $pId){
			for($i=0;$i<sizeof($managerPermissionArray);$i++){
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
				mysql_query($permissionQry);
			}
		}
		$project_ids = array();
	}
	
	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		foreach($project_ids as $pId){
			for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
				mysql_query($permissionQry);
			}
		}
	}
}

//Code for Set Permissions start here
if($_REQUEST['task'] == 'permission_project'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);	
	$projectWisePermissions = array(
		'web_edit_inspection',
		'web_delete_inspection',
		'web_close_inspection',
		'iPad_add_inspection',
		'iPad_edit_inspection',
		'iPad_delete_inspection',
		'iPad_close_inspection',
		'iPhone_add_inspection',
		'iPhone_close_inspection',
		'web_checklist'
	);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($managerPermissionArray);$i++){
			if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
				$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
				if($project_ids[0]['project_id'] != ''){
					foreach($project_ids as $pId){
						echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
	echo '<br />';
							mysql_query($permissionQry);
					}
				}
			}else{
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
echo '<br />';
#				mysql_query($permissionQry);
			}
		}
	}

	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
			if(in_array($keyInspectorPermissionArray[$i], $projectWisePermissions)){
				$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
				if($project_ids[0]['project_id'] != ''){
					foreach($project_ids as $pId){
						echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
	echo '<br />';
						mysql_query($permissionQry);
					}
				}
			}else{
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
echo '<br />';
#				mysql_query($permissionQry);
			}
		}
	}
}

if($_REQUEST['task'] == 'set_userid_export'){
	$exp = $object->selQRYMultiple('export_files_id, path', 'exportData', 'created_date >= "1970-01-01 00:00:00"');
	foreach($exp as $exportData){
		$pathData = explode('/', $exportData['path']);
		echo $exportData['path'].'<br />';
echo 		$updateQry = 'UPDATE exportData SET userid = "'.$pathData[3].'", last_modified_date = NOW() WHERE export_files_id = "'.$exportData['export_files_id'].'"';
	mysql_query($updateQry);
	}
}

if($_REQUEST['task'] == 'set_single_permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);	
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	foreach($ids as $id){
		echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$_REQUEST['permission_name']."',
											is_allow = '".$managerPermissionArray[$_REQUEST['permission_name']]."',
											created_by = '0',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
echo '<br />';
				mysql_query($permissionQry);
	}

	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$_REQUEST['permission_name']."',
											is_allow = '".$inspectorPermissionArray[$_REQUEST['permission_name']]."',
											created_by = '0',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
echo '<br />';
				mysql_query($permissionQry);
	}
}

if($_REQUEST['task'] == 'insert_sync_data'){
	$project_ids = $object->selQRYMultiple('project_id', 'projects', 'is_deleted IN (0, 2, 1)');
	count($project_ids);
	foreach($project_ids as $pId){	
echo		$ipadQuery = "INSERT INTO sync_permission SET no_of_days = '100000', status = '\'All Open\'', project_id = '".$pId['project_id']."', device_type = 'iPad', created_by = '0', created_date = NOW(), last_modified_date = NOW(), last_modified_by = '0'";
		mysql_query($ipadQuery);
		
echo		$iphoneQuery = "INSERT INTO sync_permission SET no_of_days = '100000', status = '\'All Open\'', project_id = '".$pId['project_id']."', device_type = 'iPhone', created_by = '0', created_date = NOW(), last_modified_date = NOW(), last_modified_by = '0'";
		mysql_query($iphoneQuery);
	}
}

if($_REQUEST['task'] == 'correct_standard'){
	$standard = $object->selQRYMultiple('standard_defect_id, tag', 'standard_defects', 'is_deleted = 0 and tag != ""');
	foreach($standard as $st){
echo		$qry = "UPDATE standard_defects SET tag = '".trim($st['tag'], ';').";', last_modified_date = NOW() WHERE standard_defect_id = ".$st['standard_defect_id'];
mysql_query($qry);

	}
}

if($_REQUEST['task'] == 'tag_update'){
	$drawingTag = $object->selQRYMultiple('draw_mgmt_images_id, draw_mgmt_images_tags', 'draw_mgmt_images', 'is_deleted = 0 and draw_mgmt_images_tags != ""');
/*	function addSpaces($tagEle){ if($tagEle != ''){ return(" ".trim($tagEle)); } }*/
	function removeSpaces($tagEle){ if($tagEle != ''){ return(trim($tagEle)); } }
	foreach($drawingTag as $dtag){
		$drawTagsTemp = $dtag['draw_mgmt_images_tags'];
		$tagAr = explode(';', $drawTagsTemp);
		$spTagArr = array_map("removeSpaces", $tagAr);

		$drawTags = implode(';', $spTagArr);
		$drawTags = trim($drawTags, ";");
		if($drawTags != ""){
			$drawTags = $drawTags.';';
		}
		$qry = "UPDATE draw_mgmt_images SET draw_mgmt_images_tags = '".$drawTags."', last_modified_date = NOW() WHERE draw_mgmt_images_id = ".$dtag['draw_mgmt_images_id'];
echo	$qry.';';
echo '<br />';
		mysql_query($qry);

	}
}

if($_REQUEST['task'] == 'set_subcontractor_permission'){
	$userType = $_REQUEST['userType'];//manager or inspector
	$editPerm = $inspectorPermissionArray['iPhone_edit_inspection'];
	$parEditPerm = $inspectorPermissionArray['iPhone_edit_inspection_partial'];
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` = "'.$userType.'"');
	foreach($ids as $id){
		$proData = $object->selQRYMultiple('project_id, user_role', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		foreach($proData as $pData){
			if($pData['user_role'] == 'Sub Contractor'){
				$permissionQry = "INSERT INTO user_permission (user_id, project_id, permission_name, is_allow, created_by, created_date, last_modified_by, last_modified_date) VALUES ('".$id['user_id']."', '".$pData['project_id']."', 'iPhone_edit_inspection', 0, 0, NOW(), 0, NOW()), ('".$id['user_id']."', '".$pData['project_id']."', 'iPhone_edit_inspection_partial', 1, 0, NOW(), 0, NOW())";
			}else{
				$permissionQry = "INSERT INTO user_permission (user_id, project_id, permission_name, is_allow, created_by, created_date, last_modified_by, last_modified_date) VALUES ('".$id['user_id']."', '".$pData['project_id']."', 'iPhone_edit_inspection', '".$editPerm."', 0, NOW(), 0, NOW()), ('".$id['user_id']."', '".$pData['project_id']."', 'iPhone_edit_inspection_partial', '".$parEditPerm."', 0, NOW(), 0, NOW())";
			}
			#echo $permissionQry;
			mysql_query($permissionQry);
		}
	}
}

if($_REQUEST['task'] == 'update_fixedbydays'){
	$tableName = $_REQUEST['tableName'];//manager or inspector
	if($tableName == 'check_list_items'){
		$tableData = $object->selQRYMultiple('check_list_items_id, issued_to', 'check_list_items', 'is_deleted IN (0, 1)');
		foreach($tableData as $tData){
			if(trim($tData['issued_to']) == ''){
echo				$qry = "UPDATE check_list_items SET fix_by_days = 0, last_modified_date = NOW() WHERE check_list_items_id = ".$tData['check_list_items_id'];
			}else{
	echo			$qry = "UPDATE check_list_items SET fix_by_days = 3, last_modified_date = NOW() WHERE check_list_items_id = ".$tData['check_list_items_id'];
			}
			mysql_query($qry);
		}
	}
	
	if($tableName == 'standard_defects'){
		$tableData = $object->selQRYMultiple('standard_defect_id, issued_to', 'standard_defects', 'is_deleted IN (0, 1)');
		foreach($tableData as $tData){
			if(trim($tData['issued_to']) == ''){
		echo		$qry = "UPDATE standard_defects SET fix_by_days = 0, last_modified_date = NOW() WHERE standard_defect_id = ".$tData['standard_defect_id'];
			}else{
			echo	$qry = "UPDATE standard_defects SET fix_by_days = 3, last_modified_date = NOW() WHERE standard_defect_id = ".$tData['standard_defect_id'];
			}
			mysql_query($qry);
		}
	}
}


if($_REQUEST['task'] == 'set_single_permission_projectwise'){
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	$keyManagerPermissionArray = array_keys($managerPermissionArray);	
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		
		if($project_ids[0]['project_id'] != ''){
			foreach($project_ids as $pId){
				echo $permissionQry = "INSERT INTO user_permission SET
									user_id = '".$id['user_id']."',
									permission_name = '".$_REQUEST['permission_name']."',
									is_allow = '".$managerPermissionArray[$_REQUEST['permission_name']]."',
									created_by = '0',
									project_id = '".$pId['project_id']."',
									created_date = NOW(),
									last_modified_date = NOW()";
				echo '<br />';
				mysql_query($permissionQry);
			}
		}	
	}
	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		if($project_ids[0]['project_id'] != ''){
			foreach($project_ids as $pId){
				echo $permissionQry = "INSERT INTO user_permission SET
									user_id = '".$id['user_id']."',
									permission_name = '".$_REQUEST['permission_name']."',
									is_allow = '".$inspectorPermissionArray[$_REQUEST['permission_name']]."',
									created_by = '0',
									project_id = '".$pId['project_id']."',
									created_date = NOW(),
									last_modified_date = NOW()";
				echo '<br />';
				mysql_query($permissionQry);
			}
		}
	}
}

if ($_REQUEST['task'] == 'correct_location_breadcrumb_promon_name'){
	$locations = $object->selQRYMultiple('sub_location_id, progress_id', 'progress_monitoring', 'is_deleted IN (1, 0)');
	if(!empty($locations)){
		foreach($locations as $row){
			$location_id = $row["sub_location_id"];
			$progress_id = $row["progress_id"];
			$locationsStr = $object->subLocationsProgressMonitoring_update($location_id, ' > ');
echo			$query = "UPDATE progress_monitoring SET location_tree_name = '".$locationsStr."', last_modified_date = NOW() WHERE progress_id = ".$progress_id." AND sub_location_id = ".$location_id;
echo '<br />';
			mysql_query ($query);
		}
	}
}
if ($_REQUEST['task'] == 'correct_location_breadcrumb_promon_id'){
	$locations = $object->selQRYMultiple('sub_location_id, progress_id', 'progress_monitoring', 'is_deleted IN (1, 0)');
	if(!empty($locations)){
		foreach($locations as $row){
			$location_id = $row["sub_location_id"];
			$progress_id = $row["progress_id"];
			$locationsStr = $object->subLocationsProgressMonitoring_ids($location_id, ' > ');
echo			$query = "UPDATE progress_monitoring SET location_tree = '".$locationsStr."', last_modified_date = NOW() WHERE progress_id = ".$progress_id." AND sub_location_id = ".$location_id;
echo '<br />';
			mysql_query ($query);
		}
	}
}

if($_REQUEST['task'] == 'set_default_sync_locations'){
	$projects = $object->selQRYMultiple('project_id', 'projects', 'is_deleted>=0');
	foreach($projects as $pro){
		$updateQry = "UPDATE sync_permission SET
						location_ids = 'Select All',
						last_modified_date =  NOW()
					WHERE
						project_id = ".$pro['project_id'];
		mysql_query($updateQry);
	}
}

if($_REQUEST['task'] == 'location_tree_promon'){
	$locData = $object->selQRYMultiple('location_id, location_title, location_parent_id', 'project_monitoring_locations', 'is_deleted in (0, 1)');
	foreach($locData as $lData){
		$locNameTree = $object->promon_sublocationParent($lData['location_id'], ' > ');
		echo $query = 'UPDATE project_monitoring_locations SET location_tree_name = "'.$locNameTree.'", last_modified_date = NOW() WHERE location_id = '.$lData['location_id'];
		mysql_query($query);
	}
}
if($_REQUEST['task'] == 'location_tree_qa'){
	$locData = $object->selQRYMultiple('location_id, location_title, location_parent_id', 'qa_task_locations', 'is_deleted in (0, 1)');
	foreach($locData as $lData){
		$locNameTree = $object->qa_sublocationParent($lData['location_id'], ' > ');

		echo $query = 'UPDATE qa_task_locations SET location_tree_name = "'.$locNameTree.'", last_modified_date = NOW() WHERE location_id = '.$lData['location_id'];
		mysql_query($query);
	}
}

if($_REQUEST['task'] == 'default_leave'){
	$projects = $object->selQRYMultiple('project_id', 'projects', 'is_deleted>=0');

	$defaultLeave = $object->selQRYMultiple('date, leave_type, reason, is_leave', 'public_holidays', 'is_deleted = 0');

	foreach($projects as $pro){
	
		foreach($defaultLeave as $val){
echo			$insertQry = "INSERT INTO project_leave SET 
								project_id = '".$pro['project_id']."', 
								date = '".$val['date']."',
								leave_type = '".$val['leave_type']."',
								reason = '".$val['reason']."',
								is_leave = '".$val['is_leave']."',
								created_date = NOW(),
								last_modified_date = NOW(),
								created_by = 0,
								last_modified_by = 0";
			mysql_query($insertQry);
		}
	}
}

if($_REQUEST['task'] == 'project_drawing_thumbnail'){
	$object->resizeImages('./project_drawings/'.$_GET['project_id'].'/'.$_GET['file_name'], 150, 150, './project_drawings/'.$_GET['project_id'].'/thumbnail/thumb_'.$_GET['file_name']);
}
?>