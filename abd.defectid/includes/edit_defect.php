<?php set_time_limit(36000000000000);
ob_start(); if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<script type="text/javascript" src="selectivizr-min.js"></script>
<?php }
$df_id = base64_decode($_GET['did']);
$pid = base64_decode($_GET['pid']);


$owner_id = $_SESSION['ww_owner_id'];	
$msg ='';
include('includes/commanfunction.php');
$object= new COMMAN_Class();
function searchArray($array, $val){
	foreach($array as $arr){
		if(in_array($val, $arr)){
			return true;
		}
	}
	return false;
} ?>
<style>
#locationsContainer{ overflow-y: scroll; max-height: 200px; min-height: 150px; border-radius:5px; -moz-border-radius: 5px;  -webkit-border-radius: 5px; border:1px solid; margin-top:15px; width:420px; }
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #FFF; }
.clickableLines{ cursor:pointer; padding:5px; margin-right:5px;}
.clickableLines:hover{ background-color:#336FCE;padding:5px; margin-right:5px;color:#FFFFFF;}
#dropDown{ cursor:pointer; }
#discriptionHide{ display:none; height: 150px; overflow-y: scroll; position:absolute; background:#FFFFFF; border:1px solid #0BA4FF; width:420px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; z-index:1000; color:#000000; text-shadow:none; }
.issueTo{ border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; border:1px solid; width:150px; border-color:#FFFFFF; height:25px;}
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
/*ul.telefilms li{height:15px;}*/
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
</style>
<!-- Ajax Post -->
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var validator = $("#defect_form").validate({
		rules:{
			raisedBy:{
				required: true
			}
		},
		messages:{
			raisedBy:{
				required: '<div class="error-edit-profile">The raised by name field is required</div>'
			},
			debug:true
		}
	});
});

var items=0;
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
function validateDelete(){
	var r = jConfirm('Do you want to delete Inspection ?', null, function(r){
		if (r==true){
			alert('Word');
		}else{
			return false;
		}
	});
}
</script>
<!-- Ajax Post -->
<!-- Date Picker Starts -->
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<style type="text/css" title="currentStyle">
@import "datatable/examples_support/themes/smoothness/jquery-ui-1.8.17.custom.css";
</style>
<script language="javascript">
window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_1",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_2",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_3",
			dateFormat:"%d-%m-%Y"
		});
	};
</script>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
<style>.fixedByDate{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }</style>
<!-- Date Picker Ends -->
<link href="../style.css" rel="stylesheet" type="text/css" />
<?php
if(isset($_POST['removeProject'])){
 	$df_id = $_POST['removeProject'];
	$projectId = $_POST['projectId'];
	
	$deleteQry = "UPDATE project_inspections SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$df_id."' AND project_id = '".$projectId."'";
	mysql_query($deleteQry);

	$deleteQry1 = "UPDATE issued_to_for_inspections SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$df_id."' AND project_id = '".$projectId."'";
	mysql_query($deleteQry1);

	$deleteQry2 = "UPDATE inspection_graphics SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$df_id."' AND project_id = '".$projectId."'";
	mysql_query($deleteQry2);


	if(isset($_SESSION['ww_is_company'])){?>
	<script language="javascript" type="text/javascript">window.location.href="?sect=c_defect&bk=Y&ms=<?=base64_encode('Deleted')?>";</script>	
<?php	}else
if(isset($_SESSION['ww_is_builder'])){?>
	<script language="javascript" type="text/javascript">window.location.href="?sect=i_defect&bk=Y&ms=<?=base64_encode('Deleted')?>";</script>	
<?php	} 
}
if(isset($_POST['button'])){
	$description = $_POST['description'];
	$projectId = $_POST['projectId'];
	$raisedBy = $_POST['raisedBy'];
	$note = $_POST['note'];
	$df_id = $_POST['df_id'];
	$location = $_POST['location'];
	$locationTree = $_POST['locationTree'];
//	print_r($_POST);
		$updateQry = "UPDATE project_inspections SET
						inspection_description = '".addslashes($description)."',
						inspection_notes = '".addslashes($note)."',
						inspection_raised_by = '".addslashes($raisedBy)."',
						location_id = '".$location."',
						last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW(),
						inspection_location =  '".$locationTree."'
					WHERE
						inspection_id = '".$df_id."'";
	mysql_query($updateQry) or die(mysql_error());
	$updateCheckList = "UPDATE inspection_check_list SET last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$df_id."'";
	mysql_query($updateCheckList) or die(mysql_error());
	$updateImages = "UPDATE inspection_graphics SET last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id'].", original_modified_date = NOW() WHERE inspection_id = '".$df_id."'";
	mysql_query($updateImages) or die(mysql_error());

	for($i=0; $i<sizeof($_POST["issue_to_id"]); $i++){
		$issue_to_id = '';$issueTo = '';
		$issue_to_id = $_POST['issue_to_id'][$i];
		$issueTo = $_POST['issueTo'][$i];
		$fixedByDate = date('Y-m-d', strtotime($_POST['fixedByDate'][$i]));
		$costAttribute = $_POST['costAttribute'][$i];		
		$status = $_POST['status'][$i];		
		$costImpact = $_POST['costImpact'][$i];		
		$costImpactPrice = $_POST['costImpactPrice'][$i];		

		if ($status == "Closed"){
			$closed_date = date();
		}else{
			$closed_date = '0000-00-00';
		}

		if($issue_to_id == '' && $issueTo != ''){
			$insertQryMul = "INSERT INTO issued_to_for_inspections SET
						issued_to_name = '".addslashes($issueTo)."',
						inspection_id = '".$df_id."',
						inspection_fixed_by_date = '".$fixedByDate."',
						last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
						last_modified_date = NOW(),
						created_by = '".$_SESSION['ww_builder']['user_id']."',
						created_date = NOW(),
						original_modified_date = NOW(),
						project_id = '".$projectId."',
						cost_attribute = '".$costAttribute."',
						inspection_status = '".$status."',
						cost_impact_type = '".$costImpact."',
						cost_impact_price = '".$costImpactPrice."',
						closed_date = '".$closed_date."'";
			mysql_query($insertQryMul);
		}else if($issueTo != ''){
			$updateQryMul = "UPDATE issued_to_for_inspections SET
						issued_to_name = '".addslashes($issueTo)."',
						inspection_fixed_by_date = '".$fixedByDate."',
						last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW(),
						cost_attribute = '".$costAttribute."',
						inspection_status = '".$status."',
						cost_impact_type = '".$costImpact."',
						cost_impact_price = '".$costImpactPrice."',
						closed_date = '".$closed_date."'
					WHERE
						issued_to_inspections_id = '".$issue_to_id."'";
			mysql_query($updateQryMul);
		}
/*		if ($costAttribute == "None"){
			$costImpact = 'None';		
			$costImpactPrice = 0;		
		}
		$selIssueTo = $object->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'issue_to_name = "'.$issueTo.'" AND is_deleted = 0');
		if(empty($selIssueTo)){
			$insertNewIssueTo = "INSERT INTO inspection_issue_to SET issue_to_name = '".addslashes($issueTo)."', is_deleted = '0', project_id = '".$projectId."'";
			mysql_query($insertNewIssueTo);
		}*/
		
		if ($status == "Closed"){
			$imageName = $projectId.'_sign_'.$df_id.'.png';
			copy('./images/master_signoff.png', './inspections/signoff/'.$imageName);
			$secUpdateQRY = "UPDATE project_inspections SET inspection_sign_image = '".$imageName."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = ".$df_id." AND project_id = ".$projectId;
			mysql_query($secUpdateQRY);
		}
	}
	if(isset($_SESSION['ww_is_company'])){?>
	<script language="javascript" type="text/javascript">window.location.href="?sect=c_defect&bk=Y&ms=<?=base64_encode('Updated')?>";</script>	
<?php	}else{
		if(isset($_SESSION['ww_is_builder'])){?>
			<script language="javascript" type="text/javascript">window.location.href="?sect=i_defect&bk=Y&ms=<?=base64_encode('Updated')?>";</script>	
<?php	}
	}
}

$inspectionDetail = $object->selQRY('inspection_id, project_id, inspection_description, inspection_notes, inspection_raised_by, inspection_inspected_by, location_id, inspection_location', 'project_inspections', 'inspection_id = "'.$df_id.'"and is_deleted = 0');
$projectName = $object->getDataByKey('user_projects', 'project_id', $inspectionDetail['project_id'], 'project_name');

	$issToList = '';?>
<div class="content_center" style="margin-left:70px;margin-top:80px\9;">
	<div class="content_hd" style="background-image:url(images/edit_defect_hd.png);margin: -5px 0 -30px -80px;margin-top:-85px\9;"></div>
	<div class="signin_form1" style="margin-top:15px;margin-top:-25px\9;">
	<?php if($msg != ''){?>
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;">
			<div class="success_r" style="height:35px;width:405px;"><p><?php echo $msg; ?></p></div>
		</div>
	<?php }
	$q = "select location_id, location_title from project_locations where project_id = '".$inspectionDetail['project_id']."' and location_parent_id = '0' and is_deleted = '0' order by location_title";
$re = mysql_query($q);
while($rw = mysql_fetch_array($re)){	$val[] = $rw;	}?>
		<form action="" method="post" name="defect_form" id="defect_form">		
		<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="border: 1px solid;width: 670px;">
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Project Name</td>
					<td width="312" colspan="2"><input type="text" name="projectName" readonly="readonly" value="<?=$projectName?>" class="input_small" />
					<input type="hidden" name="projectId" id="projectId" value="<?=$inspectionDetail['project_id']?>"  />
					<? #print_r($_SESSION);?>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Location</td>
					<td width="312" colspan="2">
					<?php $locations_exists = $object->subLocations($inspectionDetail['location_id'], ' > '); ?>
						<div id="location_exists"><?=stripslashes($locations_exists);?></div>
						<input type="hidden" name="locationTree" id="locationTree" value="<?=stripslashes($locations_exists);?>"  />
<div id="locationsContainer">
<?php $i=0; if(!empty($val)){foreach($val as $locations){$i++;?>
<ul class="telefilms">
<li id="li_<?php echo $locations['location_id']?>">
<span class="jtree-button demo1" id="<?php echo $locations['location_id']?>"><?php echo stripslashes($locations['location_title'])?></span>
<?php $q1 = "select location_id, location_title from project_locations where location_parent_id = '".$locations['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
$re1 = mysql_query($q1);
while($rw1 = mysql_fetch_array($re1)){	$val1[] = $rw1;	}
if(!empty($val1)){foreach($val1 as $locations1){ ?>
<ul>
<li id="li_<?php echo $locations1['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations1['location_id']?>"><?php echo stripslashes($locations1['location_title'])?></span>
<?php $q2 = "select location_id, location_title from project_locations where location_parent_id = '".$locations1['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
$re2 = mysql_query($q2);
while($rw2 = mysql_fetch_array($re2)){	$val2[] = $rw2;	}
if(!empty($val2)){foreach($val2 as $locations2){ ?>
<ul>
	<li id="li_<?php echo $locations2['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations2['location_id']?>"><?php echo stripslashes($locations2['location_title'])?></span>
	
		<?php $q3 = "select location_id, location_title from project_locations where location_parent_id = '".$locations2['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
		$re3 = mysql_query($q3);
		while($rw3 = mysql_fetch_array($re3)){	$val3[] = $rw3;	}
		if(!empty($val3)){foreach($val3 as $locations3){ ?>
		<ul>
			<li id="li_<?php echo $locations3['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations3['location_id']?>"><?php echo stripslashes($locations3['location_title'])?></span>
				<?php $q4 = "select location_id, location_title from project_locations where location_parent_id = '".$locations3['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
				$re4 = mysql_query($q4);
				while($rw4 = mysql_fetch_array($re4)){	$val4[] = $rw4;	}
				if(!empty($val4)){foreach($val4 as $locations4){ ?>
				<ul>
					<li id="li_<?php echo $locations4['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations4['location_id']?>" ><?php echo stripslashes($locations4['location_title'])?></span>
						<?php $q5 = "select location_id, location_title from project_locations where location_parent_id = '".$locations4['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
						$re5 = mysql_query($q5);
						while($rw5 = mysql_fetch_array($re5)){	$val5[] = $rw5;	}
						if(!empty($val5)){foreach($val5 as $locations5){ ?>
						<ul>
							<li id="li_<?php echo $locations5['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations5['location_id']?>" ><?php echo stripslashes($locations5['location_title'])?></span>
								<?php $q6 = "select location_id, location_title from project_locations where location_parent_id = '".$locations5['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
								$re6 = mysql_query($q6);
								while($rw6 = mysql_fetch_array($re6)){	$val6[] = $rw6;	}
								if(!empty($val6)){foreach($val6 as $locations6){ ?>
								<ul>
									<li id="li_<?php echo $locations6['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations6['location_id']?>" ><?php echo stripslashes($locations6['location_title'])?></span>
										<?php $q7 = "select location_id, location_title from project_locations where location_parent_id = '".$locations6['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
										$re7 = mysql_query($q7);
										while($rw7 = mysql_fetch_array($re7)){	$val7[] = $rw7;	}
										if(!empty($val7)){foreach($val7 as $locations7){ ?>
										<ul>
											<li id="li_<?php echo $locations7['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations7['location_id']?>" ><?php echo stripslashes($locations7['location_title'])?></span>
												<?php $q8 = "select location_id, location_title from project_locations where location_parent_id = '".$locations7['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
												$re8 = mysql_query($q8);
												while($rw8 = mysql_fetch_array($re8)){	$val8[] = $rw8;	}
												if(!empty($val8)){foreach($val8 as $locations8){ ?>
												<ul>
													<li id="li_<?php echo $locations8['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations8['location_id']?>" ><?php echo stripslashes($locations8['location_title'])?></span>
														<?php $q9 = "select location_id, location_title from project_locations where location_parent_id = '".$locations8['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
														$re9 = mysql_query($q9);
														while($rw9 = mysql_fetch_array($re9)){	$val9[] = $rw9;	}
														if(!empty($val9)){foreach($val9 as $locations9){ ?>
														<ul>
															<li id="li_<?php echo $locations9['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations9['location_id']?>" ><?php echo stripslashes($locations9['location_title'])?></span>
																<?php $q10 = "select location_id, location_title from project_locations where location_parent_id = '".$locations9['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
																	$re10 = mysql_query($q10);
																	while($rw10 = mysql_fetch_array($re10)){	$val10[] = $rw10;	}
																	if(!empty($val10)){foreach($val10 as $locations10){ ?>	
																	<ul>
																		<li id="li_<?php echo $locations10['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations10['location_id']?>" ><?php echo stripslashes($locations10['location_title'])?></span>
																			<?php $q11 = "select location_id, location_title from project_locations where location_parent_id = '".$locations10['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
																			$re11 = mysql_query($q11);
																			while($rw11 = mysql_fetch_array($re11)){	$val11[] = $rw11;	}
																			if(!empty($val11)){foreach($val11 as $locations11){ ?>	
																			<ul>
																				<li id="li_<?php echo $locations11['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations11['location_id']?>" ><?php echo stripslashes($locations11['location_title'])?></span>
																					<?php $q12 = "select location_id, location_title from project_locations where location_parent_id = '".$locations11['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
																					$re12 = mysql_query($q12);
																					while($rw12 = mysql_fetch_array($re12)){	$val12[] = $rw12;	}
																					if(!empty($val12)){foreach($val12 as $locations12){ ?>	
																					<ul>
																						<li id="li_<?php echo $locations12['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations12['location_id']?>" ><?php echo stripslashes($locations12['location_title'])?></span>
																						<?php $q13 = "select location_id, location_title from project_locations where location_parent_id = '".$locations12['location_id']."' and is_deleted = '0' and project_id = '".$inspectionDetail['project_id']."' order by location_title";
																						$re13 = mysql_query($q13);
																						while($rw13 = mysql_fetch_array($re13)){	$val13[] = $rw13;	}
																						if(!empty($val13)){foreach($val13 as $locations13){ ?>	
																						<ul>
																							<li id="li_<?php echo $locations13['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations13['location_id']?>" ><?php echo stripslashes($locations13['location_title'])?></span>
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
</div>
<div class="contextMenu" id="myMenu2">
	<ul>
		<li id="select"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Select</li>
	</ul>
</div>
						<input type="hidden" name="location" id="location" value="<?=$inspectionDetail['location_id']?>" />
						<input type="hidden" name="locationChecklist" id="locationChecklist" value="<?=$lastLocation;?>"  />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Description<span class="req"></span></td>
					<td width="312" colspan="2"><textarea name="description" id="description" class="text_area_small" cols="33" rows="5" style="width:382px;background-image:url(../images/texarea_select_box.png);height:137px;" ><?=$inspectionDetail['inspection_description']?></textarea><div style="position:absolute; margin-left: 420px;margin-top: -150px;"><img id="dropDown" src="images/downbox.png" border="0" style="background-color:none;" /></div>
					<div id="discriptionHide">
<?php //Update Dated 22-11-2012
$sdName = $object->selQRYMultiple('distinct(description), tag', 'standard_defects', 'project_id = "'.$inspectionDetail['project_id'].'"');
$tagArray = array();
foreach($sdName as $stdDefect){
	$tagData = $stdDefect['description'];
	$tagKey = explode(';', $stdDefect['tag']);
	if($stdDefect['tag'] == ''){
		$tagArray[][$stdDefect['tag']] = $tagData;
	}
	$tagKeyCount = sizeof($tagKey);
	for($i=0; $i<$tagKeyCount; $i++){
		if($tagKey[$i] != ''){
			$tagArray[][$tagKey[$i]] = $tagData;
		}
	}
}
$standardDefects = array();
foreach($tagArray as $tArray){
	$testKey = (string)key($tArray);
	$pos = strpos($locations_exists, $testKey);
	if($pos === false) {}else{
		if(!in_array($tArray[key($tArray)], $standardDefects)){
			$standardDefects[] = $tArray[key($tArray)];
		}
	}
	if(key($tArray) == ''){
		$standardDefects[] = $tArray[key($tArray)];
	}
}
#print_r($imagesData);die; ?>
						<ul id="standardDefect" style="list-style:none;margin-left:-30px;">	
					<?php if(!empty($standardDefects)){
							for($i=0; $i<sizeof($standardDefects); $i++){?>
								<li class="clickableLines"><?php echo $standardDefects[$i];?></li>
					<?php 	}
						}else{?>
							<li class="clickableLines">No One Standard Defect Found !</li>
					<?php }?>
						</ul>
					</div>
					</td>
				</tr>
				<tr <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>
					<td width="134" nowrap="nowrap" valign="top">Notes<span class="req"></span></td>
					<td width="312" colspan="2"><textarea name="note" id="note" class="text_area_small" cols="33" rows="5" style="width:382px;background-image:url(../images/texarea_select_box.png);height:137px;"><?=$inspectionDetail['inspection_notes']?></textarea></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Raised By<span class="req">*</span></td>
					<td width="312" colspan="2">
					<?php if(isset($_SESSION['ww_is_company'])){?>
							<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="">Select</option>
								<option value="Builder" <?php if($inspectionDetail['inspection_raised_by']  == 'Builder'){ echo 'selected="selected"';}else{ if($inspectionDetail['inspection_raised_by'] == ''){ echo 'selected="selected"'; } }?> >Builder</option>
								<option value="Architect" <?php if($inspectionDetail['inspection_raised_by']  == 'Architect'){ echo 'selected="selected"';}?>>Architect</option>
								<option value="Structural Engineer" <?php if($inspectionDetail['inspection_raised_by']  == 'Structural Engineer'){ echo 'selected="selected"';}?>>Structural Engineer</option>
								<option value="Services Engineer" <?php if($inspectionDetail['inspection_raised_by']  == 'Services Engineer'){ echo 'selected="selected"';}?>>Services Engineer</option>
								<option value="Superintendant" <?php if($inspectionDetail['inspection_raised_by']  == 'Superintendant'){ echo 'selected="selected"';}?>>Superintendant</option>
								<option value="General Consultant" <?php if($inspectionDetail['inspection_raised_by']  == 'General Consultant'){ echo 'selected="selected"';}?>>General Consultant</option>
								<option value="Client" <?php if($inspectionDetail['inspection_raised_by']  == 'Client'){ echo 'selected="selected"';}?>>Client</option>
								<option value="Purchaser" <?php if($inspectionDetail['inspection_raised_by'] == 'Client'){ echo 'selected="selected"';}?>>Purchaser</option>
								<option value="Sub Contractor" <?php if($inspectionDetail['inspection_raised_by'] == 'Sub Contractor'){ echo 'selected="selected"';}?> >Sub Contractor</option>
							</select>						
					<?php }else{?>
					
					<? if($_SESSION['userRole'] != 'All Defect'){?>
							<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="">Select</option>
								<option value="<?=$_SESSION['userRole']?>" selected="selected" ><?=$_SESSION['userRole']?></option>
							</select>
						<?php }else{ ?>
							<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="">Select</option>
								<option value="Builder" <?php if($inspectionDetail['inspection_raised_by']  == 'Builder'){ echo 'selected="selected"';}else{ if($inspectionDetail['inspection_raised_by'] == ''){ echo 'selected="selected"'; } }?> >Builder</option>
								<option value="Architect" <?php if($inspectionDetail['inspection_raised_by']  == 'Architect'){ echo 'selected="selected"';}?>>Architect</option>
								<option value="Structural Engineer" <?php if($inspectionDetail['inspection_raised_by']  == 'Structural Engineer'){ echo 'selected="selected"';}?>>Structural Engineer</option>
								<option value="Services Engineer" <?php if($inspectionDetail['inspection_raised_by']  == 'Services Engineer'){ echo 'selected="selected"';}?>>Services Engineer</option>
								<option value="Superintendant" <?php if($inspectionDetail['inspection_raised_by']  == 'Superintendant'){ echo 'selected="selected"';}?>>Superintendant</option>
								<option value="General Consultant" <?php if($inspectionDetail['inspection_raised_by']  == 'General Consultant'){ echo 'selected="selected"';}?>>General Consultant</option>
								<option value="Client" <?php if($inspectionDetail['inspection_raised_by']  == 'Client'){ echo 'selected="selected"';}?>>Client</option>
								<option value="Purchaser" <?php if($inspectionDetail['inspection_raised_by'] == 'Client'){ echo 'selected="selected"';}?>>Purchaser</option>
								<option value="Sub Contractor" <?php if($inspectionDetail['inspection_raised_by'] == 'Sub Contractor'){ echo 'selected="selected"';}?> >Sub Contractor</option>
							</select>						
						<?php } 
					
					}?>
						
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Inspected By</td>
					<td width="312" colspan="2">
						<input type="text" readonly="readonly" value="<?=stripslashes($inspectionDetail['inspection_inspected_by']);?>" class="input_small"  />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Issued To Detail</td>
                    <td colspan="3">
						<table width="70%" border="0" cellspacing="5" cellpadding="5">
							<tr>
								<td>&nbsp;</td>
								<td>Issued&nbsp;To</td>
								<td>Fix&nbsp;By&nbsp;Date</td>
								<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>Cost Attribute</td>
								<td>Status</td>
								<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>Cost Impact</td>
								<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>Cost Impact Price (In&nbsp;$)</td>
							</tr>
<?php 
//Update Dated 22-11-2012
$issueToName = $object->selQRYMultiple('distinct(issue_to_name), company_name, tag', 'inspection_issue_to', 'project_id = "'.$inspectionDetail['project_id'].'" and is_deleted=0 ORDER BY issue_to_name');
$issueTagArray = array();
foreach($issueToName as $issueName){
if($issueName['company_name'] != "")
	$issueTagData = $issueName['issue_to_name']." (".$issueName['company_name'].")";
else
	$issueTagData = $issueName['issue_to_name'];
	$issueTagKey = explode(';', $issueName['tag']);
	if($issueName['tag'] == ''){
		$issueTagArray[][$issueName['tag']] = $issueTagData;
	}
	$issueTagKeyCount = sizeof($issueTagKey);
	for($i=0; $i<$issueTagKeyCount; $i++){
		if($issueTagKey[$i] != ''){
			$issueTagArray[][$issueTagKey[$i]] = $issueTagData;
		}
	}
}
$issueToSelect = array();
foreach($issueTagArray as $issueTArray){
	$testKeyIssue = (string)key($issueTArray);
	$pos = strpos($locations_exists, $testKeyIssue);
	if($pos === false) {}else{
		if(!in_array($issueTArray[key($issueTArray)], $standardDefects)){
			$issueToSelect[] = $issueTArray[key($issueTArray)];
		}
	}
	if(key($issueTArray) == ''){
		$issueToSelect[] = $issueTArray[key($issueTArray)];
	}
}
$issueToSelect = array_map('trim', $issueToSelect);

$issueToData = $object->selQRYMultiple('issued_to_inspections_id, issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status, cost_impact_type, cost_impact_price', 'issued_to_for_inspections', 'inspection_id = '.$df_id.' and is_deleted=0  group by issued_to_name order by issued_to_name');

$i=0;
$issueToRedundent = '';
foreach($issueToData as $issueTo){
	if($issueToRedundent == ''){
		$issueToRedundent .= '"'.$issueTo['issued_to_name'].'"';
	}else{
		$issueToRedundent .= ', "'.$issueTo['issued_to_name'].'"';
	}
}
#echo '<pre>';print_r($issueToSelect);print_r($issueToData);die;
	if(!empty($issueToData)){
		$sizePlus = sizeof($issueToData);
		for($m=0;$m<=2;$m++){$i++;
			$currentIssueToName = $issueToData[$m]['issued_to_name'];
			$inspectionFixedByDate = $issueToData[$m]['inspection_fixed_by_date']; 
			$inspectionStatus = $issueToData[$m]['inspection_status']; 
			$costAttribute = $issueToData[$m]['cost_attribute']; 
			$costImpactType = $issueToData[$m]['cost_impact_type']; 
			$costImpactPrice = $issueToData[$m]['cost_impact_price']; ?>
	 	<tr id="hide_<?=$m;?>" <?php if(!isset($issueToData[$m]['issued_to_inspections_id'])){echo 'style="display:none;"';}else{echo 'style="display: table-row;"';}?>>
			<td style="text-shadow:none;padding:5px;">
			<?php if($sizePlus == 3){}else{
				 if($m == ($sizePlus-1) && $m != 2){?>
					<img style="cursor:pointer;" onclick="AddItem();" src="images/inspectin_add.png" align="absmiddle" />
			<?php }else if(!isset($issueToData[$m]['issued_to_inspections_id'])){?>
					<img style="cursor:pointer;" onclick="removeElement('hide_<?=$m;?>');" src="images/inspectin_delete.png" align="absmiddle" />
			<?php } 
			}?>
			</td>
			<td width="33%" style="text-shadow:none;">
				<input type="hidden" name="issue_to_id[]" id="issue_to_id_<?php echo $i;?>" value="<?php echo $issueToData[$m]['issued_to_inspections_id'];?>" />
		<?php if(in_array($currentIssueToName, $issueToSelect)){?>
				<select name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">	
					<option value="">-- Select --</option>
				<?php for($k=0;$k<sizeof($issueToSelect);$k++){ ?>
					<option value="<?php echo $cValue = trim(stripslashes($issueToSelect[$k]))?>"<?php if($cValue == $currentIssueToName){echo 'selected="selected"'; #unset($issueToSelect[$k]);
					 }?>><?=$cValue?></option>		
				<?php }?>
				</select>
		<?php }else{?>
				<select name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">	
					<option value="">-- Select --</option>
				<?php for($k=0;$k<sizeof($issueToSelect);$k++){?>
					<option  value="<?php echo $cValue = trim(stripslashes($issueToSelect[$k]))?>"><?=$cValue?></option>		
				<?php #if($cValue == 'NA' && !isset($issueToData[$m]['issued_to_inspections_id'])){ echo 'selected="selected"';}?> 				<?php }
				 if(isset($issueToData[$m]['issued_to_inspections_id'])){?>
					<option value="<?=trim(stripslashes($currentIssueToName));?>" selected="selected" ><?=trim(stripslashes($currentIssueToName))?></option> 				 
				 <?php }?>
				</select>
		<?php }?>
			</td>
			<td width="33%" style="text-shadow:none;" align="center">
				<?php 
				if(isset($issueToData[$m]['issued_to_inspections_id'])){
					if($inspectionFixedByDate != '0000-00-00'){
						$fixedByDate = date('d-m-Y', strtotime($inspectionFixedByDate));
					}else{
						$fixedByDate = '';
					}
				}else{
					$fixedByDate = date('d-m-Y');
				}?>
				<input name="fixedByDate[]" id="fixedByDate_<?php echo $i;?>" class="fixedByDate" readonly="readonly" size="10" value="<?php echo $fixedByDate;?>" />
			</td>
			<td style="text-shadow:none;" <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?> align="center">
				<select name="costAttribute[]" type="text" id="costAttribute_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">	
					<option <?php if($costAttribute == 'None'){ echo 'selected="selected"';}?> value="None">None</option>
					<option <?php if($costAttribute == 'Backcharge'){ echo 'selected="selected"';}?> value="Backcharge">Backcharge</option>
					<option <?php if($costAttribute == 'Variation'){ echo 'selected="selected"';}?> value="Variation">Variation</option>
				</select>
			</td>
			<td style="text-shadow:none;" align="center">
				<select name="status[]" id="status_<?php echo $i;?>" class="status" style="width:90px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
					<option <?php if($inspectionStatus == 'Open'){ echo 'selected="selected"';}?> value="Open">Open</option>
					<option <?php if($inspectionStatus == 'Pending'){ echo 'selected="selected"';}?> value="Pending">Pending</option>
					<option <?php if($inspectionStatus == 'Fixed'){ echo 'selected="selected"';}?> value="Fixed">Fixed</option>
					<option <?php if($inspectionStatus == 'Closed'){ echo 'selected="selected"';}?> value="Closed">Closed</option>
				</select>
			</td>
			<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>
				<select name="costImpact[]" id="costImpact_<?php echo $i;?>" class="status" style="width:80px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
			<?php if($costAttribute == 'None'){?>
					<option <?php if($costImpactType == 'None'){ echo 'selected="selected"';}?> value="None">None</option>
			<?php }else{?>
					<option <?php if($costImpactType == 'None'){ echo 'selected="selected"';}?> value="None">None</option>
					<option <?php if($costImpactType == 'Low'){ echo 'selected="selected"';}?> value="Low">Low</option>
					<option <?php if($costImpactType == 'Medium'){ echo 'selected="selected"';}?> value="Medium">Medium</option>
					<option <?php if($costImpactType == 'High'){ echo 'selected="selected"';}?> value="High">High</option>
			<?php }?>
				</select>
			</td>
			<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>
				<input name="costImpactPrice[]" id="costImpactPrice_<?php echo $i;?>" class="fixedByDate" size="5" value="<?php echo $costImpactPrice;?>" onkeypress="return checkNo(event, 'costImpact_<?php echo $i;?>', this.id, this.value);" <?php if($costAttribute == 'None'){echo 'disabled="disabled"';}?> />
			</td>
		</tr>
	<?php }
	}else{ ?><tr><td colspan="7"><em>No One Issue to Found</em></td><tr><?php }?>
</table>
					</td>
				</tr>
				<tr><td colspan="3" >&nbsp;</td></tr>
				<tr>
					<td>
						<input name="backButton" type="button" class="submit_btn" id="backButton" value="" style="background-image:url(images/back_btn.png); border:none; width:111px;height:44px;" onclick="javascript:history.back();"  />
					</td>
					<td align="center">
<?php if($_SESSION['web_delete_inspection'] == 1){?>
						<input name="remove" type="button" class="submit_btn" id="remove" style="background-image:url(images/remove_btn.png); border:none; width:111px;" onclick="checkReturn();" />
<?php }?>
					</td>
					<td>
						<input type="hidden" value="<?=$df_id?>" name="df_id" id="df_id"  />
						<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/submit.png); border:none; width:111px;" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.tree.old.js"></script>	
<script>
<?php if($_SESSION['web_close_inspection'] != 1){?>
$(".status").change(function(){
	if($(this).val() == 'Closed'){
		jAlert('You can\'t closed any inspection from here !');
		$(this).val('Open');
	}
});
<?php }?>
$("#autocomplete_1").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_1 = $("#autocomplete_1").val();
	var check = $.inArray(valueIssueTo_1, arr);
	if(check != (-1)){
		if(valueIssueTo_1 == arr[0]){}else{
			jAlert(valueIssueTo_1+' Already Selected !');
			$("#autocomplete_1").val(arr[0]);
		}
	}
});
$("#autocomplete_2").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_2 = $("#autocomplete_2").val();
	var check = $.inArray(valueIssueTo_2, arr);
	if(check != (-1)){
		if(valueIssueTo_2 == arr[1]){}else{
			jAlert(valueIssueTo_2+' Already Selected !');
			$("#autocomplete_2").val(arr[1]);
		}
	}
});
$("#autocomplete_3").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_3 = $("#autocomplete_3").val();
	var check = $.inArray(valueIssueTo_3, arr);
	if(check != (-1)){
		if(valueIssueTo_3 == arr[2]){}else{
			jAlert(valueIssueTo_3+' Already Selected !');
			$("#autocomplete_3").val(arr[2]);
		}
	}
});
$("#autocomplete_4").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_4 = $("#autocomplete_4").val();
	var check = $.inArray(valueIssueTo_4, arr);
	if(check != (-1)){
		if(valueIssueTo_4 == arr[3]){}else{
			jAlert(valueIssueTo_4+' Already Selected !');
			$("#autocomplete_4").val(arr[3]);		
		}
	}
});

$(".status").change(function(){
	if($(this).val() == 'Draft'){
		jAlert('Sorry! check list is not completed yet, Inspection is Under drafting stage.');
		$(this).val('Draft');
	}
});

function checkReturn(){
	var r = jConfirm('Do you want to delete Inspection ?', null, function(r){
		if (r==true){
			var projId = document.getElementById('df_id');
			projId.name = 'removeProject';
			document.forms['defect_form'].submit();
		}
	});
}
var align = 'center';
var top = 100;
var width = 670;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';
var copyStatus = false;
var copyId = '';
var statusLoop = <?=$i;?>;

//Default Issue to array
<?php for($g=1; $g <= $i; $g++){ ?>
	var issueTo_<?=$g?> = '';
	var issueTo_selectd_<?=$g?> = $('#autocomplete_<?=$g?>').val();
	$('#autocomplete_<?=$g?> option').each(function(){
		if(this.value != 0){
			if(issueTo_<?=$g?> == ''){
				issueTo_<?=$g?> = this.value;
			}else{
				issueTo_<?=$g?> += ','+this.value;
			}
		}
	});
	$('#costImpact_<?=$g?>').change(function(){
		var priceVal = this.value;
		var costAttr = $('#costAttribute_<?=$g?>').val();
		if(costAttr == 'None'){
			jAlert('Sorry! Please select any other cost attribute type first, for editing this Issued To cost impact type.', 'Permission Alert');
			$('#costImpact_<?=$g?>').val('None');
			return false;
		}else{
			if(priceVal == 'None'){
				$('#costImpactPrice_<?=$g?>').val('0.00');
			}
			if(priceVal == 'Low'){
				$('#costImpactPrice_<?=$g?>').val('100.00');
			}
			if(priceVal == 'Medium'){
				$('#costImpactPrice_<?=$g?>').val('1000.00');
			}
			if(priceVal == 'High'){
				$('#costImpactPrice_<?=$g?>').val('10000.00');
			}
		}
	});
	
	$('#costAttribute_<?=$g?>').change(function(){
		if(this.value == 'None'){
			$('#costImpact_<?=$g?>').html('<option value="None">None</option>');
			$('#costImpactPrice_<?=$g?>').val('0.00');
			$('#costImpactPrice_<?=$g?>').attr('disabled', true);
		}else{
			$('#costImpactPrice_<?=$g?>').attr('disabled', false);
			$('#costImpact_<?=$g?>').html('<option value="None">None</option><option value="Low">Low</option><option value="Medium">Medium</option><option value="High">High</option>');
		}
	});
<?php }?>
//Default Issue to array
function checkNo(evt, alertID, obj, objVal){
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if(charCode == 46){
		return true;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	if(objVal.length >= 11){
		if(objVal > 10000000){
			document.getElementById(obj).value = '10000000.00';
			jAlert("You can't enter more than 10000000 value");
			return false;
		}
	}
	return true;
}

function taggingIssueTo(newTree){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationSearchString="+newTree+"&projectID=<?=$inspectionDetail['project_id'];?>&uniqueId="+Math.random();
	xmlhttp.open("POST", "tagedIssuetTo.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				var resSplitResult = resString.split("@@@");
				<?php for($g=1; $g <= $i; $g++){ ?>
//					resSplitResult = resSplitResult.toString(); 
	//				uniqueIssueToArray = resSplitResult.split(",");
					var issueToOption = '<option value="">-- Select --</option><option value="NA">NA</option>';
					for(i = 0; i < resSplitResult.length; i++){
						resSplitResult[i] = jQuery.trim(resSplitResult[i]);
						var tempString = '<option value="'+resSplitResult[i]+'"';
						if(issueTo_selectd_<?=$g?> == resSplitResult[i]){
							tempString += 'selected="selected"';
						}
						tempString += '>'+resSplitResult[i]+'</option>'; 
						issueToOption += tempString;
					}
					document.getElementById('autocomplete_<?=$g?>').innerHTML = issueToOption;
				<?php }?>
			}
			taggingStandardDefect(newTree);
		}
	}
	xmlhttp.send(params);
}

function taggingStandardDefect(newTree){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationSearchString="+newTree+"&projectID=<?=$inspectionDetail['project_id'];?>&uniqueId="+Math.random();
	xmlhttp.open("POST", "tagedStandardDefect.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				document.getElementById('standardDefect').innerHTML = resString;
			}
		}
	}
	xmlhttp.send(params);
}

var previousLocId = '';

$(document).ready(function() {
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
});
$('span.demo1').contextMenu('myMenu2', {
	bindings: {
		'select': function(t) {
			if(previousLocId != ''){
				$(previousLocId).css({ 'font-weight' :'normal', 'font-style':'normal', 'text-decoration':'none' });
			}
			$(t).css({ 'font-weight' :'bold', 'font-style':'italic', 'text-decoration':'underline' });
			previousLocId = t;
			$("#location").val(t.id);
			$("#locationChecklist").val(document.getElementById(t.id).innerHTML);
			try{
				if(window.XMLHttpRequest){
					xmlhttp=new XMLHttpRequest();
				}else{
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				showProgress();
				params = "locationId="+t.id+"&uniqueId="+Math.random();
				xmlhttp.open("POST", "reloadLocationExpand.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						hideProgress();
						var newTree = xmlhttp.responseText;
						$('#location_exists').html(newTree);
						$('#locationTree').val(newTree);
						taggingIssueTo(newTree);
					}
				}
				xmlhttp.send(params);
			}catch(e){
			//	alert(e.message); 
			}
		}
	}
	});

var elementCount = 0;
var addedRow = new Array(); 
var existRow = new Array(); 
<?php for($l=0;$l<$sizePlus;$l++){?>
existRow[<?=$l;?>] = <?=$l;?>;
<?php }?>
//Add issue to row
function AddItem() {
	if(addedRow.length < 2){
		addedRow.push(++elementCount);
		if(document.getElementById('hide_'+elementCount).style.display == 'none'){
			document.getElementById('hide_'+elementCount).style.display = 'table-row';
		}else{
			if(elementCount == 1){
				document.getElementById('hide_2').style.display = 'table-row';
			}else{
				document.getElementById('hide_1').style.display = 'table-row';
			}
		}
	}else{
		jAlert("You can't add more than 3 Issue To !");
	}
}
//Remove issue to row
function removeElement(removeID){
	var r = jConfirm('Do you want to delete Issue To ?', null, function(r){
		if (r==true){
			--elementCount;
			document.getElementById(removeID).style.display = 'none';
			var idarr = removeID.split('_');
			$('#issueTo_'+idarr[1]).show();
			$('#issueTo_'+idarr[1]).val('NA');
			$('#otherIssueTo'+idarr[1]).hide();
			addedRow.pop();
		}
	});
}

var unique = function(origArr) {
	var issueToArray = new Array();
	issueToArray = origArr.split(',');
	var newArr = [], origLen = issueToArray.length, found, x, y;
    for ( x = 0; x < origLen; x++ ) {
        found = undefined;
        for ( y = 0; y < newArr.length; y++ ) {
            if ( issueToArray[x] === newArr[y] ) { 
              found = true;
              break;
            }
        }
        if (!found){
			newArr.push(issueToArray[x]);
		}
    }
	return newArr;
}

$("#dropDown").click(function () {
	if ($("#discriptionHide").is(":hidden")) { $("#discriptionHide").slideDown("slow"); }else{ $("#discriptionHide").hide("slow");}
});
$("li.clickableLines").click(function(){ $("#description").val(this.innerHTML); $("#discriptionHide").hide("slow");});
</script>
<style>
fieldset.permission { border:1px solid white; padding:15px; margin-top:30px; }
fieldset.permission legend { color:#FFFFFF; }
input[type=checkbox] { position: relative; cursor:pointer;}
label.label_check {cursor:pointer;}
</style>