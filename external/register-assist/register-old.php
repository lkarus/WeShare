<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

if(isset($_POST['submitted']))
{
   if($fgmembersite->RegisterUser())
   {
        $fgmembersite->RedirectToURL("thank-you.html");
        
   }
}

?>
<!DOCTYPE html>
<html>
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
                /*.row{
                    margin-left: 10%; 
                }*/
                /*.container{
                    margin-top: 50px;
                }*/

            </style>

    </head>
<body>

<!-- Form Code Start -->
<div id='fg_membersite'>
    <!-- Form Code Start     -->
    <form id='register' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>

       <div class="container">
            <div class="row main">
                <div class="panel-heading">
                   <div class="panel-title text-center">
                        <h2>Register: </h2>
                        <input type='hidden' name='submitted' id='submitted' value='1'/>
                        <!--<input type='text'  class='spmhidip' name='<?php //echo $fgmembersite->GetSpamTrapInputName(); ?>' /> -->
                        <div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
                    </div>
                </div>

                <div class="col-md-4">
                </div>

                <div class="col-md-4">
                     <div class="form-group">
                        <input type='text' class="form-control" name='name' id='name' placeholder='Your Full Name' />
                        <span id='register_name_errorloc' class='error'></span>
                    </div>
                    <div class="form-group">
                        <input type='text' class="form-control" name='email' id='email' placeholder='Email Address' maxlength="50" />
                        <span id='register_email_errorloc' class='error'></span>
                    </div>
                    <div class="form-group">
                        <input type='text' class="form-control" name='username' id='username' placeholder='Username' maxlength="50" />
                        <span id='register_username_errorloc' class='error'></span>
                    </div>
                    <div class="form-group">
                        <div id='thepwddiv' ></div>
                        <input type='password' placeholder="Your Password" class="form-control" name='password' id='password' maxlength="50" />
                        <div id='register_password_errorloc'>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type='submit' name='Submit' value='Register' class="btn btn-primary btn-lg btn-block login-button"/>
                    </div>
                </div>

                <div class="col-md-4">
                </div>
            </div>
       </div>
    </form>
</div>
<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->

<script type='text/javascript'>
// <![CDATA[
    var pwdwidget = new PasswordWidget('thepwddiv','password');
    pwdwidget.MakePWDWidget();
    
    var frmvalidator  = new Validator("register");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();
    frmvalidator.addValidation("name","req","Please provide your name");

    frmvalidator.addValidation("email","req","Please provide your email address");

    frmvalidator.addValidation("email","email","Please provide a valid email address");

    frmvalidator.addValidation("username","req","Please provide a username");
    
    frmvalidator.addValidation("password","req","Please provide a password");

// ]]>
</script>

<!--
Form Code End (see html-form-guide.com for more info.)
-->

</body>
</html>
