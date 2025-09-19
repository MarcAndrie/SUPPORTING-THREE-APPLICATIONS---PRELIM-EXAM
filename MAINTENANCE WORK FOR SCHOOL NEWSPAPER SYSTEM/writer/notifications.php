<?php require_once 'classloader.php'; ?>
<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}
$user_id = $_SESSION['user_id'];
$notifications = $articleObj->getNotificationsByUserID($user_id);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Notifications</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container mt-4">
    <h3>Your Notifications</h3>
    <?php if (empty($notifications)): ?>
      <div class="alert alert-info">No notifications yet.</div>
    <?php else: ?>
      <ul class="list-group">
        <?php foreach ($notifications as $notif): ?>
          <li class="list-group-item<?php if (!$notif['is_read']) echo ' font-weight-bold'; ?>">
            <?php echo htmlspecialchars($notif['message']); ?>
            <br>
            <small class="text-muted"><?php echo $notif['created_at']; ?></small>
            <?php if (strpos($notif['message'], 'Edit request for your article titled') === 0): ?>
              <form action="core/handleForms.php" method="POST" class="mt-2">
                <input type="hidden" name="notification_id" value="<?php echo $notif['notification_id']; ?>">
                <input type="hidden" name="article_title" value="<?php echo htmlspecialchars($notif['message']); ?>">
                <button type="submit" name="acceptEditRequest" class="btn btn-success btn-sm">Accept</button>
                <button type="submit" name="rejectEditRequest" class="btn btn-danger btn-sm">Reject</button>
              </form>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</body>
</html>