<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
require_once("http://wordmoment.com/vShar/include/membersite_config.php");
if(!$fgmembersite->CheckLogin()){
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

if(isset($_POST['submitted']))
{
   if($fgmembersite->ChangePassword())
   {
        $fgmembersite->RedirectToURL("changed-pwd.html");
   }
}

?>
<!DOCTYPE html>
<head>
		<!-- Bootstrap -->
	  		<title>VShar</title>
	  		<meta charset="utf-8">
	 	 	<meta name="viewport" content="width=device-width, initial-scale=1">
		 	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		 	<link rel="stylesheet" href="../css/footer.css">
		  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		  	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
                                .container{
                                    margin-top: 50px;
                                }
			</style>

	</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
	      <div class="container-fluid">
	        <div class="navbar-header">
	          <a class="navbar-brand" href="#">Project name</a>
	        </div>
	      <ul class="nav navbar-nav navbar-right">
	        <li><a href="#">Sign up</a></li>
	        <li><a href="#">Login</a></li>
	      </ul>
	        </div>
	    </nav>
<!-- Form Code Start -->

<div class="container">
            <div class="row main">
		<div class="panel-heading">
	               <div class="panel-title text-center">
	               		<h2>Change Password</h2>
                                    <p>
                                    Please, complete the following information
                                    </p>
	               	</div>
                            </div> 
				<div class="main-login main-center">
					<form id='changepwd' class="form-horizontal" action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>
                                            <div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
                                            <div class="form-group">
							<div class="cols-sm-10">
                                                            <label for="name" class="cols-sm-2 control-label">Current Password: *</label>
                                                            <div class="form-group" id='oldpwddiv' >
									
                                                                         <input type="password" class="form-control" name="name" id="oldpwd" maxlength="50" placeholder="Current Password"/>
                                                                         <span id='changepwd_oldpwd_errorloc' class='error'></span>
								</div>
                                                            <label for="name" class="cols-sm-2 control-label">New Password: *</label>
                                                            <div class="form-group" id='newpwddiv' >
									
                                                                         <input type="password" class="form-control" name="name" id="newpwd" maxlength="50" placeholder="New Password"/>
                                                                         <span id='changepwd_newpwd_errorloc' class='error'></span>
								</div>
                                                            <div class="form-group ">
                                                                <button type="submit" name="Submit" value="Submit" class="btn btn-primary btn-lg btn-block login-button">Confirm</button>
                                                        </div>
                                                            <div class="form-group ">
                                                                <a href='login-home.php' class="btn btn-primary btn-lg btn-block login-button">Home</a>
                                                        </div>
                                    
				</div>
			</div>
                    </form>
		</div>
<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->

<script type='text/javascript'>
// <![CDATA[
    var pwdwidget = new PasswordWidget('oldpwddiv','oldpwd');
    pwdwidget.enableGenerate = false;
    pwdwidget.enableShowStrength=false;
    pwdwidget.enableShowStrengthStr =false;
    pwdwidget.MakePWDWidget();
    
    var pwdwidget = new PasswordWidget('newpwddiv','newpwd');
    pwdwidget.MakePWDWidget();
    
    
    var frmvalidator  = new Validator("changepwd");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("oldpwd","req","Please provide your old password");
    
    frmvalidator.addValidation("newpwd","req","Please provide your new password");

// ]]>
</script>


</div>
<!--
Form Code End (see html-form-guide.com for more info.)
-->

</body>
</html>