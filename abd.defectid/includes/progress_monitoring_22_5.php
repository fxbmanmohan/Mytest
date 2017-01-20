<?php
include_once("commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
include('func.php');
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	
	return $string;	
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


if(isset($_POST['location_csv_x'])){ // Location/ subloaction import CSV file.

	
	$success='';
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name']))
	{
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV')
		{
			$files=$_FILES['csvFile']['tmp_name'];	
			$databasetable = "progress_monitoring"; // database table name
			$fieldseparator = ","; // CSV file comma format
			$lineseparator = "\n";
			$csvfile = $files; //CSV file name
			$addauto = 1;
			$save = 1;
			$file = fopen($csvfile,"r");
			$size = filesize($csvfile); //check file record
			if(!$size) {
				echo "File is empty.\n";
				exit;
			}
			$lines = 0;
			$queries = "";
			$linearray = array();
			$fieldarray= array();
			$record='';
			
			//while( ($line = fgets($file)) != FALSE) 
			while( ($data =  fgetcsv($file,1000,",")) != FALSE) 
			{
			      $numOfCols = count($data);
			      for ($index = 0; $index < $numOfCols; $index++)
			      {
				  $data[$index] = stripslashes(normalise($data[$index]));
			      }
			      $fieldarray[] = $data;
			}
			//end foreach
			fclose($file);
			$totalCol=count($fieldarray);
			$totalCol=$totalCol;
			$num=count($fieldarray); //count no of records
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
			for($i=1;$i<$num;$i++) //read second line beacuse first line cover headings
			{
				$colA[$i]=$fieldarray[$i][0];
			}
			for($i=1;$i<$num;$i+=2) //read second line beacuse first line cover headings
			{
				$colIsuue[1]='';
				$colIsuue[$i]=$fieldarray[$i][0];
			}
			for($k=1;$k<$totalCol;$k++)
			{
				@$colB[$k]=$fieldarray[$k];
			}
			foreach($colB as $value)
			{
				//print_r($value);
				$numA=count($value);
				@$colIsuues[]=trim($value[5]);
				$task[] = trim($value[2]);
				$location[] = trim($value[0]);
				$sublocation[] = trim($value[1]);
				$start_date[] = trim($value[3]);
				@$end_date[] = trim($value[4]);
			}
			$record=count($task);
			$count=0;
				$location_id_array = array();
				$sub_location_id_array = array();
				
				for($h=0;$h<$record;$h++)
				{
					if(!empty($location[$h]))
					{
					   if (!isset($location_id_array[$location[$h]]))
					   {
						$sql="select location_id from project_monitoring_locations where location_title='".$location[$h]."' and project_id =".$_SESSION['idp']." and is_deleted=0";
						$result=mysql_query($sql);
				 		$row_loc=mysql_num_rows($result);
						if($row_loc==0)
						{
							$lpid=0;
							$locin="insert into project_monitoring_locations (project_id,location_parent_id,location_title,last_modified_date,last_modified_by,created_date,created_by) values (".$_SESSION['idp'].",".trim($lpid).",'".trim($location[$h])."',now(),'".$builder_id."',now(),'".$builder_id."')";
							mysql_query($locin);
							$location_id_array[$location[$h]]  = mysql_insert_id();
						}else{
							$row = mysql_fetch_array($result);
							$location_id_array[$location[$h]] = $row["location_id"];
						}
					   }
					 $locationid= $location_id_array[$location[$h]];
					}
					//echo "$location[$h] - $locationid <br/>";
					//***** Sub Location   ********//
					
					if(!empty($sublocation[$h]))
					{	
					   if (!isset($sub_location_id_array[$sublocation[$h]]))
					   {
						$subloc="select location_id from project_monitoring_locations where location_title='".$sublocation[$h]."' and project_id =".$_SESSION['idp']." and location_parent_id=".$locationid." and is_deleted=0";
						//echo $subloc; 
						$result1=mysql_query($subloc);
						$row_subloc=mysql_num_rows($result1);
						if($row_subloc==0)
						{
							$sublocin="insert into project_monitoring_locations (project_id,location_parent_id,location_title,last_modified_date,last_modified_by,created_date,created_by) values (".$_SESSION['idp'].",".trim($locationid).",'".addslashes(trim($sublocation[$h]))."',now(),'".$builder_id."',now(),'".$builder_id."')" ;
							mysql_query($sublocin);
							$sub_location_id_array[$sublocation[$h]]  = mysql_insert_id();
						}else{
							$row = mysql_fetch_array($result1);
							$sub_location_id_array[$sublocation[$h]] = $row["location_id"];
						}
					   }
					 $sublocationid= $sub_location_id_array[$sublocation[$h]];
					}
					//die;
					//echo "$end_date[$h]!='' && $start_date[$h]!='' && $locationid!='' && $sublocationid!=''";
				if($end_date[$h]!='' && $start_date[$h]!='' && $locationid!='' && $sublocationid!='')
				{	
					$end_date[$h]=str_replace('-','/',$end_date[$h]);
					$end_date[$h]=str_replace('.','/',$end_date[$h]);
					$endd=explode('/',$end_date[$h]);
					if(strlen($endd[2])==2)
					{
						$endd[2]='20'.$endd[2];
					}
					@$endDate=$endd[2].'-'.$endd[1].'-'.$endd[0];
					$start_date[$h]=str_replace('-','/',$start_date[$h]);
					$startd=explode('/',$start_date[$h]);	
					@$startDate=$startd[2].'-'.$startd[1].'-'.$startd[0];
					
					$select_progress="select * from progress_monitoring where location_id= ".$locationid." and sub_location_id=".$sublocationid." and task='".$task[$h]."' and is_deleted=0";
					$result_p=mysql_query($select_progress);
					$row_p=mysql_num_rows($result_p);
					if($row_p > 0)
					{
						 $count=$count+1;
					}
					else
					{
						$percent="0%";
						 $insert ="insert into progress_monitoring (project_id,location_id,sub_location_id,task,start_date,end_date,created_by,created_date,last_modified_by,	status,percentage,last_modified_date) values (".trim($_SESSION['idp']).",".trim($locationid).",".trim($sublocationid).",'".addslashes(trim($task[$h]))."','".trim($startDate)."','".trim($endDate)."',".trim($builder_id).",now(),".trim($builder_id).",'','".trim($percent)."',now())";
						
						
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
									$insert_issue="insert into issued_to_for_progress_monitoring (progress_id,issued_to_name,last_modified_date,last_modified_by,created_date,created_by,project_id) values (".$progress_id.",'".addslashes(trim($issue_name[$s]))."',now(),".$builder_id.",now(),".$builder_id.",".$_SESSION['idp'].")";
									mysql_query($insert_issue); 
									$select_isseu="select * from inspection_issue_to where issue_to_name='".trim($issue_name[$s])."' and project_id=".$_SESSION['idp']." and 	is_deleted=0";
									$result_issue=mysql_query($select_isseu);
									$issue_row=mysql_num_rows($result_issue);
									if($issue_row == 0)
									{
										$issue_insert="insert into inspection_issue_to (issue_to_name,last_modified_date,last_modified_by,created_date,created_by,project_id) values ('".addslashes(trim($issue_name[$s]))."',now(),".$builder_id.",now(),".$builder_id.",".$_SESSION['idp'].")";	
										mysql_query($issue_insert);
									}
								}
							
							//$issuName=count($issueTos);	
							}
							else
							{
								$insert_issue="insert into issued_to_for_progress_monitoring (progress_id,issued_to_name,last_modified_date,last_modified_by,created_date,created_by,project_id) values (".$progress_id.",'".addslashes(trim($colIsuues[$h]))."',now(),".$builder_id.",now(),".$builder_id.",".$_SESSION['idp'].")";
								
									mysql_query($insert_issue); 
									$select_isseu="select * from inspection_issue_to where issue_to_name='".$colIsuues[$h]."' and project_id=".$_SESSION['idp']." and 	is_deleted=0";
									
									//echo $select_isseu; 
									$result_issue=mysql_query($select_isseu);
									$issue_row=mysql_num_rows($result_issue);
									if($issue_row == 0)
									{
										$issue_insert="insert into inspection_issue_to (issue_to_name,last_modified_date,last_modified_by,created_date,created_by,project_id) values ('".addslashes(trim($colIsuues[$h]))."',now(),".$builder_id.",now(),".$builder_id.",".$_SESSION['idp'].")";	
										mysql_query($issue_insert);
									}
							}
						
						}
					}
					$success='File uploaded successfully.';
				}
			}
			@mysql_close($con); //close db connection
			if(isset($count) && !empty($count))
			{
				$success="$count Duplicate Records";
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
<div id="rightCont" style="float:left;width:700px;">
	<div class="content_hd1" style="width:500px;margin-top:12px;">
		<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
		<a style="float:left;margin-top:-25px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>"><img src="images/back_btn2.png" style="border:none; width:87px;margin-left:586px;" /></a>
	</div>
	<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:35px;width:185px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
	</div>
	<div class="content_container" style="float:left;width:690px;border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;height:90px;">
	<!--First Box-->
	<div style="width:722px; height:50px; float:left; margin-top:5px;">
		<form method="post" name="csvLocation" id="csvLocation" enctype="multipart/form-data">
		<table width="690px" border="0" cellspacing="0" cellpadding="3">
			<tr>
				<td colspan="3" align="left"><a href="/csv/Progress_Monitoring_Template.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click here to download CSV template</strong></a></td>
                <td> <input type="button"  class="submit_btn" onclick=location.href="progress_monitoring_export.php"  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:15px;" /></td>
               
                </td>
			</tr>
			<tr>
				<td width="185px;" align="left">&nbsp;</td>
				<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
				<td width="240px;" align="left"><input type="file" name="csvFile" id="csvFile" value="" /></td>
				<td width="120px;" height="50px"><input type="image" src="images/import_csv_btn.png"  name="location_csv" id="location_csv" value="Import CSV" style="margin-left:12px;" /></td>
			</tr>
		</table>
	</form>
    
	<br clear="all" />
	</div>
	</div>
	<br clear="all" />
	<div class="big_container" style="width:722px;margin-left:9px;" ><?php include'progress_csv_table.php';?></div>
</div>
</div>