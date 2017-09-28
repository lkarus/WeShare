<?PHP
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("../");
    exit;
}

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
            width: 600px;
        }
        video::-internal-media-controls-download-button {
            display:none;
        }

        video::-webkit-media-controls-enclosure {
            overflow:hidden;
        }

        video::-webkit-media-controls-panel {
            width: calc(100% + 30px); /* Adjust as needed */
        }
        </style>
    </head>
   
    <body> 
        <h1 id="username" style="display:none"><?php echo $fgmembersite->UserUsername(); ?></h1>
        
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
                    <video id="main-video" class="center-block" controls="" preload='auto'>
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
            videoID = videoID.replace(/\//g, "_");
            videoID = videoID.replace(/%2F/g, "_");
            console.log(videoID);
/*             var roomid = document.getElementById('txt-roomid');
            roomid.value = window.location.href; */
            
            var connection = new RTCMultiConnection();
            connection.socketURL = 'http://wordmoment.com:9001/';

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

            // ......................................................
            // ......................Video-Sync......................
            // ......................................................
            var mainVideo = document.getElementById('main-video');
            var userName = $("#username").text();
            console.log(userName);

            var wantedState = 0;    // 1 for PLAY; 2 for PAUSE
            var blockPlaySignal = false;    // turned ON to block the next play signal to be sent
            var blockPauseSignal = false;   // turned ON to block the next pause signal to be sent
            var blockSeekSignal = false;   // turned ON to block the next seek signal to be sent
            var waitingNumber = 0;

            var remoteHolder = 'none';

            /*connection.extra = {
                remoteHolder: 'none'
            }

            connection.onExtraDataUpdated = function(event) {
                var extraInformation = event.extra;
                console.log("attempt updating extra to " + extraInformation.remoteHolder);

                if (typeof extraInformation.remoteHolder === "undefined") {
                    console.log('Extra update: dont do anything because value is undefined');
                }
                else if (extraInformation.remoteHolder === connection.extra.remoteHolder) {
                    console.log('Extra update: dont do anything because value is the same');
                }
                else {
                    connection.extra.remoteHolder = extraInformation.remoteHolder;
                    console.log('extra updated : ' + connection.extra.remoteHolder);
                    
                    if (connection.extra.remoteHolder.localeCompare("none") === 0) {
                        mainVideo.controls = true;
                    }
                    else {
                        mainVideo.controls = false;
                    }
                }
            }*/

            mainVideo.onplaying = function() {
                if (connection.getAllParticipants().length > 0) {   // you are not alone
                    if (!blockPlaySignal) {
                        console.log("on playing");
                        waitingNumber = connection.getAllParticipants().length;
                        console.log("waiting number updated: " + waitingNumber);

                        // blockPauseSignal is turned ON
                        // pause the video to wait for synchronzation
                        blockPauseSignal = true;
                        console.log("blockPauseSignal changed to TRUE");
                        pauseVideo();

                        // update remoteHolder
                        /*connection.extra.remoteHolder = userName;
                        connection.updateExtraData();*/
                        remoteHolder = userName;

                        // send play signal
                        connection.send({type:'initiator', message:'play', sender: userName});
                        console.log('play sent');
                        wantedState = 1;    
                    }         
                    else {  // blockPlaySignal is ON
                        console.log("Play signal blocked");
                        blockPlaySignal = false;
                    }   
                }
                else {  // no other users
                    console.log(connection.getAllParticipants().length);
                    console.log("on playing");
                }
            };

            mainVideo.onpause = function() {
                if (connection.getAllParticipants().length > 0) {   // you are not alone
                    if (!blockPauseSignal) {
                        // send pause signal
                        console.log("on pause");
                        connection.send({type:'signal', message:'pause', sender:'none'});
                        console.log("pause sent");
                    }
                    else {  // blockPauseSignal is ON
                        console.log("pause signal blocked");
                        blockPauseSignal = false;
                    }
                }
                else {  // no other users
                    console.log("on pause. no other users.");
                }
            };

            mainVideo.onseeked = function() {
                if (connection.getAllParticipants().length > 0) {   // you are not alone
                    if (!blockSeekSignal) {
                        // send pause signal
                        console.log("on seeked");
                        connection.send({type:'signal', message:'seek', sender: userName, seekTime: mainVideo.currentTime});
                        console.log("seek sent");
                    }
                    else {  // blockPauseSignal is ON
                        console.log("seek signal blocked");
                        blockSeekSignal = false;
                    }
                }
                else {  // no other users
                    console.log("on seek. no other users.");
                }
            };

            mainVideo.onwaiting = function() {
                console.log("waiting");
                connection.send({type:'signal', message:'pause', sender:'none'});
                console.log(Date.now() + ": pause sent signal waiting for loading");
            };

            // ==============================================
            // Play the video if it is currently paused.    =
            // Do nothing otherwise.                        =
            // ==============================================

            function playVideo() {
                if (mainVideo.paused) {
                    console.log(Date.now() + ": play video");
                    mainVideo.play();
                    
                }
                else {
                    console.log("video is already playing.");
                }
            }

            // ==============================================
            // Pause the video if it is currently playing.  =
            // Do nothing otherwise.                        =
            // ==============================================

            function pauseVideo() {
                if (!mainVideo.paused) {
                    mainVideo.pause();
                    console.log("pause video");
                }
                else {
                    console.log("video is already paused.");
                }
            }

            // ==============================================
            // Pause the program for ms milliseconds        =
            // Called in playHandle.                        =
            // ==============================================

            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            // ==========================================================
            // Called when receive play signal from another user.       =
            // Wait until the video have enough data to start playing.  =
            // Play the video, send sync signal back to that user.      =
            // Change handlingRequest back to FALSE.                    =
            // ==========================================================

            async function playHandle() {
                console.log('play received');
                while (mainVideo.readyState != 4) {
                    console.log('ready state is now : ' + mainVideo.readyState);
                    await sleep(3000);
                }
                blockPlaySignal = true;
                playVideo();
                blockPauseSignal = true;
                pauseVideo();
                connection.send({type:'signal', message:'playable', sender:userName});
                console.log('synced sent');
            }

            // ==========================================================
            // Called when receive playable signal from another user.   =
            // ========================================================== 

            function playableHandle() {
                console.log(Date.now() + ": playable received");
                waitingNumber = waitingNumber - 1;
                if (waitingNumber == 0) {
                    connection.send({type:'signal', message:'sync', sender:userName});
                    blockPlaySignal = true;
                    playVideo();
                    remoteHolder = 'none';
                }
                else {
                    console.log("not yet sync. waiting number is " + waitingNumber);
                }
                console.log('done handling sync');
            }

            function pauseHandle() {
                console.log('pause received');
                blockPauseSignal = true;
                pauseVideo();
            }

            function syncHandle() {
                console.log('sync received');
                blockPlaySignal = true;
                playVideo();
                mainVideo.controls = true;
                remoteHolder = 'none';
            }

            function seekHandle(time) {
                console.log('seek received');
                blockSeekSignal = true;
                mainVideo.currentTime = time;
            }

            connection.onmessage = function(event) {
                var sender = event.data.sender;
                var type = event.data.type;
                var message = event.data.message;
                // console.log("event.extra = " + event.extra.remoteHolder);
                console.log("message from " + sender + " remoteHolder: " + remoteHolder);
                // console.log(sender + type + message);

                if (remoteHolder.localeCompare(userName) === 0) {
                    // you are the remoteHolder
                    console.log("i am the remote holder");
                    if (type.localeCompare("signal") === 0) {
                        if (message.localeCompare("play") === 0) {  // receive play signal
                            playHandle();
                        }
                        else if (message.localeCompare("playable") === 0) { // receive playable signal
                            playableHandle();
                        }
                        else if (message.localeCompare("pause") === 0) {    // receive pause signal
                            pauseHandle();
                        }
                        else if (message.localeCompare("sync") === 0) {     // receive sync signal
                            syncHandle();
                        }
                        else {
                            console.log('received unidentified signal');
                        }
                    }
                    else if (type.localeCompare("initiator") === 0) {
                        console.log("im not supposed to receive an initiator");
                    }
                    else {
                        console.log('not a signal');
                    }
                }
                else if (remoteHolder.localeCompare("none") === 0
                    || sender.localeCompare(remoteHolder) === 0) {
                    // no remoteHolder or the message is from the remoteHolder
                    console.log("Valid message from remote holder");
                    if (type.localeCompare("signal") === 0) {
                        if (message.localeCompare("play") === 0) {  // receive play signal
                            playHandle();
                        }
                        else if (message.localeCompare("playable") === 0) { // receive playable signal
                            playableHandle();
                        }
                        else if (message.localeCompare("pause") === 0) {    // receive pause signal
                            pauseHandle();
                        }
                        else if (message.localeCompare("sync") === 0) {     // receive sync signal
                            syncHandle();
                        }
                        else if (message.localeCompare("seek") === 0) {     // receive seek signal
                            seekHandle(event.data.seekTime);
                        }
                        else {
                            console.log('received unidentified signal');
                        }
                    }
                    else if (type.localeCompare("initiator") === 0) {
                        remoteHolder = sender;
                        mainVideo.controls = false; // disable control
                        console.log("remoteHolder is now " + remoteHolder);
                        if (message.localeCompare("play") === 0) {  // receive play signal
                            playHandle();
                        }
                        else {
                            console.log('received unidentified signal');
                        }
                    }
                    else {
                        console.log('not a signal');
                    }
                }
                else {
                    console.log('message not from remote holder');
                }
            }
        </script>
    </body> 
   
</html>