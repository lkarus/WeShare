<?php
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

// Set the uplaod directory
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}
echo "<script>alert('Upload File Called')</script>";

if(isset($_POST['current_dir'])){
	$current_dir = $_POST['current_dir'];
	echo "<script>alert('Current Directory: ".$current_dir")</script>";
}
else{
	$current_dir = "";
	echo "<script>alert('Current Directory: ".$current_dir")</script>";
}

if($current_dir!=""){
	$uploadDir =  "/vshar/user_database".$fgmembersite->getPath()."/".$current_dir."/";
}else{
	$uploadDir =  "/vshar/user_database".$fgmembersite->getPath()."/";
}

// Set the allowed file extensions
$fileTypes = array('jpg', 'jpeg', 'gif', 'png', 'mp4','wmv','ogg'); // Allowed file extensions

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile   = $_FILES['Filedata']['tmp_name'];
	$uploadDir  = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
	$targetFile = $uploadDir . $_FILES['Filedata']['name'];

	// Validate the filetype
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

		// Save the file
		move_uploaded_file($tempFile, $targetFile);
		echo 1;

	} else {

		// The file type wasn't allowed
		echo 'Invalid file type.';

	}
}
?>