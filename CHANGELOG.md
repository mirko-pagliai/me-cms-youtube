# 2.x branch
## 2.4 branch
### 2.4.0
* added action to list videos by month (year and month) and by year;
* added "videos by month" widget;
* `Videos::categories` and `Videos::months` widgets can render as form or list;
* renamed `description` field as `text`;
* `index_by_date` action renamed as `index_by_day`;
* added `youtube_url` virtual field;
* some fixes for MeCms 2.8.2.

## 2.3 branch
### 2.3.6
* fixed bug viewing a category.

### 2.3.5
* added specific methods for previews. This improves the code;
* fixed some labels (scheduled/spot).

### 2.3.4
* added some buttons for backend;
* some fixed for MeCms 2.7.3.

### 2.3.3
* fixed titles.

### 2.3.2
* logged users can view future videos and drafts;
* improved the code to check the cache validity. Removed `checkIfCacheIsValid()` and `getNextToBePublished()` methods;
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
* it checks whether the information of a video are present. This avoids post a private video;
* updated to CakePHP 3.2.

### 2.1.7
* fixed bug in "videos categories" widget;
* at the end of a video, related videos are not shown;
* widgets now use a common view. Rewritten the code of all widgets.

### 2.1.6
* you can disable the "Skip to the video" button or set the number of seconds before it appears;
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
* many buttons are disabled after the click, to prevent some actions are performed repeatedly;
* added the changelog file.