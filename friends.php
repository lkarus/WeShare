<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
setlocale(LC_ALL,'en_US.UTF-8');
$path = "../user_database".$fgmembersite->getPath();

$show_change_success = false;
$show_change_error = false;

if(!$fgmembersite->CheckLogin())
{
  $fgmembersite->RedirectToURL("../");
  exit;
}

if(isset($_POST['friendEmail'])){
  if($fgmembersite->addFriend($_POST['friendEmail']))
  {
    $show_change_success=true;
  }
  else{
    $show_change_error=true;
  }
}

if(isset($_POST['acceptFriend'])){
    if($fgmembersite->acceptFriend($_POST['acceptFriend']))
    {
        $show_change_success=true;
    }
    else{
        $show_change_error=true;
    }
}

if(isset($_POST['deleteFriend'])){
    if($fgmembersite->deleteFriend($_POST['deleteFriend']))
    {
        $show_change_success=true;
    }
    else{
        $show_change_error=true;
    }
}

?>
<html><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script type="text/javascript" src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/friends.css">
</head><body>
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
<div class="section" style="height: 400px; margin-top:60px;">
  <div class="container">
    <?php if($show_change_success) : ?>
      <div class="alert alert-success fade in" role="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success! </strong>Your changes have been changed successfully.
      </div>
    <?php endif; ?>
    <?php if($show_change_error) : ?>
      <div class="alert alert-warning fade in" role="alert">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <i class="fa fa-3x fa-fw fa-user-times"></i><strong>Error! </strong><?php echo $fgmembersite->GetErrorMessage(); ?>
      </div>
    <?php endif; ?>
    <div class="row">
      <div class="col-md-12">
        <h2>Friendlist &nbsp;
          <i class="fa fa-fw fa-lg -circle fa-group pull-left"></i>
          <form id="search-form" action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset = 'UTF-8'>
            <button type="submit" id="add-friend-btn" style="background-color: Transparent; height: 37px; border: none; outline:none; cursoar:pointer;" class="pull-right"><i class="fa fa-fw fa-lg fa-plus-circle pull-right"></i></button>
            <input id="search-bar" type="text" name='friendEmail' class="pull-right">
            <i id="search-icon" class="-circle fa fa-fw fa-lg pull-right fa-search" onclick="show_bar()"></i>
          </form>
        </h2>
      </div>
    </div>
    <hr>
    <div class="section" style="height:200px;">
      <div class="container">
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-10" id= "friendtable">

          </div>
          <div class="col-md-1"></div>
        </div>
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
  <script>
    function getFriends() {
      $('#friendtable').load("getfriends.php?useremail=<?php echo $fgmembersite->UserEmail(); ?>&q="+$("#search-bar").val(), function (response, status, xhr) {
        if (status == "error") {
          var msg = "Sorry but there was an error: ";
          $("#friendtable").html(msg + xhr.status + " " + xhr.statusText);
        }
      });
    }

    $( document ).ready(function() {
      $("#search-bar").hide();

      window.addEventListener('click', function(e){   
        if (document.getElementById('search-icon').contains(e.target) ||document.getElementById('add-friend-btn').contains(e.target) || document.getElementById('search-bar').contains(e.target)){
            // Clicked in box
        } 
        else{
            // Clicked outside the box
            $("#search-bar").hide(400);
            $("#search-bar").val('');
            getFriends();
        }
      });

    });

    $("#search-bar").keyup(function(){
      getFriends();
    });

    function show_bar(){
      $("#search-bar").val('');
      $("#search-bar").show(400);
    }

    getFriends();
    setInterval(getFriends, 3000);
  </script>
</body></html>
