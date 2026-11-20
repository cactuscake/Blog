<?php
require 'vendor/autoload.php';
use App\Core\Database;

$pdo = Database::getInstance();

// Очистка
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE TABLE post_category");
$pdo->exec("TRUNCATE TABLE posts");
$pdo->exec("TRUNCATE TABLE categories");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// 1. Категории
$cats = [
    ['Technology', 'All about programming, servers, and new tech.'],
    ['Travel', 'Stories and guides from around the world.'],
    ['Food', 'Recipes, restaurant reviews, and culinary adventures.'],
    ['Health & Fitness', 'Tips for a healthy body and mind.'],
    ['Finance', 'Personal finance, investments, and saving money.']
];
$stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
foreach ($cats as $c) $stmt->execute($c);

// 2. Посты
// Массив: [Заголовок, Описание, Контент, ID категории]
$posts = [
    // Technology (ID: 1)
    ['PHP 8.1 Features', 'New features in PHP 8.1', '<p>Enums, readonly properties, and fibers...</p>', 1],
    ['Docker for Beginners', 'Getting started with Docker', '<p>Docker is a tool designed to make it easier to create, deploy, and run applications...</p>', 1],
    ['MySQL Optimization', 'Speed up your database', '<p>Use indexes, avoid SELECT *, and analyze slow queries...</p>', 1],
    ['Understanding Smarty Templates', 'Why use a template engine?', '<p>Separating logic from presentation is crucial for maintainability...</p>', 1],
    ['Why Clean Code Matters', 'The importance of readable code', '<p>Code is read much more often than it is written...</p>', 1],
    ['REST API Best Practices', 'Designing good APIs', '<p>Use proper HTTP methods and status codes...</p>', 1],
    ['Git Workflow for Teams', 'Branching strategies', '<p>Gitflow vs Trunk-based development...</p>', 1],

    // Travel (ID: 2)
    ['Trip to Paris', 'My vacation in France', '<p>The Eiffel Tower was amazing, and the croissants were even better...</p>', 2],
    ['Hiking in the Alps', 'A journey through the mountains', '<p>The views were breathtaking, but my legs are still recovering...</p>', 2],
    ['Budget Travel Tips', 'How to travel cheaply', '<p>Book flights in advance, stay in hostels, and eat local food...</p>', 2],
    ['Exploring Tokyo', 'A week in Japan', '<p>From Shibuya crossing to the quiet temples of Kyoto...</p>', 2],
    ['Road Trip Across USA', 'Coast to coast', '<p>Driving from New York to Los Angeles took us 14 days...</p>', 2],
    ['Best Beaches in Europe', 'Sun, sand, and sea', '<p>From the Algarve in Portugal to the Amalfi Coast in Italy...</p>', 2],

    // Food (ID: 3)
    ['Best Pasta Recipe', 'How to cook authentic pasta', '<p>Boil water, add salt, and don\'t forget the garlic...</p>', 3],
    ['The Art of Making Sushi', 'A beginner\'s guide', '<p>It takes years to master, but you can start with simple maki rolls...</p>', 3],
    ['Vegan Desserts', 'Sweet treats without animal products', '<p>Avocado chocolate mousse is a game changer...</p>', 3],
    ['Coffee Brewing Methods', 'Pour-over vs French press', '<p>Grind size and water temperature play a huge role...</p>', 3],
    ['Baking Bread at Home', 'Sourdough starter guide', '<p>Patience is the main ingredient...</p>', 3],

    // Health (ID: 4)
    ['Morning Routines', 'Start your day right', '<p>Drink water, stretch, and avoid your phone for the first hour...</p>', 4],
    ['Benefits of Meditation', 'Calm your mind', '<p>Just 10 minutes a day can reduce stress significantly...</p>', 4],
    ['Home Workouts', 'No gym required', '<p>Push-ups, squats, and planks are all you need to start...</p>', 4],
    ['Healthy Sleep Habits', 'How to sleep better', '<p>Avoid caffeine after 2 PM and keep your room dark...</p>', 4],
    ['Nutrition Basics', 'What to eat', '<p>Focus on whole foods and stay away from processed sugar...</p>', 4],

    // Finance (ID: 5)
    ['Investing for Beginners', 'Start building wealth', '<p>Index funds are a great place to start...</p>', 5],
    ['Saving Money Tips', 'Cut unnecessary expenses', '<p>Track your spending for a month, and you will be surprised...</p>', 5],
    ['Understanding Credit Scores', 'Why it matters', '<p>Pay your bills on time and keep your credit utilization low...</p>', 5],
    ['Cryptocurrency Explained', 'Digital money', '<p>Blockchain technology is behind Bitcoin...</p>', 5],
    ['Real Estate Basics', 'Buying your first home', '<p>Save for a down payment and get pre-approved for a mortgage...</p>', 5],
];

$postStmt = $pdo->prepare("INSERT INTO posts (title, description, content, views, published_at) VALUES (?, ?, ?, ?, ?)");
$linkStmt = $pdo->prepare("INSERT INTO post_category (post_id, category_id) VALUES (?, ?)");

$postId = 1;
foreach ($posts as $index => $p) {
    // Генерируем случайное количество просмотров от 50 до 5000
    $views = rand(50, 5000); 
    
    // Генерируем случайную дату публикации за последние 60 дней
    $daysAgo = rand(0, 60);
    $date = date('Y-m-d H:i:s', strtotime("-$daysAgo days"));
    
    // Вставляем пост
    $postStmt->execute([$p[0], $p[1], $p[2], $views, $date]);
    
    // Привязываем к категории
    $linkStmt->execute([$postId, $p[3]]); 
    
    // Добавим немного пересечений
    if ($index % 4 === 0 && $p[3] !== 1) {
        $linkStmt->execute([$postId, 1]);
    }
    
    $postId++;
}
echo "Seeding completed! Inserted " . count($cats) . " categories and " . count($posts) . " posts.\n";