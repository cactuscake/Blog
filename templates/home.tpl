{* home.tpl *}
<!DOCTYPE html>
<html>
<head><title>Blog Home</title></head>
<body>
    <h1>Blog Categories</h1>
    {foreach $categories as $category}
        {if $category.posts|count > 0}
            <div class="category-block">
                <h2>{$category.name}</h2>
                <p>{$category.description}</p>
                <h3>Latest Posts:</h3>
                <ul>
                    {foreach $category.posts as $post}
                        <li>
                            <a href="index.php?page=post&id={$post.id}">{$post.title}</a>
                            <small>(Views: {$post.views})</small>
                        </li>
                    {/foreach}
                </ul>
                <a href="index.php?page=category&id={$category.id}">
                    All articles in {$category.name} ({$category.post_count})
                </a>
            </div>
            <hr>
        {/if}
    {/foreach}
</body>
</html>