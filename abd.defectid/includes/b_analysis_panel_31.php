<?php
//************************************************************************************/
			/* QUERY FOR FULL ANALYSIS CHART*/
//************************************************************************************/		
	$qs="SELECT p.*, SUM(IF(d.status = 'Open',1,0)) AS open, SUM(IF(d.status='Closed',1,0)) AS closed
 FROM pms_defects AS d  LEFT JOIN user_projects AS p ON ( p.project_id= d.project_id and user_id=1 )
 GROUP BY p.project_id  ORDER BY project_id";
	
$rs=$obj->db_query($qs);
          $proj_name= array();
		  $close=array();
		  $open=array();
		  while($f=$obj->db_fetch_assoc($rs)){
			
		 // print_r($f); 
		  $open[]= $f['open'];
		   $close[]=$f['closed'];
		 $proj_name[] =  '"'.$f['project_name'].'"';
		
		 }
		
		 $pr_name=implode(',',$proj_name); 
		$open_proj=implode(',',$open); 
		$close_proj=implode(',',$close); 
		/* END CODE FOR ISSUE TO*/
//************************************************************************************/
			/* QUERY FOR ISSUE TO CHART*///************************************************************************************/
	  $issue="SELECT up.project_name,d.*, i.issue_to_name, SUM(IF(d.status = 'Open',1,0)) AS open, 
SUM(IF(d.status='Closed',1,0)) AS closed ,count(d.status) As Total FROM pms_defects AS d  LEFT JOIN 
inspection_issue_to AS i ON ( i.project_id= d.project_id )LEFT JOIN user_projects up ON d.project_id=up.project_id
 GROUP BY d.project_id  ORDER BY project_id";
		$issueto=$obj->db_query($issue);
		$issue_name= array();
		$issue_close=array();
		$issue_open=array();
		$issue_total=array();
        while($issue_chart=$obj->db_fetch_assoc($issueto))
		{
			$issue_name[]= '"'.$issue_chart['issue_to_name'].'"';
		  	$issue_total[]=$issue_chart['Total'];
			$issue_open[]=$issue_chart['open'];
			$issue_close[]=$issue_chart['closed'];
		 }	 
       		$issue_names=implode(',',$issue_name); 
			$issue_totals=implode(',',$issue_total); 
			$issue_opens=implode(',',$issue_open); 
			$issue_closes=implode(',',$issue_close); 

		/*END CODE FOR ISSUE TO */
//************************************************************************************/
			/* QUERY FOR PROGRESS MONITORING CHART*/
//************************************************************************************/		
	//Chart for Progress Monitoring
		
		  	$prg_m="SELECT p.project_name, pm .*, SUM(pm.status = 'Behind') AS Behind, SUM(pm.status = 'On Time') AS Timeo ,SUM(pm.status = 'Ahead') AS Ahead, SUM(pm.status = 'Complete') AS Complete FROM progress_monitoring_update AS pm LEFT JOIN user_projects AS p ON ( p.project_id = pm.project_id) ORDER BY Behind DESC";
		  	$p_monis=$obj->db_query($prg_m);
           	$behind= array();
			$timeo=array();
			$ahead=array();
			$complete=array();
			$project_name=array();
			
		   while($p_moni=$obj->db_fetch_assoc($p_monis))
		   {
				
				$behind[]= $p_moni['Behind'];
				$timeo[]=$p_moni['Timeo'];
				$ahead[]=$p_moni['Ahead'];
				$complete[]=$p_moni['Complete'];
				$project_name[]='"'.$p_moni['project_name'].'"';
			}
			$behinds=implode(',',$behind); 
			$timeos=implode(',',$timeo); 
			$aheads=implode(',',$ahead); 
			$completes=implode(',',$complete); 
			$project_names=implode(',',$project_name);
		    
/* END PHP CODE FOR PROGRESS MONITORING*/

?>
<link href="dashboard.css" rel="stylesheet" type="text/css" /> 
<link href="maxChart/style/style.css" rel="stylesheet" type="text/css" />
<link class="include" rel="stylesheet" type="text/css" href="dist/jquery.jqplot.min.css" />
<script class="include" type="text/javascript" src="../jquery.min.js"></script>
<script class="include" type="text/javascript" src="dist/jquery.jqplot.js"></script>
 <script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.barRenderer.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.pointLabels.min.js"></script>
<script class="code" type="text/javascript">
$(document).ready(function(){
	
	
		var s1 = [<?php echo $open_proj; ?>]; //For Full Analysis Total Open Project for each Project
		var s2 = [<?php echo $close_proj; ?>]; //For Full Analysis Total Closed Project for each Project
	   
		var s3 = [<?php echo $issue_totals; ?>]; //For Issue to Total Project for each Issue to name
		var s4 = [<?php echo $issue_opens; ?>]; //For Issue to Total  Open Project for each Issue to name
		var s5 = [<?php echo $issue_closes; ?>]; //For Issue to Total Closed Project for each Issue to name
		
		
		
		var s6 = [<?php echo $aheads; ?>]; //For Issue to Total Project for each Issue to name
		var s7 = [<?php echo $behinds; ?>]; //For Issue to Total Project for each Issue to name
		var s8 = [<?php echo $timeos; ?>]; //For Issue to Total Project for each Issue to name
		var s9 = [<?php echo $completes; ?>]; //For Issue to Total Project for each Issue to name
   
   
    // Can specify a custom tick Array.
    // Ticks should match up one for each y value (category) in the series.
    	var ticks = [<?php echo $pr_name; ?>];
	
		var ticks1 = [<?php echo $issue_names; ?>];
		
		var ticks2 = [<?php echo $project_names; ?>];
    
    	// code for Full Analysis Chart
		var plot1 = $.jqplot('chart1', [s1, s2], {
		// The "seriesDefaults" option is an options object that will
		// be applied to all series in the chart.
		seriesDefaults:{
		    renderer:$.jqplot.BarRenderer,
		    rendererOptions: {fillToZero: true,barWidth:25}
		},
			
			
		// Custom labels for the series are specified with the "label"
		// option on the series option.  Here a series option object
		// is specified for each series.
	 
		axes: {
		    // Use a category axis on the x axis and use our custom ticks.
		    xaxis: {
			renderer: $.jqplot.CategoryAxisRenderer,
			ticks: ticks
		    },
				
		    // Pad the y axis just a little so bars can get close to, but
		    // not touch, the grid boundaries.  1.2 is the default padding.
		    yaxis: {
				
			pad: 1.05,
					min: 0, 
			tickOptions: {formatString: '%d', showMark: true,show: true,showLabel: true}
		    },
				
				  highlighter: { show: false }
		},seriesColors: [ "#ff0000", "#00ff00"]
	    });
		// End code for Full Analysis Chart
		// code for Issue to Chart
	    var plot2 = $.jqplot('chart2', [s3, s4,s5], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true,barWidth:25}
        },
        // Custom labels for the series are specified with the "label"
        // option on the series option.  Here a series option object
        // is specified for each series.
 
        axes: {
            // Use a category axis on the x axis and use our custom ticks.
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks1
            },
            // Pad the y axis just a little so bars can get close to, but
            // not touch, the grid boundaries.  1.2 is the default padding.
            yaxis: {
			
                pad: 1.05,
				min: 0, 
                tickOptions: {formatString: '%d'}
            }
        },seriesColors: [  "#ff0000", "#00ff00", "#00ff00"]
    });
		// End code for Full Analysis Chart
		// code for Progress Monitoring
		var plot3 = $.jqplot('chart3', [s6, s7,s8, s9], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true,  barWidth:25},
			lineWidth: 1,
			
        },
        // Custom labels for the series are specified with the "label"
        // option on the series option.  Here a series option object
        // is specified for each series.
 
        axes: {
            // Use a category axis on the x axis and use our custom ticks.
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks2,
				
				
            },
            // Pad the y axis just a little so bars can get close to, but
            // not touch, the grid boundaries.  1.2 is the default padding.
            yaxis: {
			
                pad: 1.05,
				min: 0, 
					
                tickOptions: {formatString: '%d'}
            }
        },seriesColors: [ "#ff0000", "#00ff00","#FFFF00","#3399FF"]
    });
	
	
});  </script>
  <style type="text/css">
    
    .note {
        font-size: 0.8em;
    }
    .jqplot-yaxis-tick {
      white-space: nowrap;
    }
  </style>
<?php
$builder_id=$_SESSION['ww_builder_id'];
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
	<div class="first_box"> <!--Start Code For Full Analysis Box-->
		<h1><img src="images/analysis_big.png" width="35" height="43" align="absmiddle" /> Quality Control Summary</h1>
		 
		    <div id="chart1" style="width:450px; height:200px;margin-top:50px;"></div>
			<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
				<tr>
					<td>Showing 1-5 projects of 20 projects</td>
				</tr>
				<tr>
					<td align="right"><span style="width:20px;height:20px;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open <span style="width:20px;height:20px;background:green">&nbsp;&nbsp;&nbsp;&nbsp;</span> Close</td>
				</tr>
			</table>
	</div>  
    <!--End Code For Full Analysis Box-->
    
     <!--Start Code For Issue To  Box-->
<div class="first_box" style="margin-left:5px;">  
		<h1><img src="images/progress_monitor_big.png" width="43" height="40" align="absmiddle" /> Progress Monitor Summary</h1>
		 <div id="chart3" style="width:450px; height:200px;margin-top:50px;"></div><!--Show Chart For Progress Monitoring--> 
		<table width="100%" border="0" cellspacing="0" cellpadding="2" style="font-size:11px;">
		<tr>
			<td>Showing 1-5 projects of 20 projects</td>
		</tr>
		<tr>
			<td align="right"><span style="width:20px;height:20px;background:#ff0000;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Behind <span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Ahead <span style="width:20px;height:20px;background:#ffff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> On Time <span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Completed</td>
		</tr>
	</table>
</div>
    <!--End Code For Issue To  Box-->   
    
    <!--Start Code For Progress Task Monitoring Box--> 
    
    <div class="first_box" style="margin-left:5px;"> 
		<h1><img src="images/Issued_to.png" width="43" height="40" align="absmiddle" /> Issue To Summary (Trades and Contractors)</h1>
        	<div id="chart2" style="width:450px; height:200px;margin-top:50px;"></div><!--Show Chart For Progress Monitoring--> 
		<table width="100%" border="0" cellspacing="0" cellpadding="2" style="font-size:11px;">
            <tr>
				<tr>
					<td>Showing 1-5 Issued To of 20 Issued To</td>
				</tr>
				<tr>
					<td align="right"><span style="width:20px;height:20px;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open <span style="width:20px;height:20px;background:green">&nbsp;&nbsp;&nbsp;&nbsp;</span> Close</td>
				</tr>
            </tr>
		</table>
</div>
<!--End Code For Progress Task Monitoring Box-->   
</div>
<!--End  Code For All  Box--> 
