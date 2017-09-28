<!DOCTYPE html>
<html>
<head>
	<title>VShar</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://cdn.webrtc-experiment.com:443/rmc3.min.js"></script>
	<script src="https://cdn.webrtc-experiment.com:443/socket.io.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../external/uploadifive.css">
	<script type='text/javascript' src='../scripts/gen_validatorv31.js'></script>
	<link rel="STYLESHEET" type="text/css" href="../css/pwdwidget.css" />
	<script src="../scripts/pwdwidget.js" type="text/javascript"></script> 
	<!-- Adding boostrap menu javascript library-->
	<script type="text/javascript" src="../scripts/jQuery-Plugin-To-Create-Bootstrap-Styled-Context-Menus/dist/BootstrapMenu.min.js"></script>

	<link rel="stylesheet" type="text/css" href="../css/main.css">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- The screen 100 percent is not working-->
	<style>
		body {
			font-size: 16px;
		}
		.body_padding{

			margin-top: 50px;
		}
		.row{
			margin-left: 10%; 
		}
		.center {
			margin: auto;
			width: 60%;
			padding: 10px;
		}
		#queue {
			border: 1px solid #E5E5E5;
			height: 177px;
			overflow: auto;
			margin-bottom: 10px;
			padding: 0 3px 3px;
			width: 300px;
		}
		#taskbar{
			margin-bottom: 20px;
			margin-top: 20px;
		}
		#taskbox{
			margin-left:10px;
			margin-right:10px;
		}
		.right_click_table{
			display: inline;
		}
		#button_confirm, #button_cancle {
			    display: inline;
			}


	</style>

</head>
<!--page content-->
<body>
<?php
	$file_to_delete="None";
	if(isset($_GET['file_name']))
	{
		$file_to_delete = $_GET['file_name'];
	}
	echo "<form action='index.php' method='post'>
		<p>Do you want to delete ".$file_to_delete." ?</p>
		<input type='submit' name='Yes' value='Yes' class='btn btn-primary col-md-offset-3'/>
		<input type='submit' name='No' value='No' class='btn btn-primary col-md-offset-3'/>
		<input type='hidden' name='file_name' value='".$file_to_delete."'/>
		<input type='hidden' name='Delete'/>
	";
?>
</body>