<?php
require_once 'classloader.php';

if (!$userObj->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch articles shared with the logged-in user
$sharedArticles = $articleObj->getSharedArticles($user_id);

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
      <div class="display-4 text-center">Hello there and welcome! <span class="text-success"><?php echo $_SESSION['username']; ?></span>. Here are all the articles shared with you</div>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <?php if (count($sharedArticles) === 0): ?>
            <p class="text-center mt-4">No articles have been shared with you yet.</p>
          <?php else: ?>
          <?php foreach ($sharedArticles as $article) { ?>
          <div class="card mt-4 shadow">
            <div class="card-body">
              <h1><?php echo $article['title']; ?></h1> 
              <?php if ($article['is_admin'] == 1) { ?>
                <p><small class="bg-primary text-white p-1">  
                  Message From Admin
                </small></p>
              <?php } ?>
              <small><strong><?php echo $article['username'] ?></strong> - <?php echo $article['created_at']; ?> </small>
              <?php if (!empty($article['image_path'])): ?>
                <img src="<?php echo htmlspecialchars(getFullImagePath($article['image_path'], $projectFolder)); ?>" class="img-fluid mb-2" alt="Article Image">
              <?php endif; ?>
              <p><?php echo $article['content']; ?> </p>
              <?php if ($article['author_id'] != $_SESSION['user_id']) { ?>
                <form class="editRequestForm">
                  <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                  <input type="submit" class="btn btn-warning float-right mb-4 editRequestBtn" value="Request Edit">
                </form>
              <?php } ?>
            </div>
          </div>
          <?php } ?> 
          <?php endif;?>
        </div>
      </div>
    </div>
    <script>
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
