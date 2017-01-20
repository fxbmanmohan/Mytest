<link href="dashboard.css" rel="stylesheet" type="text/css" /> 
<?php
$builder_id=$_SESSION['ww_builder_id'];
$table="";

$project=0; $inspector=0; $trade=0; $d_total=0; $d_open=0; $d_closed=0; $d_pending=0; $d_in_progress=0; $t5=0;
$top_5_op=array(); $top_5_all=array(); $top_5_td=array(); $top_5_trades=array();

$q="SELECT *, p.project_id as pro_id FROM user_projects p 
	LEFT JOIN user b ON p.user_id=b.user_id 
	LEFT JOIN pms_builder_to_subbuilders sb ON p.project_id=sb.fk_p_id
	WHERE (p.user_id='$builder_id' OR sb.sb_id='$builder_id') and p.is_deleted=0 GROUP BY p.project_id";
	
$r=$obj->db_query($q);
$project=$obj->db_num_rows($r);

if($project>0){
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


<div class="search_multiple total_health">
	<div class="first_box"> 
		<h1><img src="images/analysis_big.png" width="35" height="43" align="absmiddle" /> Full Analysis</h1>
		<img src="images/graph_sample.jpg" width="456" height="173" />
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
				<td width="24%" align="right">Total Projects :</td>
				<td width="32%" align="left" class="txt_blue">2</td>
				<td width="27%" align="right">Total Inspectors :</td>
				<td width="17%" align="left" class="txt_blue">2</td>
		</tr>
		<tr>
				<td align="right">Total Trades :</td>
				<td align="left" class="txt_blue">3</td>
				<td align="right">Inspections Closed :</td>
				<td align="left" class="txt_blue">0</td>
		</tr>
		<tr>
				<td align="right">Inspections Open :</td>
				<td align="left" class="txt_blue">7</td>
				<td>&nbsp;</td>
				<td align="left">&nbsp;</td>
		</tr>
</table>

		
</div>
<div class="first_box" style="margin-left:5px;"> 
		<h1><img src="images/progress_monitor_big.png" width="43" height="40" align="absmiddle" /> Progress Monitor</h1>
		<img src="images/graph_sample2.jpg" width="456" height="155" />
		<table width="100%" border="0" cellspacing="0" cellpadding="2" style="font-size:11px;">
		<tr>
				<td width="24%" align="right">Total Projects :</td>
				<td width="32%" align="left" class="txt_blue">2</td>
				<td width="27%" align="right">Total Inspectors :</td>
				<td width="17%" align="left" class="txt_blue">2</td>
		</tr>
		<tr>
				<td align="right">Total Trades :</td>
				<td align="left" class="txt_blue">3</td>
				<td align="right">Inspections Closed :</td>
				<td align="left" class="txt_blue">0</td>
		</tr>
		<tr>
				<td align="right">Inspections Open :</td>
				<td align="left" class="txt_blue">7</td>
				<td>&nbsp;</td>
				<td align="left">&nbsp;</td>
		</tr>
</table>

		
</div>
    
   
</div>