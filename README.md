# Блог на чистом PHP, Smarty и MySQL

Простой, но полностью рабочий веб-сайт блога с категориями, постами, пагинацией и сортировкой. Проект написан на чистом PHP (без фреймворков) с использованием шаблонизатора Smarty и базы данных MySQL. Всё окружение упаковано в Docker.

## Технологический стек

- **PHP 8.1+**
- **Smarty** (шаблонизатор)
- **MySQL 8.0**
- **Docker & Docker Compose**
- **Apache** (веб-сервер)
- **Composer** (менеджер зависимостей)
- **SCSS**

## Предварительные требования
Перед запуском убедитесь, что на вашем компьютере установлены:

Docker Desktop (для Windows/Mac) или Docker + Docker Compose (для Linux).

Терминал (PowerShell, Bash или любой другой).

(Опционально) Composer, если вы планируете запускать его локально. Но в проекте он используется внутри Docker.

## Пошаговый запуск

### Шаг 1: Запуск контейнеров Docker
```bash
docker-compose up -d --build
```

При первом запуске скачаются образы PHP и MySQL, это может занять несколько минут.

Проверить, что контейнеры запущены:

```bash
docker-compose ps
```
Оба сервиса (php и db) должны быть в состоянии Up.

### Шаг 2: Установка зависимостей (Smarty)
Выполните команду внутри контейнера php:

```bash
docker-compose exec php composer install
```
Если файл composer.json ещё не создан или вы начинаете с нуля:

```bash
docker-compose exec php composer require smarty/smarty
```

### Шаг 3: Создание структуры базы данных
Выполните SQL-запросы для создания таблиц. Можно сделать это одной командой:

```bash
docker-compose exec db mysql -u user -ppassword blog -e "
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT
);
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    content TEXT,
    views INT DEFAULT 0,
    published_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE post_category (
    post_id INT,
    category_id INT,
    PRIMARY KEY (post_id, category_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
"
```
### Шаг 4: Наполнение базы тестовыми данными (сидинг)
```bash
docker-compose exec php php seed.php
```
Вы должны увидеть сообщение Seeding completed!. Скрипт создаст несколько категорий и постов для демонстрации.

### Шаг 5: Проверка работы
Откройте браузер и перейдите по адресу:

http://localhost:8080
Вы должны увидеть главную страницу блога со списком категорий и последними постами.

## Функционал

Главная страница — отображает категории с 3 последними постами в каждой.

Страница категории (клик по «Все статьи») — показывает список статей с возможностью сортировки (по дате / по просмотрам) и пагинацией.

Страница статьи (клик по заголовку поста) — отображает полную информацию, увеличивает счётчик просмотров и показывает 3 похожие статьи.
