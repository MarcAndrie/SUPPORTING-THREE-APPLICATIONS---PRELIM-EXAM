<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once 'classloader.php';

if (isset($_GET['as_admin']) && $_GET['as_admin'] == '1') {
  // Check if fiverr_administrator is logged in
  if (isset($_SESSION['is_fiverr_administrator']) && $_SESSION['is_fiverr_administrator']) {
    // Set client session for admin
    $_SESSION['user_id'] = $_SESSION['user_id'];
    $_SESSION['username'] = $_SESSION['username'];
    $_SESSION['is_client'] = 1;
    // Preserve fiverr_administrator status
    $_SESSION['is_fiverr_administrator'] = 1;
  } else {
    header("Location: ../fiverr_administrator/login.php");
    exit();
  }
} else {
  if (!$userObj->isLoggedIn()) {
    header("Location: login.php");
  }

  if (!$userObj->isAdmin()) {
    header("Location: ../freelancer/index.php");
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
      <div class="display-4 text-center">Hello there and welcome! <span class="text-success"><?php echo $_SESSION['username']; ?>. </span> Double click to edit your offers and then press enter to save!</div>
      <div class="text-center">
        <?php  
          if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

            if ($_SESSION['status'] == "200") {
              echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
            }

            else {
              echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>"; 
            }

          }
          unset($_SESSION['message']);
          unset($_SESSION['status']);
        ?>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-12">
          <?php
          $subcategory_id = isset($_GET['subcategory_id']) ? $_GET['subcategory_id'] : null;
          if ($subcategory_id) {
            $getProposals = $proposalObj->getProposalsBySubcategory($subcategory_id);
          } else {
            $getProposals = $proposalObj->getProposals();
          }
          ?>
          <?php foreach ($getProposals as $proposal) { ?>
          <div class="card shadow mt-4 mb-4">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h2><a href="other_profile_view.php?user_id=<?php echo $proposal['user_id'] ?>"><?php echo $proposal['username']; ?></a></h2>
                  <img src="<?php echo '../images/'.$proposal['image']; ?>" class="img-fluid" alt="">
                  <p class="mt-4 mb-4"><?php echo $proposal['description']; ?></p>
                  <h4><i><?php echo number_format($proposal['min_price']) . " - " . number_format($proposal['max_price']);?> PHP</i></h4>
                </div>
                <div class="col-md-6">
                  <div class="card" style="height: 600px;">
                    <div class="card-header"><h2>All Offers</h2></div>
                    <div class="card-body overflow-auto">

                      <?php $getOffersByProposalID = $offerObj->getOffersByProposalID($proposal['proposal_id']); ?>
                      <?php foreach ($getOffersByProposalID as $offer) { ?>
                      <div class="offer">
                        <h4><?php echo $offer['username']; ?> <span class="text-primary">( <?php echo $offer['contact_number']; ?> )</span></h4>
                        <small><i><?php echo $offer['offer_date_added']; ?></i></small>
                        <p><?php echo $offer['description']; ?></p>

                        <?php if ($offer['user_id'] == $_SESSION['user_id']) { ?>
                          <form action="core/handleForms.php" method="POST">
                            <div class="form-group">
                              <input type="hidden" class="form-control" value="<?php echo $offer['offer_id']; ?>" name="offer_id" >
                              <input type="submit" class="btn btn-danger" value="Delete" name="deleteOfferBtn">
                            </div>
                          </form>

                          <form action="core/handleForms.php" method="POST" class="updateOfferForm d-none">
                            <div class="form-group">
                              <label for="#">Description</label>
                              <input type="text" class="form-control" value="<?php echo $offer['description']; ?>" name="description">
                              <input type="hidden" class="form-control" value="<?php echo $offer['offer_id']; ?>" name="offer_id" >
                              <input type="submit" class="btn btn-primary form-control" name="updateOfferBtn">
                            </div>
                          </form>
                        <?php } ?>
                        <hr>
                      </div>
                      <?php } ?>
                    </div>
                    <div class="card-footer">
                      <form action="core/handleForms.php" method="POST">
                        <div class="form-group">
                          <label for="#">Description</label>
                          <input type="text" class="form-control" name="description">
                          <input type="hidden" class="form-control" name="proposal_id" value="<?php echo $proposal['proposal_id']; ?>">
                          <input type="submit" class="btn btn-primary float-right mt-4" name="insertOfferBtn"> 
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <script>
       $('.offer').on('dblclick', function (event) {
          var updateOfferForm = $(this).find('.updateOfferForm');
          updateOfferForm.toggleClass('d-none');
        });
    </script>
  </body>
</html>
