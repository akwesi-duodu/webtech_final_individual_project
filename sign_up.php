<?php
// Include database configuration
require_once 'dbconfig.php';

// Initialize variables for form data and errors
$first_name = $last_name = $email = $password = $confirm_password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    // Validate input data
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email already exists
$stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $errors[] = "Email is already registered.";
}

$stmt->close();


// If no errors, insert data into the database
if (empty($errors)) {
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $password_hash);

    if ($stmt->execute()) {
        // Redirect to login page or display success message
        header("Location: login.php?signup=success");
        exit;
    } else {
        $errors[] = "Error occurred during registration. Please try again.";
    }

    $stmt->close();
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
    <title>Sign Up - Aero Reservations</title>
</head>
<body>
<nav>
    <div class="nav__logo">Aero Reservations</div>
    <ul class="nav__links">
        <li class="link"><a href="index.php">Home</a></li>
        <li class="link"><a href="about_us.php">About Us</a></li>
        <li class="link"><a href="seats.php">Offers & Destinations</a></li>
    </ul>
    <li class="link"><a href="sign_up.php">Sign up</a></li>
    <li class="link"><a href="login.php">Login</a></li>
    <button class="btn">Contact</button>
</nav>

<section class="section__container signup__container">
    <h2 class="section__header">Create Your Aero Reservations Account</h2>
    <form class="signup__form" method="POST" action="">
        <?php if (!empty($errors)): ?>
            <div class="error__messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="input__group">
            <input type="text" name="first_name" value="<?php echo $first_name; ?>" required />
            <label>First Name</label>
        </div>
        <div class="input__group">
            <input type="text" name="last_name" value="<?php echo $last_name; ?>" required />
            <label>Last Name</label>
        </div>
        <div class="input__group">
            <input type="email" name="email" value="<?php echo $email; ?>" required />
            <label>Email Address</label>
        </div>
        <div class="input__group">
            <input type="password" name="password" required />
            <label>Create Password</label>
        </div>
        <div class="input__group">
            <input type="password" name="confirm_password" required />
            <label>Confirm Password</label>
        </div>
        <button type="submit" class="btn">Sign Up</button>
    </form>
    <div class="login__link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</section>

<footer class="footer">
    <style>
        .footer {
            background-color: #0f172a; /* Deep navy blue background */
            color: white;             /* White text for contrast */
            padding: 20px;            /* Adds spacing inside the footer */
            text-align: left;         /* Aligns text to the left */
        }

        .footer__col h3, 
        .footer__col h4, 
        .footer__col p {
            margin: 5px 0;            /* Adds spacing between elements */
        }
    </style>
    <div class="section__container footer__container">
        <div class="footer__col">
            <h3>Aero Reservations</h3>
            <p>Where Excellence Takes Flight...</p>
        </div>
        <div class="footer__col">
            <h4>INFORMATION</h4>
            <p>Home</p>
            <p>About</p>
            <p>Offers & Destinations</p>
        </div>
    </div>
</footer>
</body>
</html>
