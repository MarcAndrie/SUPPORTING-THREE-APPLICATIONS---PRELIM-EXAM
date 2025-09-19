<?php  
require_once '../classloader.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			if (!$userObj->usernameExists($username)) {

				if ($userObj->registerUser($username, $email, $password)) {
					header("Location: ../login.php");
				}

				else {
					$_SESSION['message'] = "An error occured with the query!";
					$_SESSION['status'] = '400';
					header("Location: ../register.php");
				}
			}

			else {
				$_SESSION['message'] = $username . " as username is already taken";
				$_SESSION['status'] = '400';
				header("Location: ../register.php");
			}
		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}
	}
	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);

	if (!empty($email) && !empty($password)) {

		if ($userObj->loginUser($email, $password)) {
			header("Location: ../index.php");
		}
		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
	}

}

if (isset($_GET['logoutUserBtn'])) {
	$userObj->logout();
	header("Location: ../index.php");
}

if (isset($_POST['insertArticleBtn'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $author_id = $_SESSION['user_id'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $image_path = null;

    if (isset($_FILES['article_image']) && $_FILES['article_image']['error'] == 0) {
        $uploadDir = __DIR__ . "/../../uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = uniqid() . "_" . basename($_FILES["article_image"]["name"]);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["article_image"]["tmp_name"], $targetFile)) {
            $image_path = "/Activities 4-1/MAINTENANCE WORK FOR SCHOOL NEWSPAPER SYSTEM/uploads/" . $fileName;
        }
    }

    if ($articleObj->createArticle($title, $description, $author_id, $category_id, $image_path)) {
        header("Location: ../index.php");
    }
}

if (isset($_POST['editArticleBtn'])) {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$article_id = $_POST['article_id'];
	$category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
	if ($articleObj->updateArticle($article_id, $title, $description, $category_id)) {
		header("Location: ../articles_submitted.php");
	}
}

if (isset($_POST['deleteArticleBtn'])) {
    $article_id = $_POST['article_id'];
    $article = $articleObj->getArticles($article_id);
    if (!$article) {
        echo "Article not found";
        exit;
    }
    $author_id = $article['author_id'];
    $title = $article['title'];
    $deleted = $articleObj->deleteArticle($article_id);

    // Notify author if deleted by admin
    if ($deleted && $author_id) {
        if ($userObj->isAdmin()) {
            $msg = "Your article titled '{$title}' was deleted by an admin.";
            $articleObj->addNotification($author_id, $msg);
        }
    }
    echo $deleted;
    exit;
}

if (isset($_POST['requestEditBtn'])) {
    $article_id = $_POST['article_id'];
    $article = $articleObj->getArticles($article_id);
    if (!$article) {
        echo 0;
        exit;
    }
    $author_id = $article['author_id'];
    $requester_id = $_SESSION['user_id'];
    $title = $article['title'];

    if ($author_id == $requester_id) {
        echo 0; // Cannot request edit for own article
        exit;
    }

    // Notify author of edit request
    $msg = "Edit request for your article titled '{$title}' (ID: {$article_id}) from user ID {$requester_id}.";
    $notified = $articleObj->addNotification($author_id, $msg);

    echo $notified ? 1 : 0;
    exit;
}

if (isset($_POST['acceptEditRequest'])) {
    $notification_id = $_POST['notification_id'];
    $message = $_POST['article_title'];

    // Extract requester ID and article ID from message
    preg_match('/from user ID (\d+)/', $message, $requester_matches);
    preg_match('/\(ID: (\d+)\)/', $message, $article_matches);
    if (isset($requester_matches[1]) && isset($article_matches[1])) {
        $requester_id = $requester_matches[1];
        $article_id = $article_matches[1];

        // Share the article with the requester
        $articleObj->shareArticle($article_id, $requester_id, $_SESSION['user_id']);

        $msg = "Your edit request has been accepted.";
        $articleObj->addNotification($requester_id, $msg);
    }

    // Mark notification as read or delete it
    $articleObj->markNotificationAsRead($notification_id);

    header("Location: ../notifications.php");
    exit;
}

if (isset($_POST['rejectEditRequest'])) {
    $notification_id = $_POST['notification_id'];
    $message = $_POST['article_title'];

    // Extract requester ID from message
    preg_match('/from user ID (\d+)/', $message, $matches);
    if (isset($matches[1])) {
        $requester_id = $matches[1];
        $msg = "Your edit request has been rejected.";
        $articleObj->addNotification($requester_id, $msg);
    }

    // Mark notification as read or delete it
    $articleObj->markNotificationAsRead($notification_id);

    header("Location: ../notifications.php");
    exit;
}
