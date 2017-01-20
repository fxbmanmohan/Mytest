<?php

$iPadVersion = '5.0.7';
$iPhoneVersion = '5.0.6';

$output = array(
	'status' => true,
	'data' => $iPadVersion,
	'iPadMessage' => 'New build version "V'.$iPadVersion.'" is available.\nWould you like to download it?',
	'iPhoneMessage' => 'New build version "V'.$iPhoneVersion.'" is available.\nWould you like to download it?',
	'iPadVersion' => $iPadVersion,
	'iPhoneVersion' => $iPhoneVersion,
	'iPadURL' => 'itms-services://?action=download-manifest&url=https://wiseworking.com.au/distribution/ipad/defectid/V'.$iPadVersion.'/app.plist',
	'iPhoneURL' => 'itms-services://?action=download-manifest&url=https://wiseworking.com.au/distribution/iphone/defectid/V'.$iPhoneVersion.'/app.plist'
);
if(isset($_REQUEST['antiqueID']))
	echo json_encode($output);
else
	echo '['.json_encode($output).']';
die;?>