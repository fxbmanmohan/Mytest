<?php
session_start();
set_time_limit(60000000000);

	include("./includes/functions.php");
	$obj = new DB_Class();
	include('./includes/commanfunction.php');
	$common = new COMMAN_Class();
//Issued TO for Inspection table, should be combined only if required. Same for Location Table.	
if(isset($_REQUEST['name'])){
	$permProjArr = array();
	$closePemission = $common->selQRYMultiple('is_allow, project_id', 'user_permission', 'user_id = "'.$_SESSION['ww_builder_id'].'" AND project_id IN ('.$_REQUEST['projName'].') AND permission_name = "web_close_inspection"');
	foreach($closePemission as $closeInsp){
		$permProjArr[$closeInsp['project_id']] = $closeInsp['is_allow'];
	}
	
	$_SESSION['qc'] = $_REQUEST;//Set Session for back implement and Remeber
	setcookie($_SESSION['ww_builder_id'].'_qc', serialize($_REQUEST), time()+864000);

	$where = " and I.inspection_type!='Memo'";
	$issuedToTable = "";
	
	if(!empty($_REQUEST['projName'])){$where.=" and I.project_id IN (".$_REQUEST['projName'].")";}

	if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		$where.=" and I.location_id in (".$common->subLocationsId($_REQUEST['location'], ", ").")";
	}
	if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['subSubLocation'])){
		$where.=" and I.location_id in (".$common->subLocationsId($_REQUEST['subSubLocation'], ", ").")";
	}else{
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
			$where.=" and I.location_id in (".$common->subLocationsId($_REQUEST['subLocation'], ", ").")";
		}
	}
#	echo $_REQUEST['subLocation'].'<br />';echo $where;
	if(!empty($_REQUEST['status'])){
		$where.=" and I.inspection_id = F.inspection_id and F.inspection_status='".$_REQUEST['status']."'";
		$issuedToTable = " , issued_to_for_inspections as F";
	}
	if(!empty($_REQUEST['inspectedBy'])){
		$where.=" and I.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
	}
	if($_REQUEST['issuedTo']!=""){
		$where.="  and I.inspection_id = F.inspection_id AND (F.issued_to_name LIKE '".$_REQUEST['issuedTo']." (%' OR F.issued_to_name LIKE '".$_REQUEST['issuedTo']."' ) ";
		$issuedToTable = " , issued_to_for_inspections as F";
	}
	//if($_REQUEST['priority']!=""){$where.=" and I.inspection_priority='".$_REQUEST['priority']."'";}
	if($_REQUEST['inspecrType']!=""){$where.=" and I.inspection_type='".$_REQUEST['inspecrType']."'";}
	if(!empty($_REQUEST['costAttribute'])){
		$where.="  and I.inspection_id = F.inspection_id and F.cost_attribute = '".$_REQUEST['costAttribute']."'";
		$issuedToTable = " , issued_to_for_inspections as F";
	}
	
	if(!empty($_SESSION['userRole']) && $_SESSION['userRole'] != 'Sub Contractor'){
		if($_SESSION['userRole'] != 'All Defect'){
			$where.=" and I.inspection_raised_by = '".$_SESSION['userRole']."'";
		}else{
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}else{
		if($_REQUEST['raisedBy'] != 'All Defect'){
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}

	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.="  and I.inspection_id = F.inspection_id AND (F.issued_to_name LIKE '".$_SESSION['userIssueTo']." (%' OR F.issued_to_name LIKE '".$_SESSION['userIssueTo']."' ) ";
		#$where.=" and I.inspection_id = F.inspection_id and  F.issued_to_name = '".$_SESSION['userIssueTo']."'";
		$issuedToTable = " , issued_to_for_inspections as F";
	}

	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$or.=" I.inspection_date_raised between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
		$issuedToTable = " , issued_to_for_inspections as F";
		$where .= " and I.inspection_id = F.inspection_id ";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$or.=" F.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
		$issuedToTable = " , issued_to_for_inspections as F";
		$where .= " and I.inspection_id = F.inspection_id ";
	}
	
	if(isset($_REQUEST['spParam']) && $_REQUEST['spParam'] != ""){
		$inspectionArr = array();
		switch($_REQUEST['spParam']){
			case 'overDue' :
				$inspData = $common->selQRYMultiple('GROUP_CONCAT(inspection_id) AS insps', 'issued_to_for_inspections', 'project_id IN ('. $_REQUEST['projName'] .') AND is_deleted = 0 AND inspection_status != "Closed" AND inspection_fixed_by_date <= NOW() AND inspection_fixed_by_date != "0000-00-00" GROUP BY inspection_id');
				foreach($inspData as $inData){
					$inspectionArr[] = $inData['insps'];
				}
			break;
			
			case 'Closed' :
				$where.=" and I.inspection_id = F.inspection_id and F.inspection_status='Closed'";
				$issuedToTable = " , issued_to_for_inspections as F";

				$inspectionArr = array();
			break;
		}
		if(!empty($inspectionArr))
			$where .= " and I.inspection_id IN ( ".join(",", $inspectionArr)." )";
	}
	
	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}

}

	$project_id = $_SESSION['idp'];
	
	$aColumns = array('I.project_id', 'I.inspection_id', 'I.inspection_id', 'I.location_id', 'I.inspection_fixed_by_date', 'I.inspection_issued_to', 'I.inspection_description', 'I.inspection_raised_by');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "I.inspection_id";
	
	/* DB table to use */
	$sTable = "project_inspections as I" . $issuedToTable;

	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	if ( isset( $_GET['iSortCol_0'] ) ){
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ){
				if ((intval( $_GET['iSortCol_'.$i] ) -1) == -1){
					$sOrder .= $aColumns[0]." ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				}else{
					$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) -1]." ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				}
			}
		}

		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" ){
			$sOrder = "";
		}
	}
	$sWhere = "";
	
	if ( $_GET['sSearch'] != "" ){
		$sWhere = "WHERE I.is_deleted = '0' ". $where ." and (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
			if ( $sWhere == "" ){
				$sWhere = "WHERE ";
			}else{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ($sWhere == ""){
		$sWhere = "WHERE I.is_deleted = '0' ". $where;
	}
	
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)).", I.check_list_item_id FROM $sTable $sWhere group by I.inspection_id $sOrder $sLimit ";
#echo $sQuery;die;
	$rResult = $obj->db_query($sQuery) or die(mysql_error());
	$inspArr = array();
	$locidsArr = array();
	while($aRow = mysql_fetch_assoc($rResult)){	
		$inspArr[] = $aRow['inspection_id'];
		$locidsArr[] = $aRow['location_id'];
	}
	mysql_data_seek($rResult, 0);
	/* Data set length after filtering */
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "SELECT COUNT(".$sIndexColumn.") FROM   $sTable $sWhere";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultFilterTotal[0];

	//$iTotal = mysql_num_rows($rResult);
	
//Find Issue to name and location name data here
	$locationTree = array();
	$locationTreeData = $common->selQRYMultiple('location_id, location_title, location_name_tree', 'project_locations', 'is_deleted = 0 AND project_id IN ('.$_REQUEST['projName'].') AND location_id IN ('.join(',', $locidsArr).')');
	foreach($locationTreeData as $locData){
		$locationTree[$locData['location_id']] = $locData['location_name_tree'];
	}
	
	$issueToNameData = array();
	$issueToData = $common->selQRYMultiple('inspection_id, GROUP_CONCAT(issued_to_name SEPARATOR "<br />") AS issuetoName, GROUP_CONCAT(inspection_fixed_by_date SEPARATOR "<br />") AS fixedbydate, GROUP_CONCAT(inspection_status SEPARATOR "<br />") AS stats', 'issued_to_for_inspections', 'is_deleted = 0 AND project_id IN ('.$_REQUEST['projName'].') AND inspection_id IN ('.join(',', $inspArr).') GROUP BY inspection_id');
	foreach($issueToData as $issData){
		$issueToNameData[$issData['inspection_id']] = array($issData['issuetoName'], $issData['fixedbydate'], $issData['stats']);
	}
	
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
#echo '<pre>'; while ($aRow = mysql_fetch_array($rResult)){ print_r($aRow); } echo '</pre>';die;
	function userDate($date){
		return date("d/m/Y", strtotime($date));
	}

	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		$check_list_item_id = $aRow["check_list_item_id"];
		for($i=0; $i<count($aColumns); $i++){
			if ( $aColumns[$i] == "I.inspection_id" ){
				$currentInspID = $aRow['inspection_id'];
			}
			if ( $aColumns[$i] == "I.location_id" ){
				$Location = $locationTree[$aRow['location_id']];;
				$Location = stripslashes(wordwrap($Location, 25, '<br />')); 
			}
			else if ( $aColumns[$i] == "I.inspection_id" ){
				$inspection_id = $aRow['inspection_id'];
				$issued_to_name = $issueToNameData[$currentInspID][0];
				$fix_by_date = implode("<br />", array_map('userDate', explode("<br />", $issueToNameData[$currentInspID][1])));
				$issueToData = array();
				$statusArr = explode("<br />", $issueToNameData[$currentInspID][2]);
				$status = "";
				for($j=0; $j<sizeof($statusArr); $j++){
					if ($status == ""){
						$status = $statusArr[$j];
					}else{
						$i_status = $statusArr[$j];

						if ($i_status == "Open"){
							$status = "Open";
						}elseif($i_status == "Pending" && ($status == "Fixed" || $status=="Closed") && $status!="Open" && $status!="Draft"){
							$status = 'Pending';
						}elseif ($i_status == "Fixed" && ($status == "Closed") && ($status!="Open" and $status!="Pending" and $status!="Draft")){
							$status = "Fixed";
						}elseif ($status!="Fixed" && ($status!="Open" and $status!="Pending" and $status!="Draft")){
							$status = "Closed";
						}
					}
				}
				
				if($status == 'Open'){
					$status_color = 'colOpen';
				}elseif($status == 'Draft'){
					$status_color =  'colDraft';
				}elseif($status == 'Closed'){
					$status_color =  'colClosed';
				}elseif($status == 'Pending'){
					$status_color =  'colPending';
				}elseif($status == 'Fixed'){
					$status_color = 'colFixed';
				} 
			}
			else if ( $aColumns[$i] == "I.inspection_inspected_by" ){
				$inspection_inspected_by = $aRow['inspection_inspected_by'];
			}
			if ( $aColumns[$i] == "I.inspection_description" ){
				$inspection_description = $aRow['inspection_description'];
			}
			else if ( $aColumns[$i] == "I.inspection_raised_by" ){
				$inspection_raised_by =  $aRow['inspection_raised_by'];
			}
			else if ( $aColumns[$i] == "I.inspection_id" ){
				$inspectionIDEdit =  base64_encode($aRow['inspection_id']);
			}
		}
		$row[] = $status_color;
		$row[] = $closePemission[0]['is_allow'];
		
		$row[] = $inspection_id;
		
		$c_image = '';
		if ($check_list_item_id > 0){
			$c_image = "<img src='images/c.png' valign='bottom'/>";
		}
		if($status == 'Closed' || $permProjArr[$aRow['project_id']] == 0){
			$row[] = '<input type="checkbox" class="closeInspection" name="inspectionID[]" id="inspid_'.$inspection_id.'" style="margin-left:20px;" value="'.$inspection_id.'" disabled="disabled">&nbsp;&nbsp;&nbsp;' . $c_image;
		}else{
			$row[] = '<input type="checkbox" class="closeInspection" name="inspectionID[]" id="inspid_'.$inspection_id.'" style="margin-left:20px;" value="'.$inspection_id.'">&nbsp;&nbsp;&nbsp;' . $c_image;
		} 
		
		$row[] = $Location;
		$row[] = $fix_by_date;
		$row[] = $issued_to_name;
		$row[] = $inspection_description;
		$row[] = $inspection_raised_by;
		$row[] = '<a href="pms.php?sect=show_defect_photo&id='.$inspectionIDEdit.'" style="margin-left:35px;"><img src="images/d_photo.png" border="none" /></a>';
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>