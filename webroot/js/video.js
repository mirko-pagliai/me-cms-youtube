/*!
 * This file is part of MeYoutube.
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
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
		playerVars: {
			autoplay: $('#player').data('autoplay'),
			showinfo: 0
		},
		events: {
			'onStateChange': onPlayerStateChange
		}
	});
}

//The API calls this function when the player's state changes
//See https://developers.google.com/youtube/iframe_api_reference#Events
var done = false;
function onPlayerStateChange(event) {
	//When the video is finished, if appends the second video, if it exists
	if(event.data == YT.PlayerState.ENDED && secondVideo !== undefined && !done) {
		done = true;
		//See https://developers.google.com/youtube/iframe_api_reference#loadVideoById
		player.loadVideoById(secondVideo, 0);
	}
}