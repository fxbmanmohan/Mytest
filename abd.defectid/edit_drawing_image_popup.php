<?php 
session_start();
include("./includes/functions.php");
include_once("includes/commanfunction.php");
$obj = new DB_Class();
$object = new COMMAN_Class();


if(isset($_REQUEST['name'])){
	$drawingID = $_REQUEST['imgID'];
	$drawData = $object->selQRYMultiple('draw_mgmt_images_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags', 'draw_mgmt_images', 'is_deleted = 0 AND draw_mgmt_images_id = "'.$drawingID.'"'); ?>
	<div id="apply_now">
		<form action="?sect=edit_drawing_management&imgID=<?=$_GET['imgID']?>"  method="post"  enctype="multipart/form-data" name="edit_checklist" id="edit_checklist" onSubmit="return validateSubmit()" >
			<div class="content_container">
				<div class="content_left">
					<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top: -15px;margin-top: 0px\9;">
						<?php if((isset($_SESSION['add_inspector_success'])) && (!empty($_SESSION['add_inspector_success']))) {
							if($_SESSION['add_inspector_success'] != ''){?>
						<?php /*?>	<div class="success_r" style="height:35px;width:400px;"><p><?php //$_SESSION['add_inspector_success'];?></p></div>		<?php */?>
						<?php   }
						unset($_SESSION['add_inspector_success']); 
						}
						if($err_msg != '') { ?>
						<div class="failure_r" style="height:35px;width:400px;">
							<p><?php echo $err_msg; ?></p>
						</div>
						<?php 	} ?>
					</div>
					<!--<div class="content_hd1" style="background-image:url(images/edit_drawing.png);margin-top:-50px\9;"></div>-->
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form" style="color:#000000;">
						<table width="700" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-40px;">
							<tr>
								<td valign="top">Drawing&nbsp;Image <span class="req">*</span></td>
								<?php #print_r($drawData[0]);die;?>
								<td><span id="deleteIMG_<?=$drawData[0]['draw_mgmt_images_id']?>">
									<?php	$filePath = 'project_drawings/'.$_SESSION['idp'].'/thumbnail/'.$drawData[0]['draw_mgmt_images_thumbnail'];
	if(file_exists($filePath)){?>
									<a href="<?='project_drawings/'.$_SESSION['idp'].'/'.$drawData[0]['draw_mgmt_images_name']?>" class="thickbox"><img src="<?=$filePath?>" alt="drawingImage" id="drawImage" /></a><br />
									<img id="removeImg" src="images/replace_image.png" style="margin-top:10px;cursor:pointer;" onclick="removeImages('drawImage', 'deleteIMG_<?=$drawData[0]['draw_mgmt_images_id']?>', this.id, 'deleteIMG2_<?=$drawData[0]['draw_mgmt_images_id']?>');" />
									<?php }else{?>
									<div class="innerDiv"  align="center" style="display:block;" >
										<div style="height:120px;overflow:hidden;">
											<input type="file" id="image1" name="image1" style="width:120px;height:120px;cursor: pointer;opacity: 0;" />
											<div style="width:120px;margin-top: -125px;" id="response_image_1">
												<?php if(isset($photoLogo) && !empty($photoLogo)){?>
												<img width="100" height="90" id="photoImage1" style="margin-left:2px;margin-top:8px;" src="user_signoff/<?php echo $photoLogo; ?>">
												<input type="hidden" value="<?php echo $photoLogo; ?>" name="singoff_img">
												<?php } ?>
											</div>
										</div>
									</div>
									<?php }?>
									</span> <span id="deleteIMG2_<?=$drawData[0]['draw_mgmt_images_id']?>" style="display:none;">
									<div class="innerDiv"  align="center" style="display:block;" >
										<div style="height:120px;overflow:hidden;">
											<input type="file" id="image1" name="image1" style="width:120px;height:120px;cursor: pointer;opacity: 0;" />
											<div style="width:120px;margin-top: -125px;" id="response_image_1">
												<?php if(isset($photoLogo) && !empty($photoLogo)){?>
												<img width="100" height="90" id="photoImage1" style="margin-left:2px;margin-top:8px;" src="user_signoff/<?php echo $photoLogo; ?>">
												<input type="hidden" value="<?php echo $photoLogo; ?>" name="singoff_img">
												<?php } ?>
											</div>
										</div>
									</div>
									</span></td>
							</tr>
							<tr>
								<td valign="top">&nbsp;</td>
								<td><?php if(file_exists($filePath)){?>
									<input type="file" class="drawingImage" name="drawingImage" id="drawingImage" style="display:none;" />
									<?php }else{?>
									<input type="file" class="drawingImage" name="drawingImage" id="drawingImage" style="display:block;" />
									<?php } ?></td>
							</tr>
							<tr>
								<td valign="top">Drawing&nbsp;Title <span class="req">*</span></td>
								<td><input type="text" name="drawingTitle" id="drawingTitle" class="input_small drawingTitle" value="<?=$drawData[0]['draw_mgmt_images_title']?>" /></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><div id="drawingTitleError" style="width: 100px; display:none;" class="error-edit-profile-red">Drawing Title Required</div></td>
							</tr>
							<tr>
								<td valign="top">Drawing&nbsp;Description</td>
								<td><textarea name="drawingDescription" id="drawingDescription" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"><?=$drawData[0]['draw_mgmt_images_description']?></textarea></td>
							</tr>
							<tr>
								<td valign="top">Drawing&nbsp;Tags</td>
								<td><textarea name="drawingTags" id="drawingTags" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width:252px;height:45px;"><?=$drawData[0]['draw_mgmt_images_tags'];?></textarea>
									<br />
									Please seperate location by semicolon(;) </td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input type="hidden" name="drawingID" id="drawingID" value="<?=$drawingID?>" />
									
									<!--							<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/update.png" style="border:none; width:111px;" onclick="validateSubmit();"  />-->
									
									<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/update.png);height:86px;font-size:0px; border:none; width:111px;float:left;" onclick="validateSubmit();"  />
									&nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:history.back();" onclick="yes"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div style="clear:both;"></div>
<?php }?>