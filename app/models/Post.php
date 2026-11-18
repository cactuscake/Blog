<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Post {
    public static function getByCategory(int $categoryId, string $sort = 'date', int $page = 1, int $perPage = 5): array {
        $pdo = Database::getInstance();

        $orderBy = ($sort === 'views') ? 'p.views DESC' : 'p.published_at DESC';
        
        // Считаем общее количество для пагинации
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM post_category WHERE category_id = ?");
        $countStmt->execute([$categoryId]);
        $total = $countStmt->fetchColumn();
        $totalPages = ceil($total / $perPage);
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.* FROM posts p
                JOIN post_category pc ON p.id = pc.post_id
                WHERE pc.category_id = :cid
                ORDER BY $orderBy
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cid', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'posts' => $stmt->fetchAll(),
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }

    public static function find(int $id): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        
        if ($post) {
            // Увеличиваем просмотры
            $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$id]);
            $post['views']++;
            
            // Получаем категории поста
            $catStmt = $pdo->prepare("SELECT c.* FROM categories c JOIN post_category pc ON c.id = pc.category_id WHERE pc.post_id = ?");
            $catStmt->execute([$id]);
            $post['categories'] = $catStmt->fetchAll();
        }
        return $post ?: null;
    }

    public static function getSimilar(int $postId, int $limit = 3): array {
        $pdo = Database::getInstance();
        // Находим категории текущего поста
        $catIds = $pdo->prepare("SELECT category_id FROM post_category WHERE post_id = ?");
        $catIds->execute([$postId]);
        $ids = $catIds->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($ids)) return [];
        
        // Ищем посты в тех же категориях, исключая текущий
        $inQuery = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT DISTINCT p.* FROM posts p
                JOIN post_category pc ON p.id = pc.post_id
                WHERE pc.category_id IN ($inQuery) AND p.id != ?
                ORDER BY p.published_at DESC
                LIMIT ?";
        
        $params = array_merge($ids, [$postId, $limit]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}