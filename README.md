# Youtube plugin for MeCms

[![Build Status](https://travis-ci.org/mirko-pagliai/me-youtube.svg?branch=master)](https://travis-ci.org/mirko-pagliai/me-youtube)

*me-cms-youtube* plugin allows you to manage Youtube videos with 
[//github.com/mirko-pagliai/cakephp-for-mecms](MeCms platform).

To install:

    $ composer require --prefer-dist mirko-pagliai/me-youtube
    $ bin/cake me_youtube.install all -v

Then you need to get an 
[API key for Youtube](//developers.google.com/youtube/registering_an_application) 
and edit `APP/config/youtube_keys.php`.

For widgets provided by this plugin, see 
[here](//github.com/mirko-pagliai/me-youtube/wiki/Widgets).

## Versioning
For transparency and insight into our release cycle and to maintain backward 
compatibility, MeYoutube will be maintained under the 
[Semantic Versioning guidelines](http://semver.org).