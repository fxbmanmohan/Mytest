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
	$update='update inspection_issue_to set is_deleted=1,last_modified_date=now(),last_modified_by="'.$builder_id .'" where issue_to_id="'.base64_decode($_REQUEST['id']).'"';

	mysql_query($update);
	$_SESSION['issue_to_del']='Issued to deleted successfully.';
	header('loaction:?sect=issue_to');
	
}
 


if(isset($_POST['location_csv_x'])) // Location/ subloaction import CSV file.
{
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name']))
	{
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV')
		{
			$path=$_SERVER['DOCUMENT_ROOT'].'/csv/temp/'; // CSV Path
			$upload=move_uploaded_file($_FILES['csvFile']['tmp_name'],$path.$filename);
			if($upload)
			{
				
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
				/*$csvcontent = str_replace("\r","\n",$csvcontent);
				foreach(@split($lineseparator,$csvcontent) as $line) 
				{
					$lines++;
					$line = trim($line," \t");
					$line = str_replace("\r","",$line);
					$linearray = explode($fieldseparator,$line);
					$fieldarray[] = $linearray ;
					$linemysql = implode("','",$linearray);
				}//end foreach*/
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
				$num=count($fieldarray);
				$count=0;
				for($i=1;$i<$num;$i++) //read second line beacuse first line cover headings
				{
					if(!empty($fieldarray[$i][1]))
					{
						$select="select * from inspection_issue_to where issue_to_name='".$fieldarray[$i][0]."' AND project_id=".$_SESSION['idp']."";
						$issue=mysql_query($select);
						 $row_data=mysql_num_rows($issue);
						if($row_data > 0)
						{
							
							$record=count($fieldarray[$i][1]);//keep Duplicate Record list.
							if($record>0)
							{
								$count=$count+1;
							}
						}
						else
						{
							//$creatdate=date('Y-m-d H:i:s');	
							@$insert="insert into inspection_issue_to (issue_to_name,company_name,issue_to_phone,issue_to_email,project_id,last_modified_date,last_modified_by,created_date,created_by) values ('".$fieldarray[$i][1]."','".$fieldarray[$i][0]."','".$fieldarray[$i][2]."','".$fieldarray[$i][3]."',".$_SESSION['idp'].",now(),".$builder_id.",now(),".$builder_id.")";
				
					//echo $insert; die;
							mysql_query($insert);
							$success='File uploaded successfully.';
						}
					}
				}
				
				
				
				
						
				@mysql_close($con); //close db connection
				
				if(isset($count) && !empty($count))
				{
					$success="Total $count Duplicate Records";
				}
				//$success.= "<br/>Total $record Duplicate Records.";
				$msg1= "<br/>Total $lines record(s) inserted.";
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


<div id="middle" style="padding-top:10px;">

<div id="leftNav" style="width:250px;float:left;">
<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
		<tr>
				<td width="24%" align="left" valign="top">
                <!--<a href="#" <?php //if($_GET['sect'] == 'o_dashboard'){echo 'class="left_btn1active"';}?>  class="left_btn1"><br />-->
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
  <div class="content_hd1" style="background-image:url(images/issued_header.png);width:500px;margin-top:20px;margin-left:260px;">
  <a href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>">
                    <img src="images/back_btn.png" style="border:none; width:111px;margin-left:530px;" /></a>
  </div>
  <div class="content_container" style="float:left;width:650px;border:1px solid; margin-bottom:70px;text-align:center;margin-left:10px;margin-right:10px;height:125px;" >
<!--First Box-->
<div style="width:722px; height:50px; float:left; margin-top:5px;">
	<form method="post" name="csvIssueto" id="csvIssueto" enctype="multipart/form-data">
		<table width="650px" border="0" cellspacing="0" cellpadding="3">
			<tr>
				<td colspan="3" align="left"><font size="+1"><a href="/csv/issueto.csv" style="text-decoration:none;color:#FFF;">Click here to download CSV template</a></font></td>
			</tr>   
			<?php if((isset($success)) && (!empty($success))) { ?>
			<tr>
				<td colspan="2" align="center" height="50px">
					<div class="success_r" style="height:30px;width:300px;"><p><?php echo $success; ?></p></div>
				</td>
			</tr>
			<?php } ?>
			<?php if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<tr>
				<td colspan="2" align="center" height="50px">
					<div class="failure_r" style="height:30px;width:300px;"><p><?php echo $err_msg; ?></p></div>
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
<div class="big_container" style="width:722px;float:left;margin-left:245px;margin-top:-100px;" >

	<?php include'issueto_csv_table.php';?>
</div>
</div>
