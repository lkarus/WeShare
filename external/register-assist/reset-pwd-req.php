<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

$emailsent = false;
if(isset($_POST['submitted']))
{
   if($fgmembersite->EmailResetPasswordLink())
   {
        $fgmembersite->RedirectToURL("reset-pwd-link-sent.html");
        exit;
   }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>Reset Password Request</title>
      <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css" />
      <script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
      <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <link rel="stylesheet" href="../../css/footer.css">
      <!-- The screen 100 percent is not working-->
    <style>
				body {
					font-size: 16px;
				}
				.body_padding{

					margin-top: 50px;
				}
                
                .main {
                    margin-top: 60px;
                    width: 50%;
                    margin-left: 25%;
                    text-align: center;
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

<div class="row main">
	<div class="panel-heading">
        <div class="panel-title text-center">
            <h2>Reset Password</h2>
            <p>
             A link to reset your password will be sent to the email address.
            </p>
         </div>
     </div> 
	 <div class="main-login main-center">
        <form id='resetreq' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>
            <fieldset>
                <input type='hidden' name='submitted' id='submitted' value='1'/>
                    <!-- <div class='short_explanation'>* required fields</div> -->
                <div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
                <div class='container'>
                    <input type="email" class="form-control" name='email' id='email' placeholder="Email" value='<?php echo $fgmembersite->SafeDisplay('email') ?>' maxlength="50" /></br>
                    <span id='resetreq_email_errorloc' class='error'></span>
                </div>
                <div class='container'>
                    <button type="submit" id="submit" class="btn btn-primary btn-lg btn-block login-button">Submit</button>
                </div>
            </fieldset>
        </form>
	</div>
</div>
<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->

<script type='text/javascript'>
// <![CDATA[

    var frmvalidator  = new Validator("resetreq");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("email","req","Please provide the email address used to sign-up");
    frmvalidator.addValidation("email","email","Please provide the email address used to sign-up");

// ]]>
</script>
<!--
Form Code End (see html-form-guide.com for more info.)
-->

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class = "col-md-4">
                <p>&copy;&nbsp;<a href="">Copyright</a>&emsp;|&emsp;<a href="">About us</a>&emsp;|&emsp;<a href="">Contact us</a></p>
            </div>
            <div class = "col-md-4"></div>
            <div class = "col-md-4"><p>Designed by</p></div>
        </div>
    </div>
</footer>

</body>
</html>