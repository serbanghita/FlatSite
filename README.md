FlatSite
========

> Generate and deploy pure static websites.

If your website is based around _posts_ and _tags_, and you want to make it database free
so you can be able to host it anywhere (including GitHub), here is the solution.

1. Configure your website using the `private` folder.

```
example/private/
            posts/
            themes/
            config.json
```

2. Add your posts in the `private/posts/` folder.

```
example/private/posts/
    my-first-article.html
    my-second-article.html
    ...
```

3. Each `post` must be proper HTML formatted.

```
<h1 class="post-title">Title</h1>
<div class="post-date">2015-05-15 19:00</div>

<div class="post-body">
    My article text here.
</div>

<div class="post-tags">php, programming</div>
```

4. Generate it using `lib/app.php`

5. Check the `example/public/`. This folder will contain all your website pages and assets.