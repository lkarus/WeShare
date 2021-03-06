<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
  setlocale(LC_ALL,'en_US.UTF-8');
  $path = "../user_database".$fgmembersite->getPath();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>VShar</title>

    <!--For reference in the new interface-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <!-- <script type="text/javascript" src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/friends_new.css">
    <link rel="stylesheet" href="css/home_new.css">
    <script type="text/javascript" src="scripts/friends.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="./external/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>    

    <!-- Add fancyBox -->
    <link rel="stylesheet" href="./external/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script type="text/javascript" src="./external/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

   <!-- Add fancyBox - button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="./external/fancybox/source/helpers/jquery.fancybox-buttons.css?v=2.1.5" />
	<script type="text/javascript" src="./external/fancybox/source/helpers/jquery.fancybox-buttons.js?v=2.1.5"></script>

	<!-- Add fancyBox - thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="./external/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=2.1.5" />
	<script type="text/javascript" src="./external/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=2.1.5"></script>

	<!-- Add fancyBox - media helper (this is optional) -->
	<script type="text/javascript" src="./external/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.0"></script>
    
  </head>
  <body>
      <div class="cover">
      <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="."><h3 style="margin-top: 2px;">WeShare</h3></a>
          </div>
          <div class="collapse navbar-collapse" id="navbar-ex-collapse">
                <?php if ($fgmembersite->CheckLogin()) : ?>
            <ul class="nav navbar-nav navbar-right">
              <li>
                <div class="btn-group btn-group-lg">
                  <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown"> <?php echo $fgmembersite->UserUsername()?>&nbsp; <span class="fa fa-caret-down"></span></a>
                  <ul class="dropdown-menu" role="menu">
			<li>
                      <a href="friends.php"><i class="fa fa-lg fa-fw fa-users"></i>&nbsp;Friends</a>
                    </li>

                    <li>
                      <a href="setting.php"><i class="fa fa-lg fa-fw fa-gear"></i>&nbsp;Settings</a>
                    </li>
                    <li>
                      <a href="external/register-assist/logout.php">
                      <i class="fa fa-fw ar fa-lg fa-sign-out"></i>
                      &nbsp;
                      Logout</a>
                    </li>
                  </ul>
                  <?php else : ?>
                    <ul class="nav navbar-nav navbar-right">
                     <li>
                <li><a data-fancybox-type="iframe" class="various" href="./external/register-assist/login.php"><h5>Login</h5></a></li>
                </li>
                <li><li><a data-fancybox-type="iframe" class="register" href="./external/register-assist/register.php"><h5>Register</h5></a></li>
                </li>
              </ul>
              <?php endif;?>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div id='use-without-login'>
        <div class="section" style="height:100%; padding-top: 15%;">
            <div class="container" id="highlight">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h1>WeShare</h1>
                        <h4>Let you watch videos with people who are far away</h4>
                        <br>
                        <br>
                        <input id="try_me_id" type="button" class="btn btn-lg btn-default" value="Try Me"></input>
                        <br>
                        <br>
                        <form id="youtube_form" action="youtube.html" method="get">
                            <input style="width: 60%; height: 52px; text-align: center;" name="yt" type="text" placeholder='Paste Youtube video link HERE.'>
                            <input type="submit" value="GO" class='btn btn-lg btn-default'>
                        </form>
                        <script>
                            /*var youtube_form = document.getElementById('youtube_form');
                            function try_me() {
                                    alert("Testing");
                                    youtube_form.style.visibility = "visible";
                            }​
                            var try_me_element = document.getElementById('try_me');*/

                          $("#youtube_form").hide();
                            $("#try_me_id").click(function(){
                              $("#youtube_form").show('slow');
                            })
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section" style="padding: 60px 0px; background-color: #2b3e50;">
      <div class="container">
        <div class="row text-center">
          <div class="col-md-4 text-center">
            <img src="img/sync.PNG" class="center-block img-responsive" style="height:200px; width:200px;">
            <h2>Video Synchronization</h2>
            <p contenteditable="true">Always watch the same thing with your friends at the same time.</p>
          </div>
          <div class="col-md-4">
            <img src="img/video-call.png" class="center-block img-responsive" style="width:200px; height:200px">
            <h2>Video call</h2>
            <p>See, talk with your friends as if you were together.</p>
          </div>
          <div class="col-md-4">
            <img src="img/add-user.png" class="center-block img-responsive" style="width:200px; height:200px">
            <h2>Friendlist</h2>
            <p>Check your friends' status,
              <br>join/invite a friend with ease.</p>
          </div>
        </div>
      </div>
    </div>  
    <footer class="section" style="background-color: #4e5d6c;">
      <div class="container">
        <div class="row">
          <div class="col-sm-6">
            <h1>Team 7</h1>
            <p>Chayanin Wong: cwong@kaist.ac.kr
              <br>Phan Duy Loc: duyloc_1503@kaist.ac.kr
              <br>Makara Phav: makaraphav@kaist.ac.kr</p>
          </div>
          <div class="col-sm-6">
            <p class="text-info text-right">
              <br>
              <br>
            </p>
            <div class="row">
              <div class="col-md-12 hidden-lg hidden-md hidden-sm text-left">
                <a href="#"><i class="fa fa-3x fa-fw fa-instagram text-inverse"></i></a>
                <a href="#"><i class="fa fa-3x fa-fw fa-twitter text-inverse"></i></a>
                <a href="#"><i class="fa fa-3x fa-fw fa-facebook text-inverse"></i></a>
                <a href="#"><i class="fa fa-3x fa-fw fa-github text-inverse"></i></a>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 hidden-xs text-right">
                <a href="#"><i class="fa fa-3x fa-fw fa-instagram text-inverse"></i></a>
                <a href="#"><i class="fa fa-3x fa-fw fa-twitter text-inverse"></i></a>
                <a href="#"><i class="fa fa-3x fa-fw fa-facebook text-inverse"></i></a>
                <a href="#"><i class="fa fa-3x fa-fw fa-github text-inverse"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(".various").fancybox({
                fitToView	: false,
                width		: '450px',
                height		: '350px',
                autoSize	: false,
                closeClick	: false,
                openEffect	: 'none',
                closeEffect	: 'none',
                padding : 0
            });
        });
        $(document).ready(function(){
            $(".register").fancybox({
                
                fitToView	: false,
                width		: '50%',
                height		: '45%',
                autoSize	: true,
                closeClick	: false,
                openEffect	: 'none',
                closeEffect	: 'none',
                padding : 0
            });
        });
    </script>
  </body>
</html>
