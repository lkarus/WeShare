<?PHP

$show_change_success = false;
$show_change_error = false;
// Set to false to disable delete button and delete POST request.
$allow_delete = true;
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

// must be in UTF-8 or `basename` doesn't work
setlocale(LC_ALL, 'en_US.UTF-8');
function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

//As the copy function only handle a single file, it is neccessarily to build this function
//To handle the directory copy
function recurse_copy($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

if (!$fgmembersite->CheckLogin()) {
    $fgmembersite->RedirectToURL("../");
    exit;
}

if (isset($_POST['change_pwd'])) {
    if ($fgmembersite->ChangePassword()) {
        $show_change_success = true;
    } else {
        $show_change_error = true;
    }
}

if (isset($_POST['change_name'])) {
    if ($fgmembersite->ChangeName()) {
        $show_change_success = true;
    } else {
        $show_change_error = true;
    }
}

if (isset($_POST['friendEmail'])) {
    if ($fgmembersite->addFriend($_POST['friendEmail'])) {
        $show_change_success = true;
    } else {
        $show_change_error = true;
    }
}

if (isset($_POST['acceptFriend'])) {
    if ($fgmembersite->acceptFriend($_POST['acceptFriend'])) {
        $show_change_success = true;
    } else {
        $show_change_error = true;
    }
}

if (isset($_POST['new_dir'])) {

    //$dir = "";
    $dir = $_POST['new_dir'];
    if (isset($_POST['current_dir'])) {
        $dir = $_POST['current_dir'] . "/" . $dir;
        echo "<script type='text/javascript'>alert('" . $_POST['current_dir'] . "');</script>";
    }
    //else{

    //echo "<script type='text/javascript'>alert('The directory name is empty');</script>";
    //}
    $dir = dirname(dirname(__FILE__)) . "/user_database" . $fgmembersite->getPath() . "/" . $dir;
    //echo "<script type='text/javascript'>alert('$dir');</script>";
    @mkdir($dir);
}

if (isset($_POST['Delete'])) {
    $dir = $dir = dirname(dirname(__FILE__)) . "/user_database" . $fgmembersite->getPath() . "/";
    if (isset($_POST['Yes'])) {
        $dir_input = $_POST['file_name'];
        //echo "<script type='text/javascript'>alert('$dir_input');</script>";
        $dir_input = $dir . $dir_input;
        //cho "<script type='text/javascript'>alert('$dir_input');</script>";
        if (is_dir($dir_input)) {
            //echo "<script type='text/javascript'>alert('Yes it is directory');</script>";
            rmdir($dir_input);
        } else {
            unlink($dir_input);
        }
    } else {
        //redirect to index.php
    }
}

if (isset($_POST['method'])) {
    $dir = $dir = dirname(dirname(__FILE__)) . "/user_database" . $fgmembersite->getPath() . "/";
    if ($_POST['method'] == "Download") {
        // if file name in the root directory of user database f=> ile_path = ""
        $file_name = $_POST['file_name'];
        $file_path = $_POST['path_to_file'];


    } elseif ($_POST['method'] == "Rename") {
        // if file name in the root directory of user database f=> ile_path = ""
        $file_name = $_POST['file_name'];
        $file_path = $_POST['path_to_file'];
        $new_file_name = $_POST['new_name'];
        if (is_dir($file_name)) {

            $file_path_old_name = $dir . $file_path . "/" . $file_name;
            $file_path_new_name = $dir . $file_path . "/" . $new_file_name;
            rename($file_path_old_name, $file_path_new_name);
        } else {
            $file_path_old_name = $dir . $file_path . "/" . $file_name;
            // Getting the extension
            $arry_string = explode(".", $file_name);
            $file_path_new_name = $dir . $file_path . "/" . $new_file_name . "." . $arry_string[1];
            rename($file_path_old_name, $file_path_new_name);
        }
    }
}

if (isset($_POST['seleted_dir_to_move'])) {
    echo "<script type='text/javascript'>alert('" . $_POST['seleted_dir_to_move'] . "');</script>";
    if (isset($_POST['file_name'])) {
        $file_name = $_POST['file_name'];
        echo "<script type='text/javascript'>alert('" . $_POST['file_name'] . "');</script>";
        if (isset($_POST['path'])) {
            echo "<script type='text/javascript'>alert('" . $_POST['path'] . "');</script>";
        }
    }
}
// Checking for changing directory
/*
if(isset($_POST['rename'])){
	$new_name =
}
*/
?>
<!DOCTYPE html>
<html>
<head>
    <title>VShar</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.webrtc-experiment.com:443/rmc3.min.js"></script>
    <script src="https://cdn.webrtc-experiment.com:443/socket.io.js"></script>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/footer.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="../external/uploadify/jquery.uploadifive.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="../external/uploadifive.css">
    <script type='text/javascript' src='../scripts/gen_validatorv31.js'></script>
    <link rel="STYLESHEET" type="text/css" href="../css/pwdwidget.css"/>
    <script src="../scripts/pwdwidget.js" type="text/javascript"></script>
    <!--Fancybox linking library-->
    <!-- Add fancyBox -->
    <link rel="stylesheet" href="../external/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css"
          media="screen"/>
    <script type="text/javascript" src="../external/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

    <!-- Add fancyBox - button helper (this is optional) -->
    <link rel="stylesheet" type="text/css"
          href="../external/fancybox/source/helpers/jquery.fancybox-buttons.css?v=2.1.5"/>
    <script type="text/javascript"
            src="../external/fancybox/source/helpers/jquery.fancybox-buttons.js?v=2.1.5"></script>

    <!-- Add fancyBox - thumbnail helper (this is optional) -->
    <link rel="stylesheet" type="text/css"
          href="../external/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=2.1.5"/>
    <script type="text/javascript" src="../external/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=2.1.5"></script>

    <!-- Add fancyBox - media helper (this is optional) -->
    <script type="text/javascript" src="../external/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.0"></script>
    <!-- Adding boostrap menu javascript library-->
    <script type="text/javascript"
            src="../scripts/jQuery-Plugin-To-Create-Bootstrap-Styled-Context-Menus/dist/BootstrapMenu.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../css/main.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- The screen 100 percent is not working-->
    <style>
        body {
            font-size: 16px;
        }

        .body_padding {

            margin-top: 50px;
        }

        .row {
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

        #taskbar {
            margin-bottom: 20px;
            margin-top: 20px;
        }

        #taskbox {
            margin-left: 10px;
            margin-right: 10px;
        }

        .right_click_table {
            display: inline;
        }

    </style>

</head>
<!--page content-->
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="./login_user/index.php">Project name</a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="../external/register-assist/logout.php">Logout</a></li>
            <li><a href="#">Setting</a></li>
        </ul>
    </div>
</nav>

<div class="body_padding">
    <div class="container">
        <div class="row">
            <h3>Welcome to VShar</h3>
            <div class="col-sm-2">
                <ul class="nav nav-pills nav-stacked">
                    <li id="homeLabel" class="active"><a href="index.php" onclick="showHome()">Home</a></li>
                    <li id="sharedLabel"><a href="#sharedFiles" onclick="showShared()">Shared Files</a></li>
                    <li id="uploadLabel"><a href="#upload" onclick="showUpload()">Upload</a></li>
                    <li id="profileLabel"><a href="#changeprofile" onclick="changeProfile()">Change Profile</a></li>
                    <li id="friendLabel"><a href="#friendlist" onclick="showFriendlist()">Friend List</a></li>
                </ul>
            </div>
            <div class="col-sm-8">
                <?php if ($show_change_success) : ?>
                    <div class="alert alert-success fade in">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success! </strong>Your changes have been changed successfully.
                    </div>
                <?php endif; ?>
                <?php if ($show_change_error) : ?>
                    <div class="alert alert-warning fade in">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Error! </strong><?php echo $fgmembersite->GetErrorMessage(); ?>
                    </div>
                <?php endif; ?>
                <?php $newfiles = $fgmembersite->getNewSharedWithMe();
                $numNewFiles = mysql_num_rows($newfiles);
                ?>
                <?php if ($numNewFiles > 0) : ?>
                    <div class="alert alert-success fade in">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Notice </strong>Someone has shared file:
                        <?php
                        $counter = 0;
                        while ($row = mysql_fetch_array($newfiles)) {
                            if (++$counter == $numNewFiles)
                                echo $row['filename'];
                            else
                                echo $row['filename'].", ";
                        }
                        ?>
                         with you
                    </div>
                <?php endif; ?>
                <ul class="nav nav-tabs nav-justified" id="tab-bar">
                    <li class="active"><a href="#file_play" data-toggle="tab"> Your File</a></li>
                    <li><a href="#youtube_play" data-toggle="tab">Youtube</a></li>
                </ul>
                <div style="float: right;" id="taskbar" class="btn-toolbar" role="toolbar"
                     aria-label="button group toolbar">
                    <tr>
                        <th>
                            <form>
                                <input id="file_upload" name="file_upload" type="file" multiple="true">
                                <a style="position: relative; top: 8px;"
                                   href="javascript:$('#file_upload').uploadifive('upload')"></a>

                            </form>

                            <script type="text/javascript">
                                <?php $timestamp = time();?>
                                $(function () {
                                    $('#file_upload').uploadifive({
                                        'auto': true,
                                        'checkScript': '../external/check-exists.php',
                                        'formData': {
                                            'timestamp': '<?php echo $timestamp;?>',
                                            'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                                        },
                                        'queueID': 'queue',
                                        'uploadScript': '../external/uploadifive.php',
                                        'onUploadComplete': function (file, data) {
                                            console.log(data);
                                        }
                                    });
                                });
                            </script>
                        </th>
                        <th>
                            <?php
                            if (isset($_GET['dir_open'])) {
                                //open_dir == current_dir
                                $open_dir = $_GET['dir_open'];
                                //echo '<script>alert("'.$open_dir.'");</script>';

                                echo '<form action="" method="post">
														<inpt type="hidden" name="current_dir" value"' . $open_dir . '"/>
														<input type = "text" name = "new_dir"/>
														<input type="submit" class = "btn btn-secondary" value="Create Folder" />;
													</form>';
                            } else {
                                echo '<form action="" method="post">
															<input type = "text" name = "new_dir"/>
															<input type="submit" class = "btn btn-secondary" value="Create Folder" />;
														</form>';
                            }
                            ?>
                        </th>
                    </tr>
                </div>
                <!--Script handling the insert folder action-->

                <script type="text/javascript">
                    $(document).ready(function () {
                        $(".add_directory").fancybox({
                            maxWidth: 900,
                            maxHeight: 800,
                            fitToView: false,
                            width: '40%',
                            height: '35%',
                            autoSize: false,
                            closeClick: false,
                            openEffect: 'none',
                            closeEffect: 'none'
                        });
                    });
                    $(document).ready(function () {
                        $(".register").fancybox({
                            maxWidth: 900,
                            maxHeight: 800,
                            fitToView: false,
                            width: '50%',
                            height: '45%',
                            autoSize: false,
                            closeClick: false,
                            openEffect: 'none',
                            closeEffect: 'none'
                        });
                    });
                </script>

                <script>
                    $(document).ready(function () {
                        $('[data-toggle="tooltip"]').tooltip();
                    });
                </script>
                <div class="tab-content clearfix" id="play_page">

                    <div class="tab-pane active" id="file_play">
                        <!--List all the files for play right here-->
                        <?php
                        require 'dir_listing_func.php';
                        include 'dir_listing_config.php';

                        $path = "../user_database" . $fgmembersite->getPath();
                        $current_dir = "";

                        if (isset($_GET['dir_open'])) {
                            $current_dir = $_GET['dir_open'];
                        } else {
                            $current_dir = "";
                        }
                        //echo $fgmembersite->getPath();


                        //END_BREADCRUMB

                        //TABLE
                        if ($current_dir != "") {

                            $full_path = "../user_database" . $fgmembersite->getPath() . "/" . $current_dir;
                        } else {
                            $full_path = "../user_database" . $fgmembersite->getPath();
                        }
                        $dir_handle = opendir($full_path);

                        //error opening folder
                        if ($dir_handle == false) {
                            echo "<br><br><div class='container'><div class='alert alert-danger text-center'><strong>Error!</strong> failed to open folder </div></div>\n";
                            exit('failed to open folder');
                        }

                        $folderlist = array();
                        $filelist = array();

                        while (false !== ($entry = readdir($dir_handle))) {

                            //skip hidden files(optional), current folder ".", parent folder ".."
                            if ((strpos($entry, '.') === 0 and $show_hidden_files === false) | $entry == "." | $entry == ".." | $entry == "index.html") {
                                continue;
                            } else if (is_dir($full_path . "/" . $entry)) {
                                $folderlist[] = $entry;
                            } else {
                                $filelist[] = $entry;
                            }
                        }

                        //order folder and files
                        sort($folderlist);
                        sort($filelist);

                        //foldere is empty
                        if (count($folderlist) == 0 and count($filelist) == 0) {
                            echo '<br><br><div class="container"><div class="alert alert-info text-center"><strong>This folder is empty</strong></div></div>';
                        } else {
                            //print files table
                            //print header
                            echo '
														<table class="table table-hover">
														<thead>
															<tr>
															<th width="35"></th>
															<th class="text-primary">Name</th>
												  			<th width="89" class="text-primary text-center">Size</th></tr>';
                            if ($show_modified_time === true)
                                echo '
												  			<th class="text-primary text-center">Last modified</th>';
                            echo '
												  		</thead>';

                            //print folder

                            $row_id = 1;

                            foreach ($folderlist as $val) {
                                if ($current_dir != "") {
                                    echo '
														<tr class="table_right_click" data-row-id="' . $row_id . '">
															<td><span class="glyphicon glyphicon-folder-open"></span></td>
															<td><a href="../login_user/index.php?dir_open=' . $current_dir . "/" . htmlentities($val) . '">' . htmlentities($val) . '</a></td>';
                                } else {
                                    echo '
														<tr class="table_right_click" data-row-id="' . $row_id . '">
															<td><span class="glyphicon glyphicon-folder-open"></span></td>
															<td><a href="../login_user/index.php?dir_open=' . htmlentities($val) . '">' . htmlentities($val) . '</a></td>';
                                }

                                if ($use_du_command === true && $show_folders_size === true)
                                    echo '
															<td class="text-right"></td>';
                                else
                                    echo '
															<td class="text-center">-</td>';


                                if ($show_modified_time === true)
                                    echo '<td></td>';
                                $row_id = $row_id + 1;
                            }

                            //print file
                            foreach ($filelist as $val) {
                                echo '
														<tr class="table_right_click" data-row-id="' . $row_id . '">
															<td><span class="glyphicon ' . choose_icon($val) . '"></span></td>
															<td><a href="../local_video/index.php?path=' . $fgmembersite->getPath() . "/" . rawurlencode($val) . '">' . htmlentities($val) . '</a></td>' . '
															<td class="text-right">' . formatBytes(filesize($full_path . "/" . $val), 2) . '</td>';
                                if ($show_modified_time === true)
                                    echo '
												  			<td class="text-center"><small>' . date("d/m/y H:i:s", filectime($full_path . $val)) . '</small></td>';
                                echo '
															</tr>';
                                $row_id = $row_id + 1;

                            }
                            echo '
															</table>' . "\n";
                        }
                        echo '<script type="text/javascript">
															var table_rows = {';
                        $row_number = 1;
                        foreach ($folderlist as $val) {
                            echo "'" . $row_number . "': { name: '" . $val . "', isEditable: true},";
                            $row_number = $row_number + 1;
                        }
                        foreach ($filelist as $val) {
                            echo "'" . $row_number . "': { name: '" . $val . "', isEditable: true},";
                            $row_number = $row_number + 1;
                        }
                        echo '};
													var menu = new BootstrapMenu(".table_right_click",{
														 fetchElementData: function($rowElem) {
														    var rowId = $rowElem.data("rowId");
														    return table_rows[rowId];
														  },
														  actions: [{
															    name: "Rename",
															    onClick: function(row) {
															  		var new_name = prompt("Please new folder name", "New Name");
																    if (new_name != null) {
																        
																    	$.post("index.php",{
																    		file_name: row.name,
																    		path_to_file: "",
																    		new_name: new_name,
																      		method: "Rename"
																    	});
																    }else{
																    	alert("Name Cannot be Empty");
																    }
															      
															    }
															  },
															  {
															  	name: "Move To",
															  	onClick: function(row){
															  		var file = row.name;
															  		$.fancybox.open({href: "../login_user/list_current_dir.php?file_name="+file+"&open_dir=' . $current_dir . '&path=' . $path . '", type: "iframe", autoResize: true});
															  	}
															  }, 
															  {
															    name: "Download",
															    onClick: function(row) {
															      $.post("index.php",{
															      	file_dir: row.name,
															      	method: "Download"
															      });
															    }
															},
															   {
															    name: "Delete",
															    onClick: function(row) {
															      var file = row.name;
															  		$.fancybox.open({href: "../login_user/delete_confirmation.php?file_name="+file, type: "iframe", autoResize: true});
															   }
															  },
															  {
															    name: "Share with...",
															    onClick: function(row) {
															      var file = row.name;
															  		$.fancybox.open({href: "sharewith.php?mode=share&file_name="+file+"&current_dir='.$current_dir.'&useremail='.$fgmembersite->UserEmail().'", type: "iframe", autoResize: true});
															   }
															  }]
													});
													';

                        echo '</script>';
                        //end files table print


                        ?>
                    </div>
                    <div class="tab-pane" id="youtube_play">
                        <!--Load youtube page for play right here-->
                        <div id='use-without-login'>

                            <form action="../youtube_video/index.html" method="get">
                                <div class="input-group">
                                    <input name="yt" type="text" placeholder='Paste Youtube video link HERE.'
                                           class="form-control">
                                    <!-- <button id='btn-start-YTvideo'>GO</button> -->
                                    <span class="input-group-btn">
													<button type="submit" class='btn btn-default'>GO!</button>
												</span>
                                </div>
                            </form>

                        </div>
                        <div class="center">
                            <video width='600' height='450' controls>
                                <source src=''/>
                            </video>
                        </div>
                    </div>
                    <div class="tab-pane" id="upload" style="display:none">
                        <form>
                            <div id="queue"></div>
                            <input id="file_upload" name="file_upload" type="file" multiple="true">
                            <a style="position: relative; top: 8px;"
                               href="javascript:$('#file_upload').uploadifive('upload')"></a>
                        </form>

                        <script type="text/javascript">
                            <?php $timestamp = time();?>
                            $(function () {
                                $('#file_upload').uploadifive({
                                    'auto': true,
                                    'checkScript': '../external/check-exists.php',
                                    'formData': {
                                        'timestamp': '<?php echo $timestamp;?>',
                                        'token': '<?php echo md5('unique_salt' . $timestamp);?>'
                                    },
                                    'queueID': 'queue',
                                    'uploadScript': '../external/uploadifive.php',
                                    'onUploadComplete': function (file, data) {
                                        location.reload();
                                    }
                                });
                            });
                        </script>

                    </div>
                    <div class="tab-pane" id="change_profile" style="display:none">
                        <form id='changename' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post'
                              accept-charset='UTF-8'>
                            <fieldset>
                                <legend>Change Name</legend>

                                <input type='hidden' name='change_name' id='submitted' value='1'/>
                                <div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
                                <div class="form-group">
                                    <input type='text' class="form-control" name='name' id='name'
                                           placeholder='Your Full Name'
                                           value="<?php echo $fgmembersite->UserFullName() ?>"/>
                                    <span id='register_name_errorloc' class='error'></span>
                                </div>
                                <div class="form-group">
                                    <input type='submit' name='Submit' value='Confirm'
                                           class="btn btn-primary col-md-offset-3"/>
                                </div>

                            </fieldset>
                        </form>
                        <form id='changepwd' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post'
                              accept-charset='UTF-8'>
                            <fieldset>
                                <legend>Change Password</legend>

                                <input type='hidden' name='change_pwd' id='submitted' value='1'/>
                                <div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
                                <div class='form-group'>
                                    <div class='pwdwidgetdiv' id='oldpwddiv'></div>
                                    <br/>
                                    <noscript>
                                        <input type='password' placeholder="Current password" class="form-control"
                                               name='oldpwd' id='oldpwd' maxlength="50"/>
                                    </noscript>
                                    <span id='changepwd_oldpwd_errorloc' class='error'></span>
                                </div>

                                <div class='form-group'>
                                    <div class='pwdwidgetdiv' id='newpwddiv'></div>
                                    <noscript>
                                        <input type='password' placeholder="New password" class="form-control"
                                               name='newpwd' id='newpwd' maxlength="50"/><br/>
                                    </noscript>
                                    <span id='changepwd_newpwd_errorloc' class='error'></span>
                                </div>
                                <div class="form-group">
                                    <input type='submit' name='Submit' value='Confirm'
                                           class="btn btn-primary col-md-offset-3"/>
                                </div>

                            </fieldset>
                        </form>
                    </div>

                    <div class="tab-pane" id="friendlist" style="display:none">
                        <form id='addfriend' action='<?php echo $fgmembersite->GetSelfScript() . "#friendlist"; ?>'
                              method='post' accept-charset='UTF-8'>
                            <div class="form-group">
                                <form id="friendListSubmit"
                                      action='<?php echo $fgmembersite->GetSelfScript() . "#friendlist"; ?>'
                                      method='post' accept-charset='UTF-8'>
                                    <input type='text' class="form-control" name='friendEmail' id='friendEmail'
                                           placeholder="Your friend's email" display="inline-block" width="80%"/>
                                    <input type='submit' name='Submit' value='Add'
                                           class="btn btn-primary col-md-offset-3"/>
                                </form>
                            </div>

                        </form>
                        <div id="friendtable">
                        </div>
                    </div>

                    <div class="tab-pane" id="sharedfiles" style="display:none">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs">
                                    <li id="sharedByMe" class="active">
                                        <a onclick="$('#sharedWithMe').removeClass('active'); $('#sharedByMe').addClass('active'); showShared();">Shared by Me</a>
                                    </li>
                                    <li id="sharedWithMe" >
                                        <a onclick="$('#sharedByMe').removeClass('active'); $('#sharedWithMe').addClass('active'); showShared();">Shared with me</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row" id="sharedfilestable">
                            <div class="col-md-12">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div> <!-- container -->

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <p>&copy;&nbsp;<a href="">Copyright</a>&emsp;|&emsp;<a href="">About us</a>&emsp;|&emsp;<a href="">Contact
                        us</a></p>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4"><p>Designed by</p></div>
        </div>
    </div>
</footer>

<nav id="context-menu" class="context-menu">
    <ul class="context-menu__items">
        <li class="context-menu__item">
            <a href="#" class="context-menu__link" data-action="View"><i class="fa fa-eye"></i> View Task</a>
        </li>
        <li class="context-menu__item">
            <a href="#" class="context-menu__link" data-action="Edit"><i class="fa fa-edit"></i> Edit Task</a>
        </li>
        <li class="context-menu__item">
            <a href="#" class="context-menu__link" data-action="Delete"><i class="fa fa-times"></i> Delete Task</a>
        </li>
    </ul>
</nav>
<script src="../scripts/right_click.js"></script>

<script type='text/javascript'>
    var refreshingElem;

    function getFriends() {
        $('#friendtable').load("getfriends.php?q=<?php echo $fgmembersite->UserEmail(); ?>", function (response, status, xhr) {
            if (status == "error") {
                var msg = "Sorry but there was an error: ";
                $("#friendtable").html(msg + xhr.status + " " + xhr.statusText);
            }
        });
    }

    function getSharedFiles(mode){
        $('#sharedfilestable').load("sharefiles.php?mode="+mode, function (response, status, xhr) {
            if (status == "error") {
                var msg = "Sorry but there was an error: ";
                $("#sharedfilestable").html(msg + xhr.status + " " + xhr.statusText);
            }
        });
    }

    function showHome() {
        clearInterval(refreshingElem);
        $('#play_page').children().hide();
        $('#file_play').show();
        $('#taskbar').show();
        $('#tab-bar').show();
        $('#homeLabel').parent().children().removeClass('active');
        $('#homeLabel').addClass('active');
    }

    function showUpload() {
        clearInterval(refreshingElem);
        $('#play_page').children().hide();
        $('#taskbar').hide();
        $('#upload').show();
        $('#tab-bar').hide();
        $('#uploadLabel').parent().children().removeClass('active');
        $('#uploadLabel').addClass('active');
    }

    function changeProfile() {
        clearInterval(refreshingElem);
        $('#play_page').children().hide();
        $('#taskbar').hide();
        $('#change_profile').show();
        $('#tab-bar').hide();
        $('#profileLabel').parent().children().removeClass('active');
        $('#profileLabel').addClass('active');
    }

    function showFriendlist() {
        clearInterval(refreshingElem);
        $('#play_page').children().hide();
        $('#taskbar').hide();
        $('#friendlist').show();
        $('#tab-bar').hide();
        $('#friendLabel').parent().children().removeClass('active');
        $('#friendLabel').addClass('active');
        getFriends();
        refreshingElem = setInterval(getFriends, 3000);
    }

    function showShared(){
        clearInterval(refreshingElem);
        $('#play_page').children().hide();
        $('#taskbar').hide();
        $('#tab-bar').hide();
        $("#sharedfiles").show();
        $('#sharedLabel').parent().children().removeClass('active');
        $('#sharedLabel').addClass('active');
        var mode;
        if ($('#sharedByMe').hasClass('active'))
            mode = "fromMe";
        else
            mode = "toMe";
        getSharedFiles(mode);
        refreshingElem = setInterval(getSharedFiles, 3000, mode);
    }

    function blockDisconnect() {
        connection.connectSocket(function () {
            connection.socket.emit('blockDisconnect', '<?php echo $fgmembersite->UserUsername(); ?>');
        });
    }

    if (window.location.hash) {
        var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        if (hash == "friendlist")
            showFriendlist();
        else if (hash == "changeprofile")
            changeProfile();
        else if (hash == "upload")
            showUpload();
        else if (hash == "sharedFiles")
            showShared();
        else
            showHome();
    }

    var connection = new RTCMultiConnection();
    connection.socketURL = 'http://wordmoment.com:9001';
    connection.connectSocket(function () {
        connection.socket.emit('storeClientInfo', '<?php echo $fgmembersite->UserUsername(); ?>');
        connection.socket.emit('changeStatus', 1);
    });
    // <![CDATA[
    var pwdwidget = new PasswordWidget('oldpwddiv', 'oldpwd', 'Current password');
    pwdwidget.enableGenerate = false;
    pwdwidget.enableShowStrength = false;
    pwdwidget.enableShowStrengthStr = false;
    pwdwidget.MakePWDWidget();

    var pwdwidget = new PasswordWidget('newpwddiv', 'newpwd', 'New password');
    pwdwidget.MakePWDWidget();


    var frmvalidator = new Validator("changepwd");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("oldpwd", "req", "Please provide your old password");

    frmvalidator.addValidation("newpwd", "req", "Please provide your new password");

    // ]]>
</script>

</body>

</html>
