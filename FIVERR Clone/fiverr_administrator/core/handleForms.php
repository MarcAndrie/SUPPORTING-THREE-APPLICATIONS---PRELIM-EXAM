<?php
require_once '../classloader.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$contact_number = htmlspecialchars(trim($_POST['contact_number']));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);
	$is_fiverr_administrator = isset($_POST['is_fiverr_administrator']) ? 1 : 0;

	if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			if (!$userObj->usernameExists($username)) {

				if ($userObj->registerUser($username, $email, $password, $contact_number, $is_fiverr_administrator)) {
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

if (isset($_GET['logoutBtn'])) {
	$userObj->logout();
	header("Location: ../index.php");
}

if (isset($_POST['updateUserBtn'])) {
	$contact_number = htmlspecialchars($_POST['contact_number']);
	$bio_description = htmlspecialchars($_POST['bio_description']);
	if ($userObj->updateUser($contact_number, $bio_description, $_SESSION['user_id'])) {
		header("Location: ../profile.php");
	}
}

if (isset($_POST['insertOfferBtn'])) {
		$user_id = $_SESSION['user_id'];
		$proposal_id = $_POST['proposal_id'];
		$description = htmlspecialchars($_POST['description']);
		// Check if offer already exists for this user and proposal
		$existingOffer = $offerObj->getOfferByUserAndProposal($user_id, $proposal_id);
		if ($existingOffer) {
			$_SESSION['message'] = "You have already submitted an offer for this proposal.";
			$_SESSION['status'] = '400';
			header("Location: ../index.php");
			exit();
		}
		if ($offerObj->createOffer($user_id, $description, $proposal_id)) {
			header("Location: ../index.php");
		}
}

if (isset($_POST['updateOfferBtn'])) {
	$description = htmlspecialchars($_POST['description']);
	$offer_id = $_POST['offer_id'];
	if ($offerObj->updateOffer($description, $offer_id)) {
		$_SESSION['message'] = "Offer updated successfully!";
		$_SESSION['status'] = '200';
		header("Location: ../index.php");
	}
}

if (isset($_POST['deleteOfferBtn'])) {
	$offer_id = $_POST['offer_id'];
	if ($offerObj->deleteOffer($offer_id)) {
		$_SESSION['message'] = "Offer deleted successfully!";
		$_SESSION['status'] = '200';
		header("Location: ../index.php");
	}
}

if (isset($_POST['addCategoryBtn'])) {
    $category_name = $_POST['category_name'];

    if ($categoryObj->addCategory($category_name)) {
        $_SESSION['message'] = "Category added successfully!";
        $_SESSION['status'] = "200";
    } else {
        $_SESSION['message'] = "Failed to add category.";
        $_SESSION['status'] = "400";
    }
    header("Location: ../manage_category.php");
    exit();
}

if (isset($_POST['deleteCategoryBtn'])) {
    $category_id = $_POST['category_id'];

    if ($categoryObj->deleteCategory($category_id)) {
        $_SESSION['message'] = "Category deleted successfully!";
        $_SESSION['status'] = "200";
    } else {
        $_SESSION['message'] = "Failed to delete category.";
        $_SESSION['status'] = "400";
    }
    header("Location: ../manage_category.php");
    exit();
}

if (isset($_POST['addSubcategoryBtn'])) {
    $category_id = $_POST['category_id'];
    $subcategory_name = $_POST['subcategory_name'];

    if ($subcategoryObj->addSubcategory($category_id, $subcategory_name)) {
        $_SESSION['message'] = "Subcategory added successfully!";
        $_SESSION['status'] = "200";
    } else {
        $_SESSION['message'] = "Failed to add subcategory.";
        $_SESSION['status'] = "400";
    }
    header("Location: ../manage_subcategory.php");
    exit();
}

if (isset($_POST['deleteSubcategoryBtn'])) {
    $subcategory_id = $_POST['subcategory_id'];

    if ($subcategoryObj->deleteSubcategory($subcategory_id)) {
        $_SESSION['message'] = "Subcategory deleted successfully!";
        $_SESSION['status'] = "200";
    } else {
        $_SESSION['message'] = "Failed to delete subcategory.";
        $_SESSION['status'] = "400";
    }
    header("Location: ../manage_subcategory.php");
    exit();
}


?>
