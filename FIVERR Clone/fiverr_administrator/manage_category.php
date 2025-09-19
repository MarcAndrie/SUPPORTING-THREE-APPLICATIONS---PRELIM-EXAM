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
    <div class="display-4 text-center">Manage Categories</div>
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
                <h1 class="mb-4 mt-4">Add New Category</h1>
                <div class="form-group">
                  <label for="category_name">Category Name</label>
                  <input type="text" class="form-control" name="category_name" required>
                </div>
                <input type="submit" class="btn btn-primary float-right mt-4" name="addCategoryBtn">
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mt-4 mb-4">
          <div class="card-header"><h2>All Categories</h2></div>
          <div class="card-body overflow-auto">
            <?php $categories = $categoryObj->getCategories(); ?>
            <?php foreach ($categories as $category) { ?>
            <div class="category">
              <h4><?php echo $category['category_name']; ?></h4>
              <small><i><?php echo $category['date_added']; ?></i></small>
              <form action="core/handleForms.php" method="POST" class="d-inline">
                <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                <input type="submit" class="btn btn-danger btn-sm" value="Delete" name="deleteCategoryBtn">
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
