<?php include_once("commanfunction.php");
$obj = new COMMAN_Class(); ?>
<style>
.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
div#spinner{ display: none; width:100%; height: 100%; position: fixed; top: 0; left: 0; background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC; text-align:center; padding:10px; font:normal 16px Tahoma, Geneva, sans-serif; border:1px solid #666; z-index:2; overflow: auto; opacity : 0.8; }
/*.jtree-arrow { display:none; }
ul.telefilms{list-style-image:url(images/location_icon.png); cursor:pointer;}
ul.telefilms li ul{list-style-image:url(images/sub_location_icon.png);}*/
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
/*ul.telefilms li{height:15px;}*/
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
</style>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
	<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.tree.js"></script>
	
	<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
	<script language="javascript" src="js/modal.popup.js"></script>	
<script type="text/javascript">
	var spinnerVisible = false;
    function showProgress() {
        if (!spinnerVisible) {
            $("div#spinner").fadeIn("fast");
            spinnerVisible = true;
        }
    };
    function hideProgress() {
        if (spinnerVisible) {
            var spinner = $("div#spinner");
            spinner.stop();
            spinner.fadeOut("fast");
            spinnerVisible = false;
        }
    };

$(document).ready(function() {
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
	var align = 'center';									//Valid values; left, right, center
	var top = 100; 											//Use an integer (in pixels)
	var width = 500; 										//Use an integer (in pixels)
	var padding = 10;										//Use an integer (in pixels)
	var backgroundColor = '#FFFFFF'; 						//Use any hex code
	//var source = 'rightClick.html'; 								//Refer to any page on your server, external pages are not valid e.g. http://www.google.co.uk
	var borderColor = '#333333'; 							//Use any hex code
	var borderWeight = 4; 									//Use an integer (in pixels)
	var borderRadius = 5; 									//Use an integer (in pixels)
	var fadeOutTime = 300; 									//Use any integer, 0 = no fade
	var disableColor = '#666666'; 							//Use any hex code
	var disableOpacity = 40; 								//Valid range 0-100
	var loadingImage = 'images/loadingAnimation.gif';		//Use relative path from this page

	$('span.demo1').contextMenu('myMenu2', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
			},
			'edit': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit.php?location_id='+t.id, loadingImage);
			},
			'delete': function(t) {
				var r = jConfirm('Do you want to delete location ?', null, function(r){
					if (r==true){
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_delete.php?location_id='+t.id, loadingImage);
						document.getElementById('li_'+t.id).style.display='none';
						closePopup(fadeOutTime);
						jAlert('Location Deleted Successfully !');
					}
				});
			}
		}
	});
	$('span.demo2').contextMenu('myMenu1', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
			}
		}
	});
});

function addLocation(){

var align = 'center';									//Valid values; left, right, center
var top = 100; 											//Use an integer (in pixels)
var width = 500; 										//Use an integer (in pixels)
var padding = 10;										//Use an integer (in pixels)
var backgroundColor = '#FFFFFF'; 						//Use any hex code
//var source = 'rightClick.html'; 								//Refer to any page on your server, external pages are not valid e.g. http://www.google.co.uk
var borderColor = '#333333'; 							//Use any hex code
var borderWeight = 4; 									//Use an integer (in pixels)
var borderRadius = 5; 									//Use an integer (in pixels)
var fadeOutTime = 300; 									//Use any integer, 0 = no fade
var disableColor = '#666666'; 							//Use any hex code
var disableOpacity = 40; 								//Valid range 0-100
var loadingImage = 'images/loadingAnimation.gif';		//Use relative path from this page

	var location = document.getElementById('subLocation').value;
	var locationId = document.getElementById('locationId').value;
	var checkProject = document.getElementById('checkProject').value;
	if (location==""){	return false;	}
	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	showProgress();
	if(checkProject == 'Yes'){	params = "location="+location+"&locationId=0&uniqueId="+Math.random();	}
	if(checkProject == 'No'){	params = "location="+location+"&locationId="+locationId+"&uniqueId="+Math.random();		}
	xmlhttp.open("POST", "location_tree_add.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			hideProgress();
			if(checkProject == 'No'){	document.getElementById('li_'+locationId).innerHTML+=xmlhttp.responseText;	}
			if(checkProject == 'Yes'){	document.getElementById('projectId_'+locationId).innerHTML+=xmlhttp.responseText;		}
			closePopup(500);
			$('span.demo1').contextMenu('myMenu2', {
				bindings: {
					'add': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
					},
					'edit': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit.php?location_id='+t.id, loadingImage);
					},
					'delete': function(t) {
						var r = jConfirm('Do you want to delete location ?', null, function(r){
							if (r==true){
								modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_delete.php?location_id='+t.id, loadingImage);
								document.getElementById('li_'+t.id).style.display='none';
								closePopup(fadeOutTime);
								jAlert('Location Deleted Successfully !');
							}
						});
					}
				}
			});
			$('span.demo2').contextMenu('myMenu1', {
				bindings: {
					'add': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
					}
				}
			});
		}
	}
	xmlhttp.send(params);
}
function editLocation(){
	var location = document.getElementById('locationName').value;
	var locationId = document.getElementById('locationIdEdit').value;
	if (location==""){	return false;	}
	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	showProgress();
	params = "location="+location+"&locationId="+locationId+"&uniqueId="+Math.random();
	xmlhttp.open("POST", "location_tree_edit.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			hideProgress();
			document.getElementById(locationId).innerHTML=xmlhttp.responseText;
			closePopup(500);
		}
	}
	xmlhttp.send(params);
}
</script>


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
if(isset($_POST['location_csv_x'])){ // Location/ subloaction import CSV file.
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name'])){
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV'){
			
			$files=$_FILES['csvFile']['tmp_name'];		
			$databasetable = "project_locations"; // database table name
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
				$num=count($fieldarray); //count no of reco
				$count=0;
				$farr = array(); // set array for parent id
				for($i=1;$i<$num;$i++){ //read second line beacuse first line cover headings
				
					$filedcount=count($fieldarray[$i]); 
					for($j=0;$j< $filedcount;$j++){
						
						$fieldvalue=trim($fieldarray[$i][$j]);
						
						if(!empty($fieldvalue)){
							if(isset($farr[$j-1]) && !empty($farr[$j-1]))
								$parent_id = $farr[$j-1];
							else
								$parent_id = 0; //set parent id 0 for first record
						
						
							$select="select * from project_locations where location_title='".$fieldvalue."' AND location_parent_id = $parent_id and project_id=".$_SESSION['idp']." and is_deleted=0";
							//echo $select; die;
							$result=mysql_query($select);
							  $rows=mysql_num_rows($result); 
							$rowdata=mysql_fetch_row($result);
							//echo $rows; die;
							if($rows>0){ // if exist,
							
								$pid=$rowdata[0];
								$farr[$j]=$pid;// get id
								
								//$record.='<br>'.$rowdata[3].'<br>';//keep Duplicate Record list.
									$record=count($rowdata[3]);//keep Duplicate Record list.
									if($record>0){
										$count=$count+1;
									}
							}else{
								$creatdate=date('Y-m-d H:i:s');	
								$insert="insert into project_locations (location_parent_id,project_id,location_title,last_modified_date,last_modified_by,created_date,	created_by) values ($parent_id,".$_SESSION['idp'].",'".$fieldvalue."',now(),".$builder_id.",now(),".$builder_id.")";
								
								
								mysql_query($insert);
								$id = mysql_insert_id();
								$farr[$j] = $id;
							}
							$success='File uploaded successfully.';
						}// end If when record not found
					}
				}
				@mysql_close($con); //close db connection
					
				if(isset($count) && !empty($count)){
					$success="$count Duplicate Records ";
				}
				$msg1= "<br/>$lines record(s) inserted.";
		
	}else{
		$err_msg= 'Please select .csv file.';
	}
	}else{
		$err_msg= 'Please select file.';
	}
}
?>
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
	<div class="content_container" style="float:left;width:690px;border:1px solid; margin-bottom:50px;text-align:center;margin-left:10px;margin-right:10px;height:80px;">
	<!--First Box-->
	<div style="width:722px; height:50px; float:left; margin-top:5px;">
        <form method="post" name="csvLocation" id="csvLocation" enctype="multipart/form-data">
			<table width="690px" border="0" cellspacing="0" cellpadding="3">
				<tr>
					<td colspan="4" align="left"><a href="/csv/Location_and_Sublocation_Template.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click here to download CSV template</strong></a></td>
				</tr>   
				<tr>
					<td width="185px;" align="left">&nbsp;</td>
					<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
					<td width="240px;" align="left"><input type="file" name="csvFile" id="scvFile" value="" /></td>
					<td width="120px;" height="50px"><input type="image" src="images/import_csv_btn.png"  name="location_csv" id="location_csv" value="Import CSV" /></td>
				</tr>
			</table>
		</form>
	</div>
<!--End First Box-->
<!--Second Box-->
<!--End Second Box-->
<!--Projecct Box-->
<!--End Project Box-->
		</div>
	<div class="big_container" style="width:700px;float:left;margin-top:-50px;margin-left:30px;" >
<?php
$q = "select location_id, location_title from project_locations where project_id = '".$_SESSION['idp']."' and location_parent_id = '0' and is_deleted = '0' order by location_title";
$re = mysql_query($q);
while($rw = mysql_fetch_array($re)){	$val[] = $rw;	}
#print_r($val);die;?>
<div>
<span id="projectId_<?php echo $_SESSION['idp']?>">
<span class="jtree-button demo2" id="projectId_<?php echo $_SESSION['idp']?>" style="background-image: url('images/project.png');background-position: 0 15px;background-repeat: no-repeat;display: block;height: 30px;padding-left: 40px;padding-top: 9px;width: 90%;font-size:26px;cursor: pointer;"><?php echo $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></span>
<?php $i=0; if(!empty($val)){foreach($val as $locations){$i++;?>
	<ul class="telefilms"><!-- Use 'cookie1' as unique key to save cookie only for this tree -->
		<li id="li_<?php echo $locations['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations['location_id']?>"><?php echo $locations['location_title']?></span>
			<?php $q1 = "select location_id, location_title from project_locations where location_parent_id = '".$locations['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
				$re1 = mysql_query($q1);
				while($rw1 = mysql_fetch_array($re1)){	$val1[] = $rw1;	}
				if(!empty($val1)){foreach($val1 as $locations1){ ?>
				<ul>
					<li id="li_<?php echo $locations1['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations1['location_id']?>"><?php echo $locations1['location_title']?></span>
						<?php $q2 = "select location_id, location_title from project_locations where location_parent_id = '".$locations1['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
						$re2 = mysql_query($q2);
						while($rw2 = mysql_fetch_array($re2)){	$val2[] = $rw2;	}
						if(!empty($val2)){foreach($val2 as $locations2){ ?>
						<ul>
							<li id="li_<?php echo $locations2['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations2['location_id']?>"><?php echo $locations2['location_title']?></span>
							
								<?php $q3 = "select location_id, location_title from project_locations where location_parent_id = '".$locations2['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
								$re3 = mysql_query($q3);
								while($rw3 = mysql_fetch_array($re3)){	$val3[] = $rw3;	}
								if(!empty($val3)){foreach($val3 as $locations3){ ?>
								<ul>
									<li id="li_<?php echo $locations3['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations3['location_id']?>"><?php echo $locations3['location_title']?></span>
										<?php $q4 = "select location_id, location_title from project_locations where location_parent_id = '".$locations3['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
										$re4 = mysql_query($q4);
										while($rw4 = mysql_fetch_array($re4)){	$val4[] = $rw4;	}
										if(!empty($val4)){foreach($val4 as $locations4){ ?>
										<ul>
											<li id="li_<?php echo $locations4['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations4['location_id']?>" ><?php echo $locations4['location_title']?></span>
												<?php $q5 = "select location_id, location_title from project_locations where location_parent_id = '".$locations4['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
												$re5 = mysql_query($q5);
												while($rw5 = mysql_fetch_array($re5)){	$val5[] = $rw5;	}
												if(!empty($val5)){foreach($val5 as $locations5){ ?>
												<ul>
													<li id="li_<?php echo $locations5['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations5['location_id']?>" ><?php echo $locations5['location_title']?></span>
														<?php $q6 = "select location_id, location_title from project_locations where location_parent_id = '".$locations5['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
														$re6 = mysql_query($q6);
														while($rw6 = mysql_fetch_array($re6)){	$val6[] = $rw6;	}
														if(!empty($val6)){foreach($val6 as $locations6){ ?>
														<ul>
															<li id="li_<?php echo $locations6['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations6['location_id']?>" ><?php echo $locations6['location_title']?></span>
																<?php $q7 = "select location_id, location_title from project_locations where location_parent_id = '".$locations6['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																$re7 = mysql_query($q7);
																while($rw7 = mysql_fetch_array($re7)){	$val7[] = $rw7;	}
																if(!empty($val7)){foreach($val7 as $locations7){ ?>
																<ul>
																	<li id="li_<?php echo $locations7['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations7['location_id']?>" ><?php echo $locations7['location_title']?></span>
																		<?php $q8 = "select location_id, location_title from project_locations where location_parent_id = '".$locations7['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																		$re8 = mysql_query($q8);
																		while($rw8 = mysql_fetch_array($re8)){	$val8[] = $rw8;	}
																		if(!empty($val8)){foreach($val8 as $locations8){ ?>
																		<ul>
																			<li id="li_<?php echo $locations8['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations8['location_id']?>" ><?php echo $locations8['location_title']?></span>
																				<?php $q9 = "select location_id, location_title from project_locations where location_parent_id = '".$locations8['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																				$re9 = mysql_query($q9);
																				while($rw9 = mysql_fetch_array($re9)){	$val9[] = $rw9;	}
																				if(!empty($val9)){foreach($val9 as $locations9){ ?>
																				<ul>
																					<li id="li_<?php echo $locations9['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations9['location_id']?>" ><?php echo $locations9['location_title']?></span>
																						<?php $q10 = "select location_id, location_title from project_locations where location_parent_id = '".$locations9['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																							$re10 = mysql_query($q10);
																							while($rw10 = mysql_fetch_array($re10)){	$val10[] = $rw10;	}
																							if(!empty($val10)){foreach($val10 as $locations10){ ?>	
																							<ul>
																								<li id="li_<?php echo $locations10['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations10['location_id']?>" ><?php echo $locations10['location_title']?></span>
																									<?php $q11 = "select location_id, location_title from project_locations where location_parent_id = '".$locations10['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																									$re11 = mysql_query($q11);
																									while($rw11 = mysql_fetch_array($re11)){	$val11[] = $rw11;	}
																									if(!empty($val11)){foreach($val11 as $locations11){ ?>	
																									<ul>
																										<li id="li_<?php echo $locations11['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations11['location_id']?>" ><?php echo $locations11['location_title']?></span>
																											<?php $q12 = "select location_id, location_title from project_locations where location_parent_id = '".$locations11['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																											$re12 = mysql_query($q12);
																											while($rw12 = mysql_fetch_array($re12)){	$val12[] = $rw12;	}
																											if(!empty($val12)){foreach($val12 as $locations12){ ?>	
																											<ul>
																												<li id="li_<?php echo $locations12['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations12['location_id']?>" ><?php echo $locations12['location_title']?></span>
																												<?php $q13 = "select location_id, location_title from project_locations where location_parent_id = '".$locations12['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																												$re13 = mysql_query($q13);
																												while($rw13 = mysql_fetch_array($re13)){	$val13[] = $rw13;	}
																												if(!empty($val13)){foreach($val13 as $locations13){ ?>	
																												<ul>
																													<li id="li_<?php echo $locations13['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations13['location_id']?>" ><?php echo $locations13['location_title']?></span>
																													</li>
																												</ul>
																											<?php }$val13 =array();}?>
																											</li>
																										</ul>
																									<?php }$val12 =array();}?>
																									</li>
																								</ul>
																							<?php }$val11 =array();}?>
																							</li>
																						</ul>
																					<?php }$val10 =array();}?>
																					</li>
																				</ul>
																			<?php }$val9 =array();}?>
																			</li>
																		</ul>
																	<?php }$val8 =array();}?>
																	</li>
																</ul>
															<?php }$val7 =array();}?>
															</li>
														</ul>
													<?php }$val6 =array();}?>
													</li>
												</ul>
											<?php }$val5 =array();}?>
											</li>
										</ul>
									<?php }$val4 =array();}?>
									</li>
								</ul>
							<?php }$val3 =array();}?>								
							</li>
						</ul>
					<?php }$val2 =array();}?>
					</li>
				</ul>
			<?php }$val1 =array();}?>
		</li>
	</ul>
<?php }$val=array();}?>
</span>
</div>
<div class="contextMenu" id="myMenu2">
	<ul>
		<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
		<li id="edit"><img src="images/edit_right.png"  align="absmiddle" width="16" height="16" /> Edit</li>
		<li id="delete"><img src="images/delete.png"  align="absmiddle" width="14" height="15" /> Delete</li>
	</ul>
</div>
<div class="contextMenu" id="myMenu1">
	<ul>
		<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
	</ul>
</div>
<div id="spinner" style="z-index:1000"></div>
</div>
</div>
<!--<div class="big_container" style="width:722px;float:left;margin-top:50px;" ><?php #include'csv_table.php';?></div>-->
</div>