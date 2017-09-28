<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

if(isset($_GET['code']))
{
   if($fgmembersite->ConfirmUser())
   {
        $fgmembersite->RedirectToURL("thank-you-regd.html");
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
		 	<link rel="stylesheet" href="../../css/bootstrap.min.css">
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
<!-- <nav class="navbar navbar-inverse navbar-fixed-top">
	      <div class="container-fluid">
	        <div class="navbar-header">
	          <a class="navbar-brand" href="#">Project name</a>
	        </div>
	      <ul class="nav navbar-nav navbar-right">
	        <li><a href="#">Sign up</a></li>
	        <li><a href="#">Login</a></li>
	      </ul>
	        </div>
	    </nav> -->
	<div class="container">
            <div class="row main">
		<div class="panel-heading">
	               <div class="panel-title text-center">
	               		<h2>Confirm registration</h2>
                                    <p>
                                    Please enter the confirmation code in the box below
                                    </p>
	               	</div>
                            </div> 
				<div class="main-login main-center">
					<form id='confirm' class="form-horizontal" action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='get' accept-charset='UTF-8'>
                                            <div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
                                            <div class="form-group">
							<label for="name" class="cols-sm-2 control-label">Confirmation Code: *</label>
							<div class="cols-sm-10">
								<div class="form-group">
									
                                                                         <input type="text" class="form-control" name="name" id="code" maxlength="50" placeholder="Confimation Code"/>
                                                                         <span id='register_code_errorloc' class='error'></span>
								</div>
                                                                <div class="form-group ">
                                                                    <button type="submit" name="Submit" class="btn btn-primary btn-lg btn-block login-button">Confirm</button>
                                                                </div>
                                                                <div class="form-group ">
                                                                    <button type="button" class="btn btn-primary btn-lg btn-block login-button">Resend</button>
                                                                </div>
                                                            </div>
						</div>
					</form>
				</div>
			</div>
		</div>
<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->

    <script type='text/javascript'>
    // <![CDATA[

        var frmvalidator  = new Validator("confirm");
        frmvalidator.EnableOnPageErrorDisplay();
        frmvalidator.EnableMsgsTogether();
        frmvalidator.addValidation("code","req","Please enter the confirmation code");

    // ]]>
    </script>
</div>
<!--
Form Code End (see html-form-guide.com for more info.)
-->

</body>
</html>