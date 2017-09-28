<?PHP
//echo "{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php";
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
 
if(isset($_POST['submitted']))
{
   if($fgmembersite->Login())
   {
        /*$user_rec = array();
        $fgmembersite->GetUserFromEmail($fgmembersite->UserEmail(),$user_rec);
        $path = $fgmembersite->getPath($user_rec);
        $fgmembersite->RedirectToURL("../../login_user/index.php");*/
        echo "<script>self.parent.location.href = 'https://wordmoment.com/vshar/index.php';</script>";
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
          <h1 class="text-center">Login</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12   ">
          <form id="login_form" action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>
          <input type='hidden' name='submitted' id='submitted' value='1'/>
            <div class="form-group"> <label>Username</label>
              <input type="text" name="username" id="username" class="form-control" placeholder="Enter username"> </div>
            <div class="form-group"> <label>Password</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Password"> </div>
            <button type="submit" class="btn btn-primary pull-right btn-block">Login</button>
          </form>
          <a href='reset-pwd-req.php' class="pull-right">Forget Password</a>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
  <script src="https://pingendo.com/assets/bootstrap/bootstrap-4.0.0-alpha.6.min.js"></script>
  <script type='text/javascript'>
// <![CDATA[
    
    var frmvalidator  = new Validator("login_form");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("username","req","Please provide your username");
    
    frmvalidator.addValidation("password","req","Please provide the password");
// ]]>
</script>
</body>

</html>