# Social Network Script

**Name:** Social Network Script <br/>
**Contributors:** Akintunde Jegede <br/>
**Programmed with:** PHP<br/>
**Requires at least:** php 4.* <br/>
**Major Dependencies:** bootstrap & jquery<br/>
**License:** APACHE LICENSE <br/>
**License URI:** https://www.apache.org/licenses/LICENSE-2.0 <br/>
**Live Demo Address:** quotes.akin.com.ng <br/>


## Short Summary:
This script is a multifunctional blogging + Social Networking script that has almost all the features of a social network


## Description:
This script is a social network script that is capable of allowing text and image posts from users that are registered and verified by mail. It has an automatic text to image conversion  system  that allows you to choose from quite a large store of backgrounds picture, it also allows you to use your own custom background. The script also features a strong notification system that alerts you of activities on your profile and posts. It has an interests fied which when filled provides an inteeliligent feed section that shows only the thing you claim to be interested in. It also has an autotagging section which automatically tags your posts with numerous synonymous tags.


## Features
1) Autotagging System
2) Hashtag System
3) Fulltext Custom-made Search
4) Automatic text to Image Conversion (with auto-reduction of text count and custom background)
5) Image & Text Upload
6) Support for Gif Images 
7) Robust Notification System
8) Well updated Legal Pages Copywriting
9) Multi-Device System

Below is a preview:
![Preview](http://quotes.akin.com.ng/a.PNG)


## How it works:

The script relies on certain supporting pages to function via the backend. They include:
1) Inc.php (Contains Globally Declared Variables,Classes and Functions)
2) libs/imdet (contains files that edits and updates image properties and attributes for SEO reasons)
3) libs/class.user.php (contains functions for handling login and registration )
4) libs/major_class.php (contains major site functions)
5) libs/pdo_pagination.php (heavily used in major_class.php for pagination)
6) libs/resizer.php (contains functions for resizing  gif functions)
7) libs/simpleimage.php(contains text to image functions)

## Store Folder
This contain html snippets, included and used a several times on the website

## Cache Folder
Stores cached pages of the website when $cache = 1 in the inc.php


## Assets
This stores all js and css dependencies. it also houses the sendmail.php that can power any mail sending form on the site

## Ad
Contains 'materials' for a deprecated ad features


## Cron Job Pages
These pages perform certain automated works on the site. They Include:
sitemap.php: Updates the sitemaps when run <br/>
fixim.php: Adds Descriptive Properties to All Images ON site for seo poperties<br/>
fixcomments.php: Fixes errors with comments <br/>
fixtags.php: Auto-tags all yet to be tagged posts <br/> 

### Other Pages
All other pages are self-explanatory on their own.

**The header and footer pages can be found in the store/ folder together with all other similar files.**


All other unstated files should be considered trivial.

## Installation 
Installation is very easy. Just follow the following steps

1. Download this script as a zip file (from [here](https://github.com/Akintunde102/AKINBLOG/archive/master.zip))
2. Copy to your server  www (or htdocs) folder
3. Upload q.sql to your mysql database
4. Edit inc.php to add new db details
3. That's it 
4. Just view from your server domain name (or from localhost if you are in a local environment)

<br/>
Test Email: fliptee@gmail.co<br/>
Test Pass: 1234

## Initial Version
1.0

## Contact Me
**Discord**: @akintunde <br/>
**Email:** jegedeakintunde[at]gmail.com<br/>
**utopian.io:** @akintunde <br/>
**github:** @akintunde102<br/>


## More ScreenShots
![Preview](http://quotes.akin.com.ng/a.PNG)
![Preview](http://quotes.akin.com.ng/b.PNG)
![Preview](http://quotes.akin.com.ng/c.PNG)
![Preview](http://quotes.akin.com.ng/d.PNG)

