<?php

session_start();

include 'C:\xampp\htdocs\web\db.php';
include 'C:\xampp\htdocs\web\Controllers\usercontroller.php';

$controller = new UserController($pdo);
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

// Gestion de l'upload de la photo
$photoName = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
	$uploadDir = "../uploads/";
	$photoName = uniqid() . "-" . basename($_FILES['photo']['name']);
	$targetPath = $uploadDir . $photoName;

	// Vérifiez si le dossier d'upload existe
	if (!is_dir($uploadDir)) {
		mkdir($uploadDir, 0777, true);
	}
    
	move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath);
}
    if ($password === $confirmPassword) {
        if ($controller->createclient($name, $lastName, $email, $password, $photoName)) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Failed to create user.";
        }
    } else {
        $error = "Passwords do not match!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>PET SHOP - Sign Up</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Roboto:wght@700&display=swap" rel="stylesheet">  

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<!-- Topbar Start -->
<div class="container-fluid border-bottom d-none d-lg-block">
    <div class="row gx-0">
        <div class="col-lg-4 text-center py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-geo-alt fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Our Office</h6>
                    <span>123 Street, New York, USA</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-center border-start border-end py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-envelope-open fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Email Us</h6>
                    <span>info@example.com</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-center py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-phone-vibrate fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Call Us</h6>
                    <span>+012 345 6789</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->
<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow-sm py-3 py-lg-0 px-3 px-lg-0">
    <a href="index.html" class="navbar-brand d-flex align-items-center ms-lg-5">
        <img src="img/logo.jpg" alt="Logo" class="me-2" style="width:100px; height:100px; object-fit:contain; border-radius:50%;">
        <h1 class="m-0 text-uppercase text-dark">Shop</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0">
            <a href="index.php" class="nav-item nav-link active">Home</a>
            <a href="about.php" class="nav-item nav-link">About</a>
            <a href="service.php" class="nav-item nav-link">Service</a>
            <a href="product.php" class="nav-item nav-link">Product</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                <div class="dropdown-menu m-0">
                    <a href="price.php" class="dropdown-item">Pricing Plan</a>
                    <a href="team.php" class="dropdown-item">The Team</a>
                    <a href="testimonial.php" class="dropdown-item">Testimonial</a>
                    <a href="blog.php" class="dropdown-item">Blog Grid</a>
                    <a href="detail.php" class="dropdown-item">Blog Detail</a>
                </div>
            </div>
            <?php if (isset($_SESSION['user_id'])) {?>

            <a href="profile.php" class="nav-item nav-link">Profile</a>
            <?php }?>

            <?php if (!isset($_SESSION['user_id'])) {?>

            <a href="login.php" class="nav-item nav-link">Login</a>
            <a href="signup.php" class="nav-item nav-link">Sign Up</a>
            <?php }?>


            <a href="contact.php" class="nav-item nav-link nav-contact bg-primary text-white px-5 ms-lg-5">Contact <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</nav>
<!-- Navbar End -->

<!-- Offer Start -->
<div class="container-fluid bg-offer my-5 py-5">
    <div class="container py-5">
        <div class="row gx-5 justify-content-start">
            <div class="col-lg-7">
                <div class="border-start border-5 border-dark ps-5 mb-5">
                    <h1 class="display-5 text-uppercase text-white mb-0">Sign Up</h1>
                </div>
                <h2 class="text-white mb-4">Create your account and join our pet-loving community!</h2>
            </div>
        </div>
    </div>
</div>
<!-- Offer End -->

<!-- Sign Up Form Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row gx-0 justify-content-center">
            <div class="col-lg-7">
                <div class="bg-light text-center pt-4">
                    <h2 class="text-uppercase">Sign Up</h2>
                    <h6 class="text-body mb-5">Please fill in the form to create an account</h6>
                    <form name="userForm" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="form-group mb-lg">
        <label for="name">Name:</label>
        <input type="text" name="name"  class="form-control input-lg" required><br>
        </div>

        <div class="form-group mb-lg">
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name"  class="form-control input-lg" required><br>
        </div>

        <div class="form-group mb-lg">
        <label for="email">Email:</label>
        <input type="email" name="email"  class="form-control input-lg" required><br>
        </div>

        <div class="form-group mb-lg">
        <label for="password">Password:</label>
        <input type="password" name="password"  class="form-control input-lg" required><br>
        </div>

        <div class="form-group mb-lg">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password"  class="form-control input-lg" required><br>
        </div>


        <div class="form-group mb-lg">
        <label for="photo">Photo:</label>
        <input type="file" name="photo"  class="form-control input-lg"><br>
        </div>
        <div class="row">
								<div class="col-sm-8">
									<div class="checkbox-custom checkbox-default">
										<input id="AgreeTerms" name="agreeterms" type="checkbox"/>
										<label for="AgreeTerms">I agree with <a href="#">terms of use</a></label>
									</div>
								</div>
								<div class="col-sm-4 text-right">
									<button type="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg" value="Create User">Sign Up</button>
								</div>
							</div>


						

							<p class="text-center">Already have an account? <a href="login.php">Sign In!</a>

    </form>   
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Sign Up Form End -->

<!-- Footer Start -->
<div class="container-fluid bg-light mt-5 py-5">
    <div class="container pt-5">
        <div class="row g-5">
            <!-- (Your footer content same as before) -->
            <!-- Keep this as is for consistency -->
        </div>
    </div>
</div>
<div class="container-fluid bg-dark text-white-50 py-4">
    <div class="container">
        <div class="row g-5">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-md-0">&copy; <a class="text-white" href="#">Your Site Name</a>. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Designed by <a class="text-white" href="https://htmlcodex.com">HTML Codex</a></p>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<a href="#" class="btn btn-primary py-3 fs-4 back-to-top"><i class="bi bi-arrow-up"></i></a>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>
