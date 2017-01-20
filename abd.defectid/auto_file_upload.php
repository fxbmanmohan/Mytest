<?php
session_start();
$path = $_COOKIE['path'];
$name='';$fileSize='';$output='';

include('includes/commanfunction.php');

$obj = new COMMAN_Class();

if(isset($_GET['uniqueID'])){
	switch($_GET['action']){
		case 'imageOne':
			$fileElementName = 'image1';
			if(!empty($_FILES[$fileElementName]['error'])){
				switch($_FILES[$fileElementName]['error']){
					case '1':
						$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2':
						$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3':
						$error = 'The uploaded file was only partially uploaded';
						break;
					case '4':
						$error = 'No file was uploaded.';
						break;
		
					case '6':
						$error = 'Missing a temporary folder';
						break;
					case '7':
						$error = 'Failed to write file to disk';
						break;
					case '8':
						$error = 'File upload stopped by extension';
						break;
					case '999':
					default:
						$error = 'No error code avaiable.';
				}
			}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
				$error = 'No file was uploaded.';
			}else{      
				$ext = explode('.', $_FILES[$fileElementName]['name']);
				$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).'.'.$ext[1];
				if($obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 799, 799, 'inspections/photo/'.$name)){
					$output = '<img src="inspections/photo/'.$name.'" width="100" height="90" style="margin-left:10px;margin-top:8px;"  /><input type="hidden" name="photo[]" value="'.$name.'" />';
					@unlink($_FILES[$fileElementName]);
				}	
			}
			echo $output;
		break;
		case 'imageTwo':
			$fileElementName = 'image2';
			if(!empty($_FILES[$fileElementName]['error'])){
				switch($_FILES[$fileElementName]['error']){
					case '1':
						$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2':
						$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3':
						$error = 'The uploaded file was only partially uploaded';
						break;
					case '4':
						$error = 'No file was uploaded.';
						break;
		
					case '6':
						$error = 'Missing a temporary folder';
						break;
					case '7':
						$error = 'Failed to write file to disk';
						break;
					case '8':
						$error = 'File upload stopped by extension';
						break;
					case '999':
					default:
						$error = 'No error code avaiable.';
				}
			}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
				$error = 'No file was uploaded.';
			}else{      
				$ext = explode('.', $_FILES[$fileElementName]['name']);
				$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).'.'.$ext[1];
				if($obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 799, 799, 'inspections/photo/'.$name)){
					$output = '<img src="inspections/photo/'.$name.'" width="100" height="90" style="margin-left:10px;margin-top:8px;"  /><input type="hidden" name="photo[]" value="'.$name.'" />';
					@unlink($_FILES[$fileElementName]);
				}	
			}
			echo $output;
		break;
		
		case 'drawing':
			$fileElementName = 'drawing';
			if(!empty($_FILES[$fileElementName]['error'])){
				switch($_FILES[$fileElementName]['error']){
					case '1':
						$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2':
						$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3':
						$error = 'The uploaded file was only partially uploaded';
						break;
					case '4':
						$error = 'No file was uploaded.';
						break;
		
					case '6':
						$error = 'Missing a temporary folder';
						break;
					case '7':
						$error = 'Failed to write file to disk';
						break;
					case '8':
						$error = 'File upload stopped by extension';
						break;
					case '999':
					default:
						$error = 'No error code avaiable.';
				}
			}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
				$error = 'No file was uploaded.';
			}else{      
				$ext = explode('.', $_FILES[$fileElementName]['name']);
				$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).'.'.$ext[1];
				if($obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 799, 799, 'inspections/drawing/'.$name)){
					$output = '<img src="inspections/drawing/'.$name.'" width="100" height="90" style="margin-left:10px;margin-top:8px;"  /><input type="hidden" name="drawing" value="'.$name.'" />';
					@unlink($_FILES[$fileElementName]);
				}	
			}
			echo $output;
		break;
		
		
		case 'drawing_mgmt':
			$fileElementName = 'image1';
			if(!empty($_FILES[$fileElementName]['error'])){
				switch($_FILES[$fileElementName]['error']){
					case '1':
						$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2':
						$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3':
						$error = 'The uploaded file was only partially uploaded';
						break;
					case '4':
						$error = 'No file was uploaded.';
						break;
		
					case '6':
						$error = 'Missing a temporary folder';
						break;
					case '7':
						$error = 'Failed to write file to disk';
						break;
					case '8':
						$error = 'File upload stopped by extension';
						break;
					case '999':
					default:
						$error = 'No error code avaiable.';
				}
			}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
				$error = 'No file was uploaded.';
			}else{      
				$ext = explode('.', $_FILES[$fileElementName]['name']);
				//$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).'.'.$ext[1];
				$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).'.'.$ext[1];
				//$obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 1600, 1600, './project_drawings/'.$_SESSION['idp'].'/'.$name);
				$imageThumbName = 'thumb_'.$name;
				if($obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 1600, 1600, './project_drawings/'.$_SESSION['idp'].'/'.$name)){
					$obj->resizeImages('./project_drawings/'.$_SESSION['idp'].'/'.$name, 150, 150, './project_drawings/'.$_SESSION['idp'].'/thumbnail/'.$imageThumbName);
					$output = '<img src="./project_drawings/'.$_SESSION['idp'].'/'.$name.'" width="100" height="90" style="margin-left:10px;margin-top:8px;"  /><input type="hidden" name="drawingImage" value="'.$name.'" /><input type="hidden" name="drawingImageThumb" value="'.$imageThumbName.'" />';
					@unlink($_FILES[$fileElementName]);
				}	
			}
			echo $output;
		break;
	}
}
?>