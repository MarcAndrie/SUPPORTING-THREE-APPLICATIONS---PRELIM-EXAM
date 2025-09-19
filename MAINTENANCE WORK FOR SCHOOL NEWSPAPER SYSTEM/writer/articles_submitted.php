<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if ($userObj->isAdmin()) {
  header("Location: ../admin/index.php");
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
      <div class="row justify-content-center">
        <div class="col-md-6">
          <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <input type="text" class="form-control mt-4" name="title" placeholder="Input title here">
            </div>
            <div class="form-group">
              <textarea name="description" class="form-control mt-4" placeholder="Submit an article!"></textarea>
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

          <div class="display-4">Double click to edit article</div>
          <?php $articles = $articleObj->getArticlesByUserID($_SESSION['user_id']); ?>
          <?php foreach ($articles as $article) { ?>
          <div class="card mt-4 shadow articleCard">
            <div class="card-body">
              <h1><?php echo $article['title']; ?></h1> 
              <small><?php echo $article['username'] ?> - <?php echo $article['created_at']; ?> </small>
              <?php if ($article['is_active'] == 0) { ?>
                <p class="text-danger">Status: PENDING</p>
              <?php } ?>
              <?php if ($article['is_active'] == 1) { ?>
                <p class="text-success">Status: ACTIVE</p>
              <?php } ?>
              <?php if (!empty($article['category_name'])): ?>
                <p><small class="badge badge-info"><?php echo htmlspecialchars($article['category_name']); ?></small></p>
              <?php endif; ?>
              <?php if (!empty($article['image_path'])): ?>
                <img src="<?php echo htmlspecialchars(getFullImagePath($article['image_path'], $projectFolder)); ?>" class="img-fluid mb-2" alt="Article Image">
              <?php endif; ?>
              <p><?php echo $article['content']; ?> </p>
              <form class="deleteArticleForm">
                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                <input type="submit" class="btn btn-danger float-right mb-4 deleteArticleBtn" value="Delete">
              </form>
              <form class="editRequestForm">
                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                <input type="submit" class="btn btn-warning float-right mb-4 mr-2 editRequestBtn" value="Request Edit">
              </form>
              <div class="updateArticleForm d-none">
                <h4>Edit the article</h4>
                <form action="core/handleForms.php" method="POST">
                  <div class="form-group mt-4">
                    <input type="text" class="form-control" name="title" value="<?php echo $article['title']; ?>">
                  </div>
                  <div class="form-group">
                    <textarea name="description" id="" class="form-control"><?php echo $article['content']; ?></textarea>
                  </div>
                  <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control mt-2">
                      <option value="">Select a category</option>
                      <?php
                      $categories = $articleObj->getCategories();
                      foreach ($categories as $category) {
                          $selected = ($article['category_id'] == $category['category_id']) ? 'selected' : '';
                          echo '<option value="' . $category['category_id'] . '" ' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                  <input type="submit" class="btn btn-primary float-right mt-4" name="editArticleBtn">
                </form>
              </div>
            </div>
          </div>  
          <?php } ?> 
        </div>
      </div>
    </div>
    <script>
      $('.articleCard').on('dblclick', function (event) {
        var updateArticleForm = $(this).find('.updateArticleForm');
        updateArticleForm.toggleClass('d-none');
      });

      $('.deleteArticleForm').on('submit', function (event) {
        event.preventDefault();
        var formData = {
          article_id: $(this).find('.article_id').val(),
          deleteArticleBtn: 1
        }
        if (confirm("Are you sure you want to delete this article?")) {
          $.ajax({
            type:"POST",
            url: "core/handleForms.php",
            data:formData,
            success: function (data) {
              if (data) {
                location.reload();
              }
              else{
                alert("Deletion failed");
              }
            }
          })
        }
      })

      $('.editRequestForm').on('submit', function (event) {
        event.preventDefault();
        var formData = {
          article_id: $(this).find('.article_id').val(),
          requestEditBtn: 1
        }
        if (confirm("Are you sure you want to request an edit for this article?")) {
          $.ajax({
            type:"POST",
            url: "core/handleForms.php",
            data:formData,
            success: function (data) {
              if (data) {
                alert("Edit request sent successfully!");
              }
              else{
                alert("Edit request failed");
              }
            }
          })
        }
      })
    </script>
  </body>
</html>