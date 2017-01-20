<?php include'data-table.php'; ?>
<?php include_once("commanfunction.php");

$obj = new COMMAN_Class(); 
if(isset($_REQUEST['id'])){
	$id=$_REQUEST['id'];
	$_SESSION['project_id']=$id;
}else
	$id = ''; 
?>
<script type="text/javascript" src="selectivizr-min.js"></script>
<script>
var checklistArray = new Array();
function removeElement(parentDiv, childDiv){
	if (childDiv == parentDiv) {
		alert("The parent div cannot be removed.");
	}else if(document.getElementById(childDiv)) {
		$.alerts.okButton = '&nbsp;Yes&nbsp;';
		$.alerts.cancelButton = '&nbsp;No&nbsp;';
		jConfirm('Do you want to delete Checklist Item ?', 'Delete Confirmation',function(result){
			if(result){
				var child = document.getElementById(childDiv);
				var parent = document.getElementById(parentDiv);
				parent.removeChild(child);
			}else{
				return false;
			}
		});
	}
}
var items=0;
function AddItem() {
	div=document.getElementById("items");
	button=document.getElementById("add");
	items++;
	newitem="<table><tr><td><input type=\"text\" name=\"checklist[]\" id=\"checklist[]\" class=\"input_small\" onblur=\"checklistId(this, this.value);\"  /></td><td><a href=\"javascript:\" id=\"delete\" onclick=\"removeElement('items','New_"+items+"');\"><img src=\"images/inspectin_delete.png\" style=\"margin-left:7px;\"  /></a></td></tr></table>";
	newnode=document.createElement("span");
	newnode.setAttribute('id','New_'+items);
	newnode.innerHTML=newitem;
	div.insertBefore(newnode,button);
}
</script>
<?php 	
$err_msg='';
//insert for Assign inspector
if(!@session_is_registered('no_refresh')){
	$_SESSION['no_refresh'] = "";
}
if(isset($_REQUEST['id'])){
	$update = 'UPDATE check_list_items SET is_deleted = 1 WHERE check_list_items_id = "'.base64_decode($_REQUEST['id']).'"';
	mysql_query($update);

	$updateData = 'UPDATE inspection_check_list SET is_deleted = 1 WHERE check_list_items_id = "'.base64_decode($_REQUEST['id']).'" AND project_id = "'.$_SESSION['idp'].'"';
	mysql_query($updateData);

		$selfoInspection = $obj->selQRYMultiple('insepection_check_list_id, inspection_id, check_list_items_status, is_deleted', 'inspection_check_list', 'check_list_items_id = "'.base64_decode($_REQUEST['id']).'" AND project_id = "'.$_SESSION['idp'].'" ORDER BY check_list_items_status');
		if(!empty($selfoInspection)){
			$arrayNA = array();
			$insp4update = '';
			foreach($selfoInspection as $infections){
				if($infections['check_list_items_status'] == 'NA' && $infections['is_deleted'] == 0){
					$arrayNA[$infections['inspection_id']] = 1;	
				}else if ($infections['is_deleted'] == 0)
				{
					if(array_key_exists($infections['inspection_id'], $arrayNA)){
					}else{
						if($insp4update == ''){
							$insp4update = $infections['inspection_id'];
						}else{
							$insp4update .= ', '.$infections['inspection_id'];
						}
					}
				}
			}
		}
		if($insp4update != ''){
	echo		$updateQRY = "UPDATE issued_to_for_inspections SET inspection_status = 'Open' WHERE inspection_id IN (".$insp4update.") AND project_id = '".$_SESSION['idp']."' AND inspection_status = 'Draft'";
			mysql_query($updateQRY);
		}
die;
	$_SESSION['checklist_del']='Checklist name deleted successfully.';
	header('loaction:?sect=checklist');
}


if(isset($_POST['save'])){
	if($_POST['no_refresh'] == $_SESSION['no_refresh']){}else{
		$checkListExist = '';$dCount = 0;
		for($i=0; $i<sizeof($_POST['checklist']); $i++){
			if($_POST['checklist'][$i]!=''){
				$checkListExist = $obj->getRecords('check_list_items', 'check_list_items_name', $_POST['checklist'][$i], 'project_id', $_SESSION['idp'], 'check_list_items_id');
				if($checkListExist == ''){
					$inssertChecklist="INSERT INTO check_list_items SET
									project_id = '".$_SESSION['idp']."',
									check_list_items_name = '".$_POST['checklist'][$i]."',
									created_date = NOW()";
					mysql_query($inssertChecklist);			
				}else{
					$dCount++;
				}
			}else{
				$err_msg = 'checklist can not be empty!';
			}
		}
		if($err_msg == ''){
			$_SESSION['add_inspector_success'] = 'Checklist Items Inserted Successfully !';
		}
		if($dCount != ''){
			#$_SESSION['add_inspector_success'] .= '<br />'.$dCount.' Duplicate Records';
		}
		$_SESSION['no_refresh'] = $_POST['no_refresh'];
	}
}

?>
	<div id="middle" style="padding-top:10px;">
		<div id="leftNav" style="width:250px;float:left;">
			<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
				<tr>
					<td width="24%" align="left" valign="top">
						<a href="pms.php?sect=project_configuration" <?php if($_GET['sect'] == 'project_configuration'){echo 'class="left_btn2active"';}?> class="left_btn2" ><br /></a><br />
						<a href="pms.php?sect=issue_to"  <?php if($_GET['sect'] == 'issue_to'){echo 'class="left_btn3active"';}?> class="left_btn3"><br /></a><br />
						<a href="pms.php?sect=standard_defect" <?php if($_GET['sect'] == 'standard_defect'){echo 'class="left_btn4active"';}?>  class="left_btn4"><br /></a><br />
<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
						<a href="pms.php?sect=progress_monitoring" <?php if($_GET['sect'] == 'progress_monitoring'){echo 'class="left_btn5active"';}?> class="left_btn5" ><br /></a><br />
<?php }?>
						<a href="pms.php?sect=show_sub_loc" <?php if($_GET['sect'] == 'show_sub_loc'){echo 'class="left_btn6active"';}?> class="left_btn6" ><br /></a><br />
<?php if($_SESSION['set_user_permission'] == 1){?>
						<a href="pms.php?sect=permissions" <?php if($_GET['sect'] == 'permissions'){echo 'class="left_btn7active"';}?> class="left_btn7" ><br /></a><br />
<?php }?>
<?php if($_SESSION['web_menu_checklist'] == 1){?>
	<a href="pms.php?sect=checklist" <?php if($_GET['sect'] == 'checklist'){echo 'class="left_btn8active"';}?> class="left_btn8" ><br /></a><br />
<?php }?>

					</td>
					<td width="40%" valign="top"></td>
					<td width="21%" valign="top"><!--<a href="#"><img src="images/add_btn.png" width="65" height="26" vspace="20" /></a><br /><a href="#"><img src="images/remove_btn.png" width="65" height="27" /></a>--></td>
				</tr>
			</table>
		</div>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);  ?>
		<div id="rightCont" style="float:left;width:700px;">
			<div class="content_hd1" style="width:500px;margin-top:12px;">
				<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font><br />
				<a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" style="display: block;float: none;height: 35px;margin-left: 605px;margin-top: -25px;width: 87px;">
					<img src="images/back_btn2.png" />
				</a>
			</div>
			<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;"><?php if((isset($_SESSION['add_inspector_success'])) && (!empty($_SESSION['add_inspector_success']))) {
		if($_SESSION['add_inspector_success'] != ''){?>
			<div class="success_r" style="height:35px;width:300px;"><p><?=$_SESSION['add_inspector_success'];?></p></div>		
<?php   }
		unset($_SESSION['add_inspector_success']);}
		if($err_msg != '') { ?>
			<div class="failure_r" style="height:35px;width:185px;"><p><?php echo $err_msg; ?></p></div>
<?php 	} ?>
			</div>
			<div class="big_container" style="width:722px;float:left;margin-top:-50px;" >
				<div style="border:1px solid #ffffff; margin:45px 20px 10px 10px;text-align:center;">
					<form action="?sect=checklist" id="addchecklist" name="addchecklist"  method="post" style="margin-top:10px;" >
						<table width="100%" border="0" cellspacing="0" cellpadding="0" >
							<tr>
								<td colspan="3" align="left" style="padding-bottom:15px;">
									<strong style="font-size:16px;color:#ffffff;">&nbsp;&nbsp;Add Checktist Items Name</strong>
								</td>
							</tr>
							<tr>
								<td width="30%" style="color:#FFFFFF;"></td>
								<td width="50%" valign="top">
                                <input type="text" name="checklist[]" id="checklist[]" onblur="checklistId(this, this.value);" class="input_small" style="margin:20px 6px 4px -8px; " />&nbsp;&nbsp;<a href="javascript:" onclick="AddItem();"><img src="images/inspectin_add.png" /></a>
<div ID="items"></div><div id="massage"></div>
 								</td>
								<td width="20%">
								</td>
							</tr>
						</table>
                         <input type="submit" class="save_btn" name="save" style="background-color:transparent; background-image:url(images/submit_btn.png); font-size:0px; border:none; height:29px; width:87px;margin:15px 0 15px 545px;"/>
                         <input type="hidden" name="no_refresh" value="<?php echo uniqid(rand());?>" />
                     </form>  
				</div>
<div class="big_container" style="width:722px;float:left;margin-left:10px;" ><?php include'checklist_item.php';?></div>
				<div class="spacer"></div>
			</div>
		</div>
	</div>