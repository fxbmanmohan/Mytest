<?php $builder_id=$_SESSION['ww_builder_id'];
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=HOME_SCREEN?>";
	</script>
<?php }
function FillSelectBox($field, $table, $where, $group){
	$q = "SELECT $field FROM $table WHERE $where GROUP BY $group";
	$q = mysql_query($q);
	while($q1 = mysql_fetch_array($q)){
		echo '<option value="'.$q1[0].'">'.$q1[1].'</option>';
	}
}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" type="text/css"/>
<title>Report</title>
<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
</style>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
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
</script>
</head>
<body id="dt_example">
<script language="javascript" type="text/javascript">
function startAjaxQA(val){
	AjaxShow("POST","ajaxFunctions.php?type=locationQA && proID="+val,"ShowLocationQA");
}
function subLocate1QA(val){
	AjaxShow("POST","ajaxFunctions.php?type=subLocationQA1 && proID="+val,"ShowSubLocation1QA");
}
function subLocate2QA(val){
	AjaxShow("POST","ajaxFunctions.php?type=subLocationQA2 && proID="+val,"ShowSubLocation2QA");
}
function subLocate3QA(val){
	AjaxShow("POST","ajaxFunctions.php?type=subLocationQA3 && proID="+val,"ShowSubLocation3QA");
}
function resetIds(){
	document.getElementById('projectQAError').style.display = 'none';
	document.getElementById('locationQAError').style.display = 'none';
	document.getElementById('subLocationQA1Error').style.display = 'none';
	document.getElementById('subLocationQA2Error').style.display = 'none';
}
function toggleFolder(folderid){
	$("#"+folderid).toggle();
}
function validateAndSubmit(){
	try{
		document.getElementById("container_progress").innerHTML = '';
		var params = '';
		var startWith = 0;
		var projNameQA = document.getElementById('projNameQA').value;
		var locationQA = document.getElementById('locationQA').value;
		var subLocationQA1 = document.getElementById('subLocationQA1').value;
		var subLocationQA2 = document.getElementById('subLocationQA2').value;
		var subLocationQA3 = document.getElementById('subLocationQA3').value;
		var sortBy = document.getElementById('sortBy').value;
		
		if(projNameQA == ''){ document.getElementById('projectQAError').style.display = 'block'; return false; }
		if(locationQA == ''){ document.getElementById('locationQAError').style.display = 'block'; return false; }

		params = "SearchInsp=1&projNameQA="+projNameQA+"&locationQA="+locationQA+"&subLocationQA1="+subLocationQA1+"&subLocationQA2="+subLocationQA2+"&subLocationQA3="+subLocationQA3+"&sortBy="+sortBy+"&name="+Math.random();

		if (window.XMLHttpRequest){	xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		showProgress();
		var url = 'QA_ajax_result.php?' + params;
		xmlhttp.open("GET", url, true);
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById("container_progress").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message); 
	}
}
</script>
<br/>
<div class="content_hd1" style="background-image:url(images/quality_assurance.png);">&nbsp;</div>
<br clear="all" />
<div class="search_multiple" style="border:1px solid; text-align:center;width:960px;margin-left: 20px;">
	<table width="900" cellpadding="0" cellspacing="5" border="0">
		<tr>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Project Name <span class="reqire">*</span></td>
			<td colspan="2">
				<select name="projNameQA" id="projNameQA" class="select_box" onChange="resetIds();startAjaxQA(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
					<?php FillSelectBox("project_id, project_name", "user_projects", "project_id >='1' and user_id =".$builder_id." and is_deleted = 0", "project_name"); ?>
				</select>
				<div class="error-edit-profile" style="width:220px;display:none;" id="projectQAError">The project field is required</div>
			</td>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Location <span class="reqire">*</span></td>
			<td colspan="2">
				<div id="ShowLocationQA">
				<select name="locationQA" id="locationQA"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select>
				</div>
				<div class="error-edit-profile" style="width:220px;display:none;" id="locationQAError">The Location field is required</div>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sub Location 1</td>
			<td colspan="2">
				<div id="ShowSubLocation1QA">
				<select name="subLocationQA1" id="subLocationQA1"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select>
				</div>
				<div class="error-edit-profile" style="width:220px;display:none;" id="subLocationQA1Error">The Sub Location 1 field is required</div>
			</td>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sub Location 2</td>
			<td colspan="2">
				<div id="ShowSubLocation2QA">
				<select name="subLocationQA2" id="subLocationQA2"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select>
				</div>
				<div class="error-edit-profile" style="width:220px;display:none;" id="subLocationQA2Error">The Sub Location 2 field is required</div>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sub Location 3</td>
			<td colspan="2">
				<div id="ShowSubLocation3QA">
				<select name="subLocationQA3" id="subLocationQA3" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select>
				</div>
			</td>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sort By</td>
			<td colspan="2"id="ShowIssuedTo" >
				<select name="sortBy" id="sortBy" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
					<option value="location_title">Location</option>
					<option value="status">Status</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="left">&nbsp;</td>
			<td align="left"><div id="report_timer" style="color:#FFFFFF;"></div></td>
			<td><!--input type="hidden" value="create" name="sect" id="sect" /-->
				<input name="SearchInsp" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/search_btn_web.png); width:113px; height:46px;" onClick="validateAndSubmit();" />
			</td>
		</tr>
	</table>
</div>
<br/>
<br/>
<div id="container_progress" style="width:980px;margin-top:-20px;">&nbsp;</div>
<script type="text/javascript">
function closetaskids(){
	var taskArray = new Array();
	var projectID = document.getElementById('projNameQA').value;
	var taskCount = document.allTaskTable.elements["taskID[]"].length;
	if(taskCount === undefined){
		var taskId = document.getElementById('taskID');
		if(taskId.checked){
			taskArray = taskId.value;
		}
	}else{
		for(var i=0; i<taskCount; i++){
			var taskId = document.allTaskTable.elements["taskID[]"][i];
			if(taskId.checked){
				taskArray[i] = taskId.value;
			}else{
				taskArray[i] = 0;
			}
		}
	}
//Filter Array
	var newArr = []; 
	for (var index in taskArray) {  if( taskArray[index] ) {  newArr.push( taskArray[index] ); }  }  
	taskArray = newArr;
//Filter Array
	if(taskArray != ''){
		if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		showProgress();
		params = "taskIDs="+taskArray+"&projectID="+projectID+"&strangeID="+Math.random();
		xmlhttp.open("POST", "inspection_close_bulk.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				if(xmlhttp.responseText == 'Done'){
					jAlert('Selected task closed successfully !');
					validateAndSubmit();
				}else{
					jAlert('Error in updating record try after some time !');
					validateAndSubmit();
				}
			}
		}
		xmlhttp.send(params);
	}else{
		jAlert('You must select at least one task to perform this action !');
		document.getElementById('checkall').checked = false;
		toggleCheck(document.getElementById('checkall'));
	}
}
function toggleCheck(obj, tableID){
	var checkedStatus = obj.checked;
	$('#'+tableID+' tbody tr').find('td:first :checkbox').each(function () {
		if(!$(this).is(':disabled')){
			$(this).prop('checked', checkedStatus);
		}
	});
}
function holeProjectChecked(obj, tableID){
	var tableIdArray = tableID.split(", ");
	var checkedStatus = obj.checked;
	for(i = 0; i < tableIdArray.length; i++){
		$('#f'+tableIdArray[i]+' tbody tr').find('td:first :checkbox').each(function () {
			if(!$(this).is(':disabled')){
				$(this).prop('checked', checkedStatus);
			}
		});
		$('#checkall_'+tableIdArray[i]).prop('checked', checkedStatus);
	}
}
</script>