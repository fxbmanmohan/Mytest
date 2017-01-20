<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
$builder_id=$_SESSION['ww_builder_id'];

if(isset($_POST['button_x']))
{
	if(isset($_POST['contact_name']) && !empty($_POST['contact_name']))
	{
		$issue_contact_name=$_POST['contact_name'];	
	}
	else
	{
		$issue_contact_name_err='<div class="error-edit-profile">The contact name field is required</div>';
	}
	
	if(isset($_POST['company_name']) && !empty($_POST['company_name']))
	{
		$issue_company_name=$_POST['company_name'];	
	}
	else
	{
		$issue_company_name_err='<div class="error-edit-profile">The company name field is required</div>';
	}
	
	if(isset($_POST['phone']) && !empty($_POST['phone']))
	{
		$issue_phone=$_POST['phone'];	
	}
	else
	{
		$issue_phone_err='<div class="error-edit-profile">The phone field is required</div>';
	}
	
	if(isset($_POST['emailid']) && !empty($_POST['emailid']))
	{
		$issue_emailid=$_POST['emailid'];	
	}
	else
	{
		$issue_emailid_err='<div class="error-edit-profile">The email field is required</div>';
	}
		if(isset($issue_contact_name) || isset($issue_company_name) || isset($issue_phone) || isset($issue_emailid))
		{
			$issue_insert="insert into inspection_issue_to (issue_to_name,company_name,issue_to_phone,issue_to_email,last_modified_date,	last_modified_by,created_date,created_by,	project_id) values ('".$issue_contact_name."','".$issue_company_name."','".$issue_phone."','".$issue_emailid."',now(),".$builder_id.",now(),".$builder_id.",".$_SESSION['idp'].")";
			
			
			mysql_query($issue_insert);
			$_SESSION['issue_add']='Issued to added successfully.';
			header('location:?sect=issue_to');
			
		}
		else
		{
			$_SESSION['issue_add_err']='Issued to not added.';
		}
}

?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_issued_csv.js"></script>

<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="addissueto" id="addissueto" >
		
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/hd_add_issue.png);"></div>
					
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<?php if(isset($_SESSION['issue_add_err'])) { ?><tr><td colspan="2" align="center"><div class="failure_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['issue_add_err'];?></p><?php unset($_SESSION['issue_add_err']);  ?></div></td></tr> <?php }?>
                            
                            
                            <tr>
								<td nowrap="nowrap" valign="top">Contact Name <span class="req">*</span></td>
								<td>
                               
                                	<input name="contact_name" type="text" class="input_small" id="contact_name" />
                                     <?php if(isset($issue_contact_name_err)) { echo $issue_contact_name_err; } ?>
                                </td>
							</tr>
                            
                            
                            <tr>
								<td valign="top">Company Name <span class="req">*</span></td>
								<td><input name="company_name" type="text" class="input_small" id="company_name" />
                                <?php if(isset($issue_company_name_err)) { echo $issue_company_name_err; } ?>
                                </td>
							</tr>
							
							<tr>
								<td nowrap="nowrap" valign="top">Phone<span class="req">*</span></td>
								<td><input name="phone" type="text" class="input_small" id="phone" />
                                <?php if(isset($issue_phone_err)) { echo $issue_phone_err; } ?>
                                </td>
							</tr>
							<tr>
								<td width="133" valign="top">Email<span class="req">*</span></td>
								<td width="252"><input name="emailid" type="text" class="input_small" id="emailid" />
                                <?php if(isset($issue_emailid_err)) { echo $issue_emailid_err; } ?>
                                </td>
							</tr>
												
							
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/save.png" style="border:none; width:111px;" />
								</td>
							</tr>
						</table>
					</div>
				</div>
				
			</div>
		</form>
	</div>
</div>