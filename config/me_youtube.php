<?php
/**
 * Before using the plugin, you have to get the API key:
 * https://developers.google.com/youtube/registering_an_application
 */

return [
    'MeYoutube' => [
        //Youtube videos
        'video' => [
            //Displays the video author
            'author' => TRUE,
            //Displays the video category
            'category' => TRUE,
            //Displays the video created datetime
            'created' => TRUE,
            //Displays the "Skip to the video" button
            'skip_button' => TRUE,
            //Seconds before showing the "Skip to the video" button.
            //Use `0` to show it immediately
            'skip_seconds' => 3,
            //Plays a spot automatically before each video
            'spot' => TRUE,
            //Displays the Shareaholic social buttons.
            //Remember you have to set app and site IDs.
            //See `shareaholic.app_id` and `shareaholic.site_id` in the 
            //MeCms configuration
            'shareaholic' => FALSE,
        ],
    ],
    'Youtube' => [
        //API key
        'key' => 'your-key-here'
    ],
];