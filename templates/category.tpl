{* category.tpl *}
<!DOCTYPE html>
<html>
<head><title>{$category.name}</title></head>
<body>
    <h1>{$category.name}</h1>
    <p>{$category.description}</p>
    
    <div class="sorting">
        Sort by: 
        <a href="index.php?page=category&id={$category.id}&sort=date">Date</a> | 
        <a href="index.php?page=category&id={$category.id}&sort=views">Views</a>
    </div>

    {foreach $posts as $post}
        <div class="post">
            <h3><a href="index.php?page=post&id={$post.id}">{$post.title}</a></h3>
            <p>{$post.description}</p>
            <small>Views: {$post.views} | Date: {$post.published_at}</small>
        </div>
    {foreachelse}
        <p>No posts in this category.</p>
    {/foreach}

    {* Пагинация *}
    {if $totalPages > 1}
        <div class="pagination">
            {for $p=1 to $totalPages}
                {if $p == $currentPage}
                    <strong>{$p}</strong>
                {else}
                    <a href="index.php?page=category&id={$category.id}&sort={$currentSort}&p={$p}">{$p}</a>
                {/if}
            {/for}
        </div>
    {/if}
</body>
</html>