/*!
 * This file is part of MeYoutube.
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @see			https://developers.google.com/youtube/iframe_api_reference
 */
//If there's a spot, then the first video is the spot
if($('#player').data('spot') !== undefined) {
	var firstVideo = $('#player').data('spot');
	var secondVideo = $('#player').data('id');
}
else
	var firstVideo = $('#player').data('id');

//"Skip to the video" button
var skipToVideo = $('#skip-to-video');

//See https://developers.google.com/youtube/iframe_api_reference#Getting_Started
//Loads the IFrame Player API code asynchronously
var tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

//Creates an <iframe> (and YouTube player) after the API code downloads
//See https://developers.google.com/youtube/iframe_api_reference#Loading_a_Video_Player
var player;
function onYouTubeIframeAPIReady() {
	player = new YT.Player('player', {
		height: '390',
		width: '640',
		videoId: firstVideo,
		//For "playerVars", see https://developers.google.com/youtube/player_parameters?playerVersion=HTML5#Parameters
		playerVars: {
			autoplay: $('#player').data('autoplay'),
			rel: 0,
			showinfo: 0
		},
		events: {
			'onReady': onPlayerReady,
			'onStateChange': onPlayerStateChange
		}
	});
}

//The API will call this function when the video player is ready.
function onPlayerReady(event) {
	//Shows the "Skip to the video" button
	if(secondVideo !== undefined && skipToVideo.length)
		setTimeout(function() {
			skipToVideo.fadeIn('fast');
		}, skipToVideo.data('secs')*1000);
}

//The API calls this function when the player's state changes
//See https://developers.google.com/youtube/iframe_api_reference#Events
var done = false;
function onPlayerStateChange(event) {
	//When the video is finished, plays the second video
	if(event.data == YT.PlayerState.ENDED)
		playNextVideo();
}

//Function to play the second video
function playNextVideo() {
	//Appends and plays the second video, if it exists
	if(secondVideo !== undefined && !done) {
		done = true;
		//See https://developers.google.com/youtube/iframe_api_reference#loadVideoById
		player.loadVideoById(secondVideo, 0);

		//Removes the "Skip to the video" button
		skipToVideo.remove();
	}
}

//On click on the "Skip to the video" butto, plays the second video
skipToVideo.click(function() {
	playNextVideo();
});