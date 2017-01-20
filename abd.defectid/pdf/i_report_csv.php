<?php
session_start();
include('../includes/commanfunction.php');
if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}
elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

$obj= new COMMAN_Class();
$issued_to_add = '';

	if(!empty($_REQUEST['projName'])){
		$where=" and I.project_id='".$_REQUEST['projName']."'";
	}
	if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		$postCount++;
		$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['location'], ", ").")";
	}
	if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
		$postCount++;
		$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['sub_subLocation'], ", ").")";
	}else{
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
			$postCount++;
			$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['subLocation'], ", ").")";
		}
	}
	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and F.inspection_status='".$_REQUEST['status']."'";
	}
	if(!empty($_REQUEST['inspectedBy'])){
		$postCount++;
		$where.=" and I.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
	}
	if($_REQUEST['issuedTo']!=""){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['issuedTo']);
		$mulIssueToWhere = '';
		$mulIssueTo = '';
		$loopMul = count($isMul);
		for($g=0; $g<$loopMul; $g++){
			if($mulIssueToWhere == ""){
				$mulIssueTo = "'".$isMul[$g]."'";
				$mulIssueToWhere .= " (F.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR F.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
			}else{
				$mulIssueTo .= ", '".$isMul[$g]."'";
				$mulIssueToWhere .= " OR  (F.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR F.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
			}
		}
		$where.=" AND (".$mulIssueToWhere.") AND F.inspection_id = I.inspection_id";
	}
	if($_REQUEST['inspecrType']!=""){
		$postCount++;
		$where.=" and I.inspection_type='".$_REQUEST['inspecrType']."'";
	}
	if(!empty($_REQUEST['costAttribute'])){
		$postCount++;
		$where.=" and F.cost_attribute = '".$_REQUEST['costAttribute']."'";
	}
	
	if(!empty($_SESSION['userRole'])){
		if($_SESSION['userRole'] != 'All Defect' && $_SESSION['userRole'] != "Sub Contractor"){
			$where.=" and I.inspection_raised_by = '".$_SESSION['userRole']."'";
		}else{
			$postCount++;
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}else{
		$postCount++;
		if($_REQUEST['raisedBy'] != 'All Defect')
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
	}
	

	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$postCount++;
		$or.=" I.inspection_date_raised between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$postCount++;
		$or.=" F.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
	}
	
	if(!empty($_REQUEST['searchKeyward'])){
		$postCount++;
		$where.=" and I.inspection_location LIKE '%".$_REQUEST['searchKeyward']."%'";
	}
	
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.="  and I.inspection_id = F.inspection_id AND (F.issued_to_name LIKE '".$_SESSION['userIssueTo']." (%' OR F.issued_to_name LIKE '".$_SESSION['userIssueTo']."' ) ";
	}
	
	$where .=  " and I.inspection_type!='Memo'";

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	
	$orderby = "";	
	if ($_REQUEST["sortby"]){
		if ($_REQUEST["sortby"] == "location_id")
			$orderby .= ", I.inspection_location";
		else if ($_REQUEST["sortby"] == "issued_to_name")
			$orderby .= ",  F.issued_to_name";
		else
			$orderby .= ", I.".$_REQUEST["sortby"];
	}

	/*if ($_REQUEST["sortby"])
	{
		$orderby = "order by I." . $_REQUEST["sortby"];
	}*/

if($report_type == "pdfSummayWithImages"){$pageBreak = 11; $limit = ($pageBreak*10);}else{$pageBreak = 19; $limit = ($pageBreak*10);}	

	$qi="SELECT
		P.project_name as Project,
		I.inspection_id as InspectionId,
		I.location_id as Location,
		I.inspection_date_raised as DateRaised,
		I.inspection_inspected_by as InspectedBy,
		I.inspection_type as InspectonType,
	I.inspection_raised_by as RaisedBy,
		F.issued_to_name as IssueToName,
		F.inspection_fixed_by_date as FixedByDate,
		F.cost_attribute as CostAttribute,
		F.inspection_status as Status,
		I.inspection_description as Description,
		I.inspection_notes as Note,
		F.inspection_id as InspectionId_FOR,
		F.cost_impact_type as CostImpact,
		F.cost_impact_price as CostImpactPrice
	FROM
		user_projects as P, issued_to_for_inspections as F,
		project_inspections as I
	WHERE
		I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' $where group by I.inspection_id $orderby";

$result = mysql_query($qi);
$output = '';
function echocsv($fields){
	$op = '';
	$separator = '';
	foreach ($fields as $field){
		if(preg_match('/\\r|\\n|,|"/', $field)){
			$field = '"' . str_replace( '"', '""', $field ) . '"';
		}
		$op .= $separator . $field;
		$separator = ',';
	}
	$op .= "\r\n";
	return $op;
}
$fileName = 'Report_'.microtime().'.csv';
$noofRecord = mysql_num_rows($result);
$row = mysql_fetch_assoc($result);

if($row){
	$header = array('Project', 'Inspection Id', 'Location', 'Date Raised', 'Inspected By', 'Inspecton Type', 'Raised By', 'Issue To', 'Fixed By Date', 'Cost Attribute', 'Status', 'Description', 'Note', 'Cost Impact', 'Cost Impact Price');
    #echocsv(array_keys($row));
	$output .= echocsv($header);

	while($row){
		$issueToData = $obj->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status', 'issued_to_for_inspections', 'inspection_id = '.$row['InspectionId'] . ' and is_deleted=0');
		
		if(!empty($_POST['costAttribute'])){
			$issueToData = $obj->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status', 'issued_to_for_inspections', 'inspection_id = '.$row['InspectionId'].' and cost_attribute = "'.$_POST['costAttribute'].'" and is_deleted=0');
		}
		
		if($_POST['issuedTo']!=""){
			$issueToData = $obj->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status', 'issued_to_for_inspections', 'inspection_id = '.$row['InspectionId'].' and issued_to_name = "'.$_POST['issuedTo'].'" and is_deleted=0');
		}
		
		if(!empty($_POST['status'])){
			$issueToData = $obj->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status', 'issued_to_for_inspections', 'inspection_id = '.$row['InspectionId'].' and inspection_status = "'.$_POST['status'].'" and is_deleted=0');
		}

		$issueToData_issueToName = ""; $issueToData_fixedByDate= ""; $issueToData_status= "";$issueToData_costAttribute = "";
		if(!empty($issueToData)){
			foreach($issueToData as $issueData){
				if($issueToData_issueToName == ''){
					$issueToData_issueToName = stripslashes($issueData['issued_to_name']);
				}else{
					$issueToData_issueToName .= ' > '.stripslashes($issueData['issued_to_name']);
				}

				if($issueToData_fixedByDate == ''){
					$issueData['inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate = stripslashes(date("d/m/Y", strtotime($issueData['inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
				}else{
					$issueData['inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate .= ' > '.stripslashes(date("d/m/Y", strtotime($issueData['inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
				}
			
				if($issueToData_status == ''){
					$issueToData_status = stripslashes($issueData['inspection_status']);
				}else{
					$issueToData_status .= ' > '.stripslashes($issueData['inspection_status']);
				}
				
				if($issueToData_costAttribute == ''){
					$issueToData_costAttribute = stripslashes($issueData['cost_attribute']);
				}else{
					$issueToData_costAttribute .= ' > '.stripslashes($issueData['cost_attribute']);
				}
			}
			if($row['DateRaised'] != '0000-00-00'){
				$row['DateRaised'] = date("d/m/Y", strtotime($row['DateRaised']));
			}
			$row['FixedByDate'] = $issueToData_fixedByDate;
			$row['IssueToName'] = $issueToData_issueToName;
			$row['CostAttribute'] = $issueToData_costAttribute;
			$row['Status'] = $issueToData_status;
		}

		$row['Location'] = $obj->subLocations($row['Location'], ' > ');
		//$row['InspectionId'] = '';
		$row['InspectionId_FOR'] = '';
		$output .= echocsv($row);
		$row = mysql_fetch_assoc($result);
	}
	
	$d = '../report_csv/'.$owner_id;
	if(!is_dir($d))
		mkdir($d);
	if (file_exists($d.'/'.$fileName))
		unlink($d.'/'.$fileName);
	$tempFile = $d.'/'.$fileName;
	$fh = fopen($tempFile, 'w') or die("can't open file");
	$stringData = $output;
	fwrite($fh, $stringData);
	fclose($fh);
	
	$fieSize = filesize($tempFile);
	
	$fieSizeDisplay = floor($fieSize/(1024));
		
	if ($fieSizeDisplay > 1024){
		$fieSizeDisplay = floor($fieSizeDisplay/(1024)) . "Mbs";
	}else{
		if($fieSize < 1024){
			$fieSizeDisplay = $fieSize . "Bytes";
		}else{
			$fieSizeDisplay .= "Kbs";
		}
	}

	$rply = $noofRecord.' Records '. $fieSizeDisplay;
	echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="clearDivCSV();" href="./report_csv/'.$owner_id.'/'.$fileName.'" target="_blank" class="view_btn"></a></div>';
}?>