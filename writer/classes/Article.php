<?php  

require_once 'Database.php';
require_once 'User.php';
/**
 * Class for handling Article-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Article extends Database {
    /**
     * Creates a new article.
     * @param string $title The article title.
     * @param string $content The article content.
     * @param int $author_id The ID of the author.
     * @param int|null $category_id The category ID.
     * @param string|null $image_path The image path.
     * @return int The ID of the newly created article.
     */
    public function createArticle($title, $content, $author_id, $category_id = null, $image_path = null) {
        $sql = "INSERT INTO articles (title, content, author_id, category_id, is_active, image_path) VALUES (?, ?, ?, ?, 0, ?)";
        return $this->executeNonQuery($sql, [$title, $content, $author_id, $category_id, $image_path]);
    }

    /**
     * Retrieves articles from the database.
     * @param int|null $id The article ID to retrieve, or null for all articles.
     * @return array
     */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, categories.name AS category_name FROM articles
                    LEFT JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                    LEFT JOIN categories ON articles.category_id = categories.category_id
                    WHERE article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, categories.name AS category_name FROM articles
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                LEFT JOIN categories ON articles.category_id = categories.category_id
                ORDER BY articles.created_at DESC";
        return $this->executeQuery($sql);
    }

    public function getActiveArticles($id = null) {
        if ($id) {
            $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, categories.name AS category_name FROM articles
                    LEFT JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                    LEFT JOIN categories ON articles.category_id = categories.category_id
                    WHERE article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, categories.name AS category_name FROM articles
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                LEFT JOIN categories ON articles.category_id = categories.category_id
                WHERE is_active = 1 ORDER BY articles.created_at DESC";

        return $this->executeQuery($sql);
    }

    public function getArticlesByUserID($user_id) {
        $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, categories.name AS category_name FROM articles
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                LEFT JOIN categories ON articles.category_id = categories.category_id
                WHERE author_id = ? ORDER BY articles.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    /**
     * Updates an article.
     * @param int $id The article ID to update.
     * @param string $title The new title.
     * @param string $content The new content.
     * @param int|null $category_id The category ID.
     * @return int The number of affected rows.
     */
    public function updateArticle($id, $title, $content, $category_id = null) {
        $sql = "UPDATE articles SET title = ?, content = ?, category_id = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$title, $content, $category_id, $id]);
    }
    
    /**
     * Toggles the visibility (is_active status) of an article.
     * This operation is restricted to admin users only.
     * @param int $id The article ID to update.
     * @param bool $is_active The new visibility status.
     * @return int The number of affected rows.
     */
    public function updateArticleVisibility($id, $is_active) {
        $userModel = new User();
        if (!$userModel->isAdmin()) {
            return 0;
        }
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [(int)$is_active, $id]);
    }

    /**
     * Deletes an article.
     * @param int $id The article ID to delete.
     * @return int The number of affected rows.
     */
    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    public function getNotificationsByUserID($user_id) {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    /**
     * Add a notification for a user
     * @param int $user_id
     * @param string $message
     * @return bool
     */
    public function addNotification($user_id, $message) {
        $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$user_id, $message]);
    }

    /**
     * Mark a notification as read
     * @param int $notification_id
     * @return bool
     */
    public function markNotificationAsRead($notification_id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = ?";
        return $this->executeNonQuery($sql, [$notification_id]);
    }

    /**
     * Share an article with a user
     * @param int $article_id
     * @param int $shared_with_user_id
     * @param int $shared_by_user_id
     * @return bool
     */
    public function shareArticle($article_id, $shared_with_user_id, $shared_by_user_id) {
        $sql = "INSERT INTO shared_articles (article_id, shared_with_user_id, shared_by_user_id) VALUES (?, ?, ?)";
        return $this->executeNonQuery($sql, [$article_id, $shared_with_user_id, $shared_by_user_id]);
    }

    /**
     * Get articles shared with a user
     * @param int $user_id
     * @return array
     */
    public function getSharedArticles($user_id) {
        $sql = "SELECT a.*, u.username FROM articles a
                JOIN shared_articles sa ON a.article_id = sa.article_id
                JOIN school_publication_users u ON a.author_id = u.user_id
                WHERE sa.shared_with_user_id = ?
                ORDER BY a.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    /**
     * Get all categories
     * @return array
     */
    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY name";
        return $this->executeQuery($sql);
    }
}
?>
