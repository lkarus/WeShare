<?php
$file = $_GET['download_file'];
#echo "<script type='text/javascript'>alert('".$_POST['download_file']."');</script>";
if (file_exists($file)) {
	#echo "<script type='text/javascript'>alert('Download File In');</script>";
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}
?>