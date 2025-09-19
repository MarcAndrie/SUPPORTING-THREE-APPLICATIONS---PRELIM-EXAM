<?php

require_once 'Database.php';

/**
 * Class for handling Category-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Category extends Database {

    /**
     * Adds a new category.
     * @param string $category_name The name of the category.
     * @return bool True on success, false on failure.
     */
    public function addCategory($category_name) {
        $sql = "INSERT INTO categories (category_name) VALUES (?)";
        try {
            $this->executeNonQuery($sql, [$category_name]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Retrieves all categories from the database.
     * @return array
     */
    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY category_name";
        return $this->executeQuery($sql);
    }

    /**
     * Retrieves a category by ID.
     * @param int $category_id The category ID.
     * @return array
     */
    public function getCategoryById($category_id) {
        $sql = "SELECT * FROM categories WHERE category_id = ?";
        return $this->executeQuerySingle($sql, [$category_id]);
    }

    /**
     * Updates a category.
     * @param int $category_id The category ID.
     * @param string $category_name The new category name.
     * @return int The number of affected rows.
     */
    public function updateCategory($category_id, $category_name) {
        $sql = "UPDATE categories SET category_name = ? WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$category_name, $category_id]);
    }

    /**
     * Deletes a category.
     * @param int $category_id The category ID.
     * @return int The number of affected rows.
     */
    public function deleteCategory($category_id) {
        $sql = "DELETE FROM categories WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$category_id]);
    }
}

?>
