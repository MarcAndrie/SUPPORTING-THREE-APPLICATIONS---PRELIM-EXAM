<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
}  
// Define project folder path
$projectFolder = '/Activities 4-1/MAINTENANCE WORK FOR SCHOOL NEWSPAPER SYSTEM';
// Declare function only if not already declared
if (!function_exists('getFullImagePath')) {
    function getFullImagePath($imagePath, $projectFolder) {
        if (empty($imagePath)) {
            return '';
        }
        if (strpos($imagePath, $projectFolder) === 0) {
            return $imagePath;
        }
        return $projectFolder . $imagePath;
    }
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
      <div class="display-4 text-center">Hello there and welcome to the admin side! <span class="text-success"><?php echo $_SESSION['username']; ?></span>. Here are all the articles</div>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <input type="text" class="form-control mt-4" name="title" placeholder="Input title here">
            </div>
            <div class="form-group">
              <textarea name="description" class="form-control mt-4" placeholder="Message as admin"></textarea>
            </div>
            <div class="form-group">
              <label>Category</label>
              <select name="category_id" class="form-control mt-2">
                <option value="">Select a category</option>
                <?php
                $categories = $articleObj->getCategories();
                foreach ($categories as $category) {
                    echo '<option value="' . $category['category_id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label>Upload Image</label>
              <input type="file" class="form-control" name="article_image" accept="image/*">
            </div>
            <input type="submit" class="btn btn-primary form-control float-right mt-4 mb-4" name="insertArticleBtn">
          </form>

          <hr>
          <h4>Add New Category</h4>
          <form action="core/handleForms.php" method="POST">
            <div class="form-group">
              <input type="text" class="form-control" name="category_name" placeholder="Category name" required>
            </div>
            <input type="submit" class="btn btn-success" name="insertCategoryBtn" value="Add Category">
          </form>
          <?php $articles = $articleObj->getActiveArticles(); ?>
          <?php foreach ($articles as $article) { ?>
          <div class="card mt-4 shadow">
            <div class="card-body">
              <h1><?php echo $article['title']; ?></h1> 
              <?php if ($article['is_admin'] == 1) { ?>
                <p><small class="bg-primary text-white p-1">  
                  Message From Admin
                </small></p>
              <?php } ?>
              <small><strong><?php echo $article['username'] ?></strong> - <?php echo $article['created_at']; ?> </small>
              <?php if (!empty($article['category_name'])): ?>
                <p><small class="badge badge-info"><?php echo htmlspecialchars($article['category_name']); ?></small></p>
              <?php endif; ?>
              <?php if (!empty($article['image_path'])): ?>
                <img src="<?php echo htmlspecialchars(getFullImagePath($article['image_path'], $projectFolder)); ?>" class="img-fluid mb-2" alt="Article Image">
              <?php endif; ?>
              <p><?php echo $article['content']; ?> </p>
            </div>
          </div>  
          <?php } ?> 
        </div>
      </div>
    </div>
  </body>
</html>