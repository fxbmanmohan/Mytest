<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

$builder_id = isset($_SESSION['ww_builder_id']) ? $_SESSION['ww_builder_id'] : '';

if($builder_id != ''){
	$query = "select DISTINCT project_id from user_projects where is_deleted=0 and user_id=$builder_id";
}else{
	$query = "select DISTINCT project_id from projects where is_deleted=0";
}
$noti_record_closed = $object->db_query($query);
$tmp_arr = array();
while($project_id=$object->db_fetch_assoc($noti_record_closed)){
	$tmp_arr[] = $project_id["project_id"];
}
$project_ids = join(",", $tmp_arr);



if($_POST['projectId']!=''){
	$projID = $project_id_new = $_POST['projectId'];
	
$project_ids = $projID;
if($builder_id != ''){
	$inCaluseArr = array();
	$whereConUserRole = "";
	if(!empty($_SESSION['projUserRole'])){
		if($_SESSION['projUserRole'][$project_id_new] != 'All Defect')
			$whereConUserRole = " AND inspection_raised_by = '".$_SESSION['projUserRole'][$project_id_new]."'";
	}
	mysql_query('SET SESSION group_concat_max_len = 4294967295');
	$inspectionData = $obj->selQRYMultiple('GROUP_CONCAT(inspection_id) AS insp, project_id', 'project_inspections', 'is_deleted = 0 AND project_id = ' . $projID . $whereConUserRole);
	
	$inCaluseArr[$projID] = $inspectionData[0]['insp'];
	$inCaluseArr = array_filter($inCaluseArr);	
	$inspCondition = "";
	if(!empty($inCaluseArr))	$inspCondition = " AND i.inspection_id IN (".join(",", $inCaluseArr).")";
}
#print_r($inCaluseArr);

//Count over due inspection Start Here
	if($builder_id != ''){
		$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
#echo $noti_b;
	$noti_record=$object->db_query($noti_b);
	if(mysql_num_rows($noti_record) > 0){
		$overdue_total = mysql_num_rows($noti_record);
	}else{
		$overdue_total = 0;
	}
//Count over due inspection End Here

//Count due in one day inspection Start Here
	if($builder_id != ''){
		$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() and inspection_fixed_by_date <= CURDATE() + INTERVAL 1 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() and inspection_fixed_by_date <= CURDATE() + INTERVAL 1 DAY and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
#echo $noti_one_day;
	$noti_record_one=$object->db_query($noti_one_day);
	if(mysql_num_rows($noti_record_one) > 0){
		$overdue_one_day_total = mysql_num_rows($noti_record_one); 
	}else{
		$overdue_one_day_total = 0;
	}
//Count due in one day inspection End Here

//Count due in seven day inspection Start Here
	if($builder_id != ''){
		$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 1 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 1 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
#echo $noti_seven_day;
	$noti_record_seven=$object->db_query($noti_seven_day);
	if(mysql_num_rows($noti_record_seven) > 0){
		$overdue_one_seven_total = mysql_num_rows($noti_record_seven);
	}else{
		$overdue_one_seven_total = 0;
	}	
//Count due in seven day inspection End Here

//Count due in fourteen day inspection Start Here
	if($builder_id != ''){
		$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 7 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 14 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 7 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 14 DAY and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
#echo $noti_14_day;
	$noti_record_14=$object->db_query($noti_14_day);
	if(mysql_num_rows($noti_record_14) > 0){
		$overdue_14_days_total = mysql_num_rows($noti_record_14);
	}else{
		$overdue_14_days_total = 0;
	}		
//Count due in fourteen day inspection End Here

//Count due in twentyone day inspection Start Here
	if($builder_id != ''){
		$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 14 DAY and inspection_fixed_by_date < CURDATE() + INTERVAL 21 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=$project_id_new and i.project_id=$project_id_new and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 14 DAY and inspection_fixed_by_date < CURDATE() + INTERVAL 21 DAY and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
#echo $noti_21_day;
	$noti_record_21=$object->db_query($noti_21_day);
	if(mysql_num_rows($noti_record_21) > 0){
		$overdue_21_days_total = mysql_num_rows($noti_record_21);
	}else{
		$overdue_21_days_total = 0;
	}	
//Count due in twentyone day inspection End Here

//*Closed Section*/
if($builder_id != ''){
	$closed_query = "SELECT count(*) as closeed FROM project_inspections pi, issued_to_for_inspections i where pi.inspection_id = i.inspection_id AND i.project_id = pi.project_id AND i.project_id in (" . $project_ids . ") AND i.is_deleted=0 AND pi.is_deleted=0 AND i.inspection_status = 'Closed' AND i.inspection_fixed_by_date != '0000-00-00' ".$inspCondition." group by pi.inspection_id";
}else{
	$closed_query = "SELECT count(*) as closeed FROM project_inspections pi, issued_to_for_inspections i where pi.inspection_id = i.inspection_id AND i.project_id = pi.project_id AND i.project_id in (" . $project_ids . ") AND i.is_deleted=0 AND pi.is_deleted=0 AND i.inspection_status = 'Closed' AND i.inspection_fixed_by_date != '0000-00-00' group by pi.inspection_id";
}
$closed = $object->db_query($closed_query);
$closed_total = mysql_num_rows ($closed);

//*Closed Section*/s
//Count Average Time Start Here
	if($builder_id != ''){
		$noti_closed="SELECT
							i.inspection_id,
							DATEDIFF( i.closed_date, p.inspection_date_raised ) as difference
						FROM
							`issued_to_for_inspections` AS i, user_projects AS up, project_inspections p
						WHERE
							p.inspection_id = i.inspection_id AND
							p.project_id =i.project_id AND
							p.project_id =$project_id_new AND
							i.project_id =$project_id_new AND
							up.project_id =i.project_id AND
							i.is_deleted =0 AND
							up.is_deleted =0 AND
							p.is_deleted =0 AND
							user_id =$builder_id AND
							i.inspection_status = 'Closed' AND
							i.closed_date != '0000-00-00'  ".$inspCondition." 
						GROUP BY
							i.inspection_id";
	}else{
		$noti_closed="SELECT
							i.inspection_id,
							DATEDIFF( i.closed_date, p.inspection_date_raised ) as difference
						FROM
							`issued_to_for_inspections` AS i, user_projects AS up, project_inspections p
						WHERE
							p.inspection_id = i.inspection_id AND
							p.project_id =i.project_id AND
							p.project_id =$project_id_new AND
							i.project_id =$project_id_new AND
							up.project_id =i.project_id AND
							i.is_deleted =0 AND
							up.is_deleted =0 AND
							p.is_deleted =0 AND
							i.inspection_status = 'Closed' AND
							i.closed_date != '0000-00-00'
						GROUP BY
							i.inspection_id";
	}
#echo $noti_closed;
	$noti_record_closed=$object->db_query($noti_closed);
	$overdue_closed_rows_total=mysql_num_rows($noti_record_closed);
	$new_value=0;
	while($overdue_closed=$object->db_fetch_assoc($noti_record_closed)){
		$overdue_closed_total = $overdue_closed["difference"];
		$new_value+=$overdue_closed_total;
	}	
//Count Average Time End Here

//Count Cost Impact Start Here
	if($builder_id != ''){
		$costImpactData = "SELECT sum(cost_impact_price) as totalPrice FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and user_id = $builder_id and i.project_id=$project_id_new ".$inspCondition." ";
	}else{
		$costImpactData = "SELECT sum(cost_impact_price) as totalPrice FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.project_id=$project_id_new ";
	}
#echo $costImpactData ;
	$costImpactPrice = $object->db_query($costImpactData);
	if($costImpactPriceData=$object->db_fetch_assoc($costImpactPrice)){
		$costTotalPrice = $costImpactPriceData["totalPrice"];
	}	
//Count Cost Impact End Here
	
}else{
	
	if(isset($_SESSION['notificationData']['overdue_total'])){ $overdue_total = $_SESSION['notificationData']['overdue_total']; }else{ $overdue_total = 0; }
	if(isset($_SESSION['notificationData']['overdue_one_day_total'])){ $overdue_one_day_total = $_SESSION['notificationData']['overdue_one_day_total']; }else{ $overdue_one_day_total = 0; }
	if(isset($_SESSION['notificationData']['overdue_one_seven_total'])){ $overdue_one_seven_total = $_SESSION['notificationData']['overdue_one_seven_total']; }else{ $overdue_one_seven_total = 0; }
	if(isset($_SESSION['notificationData']['overdue_14_days_total'])){ $overdue_14_days_total = $_SESSION['notificationData']['overdue_14_days_total']; }else{ $overdue_14_days_total = 0; }
	if(isset($_SESSION['notificationData']['overdue_21_days_total'])){ $overdue_21_days_total = $_SESSION['notificationData']['overdue_21_days_total']; }else{ $overdue_21_days_total = 0; }
	if(isset($_SESSION['notificationData']['closed_total'])){ $closed_total = $_SESSION['notificationData']['closed_total']; }else{ $closed_total = 0; }
	if(isset($_SESSION['notificationData']['new_value'])){ $new_value = $_SESSION['notificationData']['new_value']; }else{ $new_value = 0; }
	if(isset($_SESSION['notificationData']['overdue_closed_rows_total'])){ $overdue_closed_rows_total = $_SESSION['notificationData']['overdue_closed_rows_total']; }else{ $overdue_closed_rows_total = 0; }
}

?>
<table width="100%" cellspacing="0"  cellpadding="2" style="margin-top:0px;" class="gridtable">
	<tr>
		<td width="35%" class="clickable" onClick="inspectionsList('overDue');"><img src="images/traffic-red.png" alt="Overdue">&nbsp;Overdue</td>
		<td width="15%" class="clickable" onClick="inspectionsList('overDue');"><?php echo  $overdue_total; ?></td>
		<td width="50%" rowspan="3" align="center" valign="middle">
			<h3 style="color:#000;">Average Time to close Inspections(days)<br/>
			<?php if($overdue_closed_rows_total!=0)
				echo $avg= round($new_value/$overdue_closed_rows_total);
			else
				echo '0'; ?>
			</h3>
		</td>
	</tr>
	<tr>
		<td class="clickable" onClick="inspectionsList('dueIn1Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 1 day'));?>');"><img src="images/traffic-yellow.png" alt="Overdue" align="absbottom">&nbsp;Due in 1 day</td>
		<td class="clickable" onClick="inspectionsList('dueIn1Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 1 day'));?>');"><?php echo  $overdue_one_day_total; ?></td>
	</tr>
	<tr>
		<td class="clickable" onClick="inspectionsList('dueIn7Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 7 day'));?>');"><img src="images/traffic-yellow.png" alt="Due in 7 days" align="absbottom">&nbsp;Due in 7 days</a></td>
		<td class="clickable" onClick="inspectionsList('dueIn7Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 7 day'));?>');"><?php echo  $overdue_one_seven_total; ?></td>
	</tr>
	<tr>
		<td class="clickable" onClick="inspectionsList('dueIn14Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 14 day'));?>');"><img src="images/traffic-yellow.png" alt="Due in 14 days" align="absbottom">&nbsp;Due in 14 days</td>
		<td class="clickable" onClick="inspectionsList('dueIn14Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 14 day'));?>');"><?php echo  $overdue_14_days_total; ?></td>
		<td rowspan="3" align="center" valign="middle">
			<h3>Total cost impact of Inspections (in $)<br/>
			<?=$costTotalPrice;?></h3>
		</td>
	</tr>
	<tr>
		<td class="clickable" onClick="inspectionsList('dueIn21Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 21 day'));?>');"><img src="images/traffic-yellow.png" alt="Due in 21 days" align="absbottom">&nbsp;Due in 21 days</td>
		<td class="clickable" onClick="inspectionsList('dueIn21Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 21 day'));?>');"><?php echo  $overdue_21_days_total; ?></td>
	</tr>
	<tr>
		<td class="clickable" onClick="inspectionsList('Closed');"><img src="images/traffice-blue.png" alt="Closed" align="absbottom">&nbsp;Closed</td>
		<td class="clickable" onClick="inspectionsList('Closed');"><?php echo $closed_total; ?></td>
	</tr>
</table>