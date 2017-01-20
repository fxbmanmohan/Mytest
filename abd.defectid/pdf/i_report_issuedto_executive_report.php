<?php
ob_start();
error_reporting(0);
session_start();
set_time_limit(6000000000000000000);

include('../includes/commanfunction.php');
$obj= new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";}
	
	$limit = 19;
	$issued_to_add = '';
	if(!isset($_REQUEST['startWith'])){
		$offset = 0;
	}
	if($_REQUEST['startWith'] == 0){
		$offset = 0;
	}else{
		$offset = $_REQUEST['startWith'];
		$offsetPage = ceil($offset/2);
	}
	$postCount = 0;

	$projID = ''; $location = ''; $subLocation1 = ''; $subLocation2 = ''; $filterLoc =''; $locStr = '';
	
	if(!empty($_REQUEST['projName'])){
		$projID = $_REQUEST['projName'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name');
		$where=" and pi.project_id='".$_REQUEST['projName']."'";

		$projectDetails = $obj->selQRYMultiple('project_name, certificate_of_occupancy, open_date, proposed_completion, actual_completion', 'projects', 'project_id = '.$_REQUEST['projName'].' AND is_deleted = 0');
		if(!empty($projectDetails)){
		$certificate_of_occupancy = ($projectDetails[0]['certificate_of_occupancy']!='0000-00-00' && !empty($projectDetails[0]['certificate_of_occupancy']))?date('d/m/Y', strtotime($projectDetails[0]['certificate_of_occupancy'])):'';
		 $open_date = ($projectDetails[0]['open_date']!='0000-00-00' && !empty($projectDetails[0]['open_date']))?date('d/m/Y', strtotime($projectDetails[0]['open_date'])):'';
		 $proposed_completion=($projectDetails[0]['proposed_completion']!='0000-00-00' && !empty($projectDetails[0]['proposed_completion']))?date('d/m/Y', strtotime($projectDetails[0]['proposed_completion'])):'';
		 $actual_completion = ($projectDetails[0]['actual_completion']!='0000-00-00' && !empty($projectDetails[0]['actual_completion']))?date('d/m/Y', strtotime($projectDetails[0]['actual_completion'])):'';									
		}			
		$searchLoc = 0;
	}
	
	
	if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		$postCount++;
		$where.=" and pi.location_id in (".$obj->subLocationsId($_REQUEST['location'], ", ").")";
	}
	if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
		$postCount++;
		$where.=" and pi.location_id in (".$obj->subLocationsId($_REQUEST['sub_subLocation'], ", ").")";
	}else{
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
			$postCount++;
			$where.=" and pi.location_id in (".$obj->subLocationsId($_REQUEST['subLocation'], ", ").")";
		}
	}
	
	$queryLoc = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'location_parent_id = '.$searchLoc.' AND is_deleted = 0 AND project_id = "'.$projID.'" order by location_id');
/*	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and F.inspection_status='".$_REQUEST['status']."'";
	}*/
	
	if(!empty($_REQUEST['inspectedBy'])){
		$postCount++;
		$where.=" and pi.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
	}
	
/*	if($_REQUEST['issuedTo']!=""){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['issuedTo']);
		$mulIssueTo = '';
		$loopMul = count($isMul);
		for($g=0; $g<$loopMul; $g++){
			if($mulIssueTo == ''){
				$mulIssueTo = "'".$isMul[$g]."'";
			}else{
				$mulIssueTo .= ", '".$isMul[$g]."'";
			}
		}
		$where.=" and isi.issued_to_name IN (".$mulIssueTo.") and isi.inspection_id = pi.inspection_id";
	}*/
	
	if($_REQUEST['issuedTo']!=""){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['issuedTo']);
		$mulIssueToWhere = '';
		$mulIssueTo = '';
		$loopMul = count($isMul);
		for($g=0; $g<$loopMul; $g++){
			if($mulIssueToWhere == ""){
				$mulIssueTo = "'".$isMul[$g]."'";
				$mulIssueToWhere .= " (isi.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR isi.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
			}else{
				$mulIssueTo .= ", '".$isMul[$g]."'";
				$mulIssueToWhere .= " OR  (isi.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR isi.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
			}
		}
		$where.=" AND (".$mulIssueToWhere.") AND isi.inspection_id = pi.inspection_id";
	}
	
	if($_REQUEST['inspecrType']!=""){
		$postCount++;
		$where.=" and pi.inspection_type='".$_REQUEST['inspecrType']."'";
	}
	
	if(!empty($_REQUEST['costAttribute'])){
		$postCount++;
		$where.=" and isi.cost_attribute = '".$_REQUEST['costAttribute']."'";
	}

	if(!empty($_SESSION['userRole'])){
		if($_SESSION['userRole'] != 'All Defect' && $_SESSION['userRole'] != "Sub Contractor"){
			$where.=" and pi.inspection_raised_by = '".$_SESSION['userRole']."'";
		}else{
			$postCount++;
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and pi.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}else{
		$postCount++;
		if($_REQUEST['raisedBy'] != 'All Defect')
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and pi.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
	}

	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$postCount++;
		$or.=" pi.inspection_date_raised between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$postCount++;
		$or.=" isi.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
	}
	
	$orderby = "";	
	
	if ($_REQUEST["sortby"]){
		$orderby = "order by pi." . $_REQUEST["sortby"];
	}
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" and  isi.issued_to_name = '".$_SESSION['userIssueTo']."'";
	}

	if(!empty($_REQUEST['searchKeyward'])){
		$postCount++;
		$locationRows = $obj->selQRYMultiple ("location_id", "project_locations", "project_id=".$_REQUEST['projName'] . " and location_title LIKE '%".$_REQUEST['searchKeyward']."%' and is_deleted=0" );
		$location_id_arr = array();
		foreach ($locationRows as $locationID){
			$location_id_arr[] = $locationID["location_id"];	
		}
		$where.=" and (isi.issued_to_name LIKE '%".$_REQUEST['searchKeyward']."%' OR pi.location_id in (".join(",", $location_id_arr) ."))";
	}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	
//Retrive Location array with Title Start Here
	$locSting = $obj->subLocationsId($filterLoc, ", ");
	if($locStr != ''){
		$locSearch = $locStr.', '.$locSting;
	}else{
		$locSearch = $locSting;
	}
	$whreCon = '';
	if($locSearch != ''){
		$whreCon = 'location_id IN ('.$locSearch.') AND project_id = '.$projID.' AND is_deleted = 0';
	}else{
		$whreCon = 'project_id = '.$projID.' AND is_deleted = 0';
	}
	
	$locDataArray = $obj->selQRYMultiple('location_id, location_title', 'project_locations', $whreCon);
	$locArrayData = array();
	if(!empty($locDataArray)){
		foreach($locDataArray as $ldata){
			$locArrayData[$ldata['location_id']] = $ldata['location_title'];
		}
	}
//Retrive Location array with Title End Here

//Retrive Location Tree and Data Start Here
	$proLocArray = array();
	if(!empty($queryLoc)){
		$totalCount = sizeof($queryLoc);
		$noInspection = sizeof($queryLoc);
		foreach($queryLoc as $locId){
			$pt = $obj->getCatIdsExport($locId["location_id"]);
			$a = explode(" > ", $pt);
			$locArray = array();
			for ($i=0;$i<count($a);$i++){
				if (empty($a[$i])){ continue; }
				$childLoc = '';	
				$childLoc = $obj->getDataByKey('project_locations', 'location_parent_id', $a[$i], 'location_id');
				if($childLoc!=''){ continue; }
				$locations = $obj->subLocationsIDS($a[$i], ' > ');
				$tmparr = explode(" > ", $locations);
				$sub_c = 1;
				$sub_arr = array();
				$sub_arr[] = $tmparr[0];
				for ($j=1;$j<count($tmparr);$j++){
					$sub_arr[] = $tmparr[$j];
				}
				$locArray[] = $sub_arr;
			}
			$proLocArray[] = $locArray;
		}
	}
	$htmlInner = ''; $OverAllCount = 0;
	if(!empty($proLocArray)){
		
				$inspectionData = $obj->selQRYMultiple('isi.issued_to_name as issuedName, SUM(IF(isi.inspection_status= "Open",1,0)) AS open,
					SUM(IF(isi.inspection_status="Pending",1,0)) AS pending,
					SUM(IF(isi.inspection_status="Fixed",1,0)) AS fixed,
					SUM(IF(isi.inspection_status="Closed",1,0)) AS closed',
					'issued_to_for_inspections AS isi, project_inspections pi',
					'pi.inspection_id = isi.inspection_id AND
					isi.project_id = '.$projID.' AND
					isi.is_deleted = 0 AND
					pi.is_deleted = 0
					'.$where." and isi.issued_to_name!='' group by isi.issued_to_name order by isi.issued_to_name" );

			$proInsData[] = $inspectionData;
	}
	
	
$ajaxReplay = $OverAllCount.' Records';
$noPages = ceil(($totalCount-1)/2 +1);
if($noInspection > 0){
	require('../fpdf/fpdf.php');	
	class PDF extends FPDF{
			function Header(){// Page header
				if($this->PageNo()!=1){// Page number
					$this->Cell(0, 10, 'Page: '.$this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
					$this->ln();	
					$this->SetFont('times', 'B', 10);
					$header = array("Issued to", "Certificate of occupancy", "Proposed completion", "Actual completion", "Number of inspections", "Open", "Closed", "Pending");
					$color = array("190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190");
					$w = $this->header_width();
					$this->SetWidths($w);
					//$best_height = 7;
					$this->addColorTable($header, $color, 20);							
				}
			}
			function Footer(){// Position at 1.5 cm from bottom
				$this->SetY(-15);
				$this->SetFont('times','B',10);
				$curWidth = $this->GetX()+70;
				$curHeight = $this->GetY();
				$this->Image('../company_logo/logo.png', $curWidth, $curHeight, 50, 0, 'png');
				/*$this->SetFont('helvetica', 'B', 10);
				$this->Cell(10);
				$this->Cell(15, 4, 'DefectID,  part of the Wiseworker Quality Management Ecosystem,  helping the construction industry.', 0, 0);
				$this->Ln(5);
				$this->Cell(76);
				$this->Cell(60, 4, 'www.wiseworker.net', 0, 0);*/
			}
			function header_width(){
				return array(50, 22, 22, 22, 22, 18, 18, 18);
			}		
			function addColorTable($data, $color, $lastWidth=12){
		//Calculate the height of the row
		//kamal , new code to fix 
		// 1. Image overflow
		// 2. page break
		// 3. and height of the row  
		$nb=0;
		$image_height=0;
		for($i=0;$i<count($data);$i++){
			if (strpos($data[$i], "IMAGE##") > -1){
				$ar = explode("##", $data[$i]);
				$image_height=$ar[2]+2; // we have added 2 for a margin
				$nb=max($nb,$this->NbLines($this->widths[$i],$image_height));
			}else{
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i])); 
			}
		}
		$h = 7 * $nb;
		$h=max($h,$image_height);
		//end of new code
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++){
			if($i==(count($data)-1)){
				$w = $lastWidth;
			}else{
				$w = $this->widths[$i];
			}
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();

			//Print the text
			if (strpos($data[$i], "IMAGE##") > -1){
				$tmp = explode("##", $data[$i]);
				$data[$i] = $tmp[3];
				$this->MultiCell($w, 5, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2]),0,'C');
				$this->Rect($x,$y,$tmp[1],$tmp[2],'DF');
			}else{
				if(isset($color[$i]) && !empty($color[$i])){
					$c = explode(", ",$color[$i]);
					$c[0] = isset($c[0])?$c[0]:204;
					$c[1] = isset($c[1])?$c[1]:255;
					$c[2] = isset($c[2])?$c[2]:255;						
					//print_r($c);die;
					$this->SetFillColor($c[0], $c[1], $c[2]);
					//Draw the border
					$this->Rect($x,$y,$w,$h,'DF');
				}else{
					//Draw the border
					$this->Rect($x,$y,$w,$h);
				}
				$this->MultiCell($w,7,$data[$i],0,$a);
			}
			//$this->MultiCell($w,5,$data[$i],0,$a);	
			//Put the position to the right of the cell
			$this->SetXY($x+$w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}					
		}

	$pdf = new PDF();
	$pdf->AddPage();
	$pdf->AliasNbPages();

	$pdf->Image('../company_logo/logo.png',  135,  12, 65);
	$pdf->Ln(8);

	$pdf->SetFont('times', 'BU', 12);
	$pdf->Cell(40, 10, 'Executive (Issued to) Report');		
	$pdf->Ln(6);

	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(26, 10, 'Project Name : ');	

	$pdf->SetFont('times', '', 10);
	$pdf->Cell(10, 10, $projectName);	
	$pdf->Ln(5);

	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(11, 10, 'Date : ');	
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(20, 10, date('d/m/Y'));	
	$pdf->Ln(5);

	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(11, 10, 'Page : ');		
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(8, 10, '1 of '.'{nb}');		
	$pdf->Ln(10);
	
	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(25, 10, 'Report Filtered by :');	
	$pdf->Ln(8);
	$jk=0;

	$pdf->Cell(15, 10, '');	
	
	$x0 = $x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$jk=0;	
	if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Location Name: ');	
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$locArrayData[$_REQUEST['location']]);	
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Location Name: ');		
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$locArrayData[$_REQUEST['location']]);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Sub Location 1: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$locArrayData[$_REQUEST['subLocation']]);
		
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
		
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Sub Location 2: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$locArrayData[$_REQUEST['sub_subLocation']]);
			
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}else{
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
			$pdf->SetFont('times','B',11);		
			$pdf->Cell(50,10,'Location Name: ');
			$pdf->SetFont('times','',10);
			$pdf->Cell(25,10,$locArrayData[$_REQUEST['location']]);
			
			$jk++; if($jk%2 ==0){	$pdf->ln();}	
						
			$pdf->SetFont('times','B',11);		
			$pdf->Cell(50,10,'Sub Location: ');
			$pdf->SetFont('times','',10);
			$pdf->Cell(25,10,$locArrayData[$_REQUEST['subLocation']]);
			
			$jk++; if($jk%2 ==0){	$pdf->ln();}	
		}
	}
	
/*	if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Status: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['status']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}*/
	
	if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Inspected By: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['inspectedBy']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}

	if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Issue To: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(140,10,str_replace("'", "", $mulIssueTo));
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Priority: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['priority']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();}					
	}
	
	if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Inspection Type: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['inspecrType']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Raised By: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['raisedBy']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Cost Attribute: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['costAttribute']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Date Raised: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['DRF'].' to '.$_REQUEST['DRT']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Fixed By Date: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['FBDF'].' to '.$_REQUEST['FBDT']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	$pdf->ln();
	$first_time = 1;
	$pageCount = 1;
	$page_break = 1;
	$fill = true;
	$i = 0;
	$x0 = $x = $pdf->GetX();
	$y = $pdf->GetY();

	$x = 5;
	
	$pagebreakCount = 40;
		
	$pageCount = 0;$pageCount++;
					
	$yH = 5; //height of the row	
//Title String Header		
	$pdf->SetFont('times','B',10);
	$header = array("Issued to", "Certificate of occupancy", "Proposed completion", "Actual completion", "Number of inspections", "Open", "Closed", "Pending");
	$color = array("190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190", "190, 190, 190");
	$w = $pdf->header_width();
	$pdf->SetWidths($w);
	//$best_height = 7;
	$pdf->addColorTable($header, $color, 18);	
//Title String Header
	$pdf->SetFont('Times', '', 10);
	$openCount = 0;$closeCount = 0;$penndingCount = 0;
	$j = 0;
	$totNoOfInsp=0;
	$issueToCompanyName = '';
	$curentIssueToName = '';					
	$htmlDataArr = array();	
	
	foreach($proInsData as $proInsData0){
		foreach($proInsData0 as $piData){
			$issueToCompanyName = trim($piData['issuedName']);
			$curentIssueToName = strtolower(trim($piData['issuedName']));
			if(explode(' (', $piData['issuedName'])){
				$arr = explode(' (', $piData['issuedName']);
				$issueToCompanyName = trim($arr[0]);
				$curentIssueToName = strtolower(trim($arr[0]));
			}

			$noOfInsp = $piData['open']+$piData['pending']+$piData['closed'];
			$totNoOfInsp = $totNoOfInsp + $noOfInsp;			
			$openCount=$openCount + $piData['open'];
			$closeCount=$closeCount + $piData['closed'];
			$penndingCount=$penndingCount + $piData['pending'];								
			if(isset($htmlDataArr[$curentIssueToName]) && !empty($htmlDataArr[$curentIssueToName])){
				$htmlDataArr[$curentIssueToName]['issuedName'] = $issueToCompanyName;
				$htmlDataArr[$curentIssueToName]['noOfInsp'] += $noOfInsp;
				$htmlDataArr[$curentIssueToName]['open'] += $piData['open'];
				$htmlDataArr[$curentIssueToName]['closed'] += $piData['closed'];
				$htmlDataArr[$curentIssueToName]['pending'] += $piData['pending'];
			}else{
				$htmlDataArr[$curentIssueToName]['issuedName'] = $issueToCompanyName;
				$htmlDataArr[$curentIssueToName]['noOfInsp'] = $noOfInsp;
				$htmlDataArr[$curentIssueToName]['open'] = $piData['open'];
				$htmlDataArr[$curentIssueToName]['closed'] = $piData['closed'];
				$htmlDataArr[$curentIssueToName]['pending'] = $piData['pending'];
			}		
			/*$noOfInsp = $piData['open']+$piData['pending']+$piData['closed'];
			$totNoOfInsp = $totNoOfInsp + $noOfInsp;
			
			$pdf->Cell(90, $yH, $piData['issuedName'], 1, 0, 'L', false);
			$pdf->Cell(50, $yH, $noOfInsp, 1, 0, 'C', false);
			$pdf->Cell(20, $yH, $piData['open'], 1, 0, 'C', false);
			$openCount=$openCount + $piData['open'];
			$pdf->Cell(20, $yH, $piData['closed'], 1, 0, 'C', false);
			$closeCount=$closeCount + $piData['closed'];
			$pdf->Cell(20, $yH, $piData['pending'], 1, 0, 'C', false);
			$penndingCount=$penndingCount + $piData['pending'];*/
		}
	}
	
	foreach($htmlDataArr as $htmlData){
		$j++;
		//$y = $y + $yH;
		//$pdf->SetXY($x, $y);
		if($pageCount == 1){
			if($j == 40){
				$pageCount++;
			}
		}else{
			if(($j%$pagebreakCount) == 0){
				$pageCount++;
			}
		}		
			$pdf->addColorTable(array($htmlData['issuedName'], $certificate_of_occupancy, $proposed_completion, $actual_completion, $htmlData['noOfInsp'], $htmlData['open'], $htmlData['closed'], $htmlData['pending']), array(), 18);		
	}		
	
	$pdf->SetFont('times','B',10);
	$header = array("", "", "", "Total", $totNoOfInsp, $openCount, $closeCount, $penndingCount);
	$w = $pdf->header_width();
	$pdf->SetWidths($w);
	//$best_height = 7;
	$pdf->addColorTable($header, $color, 18);	

	$file_name = 'Issuedto_Executive_Repprt_'.microtime().'.pdf';
	$d = '../report_pdf/'.$owner_id;
	if(!is_dir($d))
		mkdir($d);
	
	if (file_exists($d.'/'.$file_name))
		unlink($d.'/'.$file_name);
	
	$tempFile = $d.'/'.$file_name;
	$pdf->Output($tempFile);
	$fieSize = filesize($tempFile);
	$fieSize = floor($fieSize/(1024));
	if ($fieSize > 1024){
		$fieSize = floor($fieSize/(1024)) . "Mbs";
	}else{
		$fieSize .= "Kbs";
	}
	$rply = $ajaxReplay.' '.$fieSize;
	echo '<br clear="all" /><div style="margin-left:10px;"> <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
}else{
	echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
}
?>