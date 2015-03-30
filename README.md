##Unused CSS Remover Tool 
######(Browser based)


A browser based tool to remove unused items from CSS file.

###Features

    *Finds unused CSS selectors
    *Create a new lightweight CSS file 
    *Compare your CSS file against multiple site urls
    *Allows you to view all unused elements and still 
    retain some of them for future possible use
    *Supports responsive design rules and new CSS-3 selectors
    *Ignores CSS import commands

    
    
###Warnings:
    * The code is a beta release. There are bugs in the code.
    * Please BACKUP YOUR OLD CSS before replacing it with new CSS. 
    * I do not take any reposnsibility for broken css rules or ugly looking sites.
    
    *Some CMS like Wordpress detects themes based on top comment in style.css.
    *The output CSS file from this program will be stripped of all comments. 
    *You may want to add back the top level comments to your style.css if using wordpress.
    
###Direction for USE
    1) Download the Archive and extract the folder to the root of your localhost or server.
    2) Open browser and run http://localhost/unusedcssremover/form.php
    
###Changing the Maximum Number of Allowed URLS
    *The current maximum number of simultaneuos URLS allowed is 5.
    *You can change the maximum number of URLS allowed by tweaking the
     value of this line of code:
    `$MAX_NUM_OF_URLS_ALLOWED = 5;`
    in the beginning of the file `step_one.php`
    
###Handling large CSS files or large number of URLs
    *You can also tweak in the value for invidual script by changing this line of code:
    set_time_limit(90);
    in files step_one.php, step_two.php and step_three.php

    *You may also tweak in the maximum execution time for php scripts in your php.ini file
    *Normally the default is  30 seconds and this may get exceeded in case of large CSS files.
