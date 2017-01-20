<?php 
include_once("functions.php");
include_once("SimpleImage.php");

$object= new DB_Class();
class COMMAN_Class{
	function getDataByKey($table, $searchKey, $searchValue, $expValue){
		$RS = mysql_query("SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."'");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_array($RS)){
				return $ROW[0];
			}
		}else{
			return false;
		}
	}	

	function selQRY($select, $table, $where){
		$RS = mysql_query("SELECT ".$select." FROM ".$table." WHERE ".$where);
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				return $ROW;
			}
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
		$qry = 'select location_id, location_parent_id from project_locations where location_id ='.$catId;
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
		$qry = 'select location_id from project_locations where location_parent_id ='.$catId;
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getCatIds($rows[0]), $path);
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

	function subLocationsId($subLocation, $saprater){
		$breadcrumb = $subLocation;
		$depth = $this->getCatIds($subLocation);
		foreach($depth as $dp){
			$breadcrumb .= $saprater . $dp;
		}
		return $breadcrumb;
	}
	
#	echo subLocations(6, ' > ');

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
		if(!file_exists($resizeDestination) && file_exists($resizeSource)){
			$simpleImage->load($resizeSource);
			if($simpleImage->getWidth() < $rWidth){//Resize by Height
				$simpleImage->resizeToHeight($rHeight);
				$simpleImage->save($resizeDestination);
			}else{//Resize by widtht
				$simpleImage->resizeToWidth($rWidth);
				$simpleImage->save($resizeDestination);
			}
		}
		return true;
	}
	function selQRYMultiple($select, $table, $where){
		//echo "SELECT ".$select." FROM ".$table." WHERE ".$where;
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
	
	/*function resizeImages($resizeSource, $rWidth, $rHeight, $resizeDestination){
		$simpleImage = new SimpleImage();
		$folder = opendir($resizeSource.'/');
		$file_types = array("jpg", "jpeg", "gif", "png", "txt", "ico");
		while($file = readdir($folder)){
			if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $file_types)){
				if(!file_exists($resizeDestination.'/'.$file)){
					$simpleImage->load($resizeSource.'/'.$file);
					if($simpleImage->getWidth() < $rWidth){//Resize by Height
						$simpleImage->resizeToHeight($rHeight);
						$simpleImage->save($resizeDestination.'/'.$file);
					}else{//Resize by widtht
						$simpleImage->resizeToWidth($rWidth);
						$simpleImage->save($resizeDestination.'/'.$file);
					}
				}
			}
		}
		return true;
	}*/
}
?>