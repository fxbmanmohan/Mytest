<?php
 include('func.php');
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
 
 $builder_id=$_SESSION['ww_builder_id'];
if(isset($_POST['location_csv_x'])) // Location/ subloaction import CSV file.
{
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name']))
	{
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV')
		{
			$path=$_SERVER['DOCUMENT_ROOT'].'/csv/'; // CSV Path
			$upload=move_uploaded_file($_FILES['csvFile']['tmp_name'],$path.$filename);
			if($upload)
			{
				$databasetable = "project_locations"; // database table name
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
				$csvcontent = fread($file,$size);
				fclose($file);
				$lines = 0;
				$queries = "";
				$linearray = array();
				$fieldarray= array();
				$record='';
				foreach(@split($lineseparator,$csvcontent) as $line) 
				{
					$lines++;
					$line = trim($line," \t");
					$line = str_replace("\r","",$line);
					$linearray = explode($fieldseparator,$line);
					$fieldarray[] = $linearray ;
					$linemysql = implode("','",$linearray);
				}//end foreach
				$num=count($fieldarray); //count no of reco
				$count=0;
				$farr = array(); // set array for parent id
				for($i=1;$i<$num;$i++) //read second line beacuse first line cover headings
				{
					$filedcount=count($fieldarray[$i]); 
					for($j=0;$j< $filedcount;$j++)
					{
						
						$fieldvalue=trim($fieldarray[$i][$j]);
						
						if(!empty($fieldvalue))
						{
							if(isset($farr[$j-1]) && !empty($farr[$j-1]))
								$parent_id = $farr[$j-1];
							else
								$parent_id = 0; //set parent id 0 for first record
						
						
							$select="select * from project_locations where location_title='".$fieldvalue."' AND location_parent_id = $parent_id and project_id=".$_SESSION['idp']."";
							$result=mysql_query($select);
							  $rows=mysql_num_rows($result); 
							$rowdata=mysql_fetch_row($result);
							
							if($rows>0) // if exist,
							{
								$pid=$rowdata[0];
								$farr[$j]=$pid;// get id
								
								//$record.='<br>'.$rowdata[3].'<br>';//keep Duplicate Record list.
									$record=count($rowdata[3]);//keep Duplicate Record list.
									if($record>0)
									{
										$count=$count+1;
									}
							}
							else
							{
								$creatdate=date('Y-m-d H:i:s');	
								$insert = "INSERT INTO project_locations SET
											location_parent_id = ".$parent_id.",
											project_id = ".$_SESSION['idp'].",
											location_title = '".$fieldvalue."',
											last_modified_date = NOW(),
											last_modified_by = ".$builder_id.",
											created_date = NOW(),
											created_by = ".$builder_id;
								mysql_query($insert);
								$id = mysql_insert_id();
								$farr[$j] = $id;
								}
							$success='File uploaded successfully.';
						}// end If when record not found
					}
					
				
				}
				@mysql_close($con); //close db connection
					
				if(isset($count) && !empty($count))
				{
					$success="Total $count Duplicate Records ";
				}
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
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/csv.js"></script>
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
<script language="javascript" type="text/javascript">
function startAjax(){
	var protype=document.getElementById('protype').value;
	var name=document.getElementById('name').value;
	var line1=document.getElementById('line1').value;
	var suburb=document.getElementById('suburb').value;
	var state=document.getElementById('state').value;
	var postcode=document.getElementById('postcode').value;
	var country=document.getElementById('country').value;
	
	if(protype!='' && name!='' && line1!='' && suburb!='' && state!='' && postcode!='' && country!=''){
		document.getElementById('sign_in_process').style.visibility = 'visible';
		document.getElementById('sign_in_response').style.visibility = 'hidden';
		return true;
	}else{
		var err = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
		document.getElementById('sign_in_response').innerHTML = err;
		return false;
	}
	
	document.getElementById('sign_in_process').style.visibility = 'visible';
	document.getElementById('sign_in_response').style.visibility = 'hidden';
	return true;
}

function stopAjax(success){
	var result = '';
	if(success == 0){
		result = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="sign_emsg">Invalid Associate To Name!<\/span><br/><br/>';
	}else if(success == 1){
		result = '<span class="sign_msg">Project added successfully!<\/span><br/><br/>';
		
		// reset form
		document.getElementById('protype').value='';
		document.getElementById('name').value='';
		document.getElementById('line1').value='';
		document.getElementById('line2').value='';
		document.getElementById('suburb').value='';
		document.getElementById('state').value='';
		document.getElementById('postcode').value='';
		document.getElementById('country').value='';
	}
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';	
	
	return true;
}
</script>

<script type="text/javascript">
$(document).ready(function() {
	
	$('#wait_1').hide();
	$('#drop_1').change(function(){
		
	  $('#wait_1').show();
	  $('#result_1').hide();
      $.get("func.php", {
		func: "drop_1",
		drop_var: $('#drop_1').val()
      }, function(response){
        $('#result_1').fadeOut();
        setTimeout("finishAjax('result_1', '"+escape(response)+"')", 400);
      });
    	return false;
	});
});

function finishAjax(id, response) {
  $('#wait_1').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}
function finishAjax_tier_three(id, response) {
  $('#wait_2').hide();
  $('#'+id).html(unescape(response));
  $('#'+id).fadeIn();
}
</script>


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
                <!-- <a href="#" <?php //if($_GET['sect'] == 'o_dashboard'){echo 'class="left_btn1active"';}?>  class="left_btn1"><br />-->
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
</div>
  <div class="content_hd1" style="background-image:url(images/location_header.png);width:500px;margin-top:10px;margin-left:260px;"></div>
  <div class="content_container" style="float:left;width:650px;border:1px solid; margin-bottom:70px;text-align:center;margin-left:10px;margin-right:10px;height:215px;" >
<!--First Box-->
<div style="width:722px; height:50px; float:left; margin-top:5px;">
		
        <form method="post" name="csvLocation" id="csvLocation" enctype="multipart/form-data">
                        <table width="100%" border="0" cellspacing="0" cellpadding="3">
							
                            
                            	

                                <tr>
                                	<td colspan="2" align="center" style="padding-left:220px;"><font size="+1"><a href="/csv/location3_new.csv" style="text-decoration:none;color:#FFF;">Click here to download CSV template</a></font></td>
                                 </tr>   
                                
                                                                  <?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { 
								?>
                                <tr>
                                	<td colspan="2" align="center" height="10px">
                                    <div class="success_r" style="height:45px;width:300px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
                                    </td>
                                </tr>
								<?php unset($_SESSION['add_project']);} ?>
								
                                
								  <?php if((isset($success)) && (!empty($success))) { 
								?>
                                <tr>
                                	<td colspan="2" align="center" height="10px">
                                    <div class="success_r" style="height:45px;width:300px;"><p><?php echo $success; ?></p></div>
                                    </td>
                                </tr>
								<?php } ?>
								
								<?php if((isset($err_msg)) && (!empty($err_msg))) { 
								?>
                                <tr>
                                	<td colspan="2" align="center" height="10px">
                                    <div class="failure_r" style="height:45px;width:300px;"><p><?php echo $err_msg; ?></p></div>
                                    </td>
                                </tr>
								<?php } ?>
                                
                                
                                
                                <tr>
										<td width="30%" height="100px">Upload CSV File :</td>
                                        <td width="70%" align="left"><input type="file" name="csvFile" id="scvFile" value="" /></td>
								</tr>
                                <tr>
										<td align="center" colspan="2" height="20px;"><input type="image" src="images/import_csv_btn.png"  name="location_csv" id="location_csv" value="Import CSV" /></td>
								</tr>
								
						</table>
                        </form><br />
<br />
<br clear="all" />
</div>



<!--End First Box-->


<!--Second Box-->
<!--End Second Box-->

<!--Projecct Box-->
<!--End Project Box-->
		</div>
<div class="big_container" style="width:722px;float:left;margin-top:50px;display:none;" >
	<?php include'csv_table.php';?>
</div>
</div>
