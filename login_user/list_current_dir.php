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

	</style>

</head>
<!--page content-->
<body>
<?php
	require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
	require 'dir_listing_func.php';
	include 'dir_listing_config.php';

	$path = $_GET['path'];
	$current_dir = "";
	if(isset($_POST['file_name'])){
		$file_name = $_POST['file_name'];
	}
	else if(isset($_GET['file_name'])){
		$file_name = $_GET['file_name'];
	}
	else{
		echo "<script>alert('File name not set by both get and post')</script>";
	}
	//echo "<script>alert('".$file_name."')</script>";

	if(isset($_GET['open_dir'])){
		$current_dir = $_GET['open_dir'];
	}
	else{
		$current_dir = "";
	}

	//echo "<script>alert('".$current_dir."')</script>";	
	//echo $fgmembersite->getPath();

	
		//END_BREADCRUMB

		//TABLE
	if($current_dir != ""){

	$full_path = $path."/".$current_dir;
	}
	else{
	$full_path = $path;	
	}
	try{
		$dir_handle = opendir($full_path);
	}catch (Exception $e) {
		echo '<script>alert(Message:'. $e->getMessage().';)</script>';
	}

			//error opening folder
			if($dir_handle == false){
				echo "<br><br><div class='container'><div class='alert alert-danger text-center'><strong>Error!</strong> failed to open folder </div></div>\n";
				exit('failed to open folder');
			}
			
			$folderlist = array();

			while( false !== ($entry = readdir($dir_handle))){

				//skip hidden files(optional), current folder ".", parent folder ".."
				if ( ( strpos($entry,'.') === 0 and $show_hidden_files===false) | $entry == "." | $entry == "index.html"){
					continue;
				}else if ( is_dir( $full_path."/".$entry ) ) {
					$folderlist[] = $entry;
				}
			}

			//Start

			//order folder and files
			
			sort($folderlist);
			//foldere is empty
			if(count ($folderlist) == 0){
				echo '<br><br><div class="container">
						<div class="alert alert-info text-center">
							<strong>This folder is empty</strong></div></div>';
			}

			else{
				//print files table
				//print header
				echo'<form method="post" action="index.php">
					<table class="table table-hover">
					<thead>
						<tr>
						<th width="35"></th>
						<th class="text-primary">Name</th>
			  			<th width="89" class="text-primary text-center">Size</th></tr>';
			  	if($show_modified_time == true)
			  		echo'
			  			<th class="text-primary text-center">Last modified</th>';
			  	echo'
			  		</thead>';

			 	//print folder

			  	//End

				$row_id = 1;

				foreach ($folderlist as $val) {
					if($current_dir != ""){
						$move_dir = $current_dir."/".$val;
						echo '
						<tr class="table_right_click" data-row-id="'.$row_id.'">
							<td><span class="glyphicon glyphicon-folder-open"></span></td>
							<td><a href="../login_user/list_current_dir.php?dir_open='.$move_dir.'&path='.$path.'&file_name='.$file_name.'">'.htmlentities($val).'</a></td>
							<td><input type="radio" name="seleted_dir_to_move" value="'.$move_dir.'"/>
								<input type="hidden" name="file_name" value="'.$file_name.'"/>
								<input type="hidden" name="path" value="'.$path.'"></td>';
					}else{
			echo '
		<tr class="table_right_click" data-row-id="'.$row_id.'">
			<td><span class="glyphicon glyphicon-folder-open"></span></td>
			<td><a href="../login_user/list_current_dir.php?dir_open='.$val.'&path='.$path.'&file_name='.$file_name.'">'.htmlentities($val).'</a></td>

			<td><input type="radio" name="seleted_dir_to_move" value="'.$val.'"></td>
			<input type="hidden" name="file_name" value="'.$file_name.'"/>
								<input type="hidden" name="path" value="'.$path.'"></td>';
					}

					if($use_du_command === true && $show_folders_size === true)
						{echo'
						<td class="text-right"></td>';}
					else
						{echo'
						<td class="text-center">-</td>';}


					if($show_modified_time === true)
						{echo'<td></td>';}
					$row_id = $row_id + 1;
					}
				}
				echo '
				<tr>
					<td></td>
					<td></td>
					<td><input type="submit" class = "btn btn-secondary" name="move_to" value="Confirm"></input></td>
				</tr>
				</table>
				</form>';
?>
</body>