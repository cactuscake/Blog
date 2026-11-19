{* post.tpl *}
<!DOCTYPE html>
<html>
<head><title>{$post.title}</title></head>
<body>
    <h1>{$post.title}</h1>
    <p><strong>Views:</strong> {$post.views}</p>
    <p><strong>Categories:</strong> 
        {foreach $post.categories as $cat}
            <a href="index.php?page=category&id={$cat.id}">{$cat.name}</a>{if !$cat@last}, {/if}
        {/foreach}
    </p>
    
    {if $post.image}
        <img src="{$post.image}" alt="{$post.title}" style="max-width: 100%;">
    {/if}
    
    <div class="content">
        {$post.content nofilter} {* nofilter, если контент содержит HTML *}
    </div>

    <hr>
    <h3>Similar Posts</h3>
    <ul>
        {foreach $similar as $sim}
            <li><a href="index.php?page=post&id={$sim.id}">{$sim.title}</a></li>
        {foreachelse}
            <li>No similar posts found.</li>
        {/foreach}
    </ul>
</body>
</html>