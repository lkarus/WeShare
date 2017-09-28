<?PHP
require_once("./include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>Home page</title>
      <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css">
      <script src="jquery.min.js" type="text/javascript"></script>
	  <script src="jquery.uploadifive.min.js" type="text/javascript"></script>
	  <link rel="stylesheet" type="text/css" href="uploadifive.css">
	  <style type="text/css">
		body {
			font: 13px Arial, Helvetica, Sans-serif;
		}
		.uploadifive-button {
			float: left;
			margin-right: 10px;
		}

		#queue {
			border: 1px solid #E5E5E5;
			height: 50px;
			overflow: auto;
			margin-bottom: 10px;
			padding: 0 3px 3px;
			width: 300px;
		}
		</style>
</head>
<body>
<div id='fg_membersite_content'>
<h2>Home Page</h2>
Welcome back <?= $fgmembersite->UserFullName();
$path = $fgmembersite->getPath();	
?>
<p><a href='change-pwd.php'>Change password</a></p>

<p><a href='access-controlled.php'>A sample 'members-only' page</a></p>
<br><br><br>
<p><a href='logout.php'>Logout</a></p>
</div>
<form action="youtube_page.php">
		Youtube Video Search: <input type="text" name="yt">
		<button type="submit" id="youtube_submit"> Search </button>
	</form>
	<h3>Uploading Your File Here</h3>
	<form>
		<div id="queue"></div>

		<input id="file_upload" name="file_upload" type="file" multiple="false">
		<a href="javascript:$('#file_upload').uploadifive('upload')">Upload Files</a>

	</form>

	<script type="text/javascript">
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadifive({
				'auto'             : false,
				'checkScript'      : 'check-exists.php',
				'formData'         : {
									   'timestamp' : '<?php echo $timestamp;?>',
									   'token'     : '<?php echo md5('unique_salt' . $timestamp);?>',
									   'path' : '<?php echo $fgmembersite->getPath();?>'
				                     },
				'queueID'          : 'queue',
				'uploadScript'     : 'uploadifive.php',
				'onUploadComplete' : function(file, data) { 
					console.log(data);
					window.location.href = "http://wordmoment.com/Login-and-Register/local_video.php?file_name="+file.name;
				}
			});
		});
	</script>
</body>
</html>
