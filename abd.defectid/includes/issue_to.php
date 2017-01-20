<?php
include_once("commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);include('func.php');
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
$builder_id=$_SESSION['ww_builder_id'];
function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	return $string;	
}
if(isset($_POST['assignSubmit'])){
	$_POST['assignIssueTo'] = (isset($_POST['assignIssueTo']) && !empty($_POST['assignIssueTo']))?$_POST['assignIssueTo']:array(0);
	
    $update="UPDATE inspection_issue_to SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE master_contact_id NOT IN(".implode(',',$_POST['assignIssueTo']).") AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
/*    $update="UPDATE inspection_issue_to_contact SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE master_issue_id NOT IN(".implode(',',$_POST['assignIssueTo']).") AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
*/	
	$projCurtIssueToData = $obj->selQRYMultiple('GROUP_CONCAT(master_contact_id) as ids', "inspection_issue_to", " is_deleted = '0' AND project_id='".$_SESSION['idp']."'");
	$oldIds = (isset($projCurtIssueToData[0]['ids']) && !empty($projCurtIssueToData[0]['ids']))?" AND contact_id NOT IN(".$projCurtIssueToData[0]['ids'].")":"";
	$issueToData = $obj->selQRYMultiple('*', "master_issue_to_contact", " is_deleted = '0' AND contact_id IN(".implode(',',$_POST['assignIssueTo']).") ".$oldIds." ");	

	foreach($issueToData as $issueTo){ 
		$issue_insert = "INSERT INTO inspection_issue_to SET
			project_id = '".$_SESSION['idp']."',
			master_issue_id = '".trim($issueTo['master_issue_id'])."',		
			master_contact_id = '".trim($issueTo['contact_id'])."',			
			issue_to_name = '".trim($issueTo['issue_to_name'])."',
			company_name = '".trim($issueTo['company_name'])."',
			issue_to_phone = '".trim($issueTo['issue_to_phone'])."',
			issue_to_email = '".trim($issueTo['issue_to_email'])."',
			tag = '".trim($issueTo['tag'])."',
			activity = '".trim($issueTo['activity'])."',
			last_modified_date = NOW(),
			last_modified_by = ".$builder_id.",
			created_date = NOW(),
			created_by = ".$builder_id;
		mysql_query($issue_insert);
		$issueToId = mysql_insert_id();
		
	/*	$issue_contact_insert = "INSERT INTO issue_to_contact SET
			project_id = '".$_SESSION['idp']."',
			issue_to_id = '".trim($issueToId)."',		
			master_issue_id = '".trim($issueTo['id'])."',	
			issue_to_name = '".trim($issueTo['issue_to_name'])."',
			company_name = '".trim($issueTo['company_name'])."',
			issue_to_phone = '".trim($issueTo['issue_to_phone'])."',
			issue_to_email = '".trim($issueTo['issue_to_email'])."',
			tag = '".trim($issueTo['tag'])."',
			activity = '".trim($issueTo['activity'])."',
			is_default= '1',
			last_modified_date = NOW(),
			last_modified_by = ".$builder_id.",
			created_date = NOW(),
			created_by = ".$builder_id;
		mysql_query($issue_contact_insert);*/
	}	
	$sucMsg = 1; $_POST['assignIssueTo']='';

}

if(isset($_REQUEST['id'])){
	$update='update inspection_issue_to set is_deleted=1,last_modified_date=now(),last_modified_by="'.$builder_id .'" where issue_to_id="'.base64_decode($_REQUEST['id']).'"';

	mysql_query($update);
	$_SESSION['issue_to_del']='Issued to deleted successfully.';
	header('loaction:?sect=issue_to');
	
}

// Delete issue to
if(isset($_REQUEST['issueToId'])){
	$update="UPDATE inspection_issue_to SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE issue_to_id = '".$_REQUEST['issueToId']."' AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
	
    $update="UPDATE issue_to_contact SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE issue_to_id = '".$_REQUEST['issueToId']."' AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
	$_SESSION['issue_to_del']='Issued to deleted successfully.';
	
?>
<script language="javascript" type="text/javascript">
window.location.href="?sect=issue_to";
</script>
<?php	}?>
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1{ background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1{ background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover{ background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13{ border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
.demo_jui td{ text-align:left; }
#example_server td img{ cursor:pointer;}
</style>
<script type="text/javascript">
var deadlock = true;//File upload
var params = "";
var align = 'center';
var top1 = 100;
var width = 800;
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
</script>
<div id="middle" style="padding-top:10px;">
<div id="leftNav" style="width:250px;float:left;">
<?php include 'side_menu.php';
$id=base64_encode($_SESSION['idp']);$hb=base64_encode($_SESSION['hb']);  ?>
</div>
<div id="rightCont" style="float:left;width:700px;">
	<div class="content_hd1" style="width:500px;margin-top:12px;">
		<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
		<a style="float:left;margin-top:-25px;width:87px;margin-left:590px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>">
			<img src="images/back_btn2.png" style="border:none;" />
		</a>
	</div><br clear="all" />
	<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:50px;width:520px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
	</div>
  	<div class="content_container" style="float:left;width:690px;text-align:center;margin-left:10px;margin-right:10px;">
<!--First Box-->
<?php include'data-table.php';?>
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
<div style="width:722px; float:left; margin:5px 0 10px 0; ">
<?php if((isset($sucMsg)) && $sucMsg==1) { $sucMsg==0;
	echo '	<div class="success_r" style="height:35px;width:300px;"><p>Action performed successfully!</p></div>	';	
  }
  if((isset($_SESSION['issue_to_del'])) && !empty($_SESSION['issue_to_del'])) { unset($_SESSION['issue_to_del']);
	echo '	<div class="success_r" style="height:35px;width:300px;"><p>Record deleted successfully!</p></div>	';	
  } ?>
	<form method="post" name="issueToForm" id="issueToForm" action="">
		<table border="0" cellspacing="0" cellpadding="3" style="width:748px;">
			<tr>
				<td colspan="2" align="left">
                <!-- Start Multiselect section -->
                	<!--link rel="stylesheet" href="js/multiselect/css/common.css" type="text/css" /-->
                <select id="assignIssueTo" class="multiselect" multiple="multiple" name="assignIssueTo[]">
                <?php $issueToProjData = $obj->selQRYMultiple('master_contact_id, issue_to_name, company_name', 'inspection_issue_to', " project_id = '".$_SESSION['idp']."' and is_deleted = '0' and issue_to_name!='' ");
					$projIssueToArr = array();
					foreach($issueToProjData as $issueTo){
						$projIssueToArr[$issueTo['master_contact_id']] = $issueTo['issue_to_name'];
					}
					
					$issueToData = $obj->selQRYMultiple('contact_id, issue_to_name, company_name', 'master_issue_to_contact', " is_deleted = '0' and issue_to_name!=''");
					$i=0;
					foreach($issueToData as $issueTo){
						if(isset($projIssueToArr[$issueTo['contact_id']])){
							echo '<option value="'.$issueTo['contact_id'].'" selected="selected">'.$issueTo['issue_to_name']." (".$issueTo['company_name'].')</option>';
						}else{
							echo '<option value="'.$issueTo['contact_id'].'">'.$issueTo['issue_to_name']." (".$issueTo['company_name'].')</option>';
						}
					}
				?>
				</select>
                <!-- End Multiselect section -->
                </td>
			</tr>   
			<tr>
				<td colspan="2"><input type="submit" style="background-image:url(images/submit_btn.png); border:none; width:111px; float:right;  height: 29px;" value="" id="assignSubmit" class="submit_btn" name="assignSubmit">
               </td>
			</tr>
		</table>
	</form>
<!--br clear="all" /-->
<!-- Issue to table section -->
<div class="big_container" style="width:722px; margin-top:0px;" >
<a href="#" onClick="addNewIssueTo();"><div style=" float:left; background:url('images/add_new_issue_to.png') !important; width:140px; height:24px; margin-bottom:2px; margin-top:0px !important;" class="add_new"></div></a>
<?php //include'project_issueto_table.php';?>
	<div class="demo_jui" style="width:99%;" >
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="example_server" width="100%">
			<thead>
				<tr>
					<th width="80%" nowrap="nowrap">Company Name</th>
					<!--th width="32%">Contact Name</th>
					<th>Phone</th>
					<th>Email</th>
					<th>Tags</th-->
					<th width="20%">Action</th>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td colspan="5" class="dataTables_empty">Loading data from server</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="spacer"></div>
</div>

<!-- Issue to table section -->
</div>
		</div>


</div>

<link type="text/css" href="js/multiselect/css/ui.multiselect.css" rel="stylesheet" />
<style>
.multiselect {width: 710px;	height: 200px;  }
.ui-multiselect div.list-container {
    border: 0 none;
    float: right !important;
    margin: 0;
    padding: 0;
}
.available, .selected{
	width:354px !important;
}
.ui-widget-header input{
	width:150px !important;
}

.add-all, .remove-all {
    background: none repeat scroll 0 0 #2070A5;
    border: 1px outset #2070A5;
    /*display: block;*/
	display:none !important;
    margin: 2px !important;
    padding: 5px !important;
}
</style>
<script type="text/javascript" src="js/multiselect/js/jquery-ui-1.8.custom.min.js"></script>
<script type="text/javascript" src="js/multiselect/js/plugins/tmpl/jquery.tmpl.1.1.1.js"></script>
<script type="text/javascript" src="js/multiselect/js/ui.multiselect.js"></script>
<script type="text/javascript">
$(document).ready(function(){
//	$(function(){
		$("#assignIssueTo").multiselect({ droppable: 'none' });
//	});
});

// Data table section
$(document).ready(function() {
	$('#example_server').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "project_issue_to_data_table.php",
		"iDisplayLength": 20,
		"bStateSave": true,
		"aoColumnDefs": [ {  "bSearchable": true, "bSortable": false, "aTargets": [ 1 ] }],
	} );
} );

// Issue to section 
function addNewIssueTo(){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_issue_to_by_ajax.php?&name='+Math.random(), loadingImage);
}

function addNewIssueToData(){
	if($('#company_name').val().trim() == ''){$('#errorCompanyName').show('slow');return false;}else{$('#errorCompanyName').hide('slow');}
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}
	showProgress();
	$.post('add_issue_to_by_ajax.php?antiqueID='+Math.random(), $('#addIssueToForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			window.location.href="?sect=issue_to";
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

// Issue to contact section 
function showIssueTo(issueToId){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'show_project_issue_to_list_by_ajax.php?issueToId='+issueToId, loadingImage, function() {loadData(issueToId); });
}

function loadData(issueToId){
	$('#projectIssueToData').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "show_project_issue_to_list_table_by_ajax.php?issueToId="+issueToId,
		"bStateSave": true,
		"bFilter": false,
	});
}

function addNewIssueToContact(issueToId){
//	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_issue_to_contact_by_ajax.php?issueToId='+issueToId+'&name='+Math.random(), loadingImage, function(){addNewIssueToContactData();});
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_project_issue_to_contact_by_ajax.php?issueToId='+issueToId+'&name='+Math.random(), loadingImage);

}

function addNewIssueToContactData(issueToId){
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}
	showProgress();
	$.post('add_project_issue_to_contact_by_ajax.php?antiqueID='+Math.random(), $('#addContactForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			window.location.href="?sect=issue_to";
			showIssueTo(issueToId);
			//closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function editIssueToContact(contactId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_project_issue_to_contact_by_ajax.php?contactId='+contactId+'&name='+Math.random(), loadingImage);
}

function RefreshTable(){
	$.getJSON("project_issue_to_data_table.php?", null, function( json ){
		table = $('#example_server').dataTable();
		oSettings = table.fnSettings();
		table.fnClearTable(this);
		
		for (var i=0; i<json.aaData.length; i++){
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
	});
}

function updateIssueToContactData(issueToId){
	var isDefault = $("#isDefault").val();
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}
	showProgress();
	$.post('edit_project_issue_to_contact_by_ajax.php?antiqueID='+Math.random(), $('#editContactForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			if(isDefault==1){
				RefreshTable();
			}
			showIssueTo(issueToId);
		//	closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function deleteIssueTo(issueToId){
	var r = jConfirm('Do you want to remove this issue to?', null, function(r){ if(r==true){ window.location = '?sect=issue_to&issueToId='+issueToId; } });
}

function deleteIssueToContact(issueToId, contId){
	var r = jConfirm('Do you want to remove this record?', null, function(r){ if(r==true){ 
		modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'show_project_issue_to_list_by_ajax.php?issueToId='+issueToId+'&contId='+contId, loadingImage, function(){showIssueTo(issueToId);});
	}});
}


// Not use
function validateSubmit(){
	var r = jConfirm('Do you want to upload "Issue To CSV" ?', null, function(r){
		if (r === true){
			document.forms["csvIssueto"].submit();	
		}else{
			return false;
		}
	});
}

function deletechecked(messagedeactive,linkdeactive){
	$.alerts.okButton = '&nbsp;Yes&nbsp;';
	$.alerts.cancelButton = '&nbsp;No&nbsp;';
	jConfirm(messagedeactive,'Delete Confirmation',function(result){
		if(result)
		{
			window.location = linkdeactive;
		}
	});
	$.alerts.okButton = '&nbsp;OK&nbsp;';
	$.alerts.cancelButton = '&nbsp;Cancel&nbsp;';
	return false;
}
/*
function getData(){
	var pro_id = document.getElementById('pro_name').value;
	document.getElementById('create_response').innerHTML='';
	
	// display loading image.
	document.getElementById('load_defects_type').style.display = 'block';
	document.getElementById('load_repairer_name').style.display = 'block';
	
	// get defects type for this project
	$("#defects_type_div").load('defects_type_response.php?pro_id='+pro_id);
	
	// get repairer for this project
	$("#repairer_name_div").load('repairer_name_response.php?pro_id='+pro_id);
	
	setTimeout("hideImg()",1000);
}*/
</script>