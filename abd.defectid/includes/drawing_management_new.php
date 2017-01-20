<script type="text/javascript" src="js/thickbox.js"></script>
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }

include_once("commanfunction.php");
include'data-table.php';
session_start();
$obj = new COMMAN_Class(); 
if(isset($_REQUEST['id'])){
	$id=$_REQUEST['id'];
	$_SESSION['project_id']=$id;
}else
	$id = ''; 
$err_msg='';
//insert for Assign inspector
if(!isset($_SESSION['no_refresh'])){
	$_SESSION['no_refresh'] = "";
}

if(isset($_POST['save'])){
	if($_POST['no_refresh'] == $_SESSION['no_refresh']){}else{
		if(isset($_FILES['drawingImage']['name']) && !empty($_FILES['drawingImage']['name'])){
			$filename = $_FILES['drawingImage']['name']; // Drawing File Name
			$file_ext = end(explode('.', $filename));
			$fil_ext_array = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');
			if(in_array($file_ext, $fil_ext_array)){
				if(trim($_POST['drawingTitle']) != ''){
					$drawImageTitle = trim(addslashes($_POST['drawingTitle']));
					$drawDescription = trim(addslashes($_POST['drawingDescription']));
					$drawTags = trim(addslashes($_POST['drawingTags']));
					$drawTags = trim($drawTags, ";");
					$drawTags = implode(";", array_map('trim', explode(";", $drawTags)));
					if($drawTags != ""){
						$drawTags = trim($drawTags) . ";";
					}

					$inssertQRY = "INSERT INTO draw_mgmt_images SET
									project_id = '".$_SESSION['idp']."',
									draw_mgmt_images_title = '".$drawImageTitle."',
									draw_mgmt_images_description = '".$drawDescription."',
									draw_mgmt_images_tags = '".$drawTags."',
									last_modified_by = '".$_SESSION['ww_builder_id']."',
									created_date = NOW(),
									created_by = '".$_SESSION['ww_builder_id']."'";
		
					mysql_query($inssertQRY);			
					$imageid = mysql_insert_id();
#File Upload Section
					$imageName = $imageid.'.'.$file_ext;
					$imageThumbName = 'thumb_'.$imageid.'.'.$file_ext;
					if(!is_dir('./project_drawings/'.$_SESSION['idp'])){
						@mkdir('./project_drawings/'.$_SESSION['idp'], 0777);
					}
					$obj->resizeImages($_FILES['drawingImage']['tmp_name'], 1600, 1600, './project_drawings/'.$_SESSION['idp'].'/'.$imageName);
#					move_uploaded_file($_FILES['drawingImage']['tmp_name'], './project_drawings/'.$_SESSION['idp'].'/'.$imageName);
					
					if(!is_dir('./project_drawings/'.$_SESSION['idp'].'/thumbnail')){
						@mkdir('./project_drawings/'.$_SESSION['idp'].'/thumbnail', 0777);
					}
					$obj->resizeImages('./project_drawings/'.$_SESSION['idp'].'/'.$imageName, 150, 150, './project_drawings/'.$_SESSION['idp'].'/thumbnail/'.$imageThumbName);
#File Upload Section
					$updateQRY = "UPDATE draw_mgmt_images SET
									draw_mgmt_images_name = '".$imageName."',
									draw_mgmt_images_thumbnail = '".$imageThumbName."'
								WHERE draw_mgmt_images_id = '".$imageid."'";
		
					mysql_query($updateQRY);
				}else{
					$err_msg = 'Please fill drawing title';		
				}
			}else{
				$err_msg = 'Please select either ".jpg" or ".png" file';
			}
		}else{
			if(trim($_POST['drawingTitle']) == ''){
				$err_msg = 'Please select drawing images and fill drawing title';
			}else{
				$err_msg = 'Please select drawing image';
			}
		}
		if($err_msg == ''){
			$_SESSION['add_inspector_success'] = 'Drawing image uploaded successfully !';
		}
		$_SESSION['no_refresh'] = $_POST['no_refresh'];
	}
}
?>
<div id="middle" style="padding-top:10px;">
	<div id="leftNav" style="width:250px;float:left;">
		<?php include 'side_menu.php';?>
	</div>
	<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);  ?>
	<div id="rightCont" style="float:left;width:700px;">
		<div class="content_hd1" style="width:500px;margin-top:12px;"> <font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font><br />
			<a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" style="display: block;float: none;height: 35px;margin-left: 585px;margin-top: -25px;width: 87px;"> <img src="images/back_btn2.png" /> </a> </div>
		<br clear="all" />
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top: -15px;margin-top: 0px\9;">
			<?php if((isset($_SESSION['add_inspector_success'])) && (!empty($_SESSION['add_inspector_success']))) {
		if($_SESSION['add_inspector_success'] != ''){?>
			<div class="success_r" style="height:35px;width:400px;">
				<p>
					<?=$_SESSION['add_inspector_success'];?>
				</p>
			</div>
			<?php   }
		unset($_SESSION['add_inspector_success']); }
		if($err_msg != '') { ?>
			<div class="failure_r" style="height:35px;width:400px;">
				<p><?php echo $err_msg; ?></p>
			</div>
			<?php 	} ?>
		</div>
		<div class="big_container" style="width:722px;float:left;margin-top:-50px;" >
			<div style="border:1px solid #ffffff; margin:45px 20px 10px 10px;text-align:center;">
				<form action="" id="drawingManagement" name="drawingManagement"  method="post" style="margin-top:10px;" enctype="multipart/form-data" onsubmit="return checkDrawingvalues();">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left:15px;" >
						<tr>
							<td style="color:white;padding-bottom:10px;" align="left">Drawing&nbsp;Image <span class="req">*</span></td>
							<td style="color:white;padding-bottom:10px;" align="left">Drawing&nbsp;Title <span class="req">*</span></td>
						</tr>
						<tr>
							<td style="color:white;" align="left"><input type="file" name="drawingImage" id="drawingImage" value="" /></td>
							<td align="left"><input type="text" name="drawingTitle" id="drawingTitle" onblur="checklistId(this, this.value);" class="input_small"  /></td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td style="color:white;padding-bottom:10px;" align="left">Drawing&nbsp;Description</td>
							<td style="color:white;padding-bottom:10px;" align="left">Drawing&nbsp;Tags</td>
						</tr>
						<tr>
							<td align="left"><textarea name="drawingDescription" id="drawingDescription" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"></textarea></td>
							<td align="left"><textarea name="drawingTags" id="drawingTags" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"></textarea>
								<input type="hidden" name="no_refresh" id="no_refresh" value="<?php echo uniqid(rand());?>"  /></td>
						</tr>
						<tr>
							<td colspan="2" align="right" height="50px"><input type="submit" style="cursor:pointer; background: url('images/submit_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-right:35px;width: 87px;color:transparent; font-size:0px;"  name="save" id="save" /></td>
						</tr>
					</table>
				</form>
			</div>
			<div id="searchDraw" style="width:712px;float:left;margin-left:10px;height:50px;" >
				<table width="100%" border="0">
					<?php /*?> <tr>
            <td style="color:#FFFFFF;">Search By Drawing Title&nbsp;:</td>
            <td><input type="text" name="searchStr" id="searchStr" class="input_small" value="" /></td>
            <td><img onclick="searchDrawImage('new');" style="cursor:pointer;" src="images/drw_search.png" alt="search" />&nbsp;&nbsp;<img onclick="searchDrawImage('clean');" style="cursor:pointer;" src="images/drw_back.png" alt="search" /></td>
          </tr><?php */?>
					<tr>
						<td style="float:right;"><img onclick="bulkUploadRegisters();" style="cursor:pointer;" src="images/bulk_upload.png" alt="Bulk Upload" /></td>
					</tr>
				</table>
			</div>
			<!-- <div class="big_container" style="width:712px;float:left;margin-left:10px;height:480px;max-height:480px;overflow:auto;" >-->
			<div class="big_container" style="width:712px;float:left;margin-left:10px;" >
				<?php //include'drawing_management_show_new.php';?>
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example_server_drawing" width="100%">
					<thead>
						<tr>
							<th width="40%">Title</th>
							<th width="25%">Description</th>
							<th width="20%">Tag</th>
							<th  nowrap="nowrap">Action</th>
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
	</div>
</div>
<br />
<style>
.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
.innerDiv{ color:#000000; float:left; border:1px solid red; width:300px; height:120px; }
div#innerModalPopupDiv, div#innerModalPopupDiv1{color:#000000;}
h3#uploaderBulk{font-size:10px;padding:0;margin:0;width: 360px;float:left;}
.bulkfiles {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 97%;cursor: default;height: 75px;}
.approveDrawingReg{margin-left:0px;}
/*div#waterMark{color: #ccc;width: 100%;z-index: 0;text-align: center;vertical-align: middle;position: absolute;top: 25px;}*/
table.collapse { border-collapse: collapse; border: 1pt solid black; }
table.collapse tr, table.collapse td { border: 1pt solid black; padding: 2px; font-family:Arial, Helvetica, sans-serif; font-size:10px;}
div#htmlContainer{overflow:auto;max-height:550px;}
#revisionBox{ float:right; margin-right:5px;}
h3#uploaderBulk img{ margin-top: -15px; padding-top: 9px; display: block; }
h3#uploaderBulk span{ display: block; margin-left: 30px; margin-top: -18px; }
.Admin ul{ background-image:url(images/tab_bg.png); position:absolute; border:1px solid #435D01; border-top-right-radius:0px; border-top-left-radius:0px; border-bottom-right-radius:5px; border-bottom-left-radius:5px; border-width:0 1px 1px; top:-9999px; left:-9999px; overflow:hidden; position:absolute; padding-left:0px; z-index:2; margin-top:-7px; }
.Admin ul li{ list-style:none; float:left; }
.Admin ul li span{ font-size:14px; display:block; padding:10px; color:#000000; height:14px !important; cursor:pointer; text-decoration:underline; }
.Admin:hover ul.admindrop{ left:auto; top:auto; z-index:99999; display:block; overflow:hidden; }
ul.buttonHolder {list-style:none;}
ul.buttonHolder li {float:left;margin-left:10px;}
ul.buttonHolder li #disableButtonDiv{height: 50px;left: -5px;position: relative;top: -5px;width: 111px;z-index: 9999;}
ul#filePanel{list-style:none; margin:0px; padding:0px;}
ul#filePanel li{float:left;}
/*td { color: #000000; }*/
.error-edit-profile-red { background: url("images/bg-error-edit-profile-red.png") no-repeat scroll 0 0 transparent; color: #000; font-size: 11px; margin: 1px 0 2px 3px; padding: 10px 3px 8px 4px; width: 240px; text-shadow: none; }
</style>
<script type="text/javascript" src="js/multiupload_drawing_managment.js"></script> 
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script type="text/javascript">
// Data table section

var align = 'center';
var top = 100;
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
var copyId = ''
$(document).ready(function() {
	$('#example_server_drawing').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "drawing_management_table.php",
		"iDisplayLength": 10,
		"bStateSave": true,
		"aoColumnDefs": [ {  "bSearchable": true, "bSortable": false, "aTargets": [ 1 , 3] }],
	});
});
function RefreshTable(){
	$.getJSON("drawing_management_table.php", null, function( json ){
		table = $('#example_server_drawing').dataTable();
		oSettings = table.fnSettings();
		table.fnClearTable(this);
		
		for (var i=0; i<json.aaData.length; i++){
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw(false);
		//table.fnDraw();
	});
}
function showPhoto(pID, imgID){
		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'display_draw_image.php?pID='+pID+'&imageID='+imgID, loadingImage);
}



function removeDrawing(divId, imgID){
	var r = jConfirm('Do you want to delete drawing image and it\'s data', null, function(r){
		if (r === true){
			var imgSrc = divId;
			showProgress();	
			$.ajax({
				url: "remove_drawing_file.php",
				type: "POST",
				data: "imageData="+imgSrc+"&imageID="+imgID,
				success: function (res) {
					hideProgress();
					RefreshTable();
				}
			});	
		}else{
			return false;
		}
	});
}
function editDrawingReg(pID, imgID){
		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_drawing_image_popup.php?pID='+pID+'&imgID='+imgID+'&name='+Math.random(), loadingImage, UploadSingleImage);
}

function searchDrawImage(opt){
	var searchStr = document.getElementById('searchStr').value;
	if(opt == 'clean'){
		document.getElementById('searchStr').value = '';
		var searchStr = '';
	}
	var searchStr = document.getElementById('searchStr').value;
	var responseArea = document.getElementById('drawingDisplay');
	showProgress();	
	$.ajax({
		url: "search_drawing_images.php",
		type: "POST",
		data: "searchSTR="+searchStr,
		success: function (res) {
			hideProgress();//start image display here
			responseArea.innerHTML = res;
		}
	});	
}


function removeImages(divId, imgID, removeButtonId, AddButtonId){
	var r = jConfirm('Do you want to delete drawing image', null, function(r){
		if (r === true){
			showProgress();	
			var imgDiv = document.getElementById(divId);
			/*var imgSrc = imgDiv.src;	
			imgDiv.src = 'images/noDrawing.jpg';
			$(".drawingImage").css('display','block');
			$("#ancor").html( '<input type="hidden" name="imageDelete" id="imageDelete" value="'+imgSrc+'"  />');
			$('#'+imgID).html('<img src="images/noDrawing.jpg" id="noImage" name="" alt="No Image Found !"  />');
			hideProgress();*/
			$("#"+imgID).hide();
			$("#"+AddButtonId).show();
			hideProgress();
		}else{
			return false;
		}
	});
}
function validateSubmit(){
	if($('#noImage').length){ imgDelete = document.getElementById('noImage').src; }else{ imgDelete = ''; }
	var isFile = document.getElementById('drawingImage').files.length;
	var drawingTitle1 = $('.drawingTitle').val();
	if(drawingTitle1 == ''){$('#drawingTitleError').show();return false;}else{$('#drawingTitleError').hide();}
	if(imgDelete != ''){
		if(isFile == 0){
			var r = jConfirm('Drawing image is not selected if you submit form so image is delete permanent do you want to continue ?', null, function(r){
				if (r === true){
					//document.forms["edit_checklist"].submit();	
					editImageSubmitAjax();
				}else{
					return false;
				}
			});
		}else{
			//document.forms["edit_checklist"].submit();	
			editImageSubmitAjax();
		}
	}else{
		//document.forms["edit_checklist"].submit();	
		editImageSubmitAjax();
	}
}
function editImageSubmitAjax(){
	$.ajax({
		type: 'POST',
		url: "ajax_reply.php?sect=drawing_mgmt_edit",
		data: $('#edit_checklist').serialize(),   // I WANT TO ADD EXTRA DATA + SERIALIZE DATA
		success: function(data){
			var datas =$.parseJSON(data);
			if(datas.status==true){
				closePopup(300);
				RefreshTable();
				$("$showmsg").html(datas.msg);
			}
		}
	});
}

function UploadSingleImage(){
	var btnUpload=$('#image1');
	var status=$('#response_image_1');
	new AjaxUpload(btnUpload, {
		action: 'auto_file_upload.php?projectType=editProject&action=drawing_mgmt&uniqueID='+Math.random(),
		name: 'image1',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg)$/.test(ext))){ 
				// extension is not allowed 
				status.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response){
			console.log(response);
			console.log(file);
			hideProgress();
			status.html(response);
			//$('#removeImg1').show('fast');
		}
	});
}
function bulkUploadRegisters(){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_drawing_management_bulk.php?&name='+Math.random(), loadingImage, bulkRegistration);
}
var mappingDocumentArr = {};//Global Array to store select element in 
var mappedDocArr = {};//Global Array to store select element to show again selected
function bulkRegistration(){
	var config = {
		support : "image/jpg,image/png,image/bmp,image/jpeg,image/gif",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_drawing_management_bulk.php?antiqueID="+Math.random()// Server side upload url
	}
	initBulkUploader(config);
	/*$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';

		TabArrTwo = subTitleArr(currValue);

		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}
		$('.drawingattribute2js').html(outputStr);
	});*/
}


</script> 
