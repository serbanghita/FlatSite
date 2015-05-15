<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title><?=$title;?></title>
    <link href="themes/default/theme.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="template">
        <header>
            <div class="search"><input id="q" name="q" placeholder="Search here ..."></div>

            <?=$tagsBlock;?>
        </header>

        <main>
            <div class="content">
                <?=$content;?>
            </div>
        </main>

        <footer></footer>
    </div>
</body>
</html>