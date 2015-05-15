<div class="posts">
    <?php foreach($posts as $url => $post): ?>
        <a href="<?=$url;?>"><?=$post['title'];?></a>
    <?php endforeach; ?>
</div>