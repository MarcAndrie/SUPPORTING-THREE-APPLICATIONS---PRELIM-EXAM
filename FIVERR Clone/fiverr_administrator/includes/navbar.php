<?php
require_once __DIR__ . '/../classloader.php';
$categories = $categoryObj->getCategories();
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="index.php">Fiverr Administrator</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAdmin" aria-controls="navbarNavAdmin" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAdmin">
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdownAdmin" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Categories
        </a>
        <ul class="dropdown-menu" aria-labelledby="categoriesDropdownAdmin">
          <?php foreach ($categories as $category): ?>
            <?php
              $subcategories = $subcategoryObj->getSubcategoriesByCategoryId($category['category_id']);
            ?>
            <li class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#"><?php echo htmlspecialchars($category['category_name']); ?></a>
              <ul class="dropdown-menu">
                <?php foreach ($subcategories as $subcategory): ?>
                  <li>
                    <a class="dropdown-item" href="index.php?subcategory_id=<?php echo $subcategory['subcategory_id']; ?>">
                      <?php echo htmlspecialchars($subcategory['subcategory_name']); ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php endforeach; ?>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="manage_category.php">Manage Categories</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="manage_subcategory.php">Manage Subcategories</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="profile.php">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../client/index.php?as_admin=1">Submit Offers as Client</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="core/handleForms.php?logoutBtn=1">Logout</a>
      </li>
    </ul>
  </div>
</nav>

<style>
.dropdown-submenu {
  position: relative;
}
.dropdown-submenu .dropdown-menu {
  top: 0;
  left: 100%;
  margin-top: -1px;
  display: none;
}
.dropdown-submenu:hover .dropdown-menu {
  display: block;
}
</style>
<script>
  // Enable nested dropdowns
  $(document).ready(function(){
    $('.dropdown-submenu a.dropdown-toggle').on("click", function(e){
      e.preventDefault();
      e.stopPropagation();
      $(this).next('ul').toggle();
    });
  });
</script>
