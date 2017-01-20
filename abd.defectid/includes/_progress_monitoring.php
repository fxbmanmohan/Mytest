<?php
ini_set('auto_detect_line_endings', true);
 include('func.php');
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

$builder_id=$_SESSION['ww_builder_id'];
if(isset($_REQUEST['id']))
{
	$update='update progress_monitoring set is_deleted=1,last_modified_date=now(),last_modified_by="'.$builder_id .'" where progress_id ="'.base64_decode($_REQUEST['id']).'"';

	$update_issuname='update issued_to_for_progress_monitoring set is_deleted=1,last_modified_date=now(),last_modified_by="'.$builder_id .'" where progress_id ="'.base64_decode($_REQUEST['id']).'"';
	mysql_query($update);
	mysql_query($update_issuname);
	$_SESSION['progress_task_del']='Progress task deleted successfully.';
	header('loaction:?sect=progress_monitoring');
	
}


if(isset($_POST['location_csv_x'])) // Location/ subloaction import CSV file.
{
	
	$success='';
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name']))
	{
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV')
		{
			$path=$_SERVER['DOCUMENT_ROOT'].'/csv/temp/'; // CSV Path
			//echo $path; die;
			$upload=move_uploaded_file($_FILES['csvFile']['tmp_name'],$path.$filename);
			if($upload)
			{
				$databasetable = "progress_monitoring"; // database table name
				$fieldseparator = ","; // CSV file comma format
				$lineseparator = "\n";
				$csvfile = $path.$filename; //CSV file name
				$addauto = 1;
				$save = 1;
				/********************************/
				
				$file = fopen($csvfile,"r");
				
				$size = filesize($csvfile); //check file record
				
				if(!$size) {
				echo "File is empty.\n";
				exit;
				}
				//$csvcontent = fread($file,$size);
				//fclose($file);
				$lines = 0;
				$queries = "";
				$linearray = array();
				$fieldarray= array();
				$record='';
/**				$csvcontent = str_replace("\r","\n",$csvcontent);
				foreach(@split($lineseparator,$csvcontent) as $line) 
				{
					
					$lines++;
					$line = trim($line," \t");
					$line = str_replace("\n","",$line);					
					$line = str_replace("\r","",$line);
					$linearray = explode($fieldseparator,$line);
					
					$fieldarray[] = $linearray ;
					$linemysql = implode("','",$linearray);
					//echo $linemysql; 
				}//end foreach
				
*/
				while( ($line = fgets($file)) != FALSE) 
				{
					
					$lines++;
					$line = trim($line," \t");
					//$line = str_replace("\n","",$line);					
					//$line = str_replace("\r","",$line);
					$linearray = explode($fieldseparator,$line);
					
					$fieldarray[] = $linearray ;
					$linemysql = implode("','",$linearray);
					//echo $linemysql; 
				}//end foreach
				
				fclose($file);
				$totalCol=count($fieldarray[0]);
				 $totalCol=$totalCol;
				//print_r($fieldarray); 
				//die;
				$num=count($fieldarray); //count no of records
				//die;
				$farr = array(); // set array for parent id
				$colA = array(); // set array for parent id
				$colB = array(); // set array for parent id
				$colC = array(); // set array for parent id
				$colD = array(); // set array for parent id
				$task=array();
				$location=array();
				$sublocation=array();
				$start_date=array();
				$end_date=array();
				$colIsuues=array();
				for($i=0;$i<$num;$i+=2) //read second line beacuse first line cover headings
				{
					$colA[1]='';
					$colA[$i]=$fieldarray[$i][0];
					
				}
				
				for($i=1;$i<$num;$i+=2) //read second line beacuse first line cover headings
				{
					$colIsuue[1]='';
					$colIsuue[$i]=$fieldarray[$i][0];
					
				}
				
				
				for($k=1;$k<$totalCol;$k++)
				{
					for($i=0;$i<$num;$i++) //read second line beacuse first line cover headings
					{
						@$colB[$k][]=$fieldarray[$i][$k];
					}
				}
				
				/*for($i=0;$i<$num;$i++) //read second line beacuse first line cover headings
				{
					@$colC[]=$fieldarray[$i][2];
					
				}
				for($i=0;$i<$num;$i++) //read second line beacuse first line cover headings
				{
					@$colD[]=$fieldarray[$i][3];
					
				}*/
				
				//print_r($colB);
				//die;
				
				foreach($colB as $value)
				{
					$numA=count($value);
					for ($i=2; $i < $numA;$i+=2)
					{
					   @$colIsuues[]=trim($colIsuue[$i+1]);
					   $task[] = trim($colA[$i]);
					   $location[] = trim($value[0]);
					   $sublocation[] = trim($value[1]);
					   $start_date[] = trim($value[$i]);
					   @$end_date[] = trim($value[$i+1]);
						
					}
					
				}
				
				
				 $record=count($task);
				
				for($h=0;$h<$record;$h++)
				{
					
					$creatdate=date('Y-m-d H:i:s');	
					$sql="select location_id from project_locations where location_title='".$location[$h]."' and project_id =".$_SESSION['idp']."";
					
					$subloc="select location_id from project_locations where location_title='".$sublocation[$h]."' and project_id =".$_SESSION['idp']."";
					
					
					$result=mysql_query($sql);
					 $row_loc=mysql_num_rows($result); 
					$result1=mysql_query($subloc);
					$row_subloc=mysql_num_rows($result1);
					
					if($row_loc==0)
					{
						
						$lpid=0;
						$locin="insert into project_locations (project_id,location_parent_id,location_title,last_modified_date,last_modified_by,created_date,created_by) values (".$_SESSION['idp'].",".$lpid.",'".$location[$h]."',now(),'".$builder_id."',now(),'".$builder_id."')";
						mysql_query($locin);
						sleep(2);
						$sql_loc="select location_id from project_locations where location_title='".$location[$h]."' and project_id =".$_SESSION['idp']."";
						$result=mysql_query($sql_loc);
						$loc=mysql_fetch_row($result);
						$locationid= $loc[0];
					
					}
					else
					{
						
						$sql_loc="select location_id from project_locations where location_title='".$location[$h]."' and project_id =".$_SESSION['idp']."";
						
						$result=mysql_query($sql_loc);
						$loc=mysql_fetch_row($result);
						$locationid= $loc[0];
						
							
					}
					
						
						
					if($row_subloc==0)
					{
						$sublocin="insert into project_locations (project_id,location_parent_id,location_title,last_modified_date,last_modified_by,created_date,created_by) values (".$_SESSION['idp'].",".$locationid.",'".$sublocation[$h]."',now(),'".$builder_id."',now(),'".$builder_id."')" ;
						
						mysql_query($sublocin);
					}
					
					
					$sql_loc="select location_id from project_locations where location_title='".$location[$h]."' and project_id =".$_SESSION['idp']."";
					
					$sql_subloc="select location_id from project_locations where location_title='".$sublocation[$h]."' and project_id =".$_SESSION['idp']."";
					
					$result=mysql_query($sql_loc);
					$result1=mysql_query($sql_subloc);
					$loc=mysql_fetch_row($result);
					$locationid= $loc[0];
					$subloc=mysql_fetch_row($result1);
					
					 $sublocationid= $subloc[0];
					
					//echo $end_date[$h]. $start_date[$h].$location[$h].$sublocation[$h]; die;
					
					
					if($end_date[$h]!='' && $start_date[$h]!='' && $location[$h]!='' && $sublocation[$h])
					{	
						   
						  $end_date[$h]=str_replace('-','/',$end_date[$h]);
						 $end_date[$h]=str_replace('.','/',$end_date[$h]);
						 
						$endd=explode('/',$end_date[$h]);
						
						if(strlen($endd[2])==2)
						{
							$endd[2]='20'.$endd[2];
						}
						
						@$endDate=$endd[2].'-'.$endd[1].'-'.$endd[0];
						
						$startd=explode('/',$start_date[$h]);	
						@$startDate=$startd[2].'-'.$startd[1].'-'.$startd[0];
						
						
						$percent="0%";
						$insert ="insert into progress_monitoring (project_id,location_id,sub_location_id,task,start_date,end_date,created_by,created_date,last_modified_by,	status,percentage,last_modified_date) values (".trim($_SESSION['idp']).",".trim($locationid).",".trim($sublocationid).",'".trim($task[$h])."','".trim($startDate)."','".trim($endDate)."',".trim($builder_id).",now(),".trim($builder_id).",'','".trim($percent)."',now())";
					
						mysql_query($insert);
						$progress_id=mysql_insert_id(); 
						
						if(isset($colIsuues[$h]) && !empty($colIsuues[$h]))
						{
							$issueTos=strpos($colIsuues[$h],';');
							if($issueTos>0)
							{
								$issue_name=explode(';',$colIsuues[$h]);
								$issunameCount=count($issue_name);
								for($s=0;$s<$issunameCount;$s++)
								{
									$insert_issue="insert into issued_to_for_progress_monitoring (progress_id,issued_to_name,last_modified_date,last_modified_by,created_date,created_by,project_id) values (".$progress_id.",'".$issue_name[$s]."',now(),".$builder_id.",now(),".$builder_id.",".$_SESSION['idp'].")";
									
									mysql_query($insert_issue); 
								}
							//$issuName=count($issueTos);	
							}
							else
							{
								$insert_issue="insert into issued_to_for_progress_monitoring (progress_id,issued_to_name,last_modified_date,last_modified_by,created_date,created_by,project_id) values (".$progress_id.",'".$colIsuues[$h]."',now(),".$builder_id.",now(),".$builder_id.",".$_SESSION['idp'].")";
								
								mysql_query($insert_issue); 
							}
						
						}
								//$pmu="insert into progress_monitoring_update (progress_id,percentage,created_by,created_date,last_modified_by,project_id) values (".$progress_id.",'".$percent."',".$builder_id.",'".$creatdate."',".$builder_id.",".$_SESSION['idp'].")";
								//echo $pmu; die; 
							//	mysql_query($pmu);
								
								$success='File uploaded successfully.';
						}
						
				
				}
				
				
							
				@mysql_close($con); //close db connection
				//$success.= "Total $record Duplicate Records.";
				$success.= "<br/>Total $record record(s) inserted.\n";
		}
		else
		{
			$err_msg='Please try again';// If file not uploaded
		}
	}//
	else
	{
		$err_msg= 'Please select .csv file.';
	}
	}
	else
	{
		$err_msg= 'Please select file.';
		}
}
?>



<!-- Ajax Post -->
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/csv.js"></script>
<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}

.box1 {
    background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent;
    border: 1px solid #0261A1;
    color: #000000;
    float: left;
    height: auto;
    width: 211px;
}
.link1 {
    background-image: url("images/blue_arrow.png");
    background-position: 175px 34%;
    background-repeat: no-repeat;
    color: #000000;
    display: block;
    height: 25px;
    text-decoration: none;
    width: 202px;
}
a.link1:hover {
    background-color: #015F9F;
    background-image: url("images/white_arrow.png");
    background-position: 175px 34%;
    background-repeat: no-repeat;
    color: #FFFFFF;
    display: block;
    height: 25px;
    text-decoration: none;
    width: 202px;
}
.txt13 {
    border-bottom: 1px solid #333333;
    color: #000000;
    font-size: 12px;
    font-weight: bold;
    height: 30px;
}

</style>
<!--<h1 style="font-size:15px;"><img src="images/project_big.png" width="48" height="39" align="absmiddle" />Projects Configuration</h1><br />-->

<div id="middle" style="padding-top:10px;">

<div id="leftNav" style="width:250px;float:left;">
<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
		<tr>
				<td width="24%" align="left" valign="top">
                <!--<a href="#" <?php//if($_GET['sect'] == 'o_dashboard'){echo 'class="left_btn1active"';}?>  class="left_btn1"><br />-->
				</a><br /><a href="pms.php?sect=project_configuration" <?php if($_GET['sect'] == 'project_configuration'){echo 'class="left_btn2active"';}?> class="left_btn2" ><br />
				</a><br /><a href="pms.php?sect=issue_to"  <?php if($_GET['sect'] == 'issue_to'){echo 'class="left_btn3active"';}?> class="left_btn3"><br />
				</a><br /><a href="pms.php?sect=standard_defect" <?php if($_GET['sect'] == 'standard_defect'){echo 'class="left_btn4active"';}?>  class="left_btn4"><br />
				</a><br /><a href="pms.php?sect=progress_monitoring" <?php if($_GET['sect'] == 'progress_monitoring'){echo 'class="left_btn5active"';}?> class="left_btn5" ><br />
				</a>
                </td>
				<td width="40%" valign="top"></td>
				
				<td width="21%" valign="top"><!--<a href="#"><img src="images/add_btn.png" width="65" height="26" vspace="20" /></a><br />
						<a href="#"><img src="images/remove_btn.png" width="65" height="27" /></a>--></td>
				
				</tr>
</table>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);
 ?>
</div>
	
  
    <div class="content_hd1" style="background-image:url(images/progress_monitoring_header.png);width:500px;margin-top:20px;margin-left:260px;">
    <a href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>">
                    <img src="images/back_btn.png" style="border:none; width:111px;margin-left:530px;" /></a>
    
    </div>
  <div class="content_container" style="float:left;width:650px;border:1px solid; margin-bottom:70px;text-align:center;margin-left:10px;margin-right:10px;height:125px;" >
    
<!--First Box-->


<div style="width:722px; height:50px; float:left; margin-top:5px;">
	<form method="post" name="csvLocation" id="csvLocation" enctype="multipart/form-data">
		<table width="650px" border="0" cellspacing="0" cellpadding="3">
			<tr>
				<td colspan="3" align="left"><font size="+1"><a href="/csv/ProgressMonitoringDemo.csv" style="text-decoration:none;color:#FFF;">Click here to download CSV template</a></font></td>
			</tr>
			<?php if((isset($success)) && (!empty($success))){?>
			<tr>
				<td colspan="2" align="center" height="50px">
					<div class="success_r" style="height:45px;width:300px;"><p><?php echo $success; ?></p></div>
				</td>
			</tr>
			<?php } ?>
			<?php if((isset($err_msg)) && (!empty($err_msg))){?>
			<tr>
				<td colspan="2" align="center" height="50px">
					<div class="failure_r" style="height:45px;width:300px;"><p><?php echo $err_msg; ?></p></div>
				</td>
			</tr>
			<?php } ?>  
			<tr>
				<td width="25%" height="80px">Upload CSV File :</td>
				<td width="50%" align="left"><input type="file" name="csvFile" id="csvFile" value="" /></td>
				<td width="25%" height="80px"><input type="image" src="images/import_csv_btn.png"  name="location_csv" id="location_csv" value="Import CSV" /></td>
			</tr>
		</table>
	</form>
<br clear="all" />
</div>



<!--End First Box-->


<!--Second Box-->
<!--End Second Box-->

<!--Projecct Box-->
<!--End Project Box-->
		</div>
<div class="big_container" style="width:722px;margin-left:245px;margin-top:-100px;" >
	<?php include'progress_csv_table.php';?>
</div>
</div>
