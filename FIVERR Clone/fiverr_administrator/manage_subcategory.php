<?php require_once 'classloader.php'; ?>
<?php
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if (!$userObj->isFiverrAdministrator()) {
  header("Location: ../client/index.php");
}
?>
<!doctype html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <style>
    body {
      font-family: "Arial";
    }
  </style>
</head>
<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container-fluid">
    <div class="display-4 text-center">Manage Subcategories</div>
    <div class="text-center">
      <?php
      if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
        if ($_SESSION['status'] == "200") {
          echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
        } else {
          echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>";
        }
      }
      unset($_SESSION['message']);
      unset($_SESSION['status']);
      ?>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card mt-4 mb-4">
          <div class="card-body">
            <form action="core/handleForms.php" method="POST">
              <div class="card-body">
                <h1 class="mb-4 mt-4">Add New Subcategory</h1>
                <div class="form-group">
                  <label for="category_id">Category</label>
                  <select class="form-control" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php $categories = $categoryObj->getCategories(); ?>
                    <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="subcategory_name">Subcategory Name</label>
                  <input type="text" class="form-control" name="subcategory_name" required>
                </div>
                <input type="submit" class="btn btn-primary float-right mt-4" name="addSubcategoryBtn">
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mt-4 mb-4">
          <div class="card-header"><h2>All Subcategories</h2></div>
          <div class="card-body overflow-auto">
            <?php $subcategories = $subcategoryObj->getSubcategories(); ?>
            <?php foreach ($subcategories as $subcategory) { ?>
            <div class="subcategory">
              <h4><?php echo $subcategory['subcategory_name']; ?> <small>(<?php echo $subcategory['category_name']; ?>)</small></h4>
              <small><i><?php echo $subcategory['date_added']; ?></i></small>
              <form action="core/handleForms.php" method="POST" class="d-inline">
                <input type="hidden" name="subcategory_id" value="<?php echo $subcategory['subcategory_id']; ?>">
                <input type="submit" class="btn btn-danger btn-sm" value="Delete" name="deleteSubcategoryBtn">
              </form>
              <hr>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
