<?php set_time_limit(3600);
require_once('../includes/property.php');
define("STOREFOLDER", './deadlock', true);

if(!is_dir(STOREFOLDER)){
	@mkdir(STOREFOLDER, 0777);
}

if(!file_exists(STOREFOLDER.'/standardDefectInsert.txt')){
	$dbConn = mysql_connect(DEF_HOST, DEF_USER, DEF_PASSWORD) or die('Server Not Found 404');
	mysql_select_db(DEF_DBNAME, $dbConn);
	mysql_query("set time_zone='Australia/Melbourne'");
	
	$fh = fopen(STOREFOLDER.'/standardDefectInsert.txt', 'w') or die("can't open file");
	fwrite($fh, date('Y-m-d h:i:s a'));
	fclose($fh);
//Data collection part start here
	$projectData = selQRYMultiple('project_id, project_name', 'projects', 'is_deleted = 0');
	$projIdArr = array();
	$porojNameArr = array();
	foreach($projectData as $proj){
		$projIdArr[] = $proj['project_id'];
		$projNameArr[$proj['project_id']] = $proj['project_name'];
	}

	$sdData = selQRYMultiple('project_id, description', 'standard_defects', 'is_deleted = 0 AND project_id IN ('.join(",", $projIdArr).')');
	$sdProjwiseArr = array();
	foreach($sdData as $sdProj){
		if(is_array($sdProjwiseArr[$sdProj['project_id']])){
			$sdProjwiseArr[$sdProj['project_id']][] = '"'.$sdProj['description'].'"';
		}else{
			$sdProjwiseArr[$sdProj['project_id']] = array();
			$sdProjwiseArr[$sdProj['project_id']][] = '"'.$sdProj['description'].'"';
		}
	}
	
	$lupCount = sizeof($projIdArr);
	for($i=0; $i<$lupCount; $i++){
		$inspData = selQRYMultiple('COUNT(inspection_id) AS sdCount, inspection_description', 'project_inspections', 'is_deleted = 0 AND project_id  = '.$projIdArr[$i].' AND inspection_description NOT IN ('.join(",", $sdProjwiseArr[$projIdArr[$i]]).') GROUP BY inspection_description');
		$inserIDArr = array();
		foreach($inspData as $insp){
			if($insp['sdCount'] > 2 && $insp['inspection_description'] != ""){
				$insertQRY = 'INSERT INTO standard_defects SET
									project_id = '.$projIdArr[$i].',
									description = "'.$insp['inspection_description'].'",
									last_modified_by = 0,
									last_modified_date = NOW(),
									created_date = NOW(),
									created_by = 0,
									fix_by_days = 3';
				echo $insertQRY;		
				mysql_query($insertQRY);
				$inserIDArr[] = mysql_insert_id();
			}
		}
		$insertQRYCron = "INSERT INTO cron_history SET cron_name = 'standard_defect_insert', update_table_name = 'standard_defects', update_table_value = '".join(",", $inserIDArr)."', frequency_of_cron = 'EVERY MIDNIGHT', created_by = 0, created_date = NOW(), last_modified_by = 0, last_modified_date = NOW()";
		mysql_query($insertQRYCron);
	}
	unlink(STOREFOLDER.'/standardDefectInsert.txt');	
}else{
	file_put_contents('Standard_defect_log.txt', date('Y-m-d h:i:s').'<===>Another one progess is going on please try again letter !<===>', FILE_APPEND);
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
}?>