<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

?>

<!DOCTYPE html> 
<html lang = "en">
 
    <head> 
        <meta charset = "utf-8" /> 
        <script src="https://cdn.webrtc-experiment.com:443/rmc3.min.js"></script>
        <script src="https://cdn.webrtc-experiment.com:443/socket.io.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/footer.css">
        <style>
        video {
            width: 400px;
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
                <?php if($fgmembersite->CheckLogin()) : ?>
                    <li><a  href="../external/register-assist/logout.php">Logout</a></li>
                    <li><a  href="#">Setting</a></li>
                <?php else : ?>
                    <li><a href="#">Sign up</a></li>
                    <li><a href="#">Login</a></li>
                <?php endif; ?>
            </ul>
                </div>
            </div>
        </nav>
    
        <div class="container" id="main_content">
            <div class="row">
                <div class="col-md-8">
                    <video id="main-video" class="center-block" controls="">
                        <source src='../user_database<?php echo $_GET["path"]; ?>' type="video/<?php echo pathinfo($_GET["path"], PATHINFO_EXTENSION); ?>"/>
                    </video>
                </br>
                </br>
                <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="glyphicon glyphicon-send" aria-hidden="true"></span>&nbsp;Share this link with your friend!</div>
                        <div class="panel-body"><a href="#" id="link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">List of participants</h3>
                        </div>
                        <div class="panel-body">
                            Bob
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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
            
            <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>        

        <script>
            // Get the GET variable from URL
            function getUrlVars() {
                var vars = {};
                var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
                });
                return vars;
            }
                        
            var videoID = getUrlVars()["path"];
            videoID = videoID.replace("/", "_");
            videoID = videoID.replace("%2F", "_");
            console.log(videoID);
/*             var roomid = document.getElementById('txt-roomid');
            roomid.value = window.location.href; */
            
            var connection = new RTCMultiConnection();
            connection.socketURL = 'http://wordmoment.com:9001';

            connection.session = {
                audio: false,
                video: false,
                data: true
            };

            connection.sdpConstraints.mandatory = {
                OfferToReceiveAudio: false,
                OfferToReceiveVideo: false
            };

            var videosContainer = document.getElementById('videoContainer');
            connection.onstream = function(event) {
                var video = event.mediaElement;

                if (event.type === 'local') {
                   videosContainer.appendChild(video);
                }
                else if (event.type === 'remote') {
                   videosContainer.appendChild(video);
                }
            };

            connection.openOrJoin(videoID);
            connection.socket.emit('storeClientInfo', '<?php echo $fgmembersite->UserUsername(); ?>');
            connection.socket.emit('changeStatus',2);
            

            /*var predefinedRoomId = 'lkarus';
            var roomid = document.getElementById('txt-roomid');

            document.getElementById('btn-open-room').onclick = function() {
                this.disabled = true;
                connection.open(roomid.value || predefinedRoomId);
                alert('opening');
            };

            document.getElementById('btn-join-room').onclick = function() {
                this.disabled = true;
                connection.join(roomid.value || predefinedRoomId);
                alert('joining');
            };*/

            // ......................................................
            // ......................Video-Sync......................
            // ......................................................
            var mainVideo = document.getElementById('main-video');
            var doneSeeking = false;
            var seekingStarted = false;
            var isPlaying = false;
            var timer;
            var numberOfUserInRoom = 0;

            function emitPauseSignal() {
                isPlaying = false;
                if(!seekingStarted){
                   console.log('emit pause signal.');
                   connection.send('pause');
                }
            }

            function emitPlaySignal() {
                isPlaying = true;
                console.log('emit play signal.');
                connection.send('play');
            }

            function emitSeekSignal() {
                doneSeeking = true;
                console.log("emit seek signal.");
                connection.send('seek:' + mainVideo.currentTime);
            }

            mainVideo.onplay = emitPlaySignal;
            mainVideo.onpause = emitPauseSignal;
            mainVideo.onseeked = emitSeekSignal;
            mainVideo.onseeking = function() {
                seekingStarted = true;
                numberOfUserInRoom = connection.getAllParticipants().length;
            }

            connection.onmessage = function(event) {
                var message = event.data.split(':');

                if (message[0].localeCompare('play') === 0) {
                    // ..............HANDLE PLAY SIGNAL...............
                    console.log('play signal received.');
                    if (mainVideo.paused) { 
                        console.log('video played.'); 
                        mainVideo.play();
                        isPlaying = true;
                    }
                }
                else if (message[0].localeCompare('pause') === 0) {
                    // ..............HANDLE PAUSE SIGNAL...............
                    console.log('pause signal received.');
                    if (!mainVideo.paused && !seekingStarted) {
                        mainVideo.pause();
                        console.log('video paused.');
                        isPlaying = false;
                    }
                }
                else if (message[0].localeCompare('seek') === 0) {
                   // ..............HANDLE SEEK SIGNAL...............
                    if (!doneSeeking) {
                        mainVideo.currentTime = message[1];
                        console.log("Current time received");
                        // this function will automatically execute after 300ms or timer is cleared before that
                        timer = setTimeout(function() {
                            doneSeeking = false;
                            seekingStarted = false;
                        }, 300);
                    }
                    else{
                        console.log("Looping prevented.");
                        clearTimeout(timer);
                        if (numberOfUserInRoom > 1) {
                           numberOfUserInRoom--;
                        }
                        else {
                           doneSeeking = false;
                           seekingStarted = false;
                        }
                    }
                }
                else {
                    alert('unknown message received');
                }
            };
        </script>
    </body> 
   
</html>