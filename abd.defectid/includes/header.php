<?php
$s = isset($_REQUEST['sect']) ? $_REQUEST['sect'] : '';
if(isset($_SESSION['ww_is_builder']))
	$f = $_SESSION['ww_is_builder'];
else
	$f = '';
?>
<?php
$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$pi = pathinfo($refering_url);
$u = str_replace('pms.php','',$pi['basename']);
?>
<div id="top">
	<div class="header_container">
		<div class="logo"><img src="images/logo.png" border="none" alt="Logo"  height="78"  /></div>
	</div>
	<!--Navigation-->
		<div id="nav">
			<ul>
			<li><a href="index.php"><img src="images/home.png" width="28" height="29" hspace="5" align="absmiddle" border="0" />Home</a></li>            
			</ul>
		</div>
	</div>