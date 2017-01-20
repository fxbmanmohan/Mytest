<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

if(isset($_POST['id'])){
	$id=$_POST['id'];
}else{
?>
<script>window.location.href="<?=SHOW_PROJECTS?>";</script>
<?php	
}
$builder_id = $_SESSION['ww_builder_id'];
$q="SELECT * FROM ".PROJECTS." WHERE project_id = '$id' AND user_id = '$builder_id' ";
if($obj->db_num_rows($obj->db_query($q)) == 0){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php
}
$f=$obj->db_fetch_assoc($obj->db_query($q));
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/edit_sub_loc.js"></script>
<script language="javascript" type="text/javascript">

function startAjax(){
	var ownerName=document.getElementById('ownerName').value;
	var userName=document.getElementById('userName').value;
	var password=document.getElementById('password').value;
	var phone=document.getElementById('phone').value;
	var email=document.getElementById('email').value;
	
	if(ownerName!='' && userName!='' && password!='' && phone!='' && email!=''){
		document.getElementById('sign_in_process').style.visibility = 'visible';
		document.getElementById('sign_in_response').style.visibility = 'hidden';
		return true;
	}else{
		var err = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
		document.getElementById('sign_in_response').innerHTML = err;
		return false;
	}
	
	document.getElementById('sign_in_process').style.visibility = 'visible';
	document.getElementById('sign_in_response').style.visibility = 'hidden';
	return true;
}

function stopAjax(success){
	var result = '';
	if(success == 0){
		result = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
	}else if(success == 1){
		result = '<span class="sign_emsg">Invalid phone number!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="sign_emsg">Invalid email id!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<span class="sign_emsg">Password must be greater than 8 characters!<\/span><br/><br/>';
	}else if(success == 4){
		result = '<span class="sign_emsg">Username already exists!<\/span><br/><br/>';
	}else if(success == 5){
		result = '<span class="sign_emsg">Email Id already exists!<\/span><br/><br/>';
	}else if(success == 6){
		result = '<span class="sign_msg">Inspactor added successfully!<\/span><br/><br/>';
	}else if(success == 7){
		result = '<span class="sign_msg">Inspactor added successfully!<\/span><br/><br/>';

		// reset form		
		document.getElementById('ownerName').value='';
		document.getElementById('userName').value='';
		document.getElementById('password').value='';
		document.getElementById('phone').value='';
		document.getElementById('email').value='';
		
	}
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';
	
	return true;
}
</script>
<!-- Ajax Post -->
<div class="content_center">
	<form action="ajax_reply.php" method="post"  enctype="multipart/form-data" name="e_r_frm" id="e_r_frm" >
		
		<div class="content_hd" style="background-image:url(images/add_new_inspactor.png);"></div>
		<div id="sign_in_process"><br />
			Adding sub location ...<br/>
			<img src="images/loader.gif" /><br/>
		</div>
		<div id="sign_in_response"></div>
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Project Id</td>
					<td width="312" colspan="2"><input type="text" class="input_small" readonly="readonly" value="<?=$f['project_id']?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top" >Project Name</td>
					<td width="312" colspan="2"><input type="text" class="input_small" readonly="readonly" value="<?=stripslashes($f['project_name'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Full Name <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="ownerName" type="text" class="input_small" id="ownerName" value="<?php if(isset($_SESSION['add_sub_loc']['full_name'])) { echo $_SESSION['add_sub_loc']['full_name']; } ?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Username <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="userName" type="text" class="input_small" id="userName" value="<?php if(isset($_SESSION['add_sub_loc']['userName'])) { echo $_SESSION['add_sub_loc']['userName']; } ?>" /> 
					<?php if(isset($_SESSION['add_inspector_user'])){ echo $_SESSION['add_inspector_user']; unset($_SESSION['add_inspector_user']);} ?>
                    
                    </td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Password <span class="req">*
						</span><div style="color:#FFF;font-size:9px;">(greater than 8 characters)</div></td>
					<td width="312" colspan="2"><input name="password" type="password" class="input_small" id="password" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Phone No. <span class="req">*
						</span><div style="color:#FFF;font-size:9px;">(only numbers)</div></td>
					<td width="312" colspan="2"><input name="phone" type="text" class="input_small" id="phone"  value="<?php if(isset($_SESSION['add_sub_loc']['phone'])) { echo $_SESSION['add_sub_loc']['phone']; } ?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Email Id <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="email" type="text" class="input_small" id="email" value="<?php if(isset($_SESSION['add_sub_loc']['email'])) { echo $_SESSION['add_sub_loc']['email']; } ?>" /> 
                    <?php if(isset($_SESSION['add_inspector_email'])){ echo $_SESSION['add_inspector_email']; unset($_SESSION['add_inspector_email']);} ?>
                    </td>
				</tr>
				<tr>
					<td colspan="3" align="center">
                    <?php 
						$id=base64_encode($_SESSION['idp']);
						$hb=base64_encode($_SESSION['hb']); 
					?>
                    
                    <input type="hidden" value="<?=$f['project_id']?>" name="proId" id="proId" />
						<input type="hidden" value="add_inspector" name="sect" id="sect" />
					<input name="button" type="submit" class="submit_btn" id="button" value="save" style="background-image:url(images/save.png); font-size:0px; border:none; width:111px;" />
					<input name="button" type="submit" class="submit_btn" id="button" value="save_n_new" style="background-image:url(images/save_n_new.png); font-size:0px; width:131px; border:none;" />
					 <a href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>"><input name="passwordUpdate" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/back_btn.png); width:111px; border:none;padding-bottom:10px;"></a>
                    </td>
				</tr>
			</table>
		</div>
	</form>
	<form method="post" action="?sect=show_sub_loc">
		<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td width="30%"><input type="hidden" value="<?=$id?>" name="id" id="id" /></td>
				<td align="center"><input name="button2" type="submit" class="submit_btn" id="button2" value="" style="background-image:url(images/show_inspactors.png); width:177px; height:44px; border:none;" />
                
                
                
                </td>
			</tr>
		</table>
	</form>
</div>
