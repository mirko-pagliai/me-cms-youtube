<?php
return ['MeYoutube' => [
	//Videos
	'video' => [
		//"Show" options
		'show' => [
			//Displays the post author
			'author' => TRUE,
			//Displays the post category
			'category' => TRUE,
			//Displays the post created datetime
			'created' => TRUE,
			//Plays a spot automatically before each video
			'spot' => TRUE,
			//Displays the Shareaholic social buttons
			//Remember you have to set app and site IDs. See `shareaholic.app_id` and `shareaholic.site_id` in the MeCms configuration
			'shareaholic' => FALSE
		]
	]
]];