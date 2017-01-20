<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; ?>
<link href="dashboard.css" rel="stylesheet" type="text/css" />
</head>
<body id="dt_example">
<div id="container">
<div class="content_hd1" style="background-image:url(images/full_analysis_hd.png);"></div>
    <div class="demo_jui">
<?php
$c_id=$_SESSION['ww_c_id'];

$manager=0; $project=0; $inspector=0; $trade=0; $d_total=0; $d_open=0; $d_closed=0; $d_pending=0; $d_in_progress=0; $t5=0; 
$top_5_op=array(); $top_5_all=array(); $top_5_td=array();

// count managers
if($obj->db_num_rows($obj->db_query("SELECT COUNT(*) AS count FROM ".BUILDERS." WHERE fk_c_id='$c_id'"))){
	$fman=$obj->db_fetch_assoc($obj->db_query("SELECT COUNT(*) AS count FROM ".BUILDERS." WHERE fk_c_id='$c_id'"));
	$manager=$fman['count'];
}

// count projects
$q="SELECT *, p.project_id AS pro_id 
	FROM ".PROJECTS." p 
	LEFT JOIN ".BUILDERS." b ON p.user_id=b.user_id 
	WHERE b.fk_c_id='$c_id'";

$r=$obj->db_query($q);
$project=$obj->db_num_rows($r);

if($project>0){
	while($f=$obj->db_fetch_assoc($r)){
		
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
	
	$d_total=$d_open+$d_closed;
	
	// Top trades with issues open
	if(isset($top_5_td['open']) && sizeof($top_5_td['open'])>0){
		arsort($top_5_td['open'],SORT_NUMERIC);
		$i=0;
		foreach($top_5_td['open'] as $key => $val){
			$top_5_td_op[$i]="<td style='color:#FFFFFF'>".stripslashes($key). "</td><td style='color:#FFFFFF' align='center'>" . $val . "</td>";
			$i++;
		}
	}

	// Top trades with total issue count
	if(isset($top_5_td['count']) && sizeof($top_5_td['count'])>0){
		arsort($top_5_td['count'],SORT_NUMERIC);
		$i=0;
		foreach($top_5_td['count'] as $key => $val){
			$top_5_td_all[$i]="<td style='color:#FFFFFF'>".stripslashes($key). "</td><td style='color:#FFFFFF' align='center'>" . $val . "</td>";
			$i++;
		}
	}
}
?>
		<div class="search_multiple total_health">
			<table width="966" cellpadding="0" cellspacing="5" border="0">
				<tr>
					<td>
						<div class="dashboard_msj" style="background-image:url(images/dashboard-projects.png);">
							<div class="statistics_msj"><?=$project?></div>
							<div class="about_msj">Total Projects</div>
						</div>
					</td>
					<td>
						<div class="dashboard_msj" style="background-image:url(images/dashboard-managers.png);">
							<div class="statistics_msj"><?=$manager?></div>
							<div class="about_msj">Total Managers</div>
						</div>
					</td>
					<td>
						<div class="dashboard_msj" style="background-image:url(images/dashboard-inspectors.png);">
							<div class="statistics_msj"><?=$inspector?></div>
							<div class="about_msj">Total Inspectors</div>
						</div>
					</td>
					<td>
						<div class="dashboard_msj" style="background-image:url(images/dashboard-trades.png);">
							<div class="statistics_msj"><?=$trade?></div>
							<div class="about_msj">Total Trades</div>
						</div>
					</td>
					<td>
						<div class="dashboard_msj" style="background-image:url(images/dashboard-issues.png);">
							<div class="statistics_msj"><?=$d_total?></div>
								<div class="about_msj">
									Inspections Open: <?=$d_open?><br />
									Inspections Closed: <?=$d_closed?><br />									
								</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="dashboard_msj" style="background-image:url(images/top-5-open.png); width:370px; height:275px;">
							<div class="statistics_top_5_msj" style="width:240px;">Top trades with inspections open</div>
								<div class="about_top_5_msj" style="margin-top:90px;">
									<table border="0" width="88%" style="color:#FFFFFF;">
									<th align="left" style="color:#FFFFFF; text-decoration:underline;">Trade</th>
									<th style="color:#FFFFFF; text-decoration:underline;">Total</th>
									<?php
									for($i=0;$i<5;$i++){
										if(isset($top_5_td_op[$i])){
											echo '<tr>';
											echo $top_5_td_op[$i];
											echo '</tr>';
										}
									}
									?>
									</table>
								</div>
						</div>
					</td>
					<td>&nbsp;</td>
					<td colspan="2">
						<div class="dashboard_msj" style="background-image:url(images/top-5.png); width:370px; height:275px;">
							<div class="statistics_top_5_msj" style="width:240px;">Top trades with total inspection count</div>
								<div class="about_top_5_msj" style="margin-top:90px;">
									<table border="0" width="88%" style="color:#FFFFFF;">
									<th align="left" style="color:#FFFFFF; text-decoration:underline;">Trade</th>
									<th style="color:#FFFFFF; text-decoration:underline;">Total</th>
									<?php
									for($i=0;$i<5;$i++){
										if(isset($top_5_td_all[$i])){
											echo '<tr>';
											echo $top_5_td_all[$i];
											echo '</tr>';
										}
									}
									?>
									</table>
								</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	
		<table width="980" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
			<thead>
                <tr>
					
					<th nowrap="nowrap">Project Name</th>					
					<th>Project Type</th>
					<th>Address</th>
					<th>Suburb</th>
					<th>State</th>
					<th>Postcode</th>
					<th>Country</th>
					<th>Detail</th>
                </tr>
            </thead>
			<tbody>
	<?php
		$r=mysql_query($q);
		while($f=mysql_fetch_assoc($r)){
			echo "<tr class='gradeA'>
				
				<td>".stripslashes($f['project_name'])."</td>
				<td>".stripslashes($f['project_type'])."</td>
				<td>".stripslashes($f['project_address_line1'])."</td>
				<td>".stripslashes($f['project_suburb'])."</td>
				<td>".stripslashes($f['project_state'])."</td>
				<td>".stripslashes($f['project_postcode'])."</td>
				<td>".stripslashes($f['project_country'])."</td>
				<td align='center'>
					<a href='?sect=c_show_project_detail&id=".base64_encode($f['pro_id'])."'><img src='images/edit.png' border='none' /></a>
				</td>
			    </tr>";
		}
	?>
			</tbody>
		</table>
    </div>
    <div class="spacer"></div>
</div>
</body>
</html>
