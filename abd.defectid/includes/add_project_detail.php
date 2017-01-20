<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
	window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
if(isset($_SESSION['add_sub_loc'])){
	unset($_SESSION['add_sub_loc']);
}
$builder_id=$_SESSION['ww_builder_id'];
$_SESSION['idp']=$id=base64_decode($_GET['id']);

$_SESSION['hb']=$hb=base64_decode($_GET['hb']);

if(isset($_POST['archive'])){
	if($_POST['isArchieve'] == 2){
		$id=base64_decode($_GET['id']); 
		$update = "UPDATE projects SET is_deleted = 0, created_date = NOW(), last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id = ".$id." AND is_deleted = 2 ";
		mysql_query($update);
		$update_user_project = "UPDATE user_projects SET is_deleted = 0, created_date = NOW(), last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id = ".$id." AND is_deleted = 2 ";
		mysql_query($update_user_project);
		$_SESSION['archieve_update']='Project Active Now!';?>
		<script language="javascript" type="text/javascript">
		window.location.href="?sect=show_project";
		</script>
<?php }else{
		$id=base64_decode($_GET['id']); 
		$update = "UPDATE projects SET is_deleted = 2, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id ='$id' AND is_deleted = 0 ";
		mysql_query($update);
		$update_user_project = "UPDATE user_projects SET is_deleted = 2, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id ='$id' AND is_deleted = 0 ";
		mysql_query($update_user_project);
		$_SESSION['archieve_update']='Project Archived successfully!';?>
		<script language="javascript" type="text/javascript">
		window.location.href="?sect=show_project";
		</script>
<?php } 
} 
$q="SELECT 
		up.*,
		up.is_deleted as archieve,
		u.user_fullname as user_fullname
	FROM
		user_projects as up,
		user as u,
		projects as p
	WHERE
		u.user_id = '$hb' AND
		up.user_id = u.user_id AND
		p.project_id = up.project_id AND
		p.project_id = '$id' AND
		p.is_deleted IN (0, 2) AND
		up.is_deleted IN (0, 2) AND
		u.is_deleted= 0
	GROUP BY
		up.project_id";
	
if($obj->db_num_rows($obj->db_query($q)) == 0){ ?>
<script language="javascript" type="text/javascript">
	window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php }
$f=$obj->db_fetch_assoc($obj->db_query($q));
?>
<div class="content_container">
	<div class="content_left" style="width:450px;">
		<div class="content_hd1" style="background-image:url(images/pro_detail_hd.png);margin-top:-50px\9;"></div>
		<div class="signin_form">
			<table width="445" border="0" align="left" cellpadding="0" cellspacing="15">
				 <?php if(isset($_SESSION['builder_seccuess_update'])) { ?>
                     <tr height="50">
                     	<td colspan="2" ><div class="success_r" style="width:250px;"><p><?php echo $_SESSION['builder_seccuess_update'];?></p></div>
                        </td>
                       </tr>
                    <?php unset($_SESSION['builder_seccuess_update']); } ?>   
                    <?php if(isset($_SESSION['add_inspector_success'])){ ?>
                     <tr height="50">
					 	<td colspan="2" align="center"><div class="success_r"><p><?php echo $_SESSION['add_inspector_success'];?></p></div>
                        </td>
					</tr>
                    <?php unset($_SESSION['add_inspector_success']); } ?>    
					<?php if($f['archieve'] == 2){ ?>
					<tr>
						<td><strong style="color:red;">Archived</strong></td>
						<td><strong style="color:red;">Yes</strong></td>
					</tr>
					<?php }else{?>
					<tr>
						<td><strong style="color:white;">Archived</strong></td>
						<td><strong style="color:white;">No</strong></td>
					</tr>
					<?php }?>
					
					<tr>
						<td><strong>Project ID</strong></td>
						<td><strong><?=$f['project_id']?></strong></td>
					</tr>
					<tr>
						<td><strong>Managing by</strong></td>
						<td><strong><?=stripslashes($f['user_fullname'])?></strong></td>
					</tr>
					<tr>
						<td>Project Name</td>
						<td><?=stripslashes($f['project_name'])?></td>
					</tr>
					<tr>
						<td>Project Type</td>
						<td><?=stripslashes($f['project_type'])?></td>
					</tr>
					<tr>
						<td width="180">Address Line 1</td>
						<td width="220"><?=stripslashes($f['project_address_line1'])?></td>
					</tr>
					<tr>
						<td>Address Line 2</td>
						<td><?=stripslashes($f['project_address_line2'])?></td>
					</tr>
					<tr>
						<td>Suburb</td>
						<td><?=stripslashes($f['project_suburb'])?></td>
					</tr>
					<tr>
						<td>State</td>
						<td><?=stripslashes($f['project_state'])?></td>
					</tr>
					<tr>
						<td >Postcode</td>
						<td><?=stripslashes($f['project_postcode'])?></td>
					</tr>
					<tr>
						<td>Country</td>
						<td><?=stripslashes($f['project_country'])?></td>
					</tr>
					<tr>
                        <td valign="top">Certificate of occupancy</td>
                        <td><?php echo ($f['certificate_of_occupancy']!='0000-00-00')?date('d/m/Y', strtotime($f['certificate_of_occupancy'])):'';?></td>
                    </tr>  
                    <tr>
                        <td valign="top">Open date</td>
                        <td><?php echo ($f['open_date']!='0000-00-00')?date('d/m/Y', strtotime($f['open_date'])):'';?></td>
                    </tr>  
                    <tr>
                        <td valign="top">Proposed completion</td>
                        <td><?php echo ($f['proposed_completion']!='0000-00-00')?date('d/m/Y', strtotime($f['proposed_completion'])):'';?></td>
                    </tr>  
                    <tr>
                        <td valign="top">Actual completion</td>
                        <td><?php echo ($f['actual_completion']!='0000-00-00')?date('d/m/Y', strtotime($f['actual_completion'])):'';?></td>
                    </tr>                    
					<?php if($builder_id==$hb){ ?>
					<tr>
						<td colspan="2">
	<?php if($_SESSION['web_edit_project'] == 1){?>
						<a href="?sect=edit_project&id=<?=base64_encode($id)?>"><img src="images/update.png" style="border:none; width:111px;" /></a>
	<?php }?>
						
						
						<a href="?sect=show_project">
						<img src="images/back_btn.png" style="border:none; width:111px;" /></a>
						
						</td>
					</tr>
					<?php } 
					
			
					
					?>
			</table>
		</div>
	</div>
	<div class="content_right" style="width:480x; float:left;margin-left:0px;margin-top:151px;">
		<!-- Inspector section -->
		<?php if($builder_id==$hb){ ?>
		<div class="signin_form1" style="float:left; width:258px;margin-top:-25px;">
        	<table border="0" align="left" cellpadding="0" cellspacing="15">
			<form method="post" action="?sect=project_configuration" name="addproject" id="addproject">
			<tr>
				<td colspan="2"><input type="hidden" value="<?=$id?>" name="id" id="id" /></td>
			</tr>
			<tr>
				<td colspan="2" align="right">
<?php if($_SESSION['web_project_configuration'] == 1){
		if($f['archieve'] != 2){?>
					<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/project_config_btn.png); width:221px; height:44px; border:none;" />
<?php 	}	
	}?>
				</td>
			</tr>
			</form>
			<form method="post" action="" name="addproject" id="addproject">
			<tr>
				<td colspan="2" align="right">
<?php if($_SESSION['web_archive_permission'] == 1){
				if($f['archieve'] == 2){ ?>
					<input type="hidden" value="<?=$f['archieve']?>" name="isArchieve" id="isArchieve" />
					<input name="archive" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/active_project.png); width:221px; height:68px; border:none;" />
				<?php }else{ ?>
					<input type="hidden" value="<?=$f['archieve']?>" name="isArchieve" id="isArchieve" />
					<input name="archive" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/archive_project.png); width:221px; height:68px; border:none;" />
				<?php } ?>
					<input type="hidden" value="<?=$id?>" name="id" id="id" />
<?php }?>
				</td>
			</tr>
			</form>
			<form method="post" action="?sect=project_manual" name="addproject" id="addproject">
			<tr>
				<td colspan="2" align="right">
<?php if($_SESSION['om_manual'] == 1){
	if($f['archieve'] != 2){?>
					<input name="archive" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/manual_om_manual.png); width:221px; height:68px; border:none;" />
					<input type="hidden" value="<?=$id?>" name="id" id="id" />
<?php }
	}
?>
				</td>
			</tr>
			</form>
		</table>
			<!--<form method="post" action="?sect=show_sub_loc" name="addproject" id="addproject">
				<table border="0" align="left" cellpadding="0" cellspacing="15">
					<tr>
						<td colspan="2"><input type="hidden" value="<?//=$id?>" name="id" id="id" /></td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/show_inspactors.png); width:177px; height:44px; border:none;" />
						</td>
					</tr>
				</table>
			</form>			
			<form method="post" action="?sect=add_sub_loc">
				<table border="0" align="left" cellpadding="0" cellspacing="15">
					<tr>
						<td colspan="2"><input type="hidden" value="<?//=$id?>" name="id" id="id" /></td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/add_inspactors.png); width:178px; height:44px; border:none;" />
                        
						</td>
					</tr>
				</table>
			</form>			-->
		</div>
		<?php } ?>
		<!--// Inspector section -->
		
		<!-- Trades section -->
		<?php // if($builder_id==$hb){ ?>
		<!--<div class="signin_form1" style="float:right; width:260px;">
			<form method="post" action="?sect=show_responsible">
				<table border="0" align="left" cellpadding="0" cellspacing="15">
					<tr>
						<td colspan="2"><input type="hidden" value="<?=$id?>" name="id" id="id" /></td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/show_traders.png); width:275px; height:46px;" />
						</td>
					</tr>
				</table>
			</form>			
			<form method="post" action="?sect=add_responsible">
				<table border="0" align="left" cellpadding="0" cellspacing="15">
					<tr>
						<td colspan="2"><input type="hidden" value="<?=$id?>" name="id" id="id" /></td>
					</tr>
					<tr>
						<td colspan="2" align="right"><input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/add_traders.png); width:275px; height:46px;" />
						</td>
					</tr>
				</table>
			</form>
		</div>-->
		<?php // } ?>
		<!--// Trades section -->
	</div>
</div>
