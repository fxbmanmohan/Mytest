<?php function FillSelectBox($field, $table, $where, $group){
		$q="select $field from $table where $where GROUP BY $group";
		$q=mysql_query($q);
		while($q1=mysql_fetch_array($q)){
			echo '<option value="'.$q1[0].'">'.$q1[1].'</option>';
		}
	}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" type="text/css"/>
<title>Report</title>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"DRF",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"DRT",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"FBDF",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"FBDT",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"DRFPM",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"DRTPM",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"FBDFPM",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"FBDTPM",
			dateFormat:"%d-%m-%Y"
		});
	};
</script>
<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
#DRF, #DRT, #FBDF, #FBDT, #DRFPM, #DRTPM, #FBDFPM, #FBDTPM{
	background:#FFF;
	cursor:default;
	height:20px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
}
.error-edit-profile { width: 220px; }
</style>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
<!-- Date Picker files start here -->
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<!-- Date Picker files -->
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
</head>
<body id="dt_example">
<script language="javascript" type="text/javascript">
function startAjax1(val){
	AjaxShow("POST","ajaxFunctions.php?type=locationPM && proID="+val,"ShowLocation1");
	AjaxShow("POST","ajaxFunctions.php?type=issuedToPM && proID="+val,"ShowIssuedTo1");
} 
function subLocate1(obj){
	AjaxShow("POST","ajaxFunctions.php?type=subLocationPM && proID="+obj,"ShowSubLocation1");
}

function startAjax(val){
	AjaxShow("POST","ajaxFunctions.php?type=location && proID="+val,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?type=inspecrBy && proID="+val,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?type=issuedTo && proID="+val,"ShowIssuedTo");
} 
function subLocate(obj){
	AjaxShow("POST","ajaxFunctions.php?type=subLocation && proID="+obj,"ShowSubLocation");
}
function changeScreen(screenID){
	if(screenID == 'buttoon_progressMonitoring'){
		document.getElementById(screenID).setAttribute("class", "buttoon_progressMonitoringActive");
		document.getElementById('button_qualityControl').setAttribute("class", "button_qualityControl");
document.getElementById('show_defect').innerHTML = '';		
		document.getElementById('button_qualityControlScreen').style.display = 'none';
		document.getElementById('buttoon_progressMonitoringScreen').style.display = 'block';
	}
	if(screenID == 'button_qualityControl'){
		document.getElementById(screenID).setAttribute("class", "button_qualityControlActive");
		document.getElementById('buttoon_progressMonitoring').setAttribute("class", "buttoon_progressMonitoring");
document.getElementById('show_defect').innerHTML = '';	
		document.getElementById('buttoon_progressMonitoringScreen').style.display = 'none';
		document.getElementById('button_qualityControlScreen').style.display = 'block';
	}
}
function resetIds(){
	document.getElementById('projectError').style.visibility = 'hidden';
	document.getElementById('reportError').style.visibility = 'hidden';
	document.getElementById('reportErrorPM').style.visibility = 'hidden';
	document.getElementById('projectPMError').style.visibility = 'hidden';
}
</script>
<div id="container" style="width:99%;margin-top:25px;min-height:510px;" >
<?php
if (isset($_SESSION["ww_is_builder"]) and $_SESSION["ww_is_builder"]== 1){
	$owner_id = $_SESSION['ww_builder_id'];
}else{
	$owner_id = $_SESSION['ww_is_company'];
}
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
$pdfForm='';
// get all inspections logged by this inspector
#$qi="SELECT *,r.resp_full_name FROM ".DEFECTS." d 	LEFT JOIN ".PROJECTS." p ON p.id = d.project_id 	LEFT JOIN ".RESPONSIBLES." r ON r.project_id = d.project_id 	WHERE d.owner_id = '$owner_id'";
?>
<script type="text/javascript" language="javascript" src="datatable/media/js/jquery.js"></script>
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
<style>
div#spinner{
    display: none;
    width:100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC;
    text-align:center;
    padding:10px;
    font:normal 16px Tahoma, Geneva, sans-serif;
    border:1px solid #666;
    z-index:2;
    overflow: auto;
	opacity : 0.8;
}
</style>
<script language="javascript" type="text/javascript">
function checkDates(date1, date2, element){
	var obj = date1.value;
	var obj1 =  date2.value;
	if(obj!='' || obj1!=''){
		if(obj=='' && obj1!=''){
			jAlert('Please Select Form Date First !');
			return false;		
		}else{
			var fromDate = new Date(obj.substr(6,4), obj.substr(3,2), obj.substr(0,2));
			var toDate = new Date(obj1.substr(6,4), obj1.substr(3,2), obj1.substr(0,2));
			if((toDate.getTime() - fromDate.getTime()) < 0){jAlert(element+' To Date in Not Less Than Form Date !');return false;}
		}
	}
}
function validateAndSubmit(){
	try{
		var params = "";var url = '';
		var projNamePM = document.getElementById("projNamePM").value;
		var locationPM = document.getElementById("locationPM").value;
		var subLocationPM = document.getElementById("subLocationPM").value;
		var statusPM = document.getElementById("statusPM").value;
		var DRFPM = document.getElementById("DRFPM").value;
		var DRTPM = document.getElementById("DRTPM").value;
		var FBDFPM = document.getElementById("FBDFPM").value;
		var FBDFPM = document.getElementById("FBDTPM").value;
		var reportTypePM = document.getElementById("reportTypePM").value;
		
		if(projNamePM == ''){ document.getElementById('projectPMError').style.visibility = 'visible'; return false;}
		if(reportTypePM == ''){ document.getElementById('reportErrorPM').style.visibility = 'visible'; return false;}

var dateChackRaised = checkDates(document.getElementById('DRFPM'), document.getElementById('DRTPM'), 'In Start Date Field');
var dateChackFixed = checkDates(document.getElementById('FBDFPM'), document.getElementById('FBDTPM'), 'In End Date Field');
if(dateChackRaised === false){	return false;	}
if(dateChackFixed === false){	return false;	}
		
		params = "SearchInsp="+1+"&projName=" + projNamePM + "&location=" + locationPM + "&subLocation=" + subLocationPM + "&status=" + statusPM + "&DRF=" + DRFPM + "&DRT=" + DRTPM + "&FBDF=" + FBDFPM + "&FBDT=" + FBDFPM;

		if(reportTypePM == 'inCompleteWork'){
			url = 'pdf/i_pdf_in_complete_works.php';	
		}else if(reportTypePM == 'doorSheet'){
			url = 'pdf/i_pdf_door_sheet.php';
		}

		if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest(); }else{ xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
		showProgress();

		url = url+'?'+params+'&name='+Math.random();

		xmlhttp.open("GET", url, true);
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById("show_defect").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message);
	}
}

function submitForm(){
try{	var params = '';
	var projName = document.getElementById('projName').value;
	var reportType = document.getElementById('reportType').value;
	var location = document.getElementById('location').value;
	var subLocation = document.getElementById('subLocation').value;
	var status = document.getElementById('status').value;
	var inspectedBy = document.getElementById('inspectedBy').value;
	var issuedTo = document.getElementById('issuedTo').value;
	var priority = document.getElementById('priority').value;
	var inspecrType = document.getElementById('inspecrType').value;
	var costAttribute = document.getElementById('costAttribute').value;
	var DRF = document.getElementById('DRF').value;
	var DRT = document.getElementById('DRT').value;
	var FBDF = document.getElementById('FBDF').value;
	var FBDT = document.getElementById('FBDT').value;
	
if(projName == ''){ document.getElementById('projectError').style.visibility = 'visible'; return false; }

if(reportType == ''){ document.getElementById('reportError').style.visibility = 'visible'; return false; }

var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');
if(dateChackRaised === false){	return false;	}
if(dateChackFixed === false){	return false;	}

	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	showProgress();
	params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+priority+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&name="+Math.random()+"&report_type=" + reportType;
	if(reportType == 'pdfDetail' || reportType == 'pdfDetailHD'){
		var url = 'pdf/i_report_pdf_detail.php';	
	}else if(reportType == 'pdfSummay' || reportType == 'pdfSummayHD'){
		var url = 'pdf/i_report_pdf_summary.php';
	}else if(reportType == 'csvReport'){
		var url = 'pdf/i_report_csv.php';
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			hideProgress();
			document.getElementById("show_defect").innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.send(params);
}catch(e){
	//alert(e.message); 
}
}
</script>

	<div id="containerTop" style="width:100%; margin:auto;">
		<div class="content_hd1" style="background-image:url(images/report_header.png); width:350px;float:left;"></div>
		<div id="button_qualityControl" class="button_qualityControlActive" style="float:left;" onclick="changeScreen(this.id)"></div>	
		<div id="buttoon_progressMonitoring" class="buttoon_progressMonitoring" style="float:left;" onclick="changeScreen(this.id)"></div>
	</div><br clear="all" />

	
<div class="search_multiple" style="border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;">
		<table width="900" cellpadding="0" cellspacing="5" border="0" id="button_qualityControlScreen" style="display:block;">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Project Name <span class="reqire">*</span></td>
				<td colspan="2">
					<select name="projName" id="projName"  class="select_box" onchange="resetIds();startAjax(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0","project_name");
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name");
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;visibility:hidden;"  id="projectError">The project field is required</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Report Type <span class="reqire">*</span></td>
				<td colspan="2">
					<select name="reportType" id="reportType"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onchange="resetIds();">
						<option value="">Select</option>
						<option value="pdfDetail">PDF Detail Report</option>
						<option value="pdfDetailHD">PDF Detail Report - HD</option>
						<option value="pdfSummay">PDF Summary Report</option>
						<option value="pdfSummayHD">PDF Summary Report - HD</option>
						<option value="csvReport">CSV report</option>
					</select>
					<div class="error-edit-profile" style="width:220px;visibility:hidden;" id="reportError">The report field is required</div>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Location </td>
				<td colspan="2" id="ShowLocation">
					<select name="location" id="location"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Sub Location</td>
				<td colspan="2" id="ShowSubLocation">
					<select name="subLocation" id="subLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Status</td>
				<td colspan="2" id="">
					<select name="status" id="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Open">Open</option>
						<option value="Pending">Pending</option>
						<option value="In Progress">In Progress</option>
						<option value="Closed">Closed</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Inspected By</td>
				<td colspan="2" id="ShowInspecrBy">
					<select name="inspectedBy" id="inspectedBy" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Issued To</td>
				<td colspan="2" id="ShowIssuedTo">
					<select name="issuedTo" id="issuedTo" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Priority</td>
				<td colspan="2" id="">
					<select name="priority" id="priority" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Low">Low</option>
						<option value="Medium">Medium</option>
						<option value="High">High</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Inspection Type</td>
				<td colspan="2">
					<select name="inspecrType" id="inspecrType" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Issue">Issue</option>
						<option value="Defect">Defect</option>
						<option value="Warranty">Warranty</option>
						<option value="Incomplete Works">Incomplete Works</option>
						<option value="Progress Monitoring">Progress Monitoring</option>
						<option value="Other">Other</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Cost Attribute</td>
				<td colspan="2">
					<select name="costAttribute" id="costAttribute" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="None">None</option>
						<option value="Back Charge">Back Change</option>
						<option value="Variation">Variation</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Date Raised</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">
				From 
					<input name="DRF" type="text" id="DRF" size="7" readonly="readonly" />
				To 
					<input name="DRT" type="text" id="DRT" size="7" readonly="readonly" />
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Fix By Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">
				From 
					<input name="FBDF" type="text" id="FBDF" size="7" readonly="readonly" />
				To 
					<input name="FBDT" type="text" id="FBDT" size="7" readonly="readonly" />
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td><!--<input type="hidden" value="report" name="sect" id="sect" />-->
					<input name="SearchInsp" type="button" onclick="submitForm();" class="submit_btn" id="button" value="" style="background-image:url(images/btn_run_report.png); width:148px; height:40px;"  />
				</td>
			</tr>
		</table>
		<table width="900" cellpadding="0" cellspacing="5" border="0" id="buttoon_progressMonitoringScreen" style="display:none;">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Project Name <span class="reqire">*</span></td>
				<td colspan="2">
					<select name="projName" id="projNamePM" class="select_box" onchange="resetIds();startAjax1(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0","project_name");
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name");
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;visibility:hidden;" id="projectPMError">The project field is required</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Report Type <span class="reqire">*</span></td>
				<td colspan="2" valign="top" >
					<select name="reportType" id="reportTypePM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onchange="resetIds();">
						<option value="">Select</option>
						<option value="inCompleteWork">Incomplete Works</option>
						<option value="doorSheet">Door Sheet</option>
					</select>
					<div class="error-edit-profile" style="width:220px;visibility:hidden;" id="reportErrorPM">The report field is required</div>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Location</td>
				<td colspan="2" id="ShowLocation1">
					<select name="location" id="locationPM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sub Location </td>
				<td colspan="2" id="ShowSubLocation1">
					<select name="subLocation" id="subLocationPM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Issue To</td>
				<td colspan="2" id="ShowIssuedTo1">
					<select name="issuedTo" id="issuedToPM" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Current Status</td>
				<td colspan="2">
					<select name="status" id="statusPM" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Ahead">Ahead</option>
						<option value="On Time">On Time</option>
						<option value="Behind">Behind</option>
						<option value="Complete">Complete</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Start Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">
				From 
					<input name="DRF" type="text" id="DRFPM" size="7" readonly="readonly" />
				To 
					<input name="DRT" type="text" id="DRTPM" size="7" readonly="readonly" />
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">End Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">
				From 
					<input name="FBDF" type="text" id="FBDFPM" size="7" readonly="readonly" />
				To 
					<input name="FBDT" type="text" id="FBDTPM" size="7" readonly="readonly" />
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td>
					<!--input type="hidden" value="create" name="sect" id="sect" /-->
					<input name="SearchInsp" type="button" onclick="validateAndSubmit();" class="submit_btn" id="button" value="" style="background-image:url(images/btn_run_report.png); width:148px; height:40px;"  />
				</td>
			</tr>
		</table>
  </div>
<div>
		<div class="demo_jui" id="show_defect" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>></div>
		<div class="spacer"></div>
  </div>
</div>
<div id="spinner"></div>  
</body>
</html>