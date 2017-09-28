<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

$fgmembersite->LogOut();
header('Location: '."/vshar/index.php");
//$fgmembersite->$path = "/userfile"
?>