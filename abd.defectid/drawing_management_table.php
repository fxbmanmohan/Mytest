<?php 
	session_start();
	include("./includes/functions.php");
	include_once("includes/commanfunction.php");
	$obj = new DB_Class();
	$object = new COMMAN_Class();
	$pr_id = $_SESSION['idp']; 
	$project_name = $object->selQRYMultiple('project_name','projects','project_id='.$pr_id.'');
	//print_r($_REQUEST['location_id']);die;
	$aColumns = array( 'draw_mgmt_images_title','draw_mgmt_images_description','draw_mgmt_images_tags','draw_mgmt_images_thumbnail','draw_mgmt_images_id');
	
	$sIndexColumn = "draw_mgmt_images_id";

	$sTable = "draw_mgmt_images";
	
	$sWhere = "is_deleted = 0 AND project_id=".$pr_id."";
	if ( $_GET['sSearch'] != "" ){
		//$sWhere = "WHERE is_deleted=0 AND master_issue_id = '".trim($_REQUEST['issueToId'])."' AND (";
		$sWhere .= " AND (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
			$sWhere .= " AND ";
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	if ( isset( $_GET['iSortCol_0'] ) ){
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ){
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}

		$sOrder = substr_replace( $sOrder, "", -2 );

		if ( $sOrder == "ORDER BY" ){
			$sOrder = "ORDER BY draw_mgmt_images_id";
		}
	}
	//$sWhere = " is_deleted = 0";
	//$sOrder = " id DESC";
	//$sLimit = " LIMIT 0,10";
	
	 $sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		WHERE
		$sWhere
		$sOrder
		$sLimit";
	$rResult = $obj->db_query($sQuery ) or die(mysql_error());
	/* Data set length after filtering */
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "SELECT COUNT(".$sIndexColumn.") FROM $sTable WHERE $sWhere";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		#print_r($aRow);
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			
			if($aColumns[$i] == "draw_mgmt_images_title" )
			{
				$draw_mgmt_images_title = $aRow[ $aColumns[$i] ];
			}
			if($aColumns[$i] == "draw_mgmt_images_name" )
			{
				$draw_mgmt_images_name = $aRow[ $aColumns[$i] ];
			}
			if($aColumns[$i] == "draw_mgmt_images_thumbnail" )
			{
				$draw_mgmt_images_thumbnail_data =  $aRow[ $aColumns[$i] ];
				$draw_mgmt_images_thumbnail = "<img src='project_drawings/".$pr_id."/thumbnail/".$aRow[ $aColumns[$i] ]."'>";
			}
			if($aColumns[$i] == "draw_mgmt_images_description" )
			{
				$draw_mgmt_images_description = $aRow[ $aColumns[$i] ];
			}
			if($aColumns[$i] == "draw_mgmt_images_tags" )
			{
				wordwrap($draw_mgmt_images_tags = $aRow[ $aColumns[$i] ], 75, "<br />", true);
			}
			if ( $aColumns[$i] == "draw_mgmt_images_id" ){
				$rowID = $aRow[ $aColumns[$i] ];
				$rmvId = "'project_drawings/".$pr_id."/thumbnail/".$draw_mgmt_images_thumbnail_data."'";
				if($aRow[ $aColumns[($i-1)] ] >= 0){
					$action = '<img class="action" src="images/view.png"  id="viewDrawing" title="View drawing" onclick="showPhoto('.$pr_id.', '.$rowID.')" />&nbsp;<img class="action" src="images/edit_right.png"  id="editDrawing" title="Edit drawing" onclick="editDrawingReg('.$pr_id.', '.$rowID.')" />&nbsp;<img class="action" src="images/delete.png"  id="deletDrawing" title="Delete drawing" onclick="removeDrawing('.$rmvId.', '.$rowID.')" />';
				}
				
			}
		}
		#	$row[] = $draw_mgmt_images_name;
		$row[] = $draw_mgmt_images_title;
		$row[] = $draw_mgmt_images_description;
		$row[] = $draw_mgmt_images_tags;
		//$row[] = $draw_mgmt_images_thumbnail;
		$row[] = $action;
		
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>