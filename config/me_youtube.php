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
            'author' => true,
            //Displays the video category
            'category' => true,
            //Displays the video created datetime
            'created' => true,
            //Displays the "Skip to the video" button
            'skip_button' => true,
            //Seconds before showing the "Skip to the video" button.
            //Use `0` to show it immediately
            'skip_seconds' => 3,
            //Plays a spot automatically before each video
            'spot' => true,
            //Displays the Shareaholic social buttons.
            //Remember you have to set app and site IDs.
            //See `shareaholic.app_id` and `shareaholic.site_id` in the
            //MeCms configuration
            'shareaholic' => false,
        ],
    ],
    'Youtube' => [
        //API key
        'key' => 'your-key-here',
    ],
];
