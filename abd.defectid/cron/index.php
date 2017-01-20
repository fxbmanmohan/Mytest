<?php set_time_limit(3600);
require_once('../includes/property.php');
define("STOREFOLDER", './deadlock', true);

if(!file_exists(STOREFOLDER.'/progrssMonitoring.txt')){
	$dbConn = mysql_connect(DEF_HOST, DEF_USER, DEF_PASSWORD) or die('Server Not Found 404');
	mysql_select_db(DEF_DBNAME, $dbConn);
	
	$fh = fopen(STOREFOLDER.'/progrssMonitoring.txt', 'w') or die("can't open file");
	fwrite($fh, date('Y-m-d h:i:s a'));
	fclose($fh);
	$currTime = date('Y-m-d h:i:s');
	$progressIDS = '';
	$selQry = "SELECT progress_id, percentage, task, project_id FROM progress_monitoring WHERE end_date <= '".$currTime."' AND status = 'In progress' AND is_deleted = 0";
	$res = mysql_query($selQry);
	if(mysql_num_rows($res) > 0){
		while($rows = mysql_fetch_assoc($res)){
			if($progressIDS == ''){
				$progressIDS = $rows['progress_id'];
			}else{
				$progressIDS .= ', '.$rows['progress_id'];
			}
			$insertQRY = "INSERT INTO progress_monitoring_update SET
						progress_id = '".$rows['progress_id']."',
						percentage = '".$rows['percentage']."',
						status = 'Behind',
						created_by = 0,
						created_date = NOW(),
						last_modified_by = 0,
						last_modified_date = NOW(),
						project_id = '".$rows['project_id']."',
						is_deleted = 0,
						resource_type = 'WEBCRON'";
			mysql_query($insertQRY);
		}
	}
	if($progressIDS != ''){
		$updateQRY = "UPDATE progress_monitoring SET status = 'Behind', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = 0 WHERE progress_id IN (".$progressIDS.")";
		mysql_query($updateQRY);
	}
	$insertQRY = "INSERT INTO cron_history SET cron_name = 'progrss_monitoring', update_table_name = 'progress_monitoring', update_table_value = '".$progressIDS."', frequency_of_cron = 'EVERY MIDNIGHT', created_by = 0, created_date = NOW(), last_modified_by = 0, last_modified_date = NOW()";
	mysql_query($insertQRY);
	unlink(STOREFOLDER.'/progrssMonitoring.txt');	
}else{
	file_put_contents('log.txt', date('Y-m-d h:i:s').'<===>Another one progess is going on please try again letter !<===>', FILE_APPEND);
}
?>