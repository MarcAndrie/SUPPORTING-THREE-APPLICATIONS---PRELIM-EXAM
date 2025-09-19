<?php

require_once 'Database.php';

/**
 * Class for handling Subcategory-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Subcategory extends Database {

    /**
     * Adds a new subcategory.
     * @param int $category_id The parent category ID.
     * @param string $subcategory_name The name of the subcategory.
     * @return bool True on success, false on failure.
     */
    public function addSubcategory($category_id, $subcategory_name) {
        $sql = "INSERT INTO subcategories (category_id, subcategory_name) VALUES (?, ?)";
        try {
            $this->executeNonQuery($sql, [$category_id, $subcategory_name]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Retrieves all subcategories from the database.
     * @return array
     */
    public function getSubcategories() {
        $sql = "SELECT s.*, c.category_name FROM subcategories s JOIN categories c ON s.category_id = c.category_id ORDER BY s.subcategory_name";
        return $this->executeQuery($sql);
    }

    /**
     * Retrieves subcategories by category ID.
     * @param int $category_id The category ID.
     * @return array
     */
    public function getSubcategoriesByCategoryId($category_id) {
        $sql = "SELECT * FROM subcategories WHERE category_id = ? ORDER BY subcategory_name";
        return $this->executeQuery($sql, [$category_id]);
    }

    /**
     * Retrieves a subcategory by ID.
     * @param int $subcategory_id The subcategory ID.
     * @return array
     */
    public function getSubcategoryById($subcategory_id) {
        $sql = "SELECT s.*, c.category_name FROM subcategories s JOIN categories c ON s.category_id = c.category_id WHERE s.subcategory_id = ?";
        return $this->executeQuerySingle($sql, [$subcategory_id]);
    }

    /**
     * Updates a subcategory.
     * @param int $subcategory_id The subcategory ID.
     * @param int $category_id The parent category ID.
     * @param string $subcategory_name The new subcategory name.
     * @return int The number of affected rows.
     */
    public function updateSubcategory($subcategory_id, $category_id, $subcategory_name) {
        $sql = "UPDATE subcategories SET category_id = ?, subcategory_name = ? WHERE subcategory_id = ?";
        return $this->executeNonQuery($sql, [$category_id, $subcategory_name, $subcategory_id]);
    }

    /**
     * Deletes a subcategory.
     * @param int $subcategory_id The subcategory ID.
     * @return int The number of affected rows.
     */
    public function deleteSubcategory($subcategory_id) {
        $sql = "DELETE FROM subcategories WHERE subcategory_id = ?";
        return $this->executeNonQuery($sql, [$subcategory_id]);
    }
}

?>
