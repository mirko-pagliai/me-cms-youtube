# 2.x branch
## 2.13 branch
### 2.13.0
* `MeCmsYoutube\Model\Entity\Video::_getPreview()` method returns an `Entity`
    with `url`, `width` and `height` properties.

## 2.12 branch
### 2.12.3-RC3
* updated for MeCms 2.22.6-RC3.

### 2.12.2-RC2
* `Video` entity has the `_getText()` mutator;
* updated for MeCms 2.22.5-RC2.

### 2.12.1-RC1
* when the search function is used, the searched text is highlighted;
* updated for MeCms 2.22.4-RC1.

### 2.12.0-beta
* fixed all templates for Bootstrap 4;
* `MenuHelper` class returns menus as arrays, without transforming them into
    html.

## 2.11 branch
### 2.11.0
* updated for CakePHP 3.5 and MeCms 2.21.

## 2.10 branch
### 2.10.6
* updated for MeCms 2.20.1.

### 2.10.5
* added "activeSpot" find method;
* added initial schema of the plugin database. Removed `UpdateShell` class;
* the MIT license has been applied;
* significantly improved all tests.

### 2.10.4
* fixed bug for widgets: they do not show anything if there are no records.

### 2.10.3
* updated for MeCms 2.19.1.

### 2.10.2
* updated for MeCms 2.19.0.

### 2.10.1
* updated for MeCms 2.18.2.

### 2.10.0
* `VideosTable::getRandomSpots()` now returns a `Collection` instance;
* updated for MeCms 2.18.0;
* added tests for all admin classes.

## 2.9 branch
### 2.9.3
* widgets (cells) use collections;
* tests that require a network connection have been marked with the
    `requireNetwork` group;
* added tests for `beforeFilter()` and `initialize()` methods of all admin
    controllers.

### 2.9.2
* fixed `VideosController::indexByDate()` method;
* fixed the `findActive()` method for `VideosCategoriesTable`;
* added tests for `VideosController` and `VideosCategoriesController` classes.

### 2.9.1
* fixed the `Categories` alias for `Videos` table;
* fixed bug, the `created` field is formatted correctly in `edit` templates.

### 2.9.0
* added `og:image:width` and `og:image:height` meta tags for previews;
* the `preview` property now contains url, width and height of the preview;
* updated for MeCms 2.17.0.

## 2.8 branch
### 2.8.1
* fixed bug for the duration of video with 0 seconds (eg. `PT4M`);
* fixed bug https://github.com/cakephp/cakephp/pull/10417;
* improved the recovery of the information during the addition of a video;
* updated for MeCms 2.16.1;
* added tests for `AppController` class;
* added tests for `isAuthorized()` method of all admin controllers.

### 2.8.0
* the cells that act as widgets now have "Widgets" in the name, for the classes
    and the template directory;
* some minor improvements for table classes;
* updated for CakePHP 3.4.

## 2.7 branch
### 2.7.4
* the whole of the widget code has been rewritten and improved, making it more
    uniform and consistent;
* removed `Videos::random()` widget, it did not make much sense;
* added tests for `AppView` and `VideosCell` classes.

### 2.7.3
* fixed (perhaps forever...) bug for sorting records in the admin panel.

### 2.7.2
* fixed bug for sorting records in the admin panel;
* added missing validation rules;
* added tests for all validation classes.

### 2.7.1
* fixed bug: now seconds and duration of the video are always added by the 
    `beforeSave()` method;
* some fixes for MeCms 2.14.12;
* added tests for `Sitemap` class;
* added tests for all entity and all tables classes.

### 2.7.0
* `Youtube` class does not contain more static methods;
* `Youtube::getInfo()` method returns an object;
* fixed a bug with videos longer than an hour;
* some fixes for MeCms 2.14.11;
* renamed repository and package. Now is `me-cms-youtube`;
* added test for `Youtube` and `InstallShell` classes.

## 2.6 branch
### 2.6.7
* updated for MeCms 2.14.10.

### 2.6.6
* to generate thumbnails, uses the `fit()` method instead of `crop()`.

### 2.6.5
* some fixed for MeCms 2.14.5.

### 2.6.4
* updated for MeCms 2.14.4.

### 2.6.3
* updated for Assets 1.1.0.

### 2.6.2
* updated for MeTools 2.10.0.

### 2.6.1
* fixed bug adding videos;
* improved admin routes. They are automatically handled by CakePHP;
* updated for MeCms 2.13.1.

### 2.6.0
* filter forms can now use records ID;
* checks if there are already routes with the same name, before declaring new;
* fixed code for CakePHP Code Sniffer;
* updated for CakePHP 3.3;
* improved routes. Now `DashedRoute` is the default route class.

## 2.5 branch
### 2.5.1
* admin indexes display ID for all elements;
* fixed bug for rss layout.

### 2.5.0
* some fixes for MeCms 2.12.0.

## 2.4 branch
### 2.4.4
* added breadcrumb.

### 2.4.3
* added links on userbar for videos categories;
* fixed messages pluralized;
* strings to be translated were defined and simplified;
* fixed cache code for widgets.

### 2.4.2
* the code to list videos by date has been greatly improved and simplified;
* some fixes for MeCms 2.10.0.

### 2.4.1
* fixed serious bug on the created date of objects when editing.

### 2.4.0
* added action to list videos by month (year and month) and by year;
* added "videos by month" widget;
* `Videos::categories` and `Videos::months` widgets can render as form or list;
* renamed `description` field as `text`;
* fixed bug on category view.
* `index_by_date` action renamed as `index_by_day`;
* added `youtube_url` virtual field;
* some fixes for MeCms 2.8.2.

## 2.3 branch
### 2.3.5
* added specific methods for previews. This improves the code;
* fixed some labels (scheduled/spot).

### 2.3.4
* added some buttons for backend;
* some fixes for MeCms 2.7.3.

### 2.3.3
* fixed titles.

### 2.3.2
* logged users can view future videos and drafts;
* improved the code to check the cache validity. Removed 
	`checkIfCacheIsValid()` and `getNextToBePublished()` methods;
* updated Facebook's tags.

### 2.3.1
* added userbar for frontend. It allows to edit an delete videos.

### 2.3.0
* now the sitemap uses the cache and handles `lastmod` and `priority` values;
* now videos categories have "created" and "modified" fields;
* rewrote the code to generate the backend menus.

## 2.2 branch
### 2.2.1
* added functions to generate the site sitemap.

### 2.2.0
* the API key has moved to `me_youtube.php`. Removed `youtube_keys.php`;
* the code for loading the configuration files has been optimized.

## 2.1 branch
### 2.1.11
* fixed a lot of little bugs and codes.

### 2.1.10
* updated for MeCms.

### 2.1.9
* updated for MeCms.

### 2.1.8
* it checks whether the information of a video are present. This avoids post 
	a private video;
* updated to CakePHP 3.2.

### 2.1.7
* fixed bug in "videos categories" widget;
* at the end of a video, related videos are not shown;
* widgets now use a common view. Rewritten the code of all widgets.

### 2.1.6
* you can disable the "Skip to the video" button or set the number of seconds 
	before it appears;
* added the "video fake" functionality;
* added routes for "videos of today" and "videos of yesterday".

### 2.1.5
* an exception is now properly thrown when a record is not found.

### 2.1.4
* added "random videos" widget;
* added the Install shell.

### 2.1.3
* added backward compatibility for old URLs.

### 2.1.2
* fixed bug for sorting some tables;
* improved queries for filters;
* fixed some bugs;
* small fixes for MeCms 2.1.3.

### 2.1.1
* fixed a serious bug when trying to re-sort the results of paginated records;
* small fixes for MeCms 2.1.2;
* small improvements for display on mobile devices.

## 2.0 branch
### 2.0.5-RC5
* added "Skip to the video" button;
* fixed little bugs.

### 2.0.4-RC4
* spots are automatically played before video;
* the video preview is shown when adding or editing a video;
* the duration of the video is saved in the database;
* the Youtube API are used now.

### 2.0.3-RC3
* in the admin panel, some views have been linked together;
* fixed the title of some actions.

### 2.0.2-RC2
* now you can list videos by date;
* added support for Shareaholic;
* small fixes for MeCms 2.1.0-RC2.

### 2.0.1-RC1
* small fixes for MeCms 2.0.1-RC1.

# 1.x branch
## 1.1 branch
### 1.1.2
* small fixes for MeCms 1.2.1.

### 1.1.1
* support for videos with `youtube.be` address;
* many buttons are disabled after the click, to prevent some actions are 
	performed repeatedly;
* added the changelog file.
