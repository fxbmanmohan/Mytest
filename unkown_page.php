<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<style>
#dt_example { color:}
.showArchived{display:none;}
#projTable tr td a { color:#000000; }
</style>
</head>
<body id="dt_example">

<div id="container" class="big_container">
<div class="content_hd1" style="background-image:url(images/estimating_project.png); margin-top:10px;"></div><br clear="all" />
<!--div style="text-transform:capitalize" class="pageTitle"><img alt="Projects" src="images/projects_icon.png"><h1>Projects</h1></div-->
</div>
<div class="spacer"></div>
</div>
</body>
</html>