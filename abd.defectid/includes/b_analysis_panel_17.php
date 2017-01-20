<?php

$builder_id = $_SESSION['ww_builder_id'] ;


$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now()";

$noti_record=$obj->db_query($noti_b);
if($overdue=$obj->db_fetch_assoc($noti_record)){
		$overdue_total = $overdue["due"];
	}
	



$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date = now()";
$noti_record_one=$obj->db_query($noti_one_day);
if($overdue_one_day=$obj->db_fetch_assoc($noti_record_one)){
		$overdue_one_day_total = $overdue_one_day["due_one"];
	}
	
	
$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE() - INTERVAL 7 DAY and inspection_fixed_by_date <= CURDATE()";

$noti_record_seven=$obj->db_query($noti_seven_day);
if($overdue_seven_day=$obj->db_fetch_assoc($noti_record_seven)){
		$overdue_one_seven_total = $overdue_seven_day["due_seven"];
	}	
	
	
$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE() - INTERVAL 14 DAY and inspection_fixed_by_date <= CURDATE()";

$noti_record_14=$obj->db_query($noti_14_day);
if($overdue_14_day=$obj->db_fetch_assoc($noti_record_14)){
		$overdue_14_days_total = $overdue_14_day["due_14"];
	}		



$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE() - INTERVAL 21 DAY and inspection_fixed_by_date <= CURDATE()";

$noti_record_21=$obj->db_query($noti_21_day);
if($overdue_21_day=$obj->db_fetch_assoc($noti_record_21)){
		$overdue_21_days_total = $overdue_21_day["due_21"];
	}	


$noti_closed="SELECT count(*) as total_row,Sum((To_days( i.closed_date ) - TO_DAYS( i.inspection_date_raised ))) as difference FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status ='Closed'";
//echo $noti_closed; die;
$noti_record_closed=$obj->db_query($noti_closed);
if($overdue_closed=$obj->db_fetch_assoc($noti_record_closed)){
		$overdue_closed_total = $overdue_closed["difference"];
		$overdue_closed_rows_total = $overdue_closed["total_row"]; 
	}		

//************************************************************************************/
			/* QUERY FOR FULL ANALYSIS CHART*/
//************************************************************************************/		
	$qs="SELECT COUNT(DISTINCT d.project_id) as count FROM project_inspections AS d, user_projects as p where (user_id=$builder_id and p.project_id = d.project_id and p.is_deleted=0 and d.is_deleted=0)";
	$rs=$obj->db_query($qs);
	if($f=$obj->db_fetch_assoc($rs)){
		$qc_total = $f["count"];
	}
	
	
	 $qsexpand = "SELECT p.project_id,p.project_name, count(*) as pcount FROM project_inspections AS d, user_projects AS p where d.project_id=p.project_id and p.user_id=$builder_id and p.is_deleted=0  and d.is_deleted=0 group by d.project_id ORDER BY pcount DESC limit 0,1";
	
	$rs_expand=$obj->db_query($qsexpand);
	if($f_expand=$obj->db_fetch_assoc($rs_expand)){
		
		
		$qc_expand = $f_expand["project_id"];
	}
	
	/* END CODE FOR QUALITY CONTROL TO*/
//************************************************************************************/
			/* QUERY FOR ISSUE TO CHART*///************************************************************************************/
	$issue="SELECT count(*) as count FROM `issued_to_for_inspections` as i, `project_inspections` as pi, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and pi.is_deleted=0 and pi.inspection_id=i.inspection_id  and user_id=$builder_id group by issued_to_name";

 	$i_rs=$obj->db_query($issue);
	if($issu=$obj->db_fetch_assoc($i_rs)){
		$it_total = $issu["count"];
	}
	$it_total = mysql_num_rows($i_rs);	
	
	$it_query_expand ="SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i, user_projects as up where up.user_id=$builder_id and up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 group by issued_to_name order by count desc limit 0,1";
	$it_rs_expd=$obj->db_query($it_query_expand);
	if($issu_expand=$obj->db_fetch_assoc($it_rs_expd)){
		$it_name_expand = $issu_expand["issued_to_name"];
	}
	
	
		/*END CODE FOR ISSUE TO */
//************************************************************************************/
			/* QUERY FOR PROGRESS MONITORING CHART*/
//************************************************************************************/		
	//Chart for Progress Monitoring
		
	$prg_m="SELECT count(DISTINCT pm.project_id) as pmcount FROM progress_monitoring AS pm, user_projects AS p where (user_id=$builder_id and  p.project_id = pm.project_id and p.is_deleted=0 and pm.is_deleted=0)";
		  	$pm_rs=$obj->db_query($prg_m);
	if($pm=$obj->db_fetch_assoc($pm_rs)){
		//echo 'Hi'; die;
		$pm_total = $pm["pmcount"];
	}
	
	 $pm_expand = "SELECT p.project_name, p.project_id FROM progress_monitoring AS pm, user_projects AS p where (user_id=$builder_id and  p.project_id = pm.project_id and p.is_deleted=0 and pm.is_deleted=0)  group by pm.project_id order by p.project_name limit 0,1";
	
	$rs_pm_expand=$obj->db_query($pm_expand);
	if($pm_f_expand=$obj->db_fetch_assoc($rs_pm_expand)){
		
		
		$pm_expand_id = $pm_f_expand["project_id"];
	}
	    
/* END PHP CODE FOR PROGRESS MONITORING*/

?>
<link href="dashboard.css" rel="stylesheet" type="text/css" /> 
<link href="maxChart/style/style.css" rel="stylesheet" type="text/css" />
<link class="include" rel="stylesheet" type="text/css" href="dist/jquery.jqplot.min.css" />

<style type="text/css">


	pre{
		display:block;
		font:12px "Courier New", Courier, monospace;
		padding:10px;
		border:1px solid #bae2f0;
		background:#e3f4f9;	
		margin:.5em 0;
		width:674px;
		}	
			
    /* image replacement */
        .graphic, #prevBtn, #nextBtn{
            margin:0;
            padding:0;
            display:block;
            overflow:hidden;
            text-indent:-8000px;
            }
    /* // image replacement */
			
</style>



<?php
$table="";

$project=0; $inspector=0; $trade=0; $d_total=0; $d_open=0; $d_closed=0; $d_pending=0; $d_in_progress=0; $t5=0;
$top_5_op=array(); $top_5_all=array(); $top_5_td=array(); $top_5_trades=array();

$q="SELECT *, p.project_id as pro_id FROM user_projects p 
	LEFT JOIN user b ON p.user_id=b.user_id 
	LEFT JOIN pms_builder_to_subbuilders sb ON p.project_id=sb.fk_p_id
	WHERE (p.user_id='$builder_id' OR sb.sb_id='$builder_id') and p.is_deleted=0 GROUP BY p.project_id";
//echo $q; die;	
$r=$obj->db_query($q);
$project=$obj->db_num_rows($r);

if($project>100000000){
	while($f=$obj->db_fetch_assoc($r)){
		$table.="<tr class='gradeA'>
			
			<td>".stripslashes($f['project_name'])."</td>
			<td>".stripslashes($f['project_type'])."</td>
			<td>".stripslashes($f['project_address_line1'])."</td>
			<td>".stripslashes($f['project_suburb'])."</td>
			<td>".stripslashes($f['project_state'])."</td>
			<td>".stripslashes($f['project_postcode'])."</td>
			<td>".stripslashes($f['project_country'])."</td>
			<td align='center'>
				<a title='Click to see project details' href='?sect=add_project_detail&id=".base64_encode($f['pro_id'])."&hb=".base64_encode($f['fk_b_id']?$f['fk_b_id']:$builder_id)."'><img src='images/edit.png' border='none' /></a>
			</td>
			</tr>";
		
		// count inspectors
		if($obj->db_num_rows($obj->db_query("SELECT COUNT(id) AS insp FROM ".OWNERS." WHERE ow_project_id='".$f['pro_id']."'"))>0){
			$fi=$obj->db_fetch_assoc($obj->db_query("SELECT COUNT(id) AS insp FROM ".OWNERS." WHERE ow_project_id='".$f['pro_id']."'"));
			$inspector=$inspector+$fi['insp'];
		}			
		
		// count trades
		if($obj->db_num_rows($obj->db_query("SELECT COUNT(resp_id) AS trade FROM ".RESPONSIBLES." WHERE project_id='".$f['pro_id']."'"))>0){
			$ft=$obj->db_fetch_assoc($obj->db_query("SELECT COUNT(resp_id) AS trade FROM ".RESPONSIBLES." WHERE project_id='".$f['pro_id']."'"));
			$trade=$trade+$ft['trade'];
		}
		
		// count issues
		if($obj->db_num_rows($obj->db_query("SELECT SUM(IF(status = 'Open',1,0)) AS open, 
												SUM(IF(status='Closed',1,0)) AS closed, 
												SUM(IF(status='Pending',1,0)) AS pending, 
												SUM(IF(status='In Progress',1,0)) AS in_progress 
												FROM ".DEFECTS." 
												WHERE project_id='".$f['pro_id']."' "))>0){
			
			
			$fd=$obj->db_fetch_assoc($obj->db_query("SELECT SUM(IF(status = 'Open',1,0)) AS open, 
													SUM(IF(status='Closed',1,0)) AS closed, 
													SUM(IF(status='Pending',1,0)) AS pending, 
													SUM(IF(status='In Progress',1,0)) AS in_progress 
													FROM ".DEFECTS." 
													WHERE project_id='".$f['pro_id']."' "));
			
			$d_open=$d_open+$fd['open'];
			$d_closed=$d_closed+$fd['closed'];
			$d_pending=$d_pending+$fd['pending'];
			$d_in_progress=$d_in_progress+$fd['in_progress'];		
		}
		
		// top 5 trades
		$q5="SELECT r.resp_comp_name, SUM(IF(d.status = 'Open',1,0)) AS open, 
			SUM(IF(d.status='Closed',1,0)) AS closed FROM ".DEFECTS." d, ".RESPONSIBLES." r 
			WHERE d.project_id='".$f['pro_id']."' AND r.resp_id=d.resp_id GROUP BY d.resp_id";
			
		$rows=$obj->db_num_rows($obj->db_query($q5));

		if($rows>0){
			$r5=$obj->db_query($q5);
			while($f5=$obj->db_fetch_assoc($r5)){
				$top_5_td['open'][$f5['resp_comp_name']]=$f5['open'];
				$top_5_td['count'][$f5['resp_comp_name']]=$f5['open']+$f5['closed'];
				$t5++;
			}
		}
	}
	
	// Analysis Panel Start //
	$d_total=$d_open+$d_closed;
	
	// Top trades with issues open
	if(isset($top_5_td['open']) && sizeof($top_5_td['open'])>0){
		arsort($top_5_td['open'],SORT_NUMERIC);
		$i=0;
		foreach($top_5_td['open'] as $key => $val){
			$top_5_td_op[$i]="<td style='color:#FFFFFF'>".stripslashes($key). "</td><td style='color:#FFFFFF'>" . $val . "</td>";
			$top_5_trades[$i]['open']=$val;
			$i++;
		}
	}

	// Top trades with total issue count
	if(isset($top_5_td['count']) && sizeof($top_5_td['count'])>0){
		arsort($top_5_td['count'],SORT_NUMERIC);
		$i=0;
		foreach($top_5_td['count'] as $key => $val){
			$top_5_trades[$i]['tarde']=stripslashes($key);
			$top_5_trades[$i]['count']=$val;
			
			$i++;
		}
	}
	
	// subtract top_5_td_op from top_5_td_all to get closed
	if(isset($top_5_trades[0]['count']) && sizeof($top_5_trades[0]['count'])>0){
		for($i=0;$i<5;$i++){
			if(isset($top_5_trades[$i]['count'])){
				$top_5_trades[$i]['closed']=$top_5_trades[$i]['count']-$top_5_trades[$i]['open'];
			}
		}
	}
	// Analysis Panel End //
}
?>

<script language="javascript">
function projectValue(p_id)
{
	
	var project_id_value=p_id;
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	params = "project_id="+project_id_value;
	xmlhttp.open("POST", "issue_to_session.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			
			it_total=xmlhttp.response;
			//alert()
			prevIT(obj1=true);
			nextIT(obj1=true);
			//alert(it_total);
		}
	}
		xmlhttp.send(params);
	  
	   //document.getElementById('myAnchor').value=name;
}

function session_destroy()
{
	if (window.XMLHttpRequest)
	{
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST", "issue_to_session_destroy.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			xmlhttp.response;
			
		}
	}
		xmlhttp.send();
	  
	   //document.getElementById('myAnchor').value=name;	
}

var expand_IT=false;
var limit = 3;
var qc_end = 0;
var qc_total = <?php echo $qc_total?>;
//alert(qc_total);
//to show previous QC
function prevQC(obj1)
{
	qc_end -= limit;
	if (qc_end <= 0)
	{
		qc_end = 0;
		document.getElementById("qc_previous").style.display = "none";
	}
	if(obj1==true)
	{
		document.getElementById("chart1").src = "/full_analysis_expand.php?count=" + qc_end;
	}
	else
	{
		document.getElementById("chart1").src = "/full_analysis.php?count=" + qc_end;
	}
	var end_l = qc_end + limit;
	if (end_l > qc_total)
	{
		end_l = qc_total;
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;
	document.getElementById("qc_next").style.display = "";
}
/// to show next QC
function nextQC(obj1)
{
	try{
	qc_end += limit;
	if(obj1==true)
	{
		document.getElementById("chart1").src = "/full_analysis_expand.php?count=" + qc_end;
	}
	else
	{
		document.getElementById("chart1").src = "/full_analysis.php?count=" + qc_end;
	}
	
	document.getElementById("qc_previous").style.display = "";

	var end_l = qc_end + limit;
	if (end_l > qc_total)
	{
		end_l = qc_total;
		document.getElementById("qc_next").style.display = "none";
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;

	}catch(e){
		alert(e.message);
	}
}




//End changes for QC Prev and Back button

//////////////////////////PROGRESS MONITORING///////////////////////////
var pm_end = 0;
var pm_total = <?php echo $pm_total?>;
//to show previous PM
function prevPM(obj1)
{
	pm_end -= limit;
	if (pm_end <= 0)
	{
		pm_end = 0;
		document.getElementById("pm_previous").style.display = "none";
	}
	if(obj1==true)
	{
		document.getElementById("chart3_if").src = "/progress_chart_expand.php?count=" + pm_end;
	}
	else
	{
		document.getElementById("chart2_if").src = "/progress_chart.php?count=" + pm_end;
	}
	
	
	
	
	
	var end_l = pm_end + limit;
	if (end_l > pm_total)
	{
		end_l = pm_total;
	}
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
	document.getElementById("pm_next").style.display = "";
}
/// to show next QC
function nextPM(obj1)
{
	try{
	pm_end += limit;
	
	//document.getElementById("chart2_if").src = "/progress_chart.php?count=" + pm_end;
	if(obj1==true)
	{
		document.getElementById("chart3_if").src = "/progress_chart_expand.php?count=" + pm_end;
	}
	else
	{
		document.getElementById("chart2_if").src = "/progress_chart.php?count=" + pm_end;
	}
	
	
	
	document.getElementById("pm_previous").style.display = "";
	var end_l = pm_end + limit;
	if (end_l > pm_total)
	{
		end_l = pm_total;
		document.getElementById("pm_next").style.display = "none";
	}
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
	}catch(e){
		alert(e.message);
	}
}

//end changes for expand PRO MON to

//////////////////////ISSUED TO/////////////////////
var it_end = 0;
var it_total = <?php echo $it_total?>;


//to show previous QC
function prevIT(obj1)
{
	//var div_objs = document.getElementsByClassName(div_ids);
	
	it_end -= limit;
	if (it_end <= 0)
	{
		it_end = 0;
		document.getElementById("it_previous").style.display = "none";
	}
	if(obj1==true)
	{
		document.getElementById("chart3_if").src = "/issue_to_chart_expand.php?count=" + it_end;
	}
	else
	{
		document.getElementById("chart3_if").src = "/issue_to_chart.php?count=" + it_end;
	}
	var end_l = it_end + limit;
	if (end_l > it_total)
	{
		end_l = it_total;
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
	document.getElementById("it_next").style.display = "";
}
/// to show next QC
function nextIT(obj1)
{
	try{
	it_end += limit;
	
	if(obj1==true)
	{
		document.getElementById("chart3_if").src = "/issue_to_chart_expand.php?count=" + it_end;
	}
	else
	{
		document.getElementById("chart3_if").src = "/issue_to_chart.php?count=" + it_end;
	}
	//document.getElementById("chart3_if").src = "/issue_to_chart.php?count=" + it_end;
	document.getElementById("it_previous").style.display = "";
	var end_l = it_end + limit;
	if (end_l > it_total)
	{
		end_l = it_total;
		document.getElementById("it_next").style.display = "none";
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
	}catch(e){
		alert(e.message);
	}
}




///next button handling at document load
$(document).ready(function(){

	//QC
	if ((qc_end+limit) >= qc_total)
	{
		document.getElementById("qc_next").style.display = "none";
	}
	var end_l = qc_end + limit;
	if (end_l > qc_total)
	{
		end_l = qc_total;
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;
	
	//PM
	if ((pm_end+limit) >= pm_total)
	{
		document.getElementById("pm_next").style.display = "none";
	}
	end_l = pm_end + limit;
	if (end_l > pm_total)
	{
		end_l = pm_total;
	}
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
	
	//IT
	if ((it_end+limit) >= it_total)
	{
		document.getElementById("it_next").style.display = "none";
	}
	end_l = it_end + limit;
	if (end_l > it_total)
	{
		end_l = it_total;
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
});
</script>
<style type="text/css">
table.gridtable {
	border-width: 1px;
	border-color: #FFF;
	border-collapse: collapse;
}
table.gridtable td {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #FFF;
}
#chart12{
	
	}
</style>
<script language="javascript">

function expandIframe (div_id, iframe_id){
	try{
	var div_obj = document.getElementById(div_id);
	var iframe_obj = document.getElementById(iframe_id);
	div_obj.style.width = "950px";
	div_obj.style.height = "450px";

	iframe_obj.style.width = "875px";
	iframe_obj.style.height = "350px";
	
	if (div_id=="full_analysis")
	{
		document.getElementById("progress_monitor_summary").style.display = "none";
		document.getElementById("issue_to_summary").style.display = "none";
		document.getElementById("notification_board").style.display = "none";
		document.getElementById("chart1").src = "/full_analysis_expand.php?count=0&pid=<?php echo $qc_expand; ?>"
		document.getElementById("exp_pm").style.display = "none";
		document.getElementById("close_pm").style.display = "block";
		document.getElementById("QC").style.display = "none";
		expand_IT=true;
		
	}
	else if (div_id=="progress_monitor_summary")
	{
		document.getElementById("full_analysis").style.display = "none";
		document.getElementById("issue_to_summary").style.display = "none";
		document.getElementById("notification_board").style.display = "none";
		document.getElementById("chart2_if").src = "/progress_chart_expand.php?count=0&pm_expd=<?php echo $pm_expand_id;?>"
		document.getElementById("exp_pm1").style.display = "none";
		document.getElementById("close_pm1").style.display = "block";
		document.getElementById("PM").style.display = "none";
		expand_IT=true;
		
	}
	else if (div_id=="issue_to_summary")
	{
		document.getElementById("full_analysis").style.display = "none";
		document.getElementById("progress_monitor_summary").style.display = "none";
		document.getElementById("notification_board").style.display = "none";
		document.getElementById("chart3_if").src = "/issue_to_chart_expand.php?count=0&it_name=<?php echo $it_name_expand; ?>";
		document.getElementById("exp_pm2").style.display = "none";
		document.getElementById("close_pm2").style.display = "block";
		document.getElementById("IT").style.display = "none";
		expand_IT=true;
		
	}
	else if (div_id=="notification_board")
	{
		document.getElementById("full_analysis").style.display = "none";
		document.getElementById("progress_monitor_summary").style.display = "none";
		document.getElementById("issue_to_summary").style.display = "none";
		document.getElementById("exp_pm3").style.display = "none";
		document.getElementById("close_pm3").style.display = "block";
		document.getElementById("chart4_nb").src = "/notification_board_expand.php";
		expand_IT=true;
	}
	
	}catch(e){
		alert(e.message);
	}
}
function closeIframe(div_id_close, iframe_id_close)
{
		var div_obj_close = document.getElementById(div_id_close);
		var iframe_obj_close = document.getElementById(iframe_id_close);
		div_obj_close.style.width = "470px";
		div_obj_close.style.height = "305px";
		iframe_obj_close.style.width = "395px";
		iframe_obj_close.style.height = "195px";
		
		if (div_id_close=="full_analysis")
		{
			document.getElementById("full_analysis").style.display = "block";
			document.getElementById("issue_to_summary").style.display = "block";
			document.getElementById("notification_board").style.display = "block";
			document.getElementById("progress_monitor_summary").style.display = "block";
			document.getElementById("chart1").src = "/full_analysis.php?count=0"
			document.getElementById("exp_pm").style.display = "block";
			document.getElementById("close_pm").style.display = "none";
			document.getElementById("QC").style.display = "block";
			expand_IT=false;
			
		}
		else if (div_id_close=="progress_monitor_summary")
		{
			document.getElementById("full_analysis").style.display = "block";
			document.getElementById("issue_to_summary").style.display = "block";
			document.getElementById("notification_board").style.display = "block";
			document.getElementById("progress_monitor_summary").style.display = "block";
			document.getElementById("chart2_if").src = "/progress_chart.php?count=0"
			document.getElementById("exp_pm1").style.display = "block";
			document.getElementById("close_pm1").style.display = "none";
			document.getElementById("PM").style.display = "block";
			expand_IT=false;
		
		}
		else if (div_id_close=="issue_to_summary")
		{
			document.getElementById("full_analysis").style.display = "block";
			document.getElementById("issue_to_summary").style.display = "block";
			document.getElementById("notification_board").style.display = "block";
			document.getElementById("progress_monitor_summary").style.display = "block";
			document.getElementById("chart3_if").src = "/issue_to_chart.php?count=0"
			document.getElementById("exp_pm2").style.display = "block";
			document.getElementById("close_pm2").style.display = "none";
			document.getElementById("IT").style.display = "block";
			expand_IT=false;
			session_destroy();
		}
		else if (div_id_close=="notification_board")
		{
			iframe_obj_close.style.width = "395px";
			iframe_obj_close.style.height = "220px";
			document.getElementById("full_analysis").style.display = "block";
			document.getElementById("issue_to_summary").style.display = "block";
			document.getElementById("notification_board").style.display = "block";
			document.getElementById("progress_monitor_summary").style.display = "block";
			
			
			document.getElementById("exp_pm3").style.display = "block";
			document.getElementById("close_pm3").style.display = "none";
			document.getElementById("chart4_nb").src = "/notification_board.php";
			expand_IT=false;
			
			
			
		}
		
		
		
		
		
		
		
		
		
}
</script>

<div class="search_multiple">
	<div class="first_box" id="full_analysis"> <!--Start Code For Full Analysis Box-->
		<h1><img src="images/analysis_big.png" width="35" height="43" align="absmiddle" /> Quality Control Summary</a></h1>
        <h1 style="margin-left:165px;font-size:14px;margin-top:20px;"><a href="#" onclick="expandIframe('full_analysis','chart1')" style="text-decoration:none;" id="exp_pm"><img src="images/maximize.png" alt="Click to expand" title="Click to expand" /></a>
        <a href="#"  onclick="closeIframe('full_analysis','chart1')" style="text-decoration:none;display:none;margin-left:500px;margin-top:-10px;" id="close_pm" ><img src="images/close_new.png" alt="Click to close" title="Click to close" /></a>
        </h1>
		 <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;">
         <tr><td width="30px">
        <a href="#" onclick="prevQC(expand_IT)"><img src="images/btn_prev.gif" id="qc_previous" style="display:none"/></a>
          </td>
          <td>
		    <iframe src="/full_analysis.php?count=0" id="chart1" style="width:400px; height:200px;margin:0px;padding:0px;border:0px;"></iframe>
            
            
			<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
				<tr id="QC" style="display:block;" >
					<td id="qc_row" ></td>
				</tr>
				<tr>
					<td align="right"><span style="width:20px;height:20px;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open <span style="width:20px;height:20px;background:yellow">&nbsp;&nbsp;&nbsp;&nbsp;</span> Pending <span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Fixed <span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Closed </td>
				</tr>
			</table>
            </td>
            <td width="30px"><a href="#" onclick="nextQC(expand_IT)"><img src="images/btn_next.gif" id="qc_next"/></a></td>
            </tr>
            </table>
	</div>  
    <!--End Code For Full Analysis Box-->
    
     <!--Start Code For Issue To  Box-->
<div class="first_box" style="margin-left:5px;" id="progress_monitor_summary">  
		<h1><img src="images/progress_monitor_big.png" width="43" height="40" align="absmiddle" /> Progress Monitor Summary</h1><h1 style="margin-left:165px;font-size:14px;margin-top:20px;"><a href="#" onclick="expandIframe('progress_monitor_summary','chart2_if')" style="text-decoration:none;" id="exp_pm1"><img src="images/maximize.png" alt="Click to expand" title="Click to expand" /></a>
        
        <a href="#" onclick="closeIframe('progress_monitor_summary','chart2_if')"   style="text-decoration:none;display:none;margin-left:450px;" id="close_pm1" ><img src="images/close_new.png" alt="Click to close" title="Click to close" /></a>
        </h1>
		 <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;">
         <tr><td width="30px">
        <a href="#" onclick="prevPM(expand_IT)"><img src="images/btn_prev.gif" id="pm_previous" style="display:none"/></a>
          </td>
          <td>
		    <iframe src="/progress_chart.php?count=0" id="chart2_if" style="width:400px; height:200px;margin:0px;padding:0px;border:0px;"></iframe>
			<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
				<tr id="PM" style="display:block;">
					<td id="pm_row"></td>
				</tr>
		<tr>
			<td align="right"><span style="width:20px;height:20px;background:#ff0000;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Behind <span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Complete <span style="width:20px;height:20px;background:#ffff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> In progress <span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Signed off  <span style="width:20px;height:20px;background:#E4E4E4">&nbsp;&nbsp;&nbsp;&nbsp;</span> Not Started</td>
		</tr>
			</table>
            </td>
            <td width="30px"><a href="#" onclick="nextPM(expand_IT)"><img src="images/btn_next.gif" id="pm_next"/></a></td>
            </tr>
            </table>
</div>
    <!--End Code For Issue To  Box-->   
    
    <!--Start Code For Progress Task Monitoring Box--> 
    
    <div class="first_box" id="issue_to_summary"> 
		<h1><img src="images/Issued_to.png" width="43" height="40" align="absmiddle" />Issue To Summary <!-- (Trades and Contractors)--></h1>
         <h1 style="margin-left:225px;font-size:14px;margin-top:20px;"><a href="#" onclick="expandIframe('issue_to_summary','chart3_if')" style="text-decoration:none;" id="exp_pm2"><img src="images/maximize.png" alt="Click to expand" title="Click to expand" /></a>
        </h1>
        <h1>
        <a href="#" onclick="closeIframe('issue_to_summary','chart3_if')"   style="text-decoration:none;display:none;margin-left:500px;" id="close_pm2" ><img src="images/close_new.png" alt="Click to close" title="Click to close" /></a>
        </h1>
        	  <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
         <tr><td width="30px">
        <a href="#" onclick="prevIT(expand_IT)"><img src="images/btn_prev.gif" id="it_previous" style="display:none"/></a>
          </td>
          <td>
		    <iframe src="/issue_to_chart.php?count=0" id="chart3_if" style="width:400px; height:200px;margin:0px;padding:0px;border:0px;"></iframe>
		<table width="100%" border="0" cellspacing="0" cellpadding="2" style="font-size:11px;">
            <tr>
				<tr id="IT" style="display:block;">
					<td id="it_row"></td>
				</tr>
				<tr>
					<td align="right"><span style="width:20px;height:20px;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open <span style="width:20px;height:20px;background:yellow">&nbsp;&nbsp;&nbsp;&nbsp;</span> Pending <span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Fixed <span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Closed </td>
				</tr>
            </tr>
		</table>
         </td>
            <td width="30px"><a href="#" onclick="nextIT(expand_IT)"><img src="images/btn_next.gif" id="it_next"/></a></td>
            </tr>
            </table>
</div>
<!--End Code For Progress Task Monitoring Box--> 
<div class="first_box" style="margin-left:5px;" id="notification_board">  
		<h1><img src="images/notification_board.png" width="43" height="40" align="absmiddle" /> Notification Board</h1>
         <h1 style="margin-left:240px;font-size:14px;margin-top:20px;"><a href="#" onclick="expandIframe('notification_board','chart4_nb')" style="text-decoration:none;" id="exp_pm3"><img src="images/maximize.png" alt="Click to expand" title="Click to expand" /></a>
        </h1>
        <h1>
        <a href="#" onclick="closeIframe('notification_board','chart4_nb')"  style="text-decoration:none;display:none;margin-left:720px;margin-top:-20px;" id="close_pm3" ><img src="images/close_new.png" alt="Click to close" title="Click to close" /></a>
        </h1>
		 <table width="100%"  border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;" class="gridtable">
         	 
             <tr>
                <td> <iframe src="/notification_board.php" id="chart4_nb" style="width:400px; height:220px;margin:0px;padding:0px;border:0px;"></iframe></td>
          	</tr>
          </table>
</div>
  
</div>
    <br/>
    <br/>
<!--End  Code For All  Box--> 
