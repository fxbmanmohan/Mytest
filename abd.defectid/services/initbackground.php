<?php
$path = $_SERVER['DOCUMENT_ROOT']."/background";
echo $command = "php ".$path."/process.php 1 12 > /dev/null & echo $!";

exec($command, $outputcmd, $return);

if(!$return){
	echo "<br/>Email has been send successfully.";
}else{
	echo "<br/>Error occur while sending email.";
}?>