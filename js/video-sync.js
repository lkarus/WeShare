// Get the GET variable from URL
function getUrlVars() {
  var vars = {};
  var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
    vars[key] = value;
  });
  return vars;
}

/* WebRTC connection establishment */
var connection = new RTCMultiConnection({
  useDefaultDevices: true
});
connection.socketURL = 'https://wordmoment.com:9002';

connection.socketMessageEvent = 'audio-conference-demo';
connection.session = {
  audio: false,
  video: false,
  data: true
};
connection.sdpConstraints.mandatory = {
  OfferToReceiveAudio: false,
  OfferToReceiveVideo: false
}; 

connection.mediaConstraints.video = false;
connection.mediaConstraints.audio = {
mandatory: {
    googEchoCancellation: true,
    googAutoGainControl: true,
    googNoiseSuppression: true,
    googHighpassFilter: true,
    googTypingNoiseDetection: true,
    googAudioMirroring: true
  },
  optional: []
};

connection.audiosContainer = document.getElementById('audios-container');
connection.onstream = function(event) {
  var width = parseInt(connection.audiosContainer.clientWidth / 2) - 20;
  var mediaElement = getMediaElement(event.mediaElement, {
    title: event.userid,
    buttons: ['full-screen'],
    width: width,
    showOnMouseEnter: false
  });
  connection.audiosContainer.appendChild(mediaElement);
  setTimeout(function() {
    mediaElement.media.play();
  }, 5000);
  
  mediaElement.id = event.streamid;
};

connection.onstreamended = function(event) {
  var mediaElement = document.getElementById(event.streamid);
  if(mediaElement) {
    mediaElement.parentNode.removeChild(mediaElement);
  }
};

var YTVideoURL = getUrlVars()["yt"];
var videoID = YTVideoURL.split('v%3D')[1];

connection.openOrJoin(videoID);

// This code loads the IFrame Player API code asynchronously.
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// This function creates an <iframe> (and YouTube player)
// after the API code downloads.
var player;
function onYouTubeIframeAPIReady() {
  player = new YT.Player('player', {
          height: '100%',
          width: '100%',
          videoId: videoID,
          playerVars: { 'autoplay': 0, 'controls': 1 },
          events: {
              'onReady': onPlayerReady,
              'onStateChange': onPlayerStateChange
          }
  });
}

// 4. The API will call this function when the video player is ready.
function onPlayerReady(event) {
    // event.target.getIframe().className += "embed-responsive embed-responsive-16by9";
}
// declaring necessary variables for synchronization
var userName = $("#username").text();
console.log(userName);
var blockPlaySignal = false;    // turned ON to block the next play signal to be sent
var blockPauseSignal = false;   // turned ON to block the next pause signal to be sent
var blockBufferSignal = false;  // turned ON to block the next buffering signal to be sent
var waitingNumber = 0;          // number of participants in the room excluding user
var remoteHolder = 'none';      // the one who initiate the play signal

function onPlayStatus() {
    if (connection.getAllParticipants().length > 0) {   // you are not alone
        if (!blockPlaySignal) {
            console.log("on playing");
            waitingNumber = connection.getAllParticipants().length;
            console.log("waiting number updated: " + waitingNumber);

            // blockPauseSignal is turned ON
            // pause the video to wait for synchronzation
            blockPauseSignal = true;
            console.log("blockPauseSignal changed to TRUE");
            player.pauseVideo();

            // update remoteHolder
            remoteHolder = userName;

            // send play signal
            connection.send({type:'initiator', message:'play', sender: userName, seekTime: player.getCurrentTime()});
            console.log('play sent');
        }         
        else {  // blockPlaySignal is ON
            console.log("Play signal blocked, blockPlaySignal to FALSE");
            blockPlaySignal = false;
        }   
    }
    else {  // no other users
        console.log(connection.getAllParticipants().length);
        console.log("on playing");
    }
}

function onPauseStatus() {
    if (connection.getAllParticipants().length > 0) {   // you are not alone
        if (!blockPauseSignal) {
            // send pause signal
            console.log("on pause");
            connection.send({type:'signal', message:'pause', sender:'none'});
            console.log("pause sent");
        }
        else {  // blockPauseSignal is ON
            console.log("pause signal blocked, blockPauseSignal to FALSE");
            blockPauseSignal = false;
        }
    }
    else {  // no other users
        console.log("on pause. no other users.");
    }
}

function onBufferStatus() {
    console.log('buffering at ' + player.getCurrentTime());
    if (connection.getAllParticipants().length > 0) {   // you are not alone
        if (!blockBufferSignal) {
            console.log("Buffering");
            connection.send({type:'signal', message:'pause', sender:'none'});
            console.log("sent buffer signal with current time.");
        }
        else {  // blockBufferSignal is ON
            console.log("buffering signal blocked, blockBufferSignal to FALSE");
            blockBufferSignal = false;
        }
    }
    else {  
        // no other users
        console.log("buffering. no other users.");
    }
}

// 5. The API calls this function when the player's state changes.
function onPlayerStateChange(event) {
    var playerStatus = event.data;
    switch (playerStatus) {
        case -1:    // unstarted
            console.log("player unstarted.");
            // block buffering signal sent at the beginning
            blockBufferSignal = true;
            break;
        case 0:     // ended
            console.log("player ended.");
            break;
        case 1:     // playing
            onPlayStatus();
            break;
        case 2:     // paused
            onPauseStatus();
            break;
        case 3:     // buffering
            onBufferStatus();
            break;
        case 5:     // cued, ready to play 
            console.log("player cued.");
            break;
        default:
            console.log("undefined player status: " + playerStatus);
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
async function playHandle(time) {
    console.log('play received');
    blockPlaySignal = true;
    console.log('blockPlaySignal to TRUE');
    blockBufferSignal = true;
    console.log('blockBufferSignal to TRUE');
    player.seekTo(time)
    player.playVideo();
    var playerState = player.getPlayerState();
    while (playerState != 1) {
        console.log('current state is now : ' + playerState);
        await sleep(500);
        playerState = player.getPlayerState();
    }
    blockPauseSignal = true;
    console.log('blockPauseSignal to TRUE');
    player.pauseVideo();
    connection.send({type:'signal', message:'playable', sender:userName});
    console.log('playable sent');
}

// ==========================================================
// Called when receive playable signal from another user.   =
// ========================================================== 
function playableHandle() {
    console.log(Date.now() + ": playable received");
    waitingNumber = waitingNumber - 1;
    if (waitingNumber == 0) {
        connection.send({type:'signal', message:'sync', sender:userName});
        console.log('sync sent')
        blockPlaySignal = true;
        console.log('blockPlaySignal to TRUE');
        player.playVideo();
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
    console.log('blockPauseSignal to TRUE');
    player.pauseVideo();
}

function syncHandle() {
    console.log('sync received');
    blockPlaySignal = true;
    console.log('blockPlaySignal to TRUE');
    player.playVideo();
    remoteHolder = 'none';
}

function bufferHandle(time) {
    console.log('buffer received');
    blockBufferSignal = true;
    console.log('blockBufferSignal to TRUE');
    player.seekTo(time);
}

async function refreshHandle(time) {

    // play the video in case this player has not started yet.
    blockPlaySignal = true;
    player.playVideo();
    while (playerState != 1) {
        console.log('current state is now : ' + playerState);
        await sleep(500);
        playerState = player.getPlayerState();
    }

    blockPauseSignal = true;
    player.pauseVideo();

    // disable control to wait for everyone to sync again
    ;

    blockPlaySignal = false;    // turned ON to block the next play signal to be sent
    blockPauseSignal = false;   // turned ON to block the next pause signal to be sent
    blockBufferSignal = false;  // turned ON to block the next buffering signal to be sent
    remoteHolder = 'none';      // the one who initiate the play signal

    blockBufferSignal = true;
    player.seekTo(time);

    // send refreshed signal
    connection.send({type:'signal', message:'refreshed', sender: userName});
    console.log('refreshed sent');
}

function refreshEDHandle() {
    waitingNumber = waitingNumber - 1;
    if (waitingNumber == 0) {
        connection.send({type:'signal', message:'unlock', sender:userName});
        console.log('unlock sent');
        // unlock control of this player
        ;
    }
    else {
        console.log("not all are refreshed. waiting number is " + waitingNumber);
    }
}

function unlockControl() {
    // unlock the control
    ;
}

connection.onmessage = function(event) {
    var sender = event.data.sender;
    var type = event.data.type;
    var message = event.data.message;
    
    console.log("message from " + sender + " remoteHolder: " + remoteHolder);
    if (remoteHolder.localeCompare(userName) === 0) {
        // you are the remoteHolder
        console.log("i am the remote holder");
        if (type.localeCompare("signal") === 0) {
            // signal message
            if (message.localeCompare("play") === 0) {  // receive play signal
                playHandle(event.data.seekTime);
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
            else if (message.localeCompare("buffer") === 0) {
                bufferHandle(event.data.seekTime);
            }
            else {
                console.log('received unidentified signal');
            }
        }
        else if (type.localeCompare("initiator") === 0) {
            // initiator message
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
                playHandle(event.data.seekTime);
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
            else if (message.localeCompare("buffer") === 0) {
                bufferHandle(event.data.seekTime);
            }
            else if (message.localeCompare("refresh") === 0) {
                refreshHandle();
            }
            else if (message.localeCompare("refreshed") === 0) {
                refreshEDHandle();
            }
            else if (message.localeCompare("unlock") === 0) {
                unlockControl();
            }
            else {
                console.log('received unidentified signal');
            }
        }
        else if (type.localeCompare("initiator") === 0) {
            remoteHolder = sender;
            console.log("remoteHolder is now " + remoteHolder);
            if (message.localeCompare("play") === 0) {  // receive play signal
                playHandle(event.data.seekTime);
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

function refreshSync() {
    if (connection.getAllParticipants().length > 0) {   // you are not alone
        player.pauseVideo();

        blockPlaySignal = false;    // turned ON to block the next play signal to be sent
        blockPauseSignal = false;   // turned ON to block the next pause signal to be sent
        blockBufferSignal = false;  // turned ON to block the next buffering signal to be sent
        remoteHolder = 'none';      // the one who initiate the play signal

        // send refresh signal
        connection.send({type:'signal', message:'refresh', sender: userName, seekTime: player.getCurrentTime()});
        console.log('refresh sent');

        // disable control to wait for everyone to sync again
        ;

        waitingNumber = connection.getAllParticipants().length;
    }
    else {  // no other users
        console.log("refreshSync: you are alone!");
    }
}