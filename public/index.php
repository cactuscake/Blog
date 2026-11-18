<?php
require __DIR__ . '/../vendor/autoload.php';

use Smarty\Smarty;
use App\Models\Category;
use App\Models\Post;

// Настройка Smarty
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__ . '/../templates/');
$smarty->setCompileDir(__DIR__ . '/../templates_c/');
$smarty->setCacheDir(__DIR__ . '/../cache/');
$smarty->setEscapeHtml(true);

// Роутинг
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

try {
    switch ($page) {
        case 'home':
            $categories = Category::getAllWithPosts(3);
            $smarty->assign('categories', $categories);
            $smarty->display('home.tpl');
            break;

        case 'category':
            $id = (int)($_GET['id'] ?? 0);
            $category = Category::find($id);
            if (!$category) throw new Exception("Category not found");
            
            $sort = $_GET['sort'] ?? 'date';
            $pageNum = max(1, (int)($_GET['p'] ?? 1));
            
            $data = Post::getByCategory($id, $sort, $pageNum);
            
            $smarty->assign('category', $category);
            $smarty->assign('posts', $data['posts']);
            $smarty->assign('totalPages', $data['totalPages']);
            $smarty->assign('currentPage', $data['currentPage']);
            $smarty->assign('currentSort', $sort);
            $smarty->display('category.tpl');
            break;

        case 'post':
            $id = (int)($_GET['id'] ?? 0);
            $post = Post::find($id);
            if (!$post) throw new Exception("Post not found");
            
            $similar = Post::getSimilar($id, 3);
            
            $smarty->assign('post', $post);
            $smarty->assign('similar', $similar);
            $smarty->display('post.tpl');
            break;

        default:
            http_response_code(404);
            echo "404 Not Found";
    }
} catch (Exception $e) {
    http_response_code(404);
    echo "Error: " . $e->getMessage();
}