<?php
require_once __DIR__ . '/../classloader.php';
$categories = $categoryObj->getCategories();
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="index.php">Client Panel</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavClient" aria-controls="navbarNavClient" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavClient">
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdownClient" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Categories
        </a>
        <div class="dropdown-menu" aria-labelledby="categoriesDropdownClient">
          <?php foreach ($categories as $category): ?>
            <?php
              $subcategories = $subcategoryObj->getSubcategoriesByCategoryId($category['category_id']);
            ?>
            <div class="dropdown-submenu">
              <a class="dropdown-item dropdown-toggle" href="#"><?php echo htmlspecialchars($category['category_name']); ?></a>
              <div class="dropdown-menu">
                <?php foreach ($subcategories as $subcategory): ?>
                  <a class="dropdown-item" href="index.php?subcategory_id=<?php echo $subcategory['subcategory_id']; ?>">
                    <?php echo htmlspecialchars($subcategory['subcategory_name']); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="profile.php">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="core/handleForms.php?logoutUserBtn=1">Logout</a>
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
      $(this).next('.dropdown-menu').toggle();
    });
  });
</script>
