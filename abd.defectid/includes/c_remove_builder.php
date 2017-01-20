<?php ob_start();
require_once'includes/functions.php';
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }?>
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/c_manager_edit.js"></script>
<script language="javascript" type="text/javascript">
function startAjax(type,val,result){//alert(val);
	AjaxShow("POST","ajaxFunctions.php?type=1 && "+type+"="+val,result);
}
$(document).ready(function() {
	var validator = $("#managerEdit").validate({
		rules:{ 
		   fullname:{
				required: true
		   },
		   cname:{
				required: true
		   },
		   username:{
				required: true,
				minlength:4,
				maxlength:12
		   },
		   memail:{
				required: true,
				email:true
		   },
		   mobile:{
				required: true,
				digits: true,
				minlength:10
		   },
		   pwd:{
				required: true,
				minlength:6,
				maxlength:12			
		   },
		   rePwd:{
		   		required: true,
				equalTo: "#pwd"			
		   },
		},
		messages:{
			fullname:{
				required: '<div class="error-edit-profile">The full name field is required</div>',
				email: '<div class="error-edit-profile">The email is not valid format</div>'
			},
			cname:{
				required: '<div class="error-edit-profile">The company name field is required</div>'
			},
			username:{
				required: '<div class="error-edit-profile">The username field is required</div>',
				minlength: '<div class="error-edit-profile">Please enter at least 4 characters</div>',
				maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
			},
			memail:{
				required: '<div class="error-edit-profile">The email field is required</div>',
				email: '<div class="error-edit-profile">Invalide email format</div>'
			},	
			mobile:{
				required: '<div class="error-edit-profile">The mobile field is required</div>',
				digits: '<div class="error-edit-profile">Please enter only digits.</div>',
				minlength: '<div class="error-edit-profile">Please enter at least 10 digits</div>'
			},			
			pwd:{
				required: '<div class="error-edit-profile">The password field is required</div>',
				minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
				maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
			},
			rePwd:{
				required: '<div class="error-edit-profile">The re password field is required</div>',
				equalTo: '<div class="error-edit-profile">The passwords you entered do not match. Please try again.</div>',
			},
			debug:true
		}
	});
	jQuery.validator.addMethod("alpha", function( value, element ) {
		return this.optional(element) || /^[a-zA-Z ]+$/.test(value);
	}, "Please use only alphabets (a-z or A-Z).");
	jQuery.validator.addMethod("numeric", function( value, element ) {
		return this.optional(element) || /^[0-9]+$/.test(value);
	}, "Please use only numeric values (0-9).");
	jQuery.validator.addMethod("alphanumeric", function( value, element ) {
		return this.optional(element) || /^[a-z A-Z0-9]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 characters.");
	jQuery.validator.addMethod("mobile", function( value, element ) {
		return this.optional(element) || /^[ 0-9+-]+$/.test(value);
	}, "You can use only 0-9 - + characters.");
	jQuery.validator.addMethod("login", function( value, element ) {
		return this.optional(element) || /^[A-Za-z0-9_.]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 _ and . characters.");
});
</script>
<?php 
if(isset($_POST['update_x'])){
	if($_POST['userType'] != 'inspector'){
		$addProj = isset($_POST['addProject']) ? 1 : 0; 
	}
	$update = "UPDATE user SET 
						user_name='".addslashes(trim($_POST['username']))."',
						user_fullname='".addslashes(trim($_POST['fullname']))."',
						company_name='".addslashes(trim($_POST['cname']))."',
						user_email='".addslashes(trim($_POST['memail']))."',
						user_phone_no='".addslashes(trim($_POST['mobile']))."',
						user_password='".md5(trim($_POST['pwd']))."',
						user_plainpassword='".addslashes(trim($_POST['pwd']))."',
						user_type='".addslashes(trim($_POST['userType']))."',
						last_modified_date = NOW(),
						last_modified_by = ".base64_decode($_POST['b_id'])."
					WHERE user_id='".base64_decode($_POST['b_id'])."' and is_deleted = 0";
	mysql_query($update);
	if($_POST['userType'] == 'manager'){
		$permissionQry_porj = "UPDATE user_permission SET is_allow = '".$addProj."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = 'web_add_project'";
		mysql_query($permissionQry_porj);
	}

	if ($_POST["oldUserType"] != $_POST['userType']){
		$userType = $_POST['userType'];
		//update permissions according to new user type
		if($userType == 'manager'){
			////// Update user level and project level permissions
			$keyManagerPermissionArray = array_keys($managerPermissionArray);
			$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection','iPhone_close_inspection', 'web_add_project');
		
			for($i=0;$i<sizeof($managerPermissionArray);$i++){
				if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
				}else{
					$permissionQry = "UPDATE user_permission SET is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = '".$keyManagerPermissionArray[$i]."'";
					mysql_query($permissionQry);
				}
			}
		}elseif($userType == 'inspector'){
			////// Update user level and project level permissions
			$keyManagerPermissionArray = array_keys($inspectorPermissionArray);
		
			$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection','iPhone_close_inspection');
		
			for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
				if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
				}else{
					$permissionQry = "UPDATE user_permission SET is_allow = '".$inspectorPermissionArray[$keyManagerPermissionArray[$i]]."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = '".$keyManagerPermissionArray[$i]."'";
					mysql_query($permissionQry);
				}
			}
		}
	}
	$_SESSION['user_update']='User updated successfully.';
	header('location:?sect=c_builder');
}

if(isset($_GET['deleteId'])){
	$update="update user set is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".base64_decode($_GET['deleteId'])." WHERE user_id='".base64_decode($_GET['deleteId'])."'";
	
	$update_up="update user_projects set is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".base64_decode($_GET['deleteId'])." WHERE user_id='".base64_decode($_GET['deleteId'])."'";
	
	mysql_query($update);
	mysql_query($update_up);

	$_SESSION['user_remove'] = 'User removed successfully.';
	header('location:?sect=c_builder');
}

$b_id = base64_decode($_GET['id']);

$q = "SELECT is_allow FROM user_permission WHERE user_id='".$b_id."' AND permission_name='web_add_project' and is_deleted = 0";
$r = $obj->db_query($q);
$temp = $obj->db_fetch_assoc($r);
$addPerm = $temp['is_allow'];

$q = "SELECT * FROM user WHERE user_id='".$b_id."' AND active='1' and is_deleted = 0";
$r=$obj->db_query($q);
$f=$obj->db_fetch_assoc($r);
if(empty($f)){?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }

?>
<div id="middle" style="padding-bottom:80px;">
	<div id="apply_now">
	<form method="post" enctype="multipart/form-data" name="managerEdit" id="managerEdit">
		<div class="content_container">
			<div class="content_left">
				<div class="content_hd1" style="background-image:url(images/builder_account_info_hd.png); width:550px;margin-top:-50px\9;"></div>
				<div class="signin_form">
					<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
						<tr>
							<td colspan="2"><strong>Personal Information</strong></td>
						</tr>
						<tr>
							<td valign="top">User Type</td>
						 	<td valign="top">
								<!--<input  type="text" class="input_small" readonly="readonly" value="<?=stripslashes($f['user_type'])?>" name="userType" id="userType" />-->
								<input id="oldUserType" name="oldUserType" type="hidden" value="<?=stripslashes($f['user_type'])?>"/>
								<select name="userType" id="userType" class="select_box" style="margin-left:0px;" >
									<option value="manager" <?php if(stripslashes($f['user_type']) == 'manager'){echo 'selected="selected"';}?> >Manager</option>
									<option value="inspector" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'selected="selected"';}?> >Inspector</option>
								</select>
							</td>
						</tr>
						<tr>
							<td valign="top">Full Name <span class="req">*</span></td>
							<td valign="top">
								<input  type="text" class="input_small" value="<?=stripslashes($f['user_fullname'])?>" name="fullname" id="fullname" />
							</td>
						</tr>
						<tr>
							<td valign="top" nowrap="nowrap">Company Name <span class="req">*</span></td>
							<td valign="top">
								<input  type="text" class="input_small" value="<?=stripslashes($f['company_name'])?>" name="cname" id="cname"/>
							</td>
						</tr>
						<tr>
							<td valign="top" nowrap="nowrap">Username<span class="req">*</span></td>
							<td valign="top">
								<input  type="text" class="input_small" readonly="readonly" value="<?=stripslashes($f['user_name'])?>" name="username" id="username"/>
							</td>
						</tr>
						<tr id="addProjRow"<?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
							<td align="right"><input type="checkbox" name="addProject" id="addProject" value="1" <?php if($addPerm == 1){ echo 'checked="checked"';}?> /></td>
							<td>User can add projects</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="content_right">
				<div class="signin_form1" style="margin-top:112px;margin-top:117px\9;">
					<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
						<tr>
							<td valign="top">Email <span class="req">*</span></td>
							<td valign="top"><input  type="text" class="input_small" value="<?=stripslashes($f['user_email'])?>"  name="memail" id="memail"/></td>
						</tr>
						<tr>
							<td valign="top">Mobile <span class="req">*</span></td>
							<td valign="top"><input  type="text" class="input_small" value="<?=stripslashes($f['user_phone_no'])?>" name="mobile" id="mobile" /></td>
						</tr>
						<tr>
							<td valign="top">Password <span class="req">*</span></td>
							<td valign="top"><input type="password" class="input_small" value="<?=stripslashes($f['user_plainpassword'])?>"  name="pwd" id="pwd"/></td>
						</tr>
						<tr>
							<td valign="top">Re Password <span class="req">*</span></td>
							<td valign="top"><input type="password" class="input_small" value="<?=stripslashes($f['user_plainpassword'])?>"  name="rePwd" id="rePwd"/></td>
						</tr>
						<tr>
							<td colspan="2">
								<table width="200" border="0">
								  <tr>
									<td><input type="hidden" value="remove_builder" name="sect" id="sect" /><input type="hidden" value="<?=base64_encode($f['user_id'])?>" name="b_id" id="b_id" /><input type="image"  value="Update" style="width:111px; height:45px; border:none;" name="update" id="update" src="images/update_btn.png"/></td>
									<td><input type="button" class="submit_btn" id="remove" name="remove" onclick="deletechecked('?sect=c_remove_builder&deleteId=<?=base64_encode($f['user_id'])?>')" style="width:111px; height:45px; border:none;background-image:url('images/remove_btn.png');color:transparent;" /></td>
									<td><a href="javascript:history.back();"><img src="images/back_btn.png" style="border:none; width:111px;" /></a></td>
								  </tr>
								</table>
                                <!--<input type="image" class="submit_btn"  value="update" src="images/update_btn.png" style="width:111px; height:45px; border:none;" name="update" id="update"/>-->
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</form>
	</div>
</div>
<script type="text/javascript">
function deletechecked(redirectURL){
	var r = jConfirm('Do you want to Delete this user ?', null, function(r){ if(r==true){ window.location = redirectURL; } });
}

$('#userType').change(function(){
	if($(this).val() == 'manager'){
		$('#addProjRow').show();
	}else{
		$('#addProjRow').hide();
	}
});
</script>