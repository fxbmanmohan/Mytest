<?php 
include_once("functions.php");
include_once("SimpleImage.php");

$object= new DB_Class();
class COMMAN_Class{
	function getDataByKey($table, $searchKey, $searchValue, $expValue){
		//echo "SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and is_deleted = 0";
		$RS = mysql_query("SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and is_deleted = 0");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_array($RS)){
				return $ROW[0];
			}
		}else{
			return false;
		}
	}	

	function selQRY($select, $table, $where){
		//echo "SELECT ".$select." FROM ".$table." WHERE ".$where;
		$RS = mysql_query("SELECT ".$select." FROM ".$table." WHERE ".$where);
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				return $ROW;
			}
		}else{
			return false;
		}
	}	
	
	function selQRYMultiple($select, $table, $where){
#echo "SELECT ".$select." FROM ".$table." WHERE ".$where;
		$RS = mysql_query("SELECT ".$select." FROM ".$table." WHERE ".$where);
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}	
	
	function defthLocation($pId){
		$RS = mysql_query("SELECT location_title FROM ".PROJECTLOCATION." WHERE location_id = '".$pId."'");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_array($RS)){
				return $ROW[0];
			}
		}else{
			return false;
		}
	}
	
	function getRecords($table, $searchKey, $searchValue, $searchKey1, $searchValue1, $expValue){
		//echo "SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and ".$searchKey1." = '".$searchValue1."' and is_deleted = '0'";
		$RS = mysql_query("SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and ".$searchKey1." = '".$searchValue1."' and is_deleted = '0'");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}
	
	function getRecordsSp($table, $searchKey, $searchValue, $expValue){
		$RS = mysql_query("SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and is_deleted = '0'");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}
	
	function getCat($catId){
		$qry = 'select location_id, location_parent_id from project_locations where location_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->getCat($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}

	function getCatIds($catId){
		$qry = 'select location_id from project_locations where location_parent_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getCatIds($rows[0]), $path);
		}
		return $path;
	}
	
	function getCatIdsExport($catId){
		$qry = 'select location_id from project_locations where location_parent_id ='.$catId.' and is_deleted=0';
		$res = mysql_query($qry);
		$path = '';
		while($rows = mysql_fetch_array($res)){
			$path .= $rows[0];
			$path .= " > " . $this->getCatIdsExport($rows[0]);
		}
		return $path;
	}

	function getCatIdsProgressMonitoring($catId){
		$qry = 'select location_id from project_monitoring_locations where location_parent_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getCatIdsProgressMonitoring($rows[0]), $path);
		}
		return $path;
	}
	
	function subLocations($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCat($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("project_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("project_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function subLocationsIDS($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCat($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $dp;
			}else{
				$breadcrumb .=  $saprater . $dp;
			}
		}
		return $breadcrumb;
	}

	function subLocationsProgressMonitoring($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoring($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function getCatProgressMonitoring($catId){
		$qry = 'select location_id, location_parent_id from project_monitoring_locations where location_id ='.$catId . ' and is_deleted=0 and location_parent_id!=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->getCatProgressMonitoring($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}

	function subLocationsId($subLocation, $saprater){
		$breadcrumb = $subLocation;
		$depth = $this->getCatIds($subLocation);
		foreach($depth as $dp){
			$breadcrumb .= $saprater . $dp;
		}
		return $breadcrumb;
	}

	function subLocationsIdProgressMonitoring($subLocation, $saprater){
		$breadcrumb = $subLocation;
		$depth = $this->getCatIdsProgressMonitoring($subLocation);
		foreach($depth as $dp){
			$breadcrumb .= $saprater . $dp;
		}
		return $breadcrumb;
	}
	
	function imageExistsFolder($folderName, $fileName){
		$folder = opendir($folderName.'/');
#		echo $folderName.''.$fileName;
		$file_types = array("jpg", "jpeg", "gif", "png", "txt", "ico");
		while($file = readdir($folder)){
			if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $file_types)){
				if($fileName == $file){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function resizeImages($resizeSource, $rWidth, $rHeight, $resizeDestination){
		$simpleImage = new SimpleImage();
		if(file_exists($resizeSource)){
			$simpleImage->load($resizeSource);
			if($simpleImage->getWidth() >= $rWidth){
				if($simpleImage->getWidth() < $rWidth){//Resize by Height
					$simpleImage->resizeToHeight($rHeight);
					if ($simpleImage->getWidth() > $rWidth)
					{
						$simpleImage->resizeToWidth($rWidth);
					}
					$simpleImage->save($resizeDestination, IMAGETYPE_PNG);
				}else{//Resize by widtht
					$simpleImage->resizeToWidth($rWidth);
					if ($simpleImage->getHeight() > $rHeight)
					{
						$simpleImage->resizeToHeight($rHeight);
					}
					$simpleImage->save($resizeDestination, IMAGETYPE_PNG);
				}
			}else{
				$simpleImage->save($resizeDestination, IMAGETYPE_PNG);
			}
		}
		return true;
	}
	
	function create_zip($files = array(), $destination = '', $overwrite = true) {
		if(file_exists($destination) && !$overwrite) { return false; }
		$valid_files = array();
		if(is_array($files)) {
			foreach($files as $file) {
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		if(count($valid_files)) {
			$zip = new ZipArchive();
			if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			foreach($valid_files as $file) {
				$tmp = explode("/", $file);
				$filename = $tmp[count($tmp)-1];
				$zip->addFile($file, $filename);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			//close the zip -- done!
			$zip->close();
			return file_exists($destination);
		}else{
			return false;
		}
	}
	
	function dateChanger($cDelimeter, $eDelimeter, $cDate){
		$pDate = explode($cDelimeter, $cDate);
		$rDate = $pDate[2].$eDelimeter.$pDate[1].$eDelimeter.$pDate[0];
		return $rDate;
	}
	
	function searchArray($array, $val){
		$response = 0;
		foreach($array as $arr){
			if($val == $arr['project_id']){
				$response = 1;
			}
		}
		return $response;
	}
	
	function checklistStatus($projectID, $inspectionID){
		$rs = mysql_query("SELECT i.insepection_check_list_id, i.check_list_items_status, c.check_list_items_id FROM inspection_check_list as i, check_list_items as c WHERE i.project_id = ".$projectID." AND i.inspection_id = ".$inspectionID." AND i.is_deleted = 0 AND c.is_deleted = 0 AND i.check_list_items_status = 'NA' AND c.check_list_items_id = i.check_list_items_id");
#		print_r($checkListItemData);
		if(mysql_num_rows($rs) > 0){
			return 1;
		}else{
			return 0;
		}
	}
	
	function checklist($projectID, $inspectionID){
		$RS = mysql_query("SELECT insepection_check_list_id FROM inspection_check_list WHERE project_id = ".$projectID." AND inspection_id = ".$inspectionID." AND is_deleted = 0");
		if(mysql_num_rows($RS) > 0){
			return 1;
		}else{
			return 0;
		}
	}

	function checklist4Project($projectID){
		$RS = mysql_query("SELECT check_list_items_id FROM check_list_items WHERE project_id = ".$projectID." AND is_deleted = 0");
		if(mysql_num_rows($RS) > 0){
			return 1;
		}else{
			return 0;
		}
	}
	
	function recurtion($locationID, $projectID){
		$data='';
		$location = $this->selQRYMultiple('location_id, location_title', 'project_locations', 'location_parent_id = "'.$locationID.'" and is_deleted = "0" and project_id = "'.$projectID.'" order by location_title');
		if(!empty($location)){
			foreach($location as $loc){
				$data .= '<ul><li id="li_'.$locations['location_id'].'"><span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span>';
				$data .= '</li></ul>';
				return $data;
			}
		}
	}
	
	function resizeImagesGeneral($resizeSource, $rWidth, $rHeight, $resizeDestination){
		$simpleImage = new SimpleImage();
		if(file_exists($resizeSource)){
			$simpleImage->load($resizeSource);
			if($simpleImage->getWidth() >= $rWidth){
				if($simpleImage->getWidth() < $rWidth){//Resize by Height
					$simpleImage->resizeToHeight($rHeight);
					if ($simpleImage->getWidth() > $rWidth)
					{
						$simpleImage->resizeToWidth($rWidth);
					}
					$simpleImage->save($resizeDestination);
				}else{//Resize by widtht
					$simpleImage->resizeToWidth($rWidth);
					if ($simpleImage->getHeight() > $rHeight)
					{
						$simpleImage->resizeToHeight($rHeight);
					}
					$simpleImage->save($resizeDestination);
				}
			}else{
				$simpleImage->save($resizeDestination);
			}
		}
		return true;
	}

	function getParentChapter($subLocation, $saprater, $col){
		$breadcrumb = '';
		$depth = $this->getParentID($subLocation);
		foreach($depth as $dp){
			if($col != 'Title'){
				if ($breadcrumb == ""){
					$breadcrumb = $dp;
				}else{
					$breadcrumb .= $saprater . $dp;
				}
			}else{
				if ($breadcrumb == ""){
					$breadcrumb = $this->getDataByKey("manual_chapter", "chapter_id", $dp, "chapter_title");
				}else{
					$breadcrumb .= $saprater . $this->getDataByKey("manual_chapter", "chapter_id", $dp, "chapter_title");
				}
			}
		}
		return $breadcrumb;
	}

	function getParentID($catId){
		$qry = 'select chapter_id, chpter_parent_id from manual_chapter where chapter_id ='.$catId.' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->chapter_id;
				$path = array_merge($this->getParentID($row->chpter_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}
	
	function statusChecker($chapetID, $projID){
		$stData = $this->selQRYMultiple('id, authority', 'manual_chapter_data', 'authority = "No" AND project_id = '.$projID.' AND chaper_id = '.$chapetID.' AND is_deleted = 0');
		if(!empty($stData)){
			if($stData[0]['authority'] == 'No'){
				return true;
			}
		}
		return false;
	}

	function getChapterIds($chId, $projID){
		$qry = 'SELECT chapter_id FROM manual_chapter WHERE chpter_parent_id ='.$chId.' AND project_id = '.$projID.' AND is_deleted = 0';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getChapterIds($rows[0], $projID), $path);
		}
		return $path;
	}

	function subChapterID($chapter, $projID, $saprater){
		$breadcrumb = $chapter;
		$depth = $this->getChapterIds($chapter, $projID);
		foreach($depth as $dp){
			$breadcrumb .= $saprater . $dp;
		}
		return $breadcrumb;
	}
	
	function bulkInsert($insertArray, $colString, $table){//$insertArray is multidimension indexed array
		$objj = new DB_Class();
		$loopCount = count(explode(',', $colString));
		$recCount = sizeof($insertArray);
		$valStr = '';
		$valString = '';
		$count = 0;
		for($j=0; $j<$recCount; $j++){
			for($i=0; $i<$loopCount; $i++){
				if($valStr == ''){
					$valStr = '"'. addslashes (trim($insertArray[$j][$i])).'"';
				}else{
					$valStr .= ', "'. addslashes (trim($insertArray[$j][$i])).'"';
				}
			}
			if($valString == ''){
				$valString = 'SELECT '.$valStr;
			}else{
				$valString .= ' UNION ALL SELECT '.$valStr;
			}
			$valStr = '';
			$count ++;
			if ($count == 500){
				$insertQRY = 'INSERT INTO '.$table.' ('.$colString.') '.$valString;
				$insertQRYNew = str_replace(array('"Now()",', '"now()",', '"NOW()",'), array('NOW(),', 'NOW(),', 'NOW(),'), $insertQRY);
				$res = $objj->db_query($insertQRYNew);
				if (!$res){//echo mysql_error();//die;
				}
				$count = 0;
				$valString = "";
			}
		}
		if ($count < 500 && $count > 0){
			$insertQRY = 'INSERT INTO '.$table.' ('.$colString.') '.$valString;
			$insertQRYNew = str_replace(array('"Now()",', '"now()",', '"NOW()",'), array('NOW(),', 'NOW(),', 'NOW(),'), $insertQRY);
			$res = $objj->db_query($insertQRYNew);
			if (!$res){//echo mysql_error();
			}
		}
		return true;
	}
	
	function checkInsetIfNotExistLoc($tableName, $colName, $val, $colName1, $val1, $expColName, $builderID, $proID, $locArray){
		$values = '';
		$QURY = "SELECT ".$expColName." FROM ".$tableName." WHERE project_id = ".$proID." AND ".$colName." = '".$val."' AND ".$colName1." = '".$val1."' AND is_deleted = 0 LIMIT 0, 1";
		$RES = mysql_query($QURY);
		if(mysql_num_rows($RES) > 0){
			while($ROW = mysql_fetch_assoc($RES)){
				$values = $ROW[$expColName];
			}
		}
		if($values != ''){
			$locId = $values;
		}else{
			$insertQRY = "INSERT INTO ".$tableName." SET
							project_id = ".$proID.",
							".$colName." = ".trim($val).",
							".$colName1." = '".addslashes(trim($val1))."',
							last_modified_date = NOW(),
							last_modified_by = '".$builderID."',
							created_date = NOW(),
							created_by = '".$builderID."'";
			mysql_query($insertQRY);
			$locId = mysql_insert_id();
		}
		if($locArray == ''){
			return $locId;
		}else{
			$path = array();
			for($m=0; $m<sizeof($locArray); $m++){
				$path[] = $locId;
				$locName = $locArray[$m];
				$currLcation[] = $this->checkInsetIfNotExistLoc($tableName, $colName, $locId, 'location_title', $locArray[$m], 'location_id', $builderID, $proID, $locIdArray);
				$path = array_merge($currLcation, $path);
				$currLcation = array();
			}
			return $path;	
		}
		
	}
	
	function recursiveInsertLocation($locationArray, $parentId, $proID, $builderID, $rowLocationTree){
		$values = '';
		for ($i=0; $i<sizeof($locationArray); $i++){
			$QURY = "SELECT location_id FROM project_monitoring_locations WHERE project_id = ".$proID." AND location_parent_id = ".$parentId." AND location_title = '".$locationArray[$i]."' AND is_deleted = 0 LIMIT 0, 1";
			$RES = mysql_query($QURY);
			$values = "";
			if(mysql_num_rows($RES) > 0){
				while($ROW = mysql_fetch_assoc($RES)){
					$values = $ROW['location_id'];
				}
			}
			if($values != ''){
				$parentId = $values;
			}else{
				$insertQRY = "INSERT INTO project_monitoring_locations SET
								project_id = ".$proID.",
								location_parent_id = ".$parentId.",
								location_title = '".$locationArray[$i]."',
								last_modified_date = NOW(),
								last_modified_by = '".$builderID."',
								created_date = NOW(),
								created_by = '".$builderID."'";
				mysql_query($insertQRY);
				$parentId = mysql_insert_id();
			}
			if ($rowLocationTree == "")
				$rowLocationTree = $parentId;
			else
				$rowLocationTree .= ' > ' . $parentId;
		}
		return $rowLocationTree;
	}
	
	function QAcheckInsetIfNotExistLoc($tableName, $colName, $val, $colName1, $val1, $expColName, $builderID, $proID, $locArray){
		$values = '';
		$QURY = "SELECT ".$expColName." FROM ".$tableName." WHERE project_id = ".$proID." AND ".$colName." = '".$val."' AND ".$colName1." = '".$val1."' AND is_deleted = 0 LIMIT 0, 1";
		$RES = mysql_query($QURY);
		if(mysql_num_rows($RES) > 0){
			while($ROW = mysql_fetch_assoc($RES)){
				$values = $ROW[$expColName];
			}
		}
		if($values != ''){
			$locId = $values;
		}else{
			$insertQRY = "INSERT INTO ".$tableName." SET
							project_id = ".$proID.",
							".$colName." = ".trim($val).",
							".$colName1." = '".addslashes(trim($val1))."',
							last_modified_date = NOW(),
							last_modified_by = '".$builderID."',
							created_date = NOW(),
							created_by = '".$builderID."'";
			mysql_query($insertQRY);
			$locId = mysql_insert_id();
		}
		if($locArray == ''){
			return $locId;
		}else{
			$path = array();
			for($m=0; $m<sizeof($locArray); $m++){
				$path[] = $locId;
				$locName = $locArray[$m];
				$currLcation[] = $this->checkInsetIfNotExistLoc($tableName, $colName, $locId, 'location_title', $locArray[$m], 'location_id', $builderID, $proID, $locIdArray);
				$path = array_merge($currLcation, $path);
				$currLcation = array();
			}
			return $path;	
		}
		
	}
	
	function QArecursiveInsertLocation($locationArray, $parentId, $proID, $builderID, $rowLocationTree){
		$values = '';
		for ($i=0; $i<sizeof($locationArray); $i++){
			$QURY = "SELECT location_id FROM qa_task_locations WHERE project_id = ".$proID." AND location_parent_id = ".$parentId." AND location_title = '".$locationArray[$i]."' AND is_deleted = 0 LIMIT 0, 1";
			$RES = mysql_query($QURY);
			$values = "";
			if(mysql_num_rows($RES) > 0){
				while($ROW = mysql_fetch_assoc($RES)){
					$values = $ROW['location_id'];
				}
			}
			if($values != ''){
				$parentId = $values;
			}else{
				$insertQRY = "INSERT INTO qa_task_locations SET
								project_id = ".$proID.",
								location_parent_id = ".$parentId.",
								location_title = '".$locationArray[$i]."',
								last_modified_date = NOW(),
								last_modified_by = '".$builderID."',
								created_date = NOW(),
								created_by = '".$builderID."'";
				mysql_query($insertQRY);
				$parentId = mysql_insert_id();
			}
			if ($rowLocationTree == "")
				$rowLocationTree = $parentId;
			else
				$rowLocationTree .= ' > ' . $parentId;
		}
		return $rowLocationTree;
	}
	
	function QAsubLocationsProgressMonitoring($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->QAgetCatProgressMonitoring($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function QAgetCatProgressMonitoring($catId){
		$qry = 'select location_id, location_parent_id from qa_task_locations where location_id ='.$catId . ' and is_deleted=0 and location_parent_id!=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->QAgetCatProgressMonitoring($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}

	function subLocationsDepthQA($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->QAlocationsDepth($subLocation);
		foreach($depth as $dp){
			if($breadcrumb == ''){
				$breadcrumb .= $dp;
			}else{
				$breadcrumb .= $saprater . $dp;
			}
		}
		return $breadcrumb;
	}
	
	function QAlocationsDepth($catId){
		$qry = 'SELECT location_id FROM qa_task_locations WHERE location_parent_id ='.$catId.' AND is_deleted=0 ';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->QAlocationsDepth($rows[0]), $path);
		}
		return $path;
	}
	
	function arrangeMultiDimensionArray($array, $order){
		$lupCount = sizeof($array); 
		$countArray = array();
		$sortArray = array();
		for($i=0; $i<$lupCount; $i++){
			$countArray[] = count($array[$i]);
		}
		if($order == 'DESC'){
			arsort($countArray);
		}else{
			asort($countArray);
		}
		$countArray = array_keys($countArray);
		$secLoop = count($countArray);
		for($j=0; $j<$secLoop; $j++){
			$sortArray[] = $array[$countArray[$j]];
		}
		return $sortArray;
	}
	
	function createFile($fileName, $fileContent, $path, $mode = 'a+'){
		$fh = fopen($path.$fileName, $mode) or die("can't open file");
		fwrite($fh, $fileContent);
		fclose($fh);
	}
	
	function subLocationsProgressMonitoring_update($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoring_update($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function getCatProgressMonitoring_update($catId){
		$qry = 'SELECT location_id, location_parent_id FROM project_monitoring_locations WHERE location_id ='.$catId.' AND is_deleted = 0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->getCatProgressMonitoring_update($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}
	
	function ProMonrecursiveInsertLocation($locationArray, $parentId, $proID, $builderID, $rowLocationTree){
		$values = '';
		for ($i=0; $i<sizeof($locationArray); $i++){
			$QURY = "SELECT location_id FROM project_monitoring_locations WHERE project_id = ".$proID." AND location_parent_id = ".$parentId." AND location_title = '".$locationArray[$i]."' AND is_deleted = 0 LIMIT 0, 1";
			$RES = mysql_query($QURY);
			$values = "";
			if(mysql_num_rows($RES) > 0){
				while($ROW = mysql_fetch_assoc($RES)){
					$values = $ROW['location_id'];
				}
			}
			if($values != ''){
				$parentId = $values;
			}else{
				$insertQRY = "INSERT INTO project_monitoring_locations SET
								project_id = ".$proID.",
								location_parent_id = ".$parentId.",
								location_title = '".$locationArray[$i]."',
								last_modified_date = NOW(),
								last_modified_by = '".$builderID."',
								created_date = NOW(),
								created_by = '".$builderID."'";
				mysql_query($insertQRY);
				$parentId = mysql_insert_id();
			}
			if ($rowLocationTree == "")
				$rowLocationTree = $parentId;
			else
				$rowLocationTree .= ' > ' . $parentId;
		}
		return $rowLocationTree;
	}
	
	function locId2LocName($locationTree){
		$locationTreeName = '';
		$locationTreeArray = explode(' > ', $locationTree);
		$lupCount = sizeof($locationTreeArray);
		for($i=0; $i<$lupCount; $i++){
			if($locationTreeName == ''){
				$locationTreeName = $this->getDataByKey('project_monitoring_locations', 'location_id', $locationTreeArray[$i], 'location_title');
			}else{
				$locationTreeName .= ' > '.$this->getDataByKey('project_monitoring_locations', 'location_id', $locationTreeArray[$i], 'location_title');
			}
		}
		return $locationTreeName;
	}
	
	function subLocationsProgressMonitoring_ids($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoring_update($subLocation);
		$breadcrumb = join($saprater, $depth);
		#print_r($depth);die;
		return $breadcrumb;
	}
	
	function validateMySqlDate($date, $mode='FULL'){
		$returnType = false;
		if($mode != 'FULL'){
			if(preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)){
				$returnType = true;
			}
		}else{
			if(preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $date)){
				$returnType = true;
			}
		}
		return $returnType;
	}	

	function justChildSubLocQA($subLocation){
		$childSubLoc = array();
		$subLoc = $this->selQRYMultiple('location_id', 'qa_task_locations', 'location_parent_id = '.$subLocation.' AND is_deleted = 0');
		foreach($subLoc as $sLoc){
			$childSubLoc[] = $sLoc['location_id'];
		}
		return $childSubLoc;
	}

	
	function QAsubLocationsProgressMonitoringWallchart($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->QAgetCatProgressMonitoringWallchart($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function QAgetCatProgressMonitoringWallchart($catId){
		$qry = 'select location_id, location_parent_id from qa_task_locations where location_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->QAgetCatProgressMonitoringWallchart($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}
	
	function recurtionProMon($locationID, $projectID){
		$data='';
		$location = $this->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_parent_id = "'.$locationID.'" and is_deleted = "0" and project_id = "'.$projectID.'" order by location_title');
		if(!empty($location)){
			foreach($location as $loc){
				$data .= '<ul><li id="li_'.$locations['location_id'].'"><span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span>';
				$data .= '</li></ul>';
				return $data;
			}
		}
	}
	
	function recurtionQA($locationID, $projectID){
		$data='';
		$location = $this->selQRYMultiple('location_id, location_title', 'qa_task_locations', 'location_parent_id = "'.$locationID.'" and is_deleted = "0" and project_id = "'.$projectID.'" order by location_title');
		if(!empty($location)){
			foreach($location as $loc){
				$data .= '<ul><li id="li_'.$locations['location_id'].'"><span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span>';
				$data .= '</li></ul>';
				return $data;
			}
		}
	}
	
	function promon_sublocationParent($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoringwithParent($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function promon_sublocationParentID($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoringwithParent($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $dp;
			}else{
				$breadcrumb .= $saprater . $dp;
			}
		}
		return $breadcrumb;
	}
	
	function getCatProgressMonitoringwithParent($catId){
		$qry = 'select location_id, location_parent_id from project_monitoring_locations where location_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			$path[] = $row->location_id;
			$path = array_merge($this->getCatProgressMonitoringwithParent($row->location_parent_id), $path);
		}
		return $path;
	}
	
	function qa_sublocationParent($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatQualityAssuranceParent($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function qa_sublocationParentID($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatQualityAssuranceParent($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $dp;
			}else{
				$breadcrumb .= $saprater . $dp;
			}
		}
		return $breadcrumb;
	}
	
	function getCatQualityAssuranceParent($catId){
		$qry = 'select location_id, location_parent_id from qa_task_locations where location_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			$path[] = $row->location_id;
			$path = array_merge($this->getCatQualityAssuranceParent($row->location_parent_id), $path);
		}
		return $path;
	}
	
	function getRecordByQuery($query){
#echo $query;
		$RS = mysql_query($query);
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}
	
	# Remove cookies from browser
	function removeCookies($id = 0){
		$uid = ($id!=0)?$id:$_SESSION['ww_builder_id'];
		$skipCookie = array($uid.'_qc', $uid.'_ir', $uid.'_pmr', $uid.'_qar', $uid.'_clr'); 
		$projNameType = array('projName', 'projName', 'projName', 'projNameQA', 'projNameCL'); 
		//if($id==0){
			foreach($skipCookie as $key=>$cookieName){
				$projectName = "";
				if(isset($_COOKIE[$cookieName])){
					$qc = unserialize($_COOKIE[$cookieName]);
					$projectName = $qc[$projNameType[$key]];
					if(count($qc)>1){
						unset($_COOKIE[$cookieName]);
					}
					setcookie($cookieName, serialize(array($projNameType[$key]=>$projectName)), time()+864000);
				}			
			}
		//}
		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				if(!in_array($name, $skipCookie) && 'PHPSESSID'!=$name){
					setcookie($name, '', time()-1000);
					setcookie($name, '', time()-1000, '/');
				}
			}
		}
	}
	
}
?>