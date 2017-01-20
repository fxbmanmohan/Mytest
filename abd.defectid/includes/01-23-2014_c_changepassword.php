<?php if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){ ?>
<script language="javascript" type="text/javascript"> window.location.href="<?=HOME_SCREEN?>"; </script>
<?php } 
$error = '';$renewpassword='';$oldPassword='';$newPassword='';
if(isset($_POST['passwordUpdate'])){
	if(!empty($_POST['oldPassword'])){$oldPassword = $_POST['oldPassword'];}else{$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter Old Password</p></div>';}
	if(!empty($_POST['newPassword'])){$newPassword = $_POST['newPassword'];}else{$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter New Password</p></div>';}
	if(!empty($_POST['renewpassword'])){$renewpassword = $_POST['renewpassword'];}else{$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter Re Enter Password</p></div>';}
	if($renewpassword == $newPassword){
		$qry = "select comp_password from ".COMPANIES." where c_id = '".$_SESSION['ww_c_id']."'";
		$rs = mysql_query($qry);
		while($fi = mysql_fetch_array($rs)){
			if(md5($oldPassword) == $fi['comp_password']){
				$qry = "update ".COMPANIES." set comp_password = '".md5($newPassword)."', last_modified_date = NOW(), comp_plainpassword = '".$newPassword."', last_modified_by = ".$_SESSION['ww_builder_id']." where c_id = '".$_SESSION['ww_c_id']."'";
				$rs1 = mysql_query($qry);
				$error .= '<div class="success_r" style="text-shadow:none; margin-left:10px;"><p>Update Successfull !</p></div>';	
			}else{
				$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Old Password Does Not Match</p></div>';	
			}
		}
	}else{
		$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter Password and Re Enter Password Does Not Match</p></div>';
	}
	$oldPwd = mysql_query("select * from user '".$_SESSION['ww_c_id']."'");
#echo mysql_num_rows($rs);
}
?>
<div class="content_center">
	<div class="content_hd" style="background-image:url(images/company_profile_hd.png);" ></div>
	<div class="signin_form" style="margin-top:-10px;">
		<form action="" name="changePassword" method="post" >
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<th colspan="2" align="left" valign="top"><?php echo $error;?></th>
				</tr>           
				<tr>
					<td width="134" valign="top">Old Password </td>
					<td width="312" colspan="2" valign="top">
						<input type="password" class="input_big" name="oldPassword" value=""  />
					</td>
				</tr>
				<tr>
					<td width="134" valign="top" nowrap="nowrap">New Password </td>
					<td width="312" colspan="2" valign="top">
						<input type="password" name="newPassword" class="input_big" value="" />
					</td>
				</tr>
				<tr>
					<td width="134" valign="top">Re&nbsp;Enter&nbsp;Password </td>
					<td width="312" colspan="2" valign="top">
						<input type="password" name="renewpassword" class="input_big" value="" />
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top"><input name="passwordUpdate" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/update.png); width:111px; border:none;">
                    
                    <a href="javascript:void();" onclick="history.back();"><input name="passwordUpdate" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/back_btn.png); width:111px; border:none;"></a>
                    </td>
				</tr>
			</table>
		</form>
	</div>
</div>