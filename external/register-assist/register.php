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
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="../../css/bootstrap.min.css"> </head>

<body>
  <div class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="text-center">Register</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
        <div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
          <form id='register' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>
          <input type='hidden' name='submitted' id='submitted' value='1'/>
            <div class="form-group"> <label>Full Name</label>
              <input type="text" id='name' name="name" class="form-control" placeholder="Enter full name"> </div>
            <div class="form-group"> <label>Email Address</label>
              <input type="email" name='email' id='email' class="form-control" placeholder="Enter email address"> </div>
            <div class="form-group"> <label>User ID</label>
              <input type="text" name='username' id='username' class="form-control" placeholder="Enter user id"> </div>
            <div class="form-group"> <label>Password</label>
              <input type="password" class="form-control" placeholder="Enter password" name='password' id='password'> </div>
            <div class="form-group"> <label>Re-enter Password</label>
              <input type="password" class="form-control" placeholder="Re-enter password" name='reenter_password' id='reenter_password'> </div>
            <button type="submit" name='Submit' class="btn btn-primary btn-block">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
  <script src="https://pingendo.com/assets/bootstrap/bootstrap-4.0.0-alpha.6.min.js"></script>
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
</body>

</html>