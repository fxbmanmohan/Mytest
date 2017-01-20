<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 

define('ATTCHMENTPATH', '/var/www/constructionid.com/vccc/project_drawing_register/'.$_SESSION['idp']);
define('ATTCHMENTCOPYPATH', '/var/www/constructionid.com/vccc/project_drawing_register/'.$_SESSION['idp']);

if(!isset($_SESSION['drRequsetCount']))	$_SESSION['drRequsetCount'] = 0;

if(isset($_REQUEST["antiqueID"])){
//Get Exsiting records Start Here
/*	$existDrawingData = array();
	$existDrawingData = $obj->selQRYMultiple('id, title, number, revision', 'drawing_register', 'is_document_transmittal = 0 AND is_deleted = 0 ORDER BY id');
	$existDrawingArr = array();
	if(!empty($existDrawingData)){
		foreach($existDrawingData as $exDrawData){
			$existDrawingArr[$exDrawData['number']] = array($exDrawData['id'], $exDrawData['revision']);
		}
	}*/
//Get Exsiting records End Here

	$filename = $_FILES['file']['name']; // Drawing File Name
	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	$fNameArr = explode('.', $filename);
	$file_ext = array_pop($fNameArr);
	$processFileName = $tempFileName = implode('.', $fNameArr);
	$revisionName = "";
	if(strpos($processFileName, "[") !== false){
		if(strpos($processFileName, "]") !== false){
			$tempArr = explode('[', $processFileName);
			$lastEle = array_pop($tempArr);
			$fileNameTitle = trim(implode('[', $tempArr));
			$revisionNameArr = explode(']', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if(strpos($processFileName, "(") !== false){
		if(strpos($processFileName, ")") !== false){
			$tempArr = explode('(', $processFileName);
			$lastEle = array_pop($tempArr);
			$fileNameTitle = trim(implode('(', $tempArr));
			$revisionNameArr = explode(')', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if(strpos($processFileName, "{") !== false){
		if(strpos($processFileName, "}") !== false){
			$tempArr = explode('{', $processFileName);
			$lastEle = array_pop($tempArr);
			$fileNameTitle = trim(implode('{', $tempArr));
			$revisionNameArr = explode('}', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if($revisionName == ""){
		$tempArr = explode('-', $processFileName);
		$lastEle = array_pop($tempArr);
		$fileNameTitle = trim(implode('-', $tempArr));
		$revisionName = $lastEle ;
	}
	
	$drawingTitle = $drawingNumber = $fileNameTitle;
	
	$fetchKey = $fileNameTitle.$revisionName;
//
	$existDrawingArr = json_decode($_POST['mappingDocumentArr'], true);
	
	

	
	if(isset($_POST['nameTitle'][$fetchKey]) && !empty($_POST['nameTitle'][$fetchKey]))
		$drawingTitle = trim($_POST['nameTitle'][$fetchKey]);		
	
	if(isset($_POST['description'][$fetchKey]) && !empty($_POST['description'][$fetchKey]))
		$description = trim($_POST['description'][$fetchKey]);		
	
	
	if(isset($_POST['documentTags'][$fetchKey]) && !empty($_POST['documentTags'][$fetchKey]))
	 	$documentTags = trim($_POST['documentTags'][$fetchKey]);		
	
	
	if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
			$filename = $_FILES['file']['name']; // Drawing File Name
			$file_ext = end(explode('.', $filename));
			$fil_ext_array = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');
			if(in_array($file_ext, $fil_ext_array)){
				if(trim($drawingTitle) != ''){
					$drawImageTitle = trim(addslashes($drawingTitle));
					$drawDescription = trim(addslashes($description));
					$drawTags = trim(addslashes($documentTags));
					$drawTags = trim($drawTags, ";");
					$drawTags = implode(";", array_map('trim', explode(";", $drawTags)));
					if($drawTags != ""){
						$drawTags = trim($drawTags) . ";";
					}

					$inssertQRY = "INSERT INTO draw_mgmt_images SET
									project_id = '".$_SESSION['idp']."',
									draw_mgmt_images_title = '".$drawingTitle."',
									draw_mgmt_images_description = '".$description."',
									draw_mgmt_images_tags = '".$documentTags."',
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
					$obj->resizeImages($_FILES['file']['tmp_name'], 1600, 1600, './project_drawings/'.$_SESSION['idp'].'/'.$imageName);
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
					$err_msg = 'Drawing Registration Added Successfully !';
				}else{
					$err_msg = 'Please fill drawing title';		
				}
			}
		}
	
	
	

	$outputArr = array('status'=> true, 'msg'=> $err_msg, 'msg'=> $err_msg);	
	
	
#echo $_SESSION['drRequsetCount'].'+++++++'.$_REQUEST['totalRequestCount'];
//Send message here
	if($_SESSION['drRequsetCount'] == $_REQUEST['totalRequestCount']){
  
	//Email and Message Board Entry End Here
		unset($_SESSION['drRequsetCount']);
		unset($_SESSION['finalDrDataArr']);
		echo json_encode($outputArr);
	}
//Send message here
}
if(isset($_REQUEST["name"])){?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Bulk Upload Drawing Images</legend>
		<form name="addDrawingForm" id="addDrawingForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top"  colspan="2" align="left">Drawing&nbsp;Images&nbsp;<span class="req">*</span></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="innerDiv" id="innerDiv" style="height:300px;width:830px;overflow:auto;">
						<div align="center" style="font-size:12px;">Drop Files Here</div>
					</div>
					<input type="file" name="multiUpload" id="multiUpload" />
					<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
				</td>
			</tr>
			
			<tr>
				<td colspan="2" align="center">
					<ul class="buttonHolder">
						<li>
							<input type="submit" name="button" class="submit_btn" id="buttonFirstSubmit" style="background-image:url(images/upload_btn1.png);font-size:0px; border:none; width:111px;float:left;"  />
							<!--<div id="disableButtonDiv"></div>-->
							<input type="hidden" name="validationFlag" id="validationFlag" value="2" />
						</li>
						<?php /*?><li>
							<img src="images/doccument_transmittal.png" style="border:none; width:111px;height:43px;" onclick="addNewRegisterDocumentTransmital();" />
						</li><?php */?>
						<li>
							<a id="ancor" href="javascript:closePopup(300);">
								<img src="images/back_btn.png" style="border:none; width:111px;" />
							</a>
						</li>
					</ul>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>