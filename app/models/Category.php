<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Category {
    public static function getAllWithPosts(int $postsLimit = 3): array {
        $pdo = Database::getInstance();
        
        // Получаем все категории
        $stmt = $pdo->query("SELECT * FROM categories");
        $categories = $stmt->fetchAll();

        foreach ($categories as &$cat) {
            // Для каждой категории получаем 3 последних поста
            $sql = "SELECT p.* FROM posts p
                    JOIN post_category pc ON p.id = pc.post_id
                    WHERE pc.category_id = :cid
                    ORDER BY p.published_at DESC
                    LIMIT :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':cid', $cat['id'], PDO::PARAM_INT);
            $stmt->bindValue(':limit', $postsLimit, PDO::PARAM_INT);
            $stmt->execute();
            $cat['posts'] = $stmt->fetchAll();
            
            // Считаем общее количество для ссылки "Все статьи"
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM post_category WHERE category_id = ?");
            $countStmt->execute([$cat['id']]);
            $cat['post_count'] = $countStmt->fetchColumn();
        }
        return $categories;
    }

    public static function find(int $id): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}