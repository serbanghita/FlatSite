# CSS media queries and IE fix

_Use CSS media queries to display the same layout on multiple resolutions_

Date: Nov 26th, 2011 9:30:00pm

**UPDATE 09 January, 2012:**

Take a peek at [adapt.js](http://adapt.960.gs/) is a lightweight JavaScript file that determines which CSS file 
to load before the browser renders a page.

Make sure you try [respond.js](https://github.com/scottjehl/Respond) - a fast &amp; lightweight polyfill for 
min/max-width CSS3 Media Queries (for IE 6-8, and more)

If you want to be able to correctly display a fluid design on multiple resolutions (including mobile) then you 
will probably use [CSS Media Queries](http://www.w3.org/TR/css3-mediaqueries/). CSS Media Queries are not 
complicated to use and were introduced by CSS3 specifications as an extension of CSS 2.1 media types. 

Note that HTML4 supports media types like _handheld_, but this is poorly supported, old mobiles don't detect it, 
the modern devices completely ignore it.

CSS Media Queries work fine on modern browsers (IE9, FF, Chrome, Safari, Opera) and on mobile devices (iPhone, 
Android, Opera Mobile &amp; Mini, Blackberry, IE Mobile 7, etc.)

And of course they don't work on IE 
    <!-- Your .css files. IE6, IE7 and IE8 ignore the media="only all ..." files. -->
       <link rel="stylesheet" type="text/css" href="core.css"><link rel="stylesheet" type="text/css" href="smartphone.css" media="only all and (max-width: 480px)" id="stylesheet-480"><link rel="stylesheet" type="text/css" href="tablets.css" media="only all and (min-width: 480px) and (max-width: 1024px)" id="stylesheet-1024"><link rel="stylesheet" type="text/css" href="wide.css" media="only all and (min-width: 1200px)" id="stylesheet-1280"><!-- The mighty fix which chooses the correct stylesheet file based on the 
    screen resolution, and strips the 'media' attribute so IE6, IE7 and IE8 
    can read/interpret it --><!--[if lt IE 9]>
       <script type="text/javascript" src="/js/css-media-query-ie.js"></script>
    <![endif]-->


**Who needs this fix?**

Developers who want to enable CSS3 Media Queries for visitors using IE6, IE7 and IE8

**What if I'm using _@media all and (max-width:480px)_ inline inside my core CSS file?**

That's smart because it reduces the HTTP requests. In that case you still have to split CSS into separate files: 
core.css smartphone.css desktops.css wide.css, but the code changes slightly:

    
    <!-- Your all-min.css contains all the .css files content together. IE6, IE7 and IE8 will ignore the '@media all ...' inline stuff. -->
    <link rel="stylesheet" type="text/css" href="all-min.css"><!-- 
    Let IE6, IE7 and IE8 users have more requests. In the end they are using old browsers right?
    First the IE browser will ignore the <link> files with medial="only all ..."
    Then css-media-query-ie.js comes into play and chooses the right file, and strips the media property!
     --><!--[if lt IE 9]>
       <link rel="stylesheet" type="text/css" href="smartphone.css" media="only all and (max-width: 480px)" id="stylesheet-480" />
       <link rel="stylesheet" type="text/css" href="tablets.css" media="only all and (min-width: 480px) and (max-width: 1024px)" id="stylesheet-1024" />
       <link rel="stylesheet" type="text/css" href="wide.css" media="only all and (min-width: 1200px)" id="stylesheet-1280" />
    
       <script type="text/javascript" src="/js/css-media-query-ie.js"></script>
    <![endif]-->
    

**Screenshots on IE6**

[
![](http://ghita.org/sites/default/files/imagefield_thumbs/articles_imgs/ie6-screenshot.png)](http://ghita.org/sites/default/files/articles_imgs/ie6-screenshot.png)

<!-- http://drupal.org/project/viewport_classes -->

**Comments from users**

_**Ian M** wrote on 09 December 2011_

But I assume this is a static solution. We have a site we are developing where things dynamically 
resize/restyle as the resolution changes. I assume this solution will not work in our case.

_**serban.ghita** wrote on 10 December 2011_

Ian, yes it's a static solution, it's applied only `onload`, I can patch it the fix  `onresize` also! 
Would that be of any help?

_**Guest** wrote on 14 February 2012_

yup!

_**Guest** wrote on 27 February 2012_

Try to open your example in last Chrome and resize window.

_**Guest** wrote on 02 November 2012_

I really like this approach.
I tweaked your script a bit with some jquery goodness.

    detectAndUseStylesheet = function () {
        var currentWidth = document.body.offsetWidth,
            allSupportedResolutions = [];
        $("link[media][id]").each(function(index, value) {
            allSupportedResolutions.push(parseInt(value.id.match(/[0-9]+$/i)[0]));
        });
        $.each(allSupportedResolutions, function (index, value) {
            if (currentWidth