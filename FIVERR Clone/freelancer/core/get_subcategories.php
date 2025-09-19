<?php
require_once '../classloader.php';

if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    $subcategories = $subcategoryObj->getSubcategoriesByCategoryId($category_id);

    echo '<option value="">Select Subcategory</option>';
    foreach ($subcategories as $subcategory) {
        echo '<option value="' . $subcategory['subcategory_id'] . '">' . $subcategory['subcategory_name'] . '</option>';
    }
}
?>
