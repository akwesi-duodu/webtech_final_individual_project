<?php
session_start();
require_once 'dbconfig.php';

// Handle logout
if (isset($_GET['logout'])) {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Redirect to index page
    header("Location: index.php");
    exit();
}

// Handle newsletter subscription
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subscribe'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (!$conn) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO NewsletterSubscribers (email, subscribed_at) VALUES (?, CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE subscribed_at = CURRENT_TIMESTAMP");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        try {
            if ($stmt->execute()) {
                $subscriptionMessage = "Thank you for subscribing!";
            } else {
                $subscriptionMessage = "Sorry, there was an error. Please try again.";
            }
            $stmt->close();
        } catch (Exception $e) {
            $subscriptionMessage = "Error: " . $e->getMessage();
        }
    } else {
        $subscriptionMessage = "Invalid email address. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Aero Reservations</title>
</head>
<body>
<nav>
    <div class="nav__logo">Aero Reservations</div>
    <ul class="nav__links">
        <li class="link"><a href="index.php">Home</a></li>
        <li class="link"><a href="about_us.php">About</a></li>
        <li class="link"><a href="seats.php">Seats</a></li>
        <li class="link"><a href="reviews.php">Reviews</a></li>
        <?php if (isset($_SESSION['user'])): ?>
            <li class="link"><a href="profile.php">My Profile</a></li>
            <li class="link"><a href="?logout=true">Logout</a></li>
        <?php else: ?>
            <li class="link"><a href="sign_up.php">Sign Up</a></li>
            <li class="link"><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
    <button class="btn">Contact</button>
</nav>

<header class="section__container header__container">
    <?php if (isset($_SESSION['user'])): ?>
        <h1 class="section__header">Welcome, <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'User'); ?><br>Find And Book Your Experience</h1>
    <?php else: ?>
        <h1 class="section__header">Find And Book<br>A Great Experience</h1>
    <?php endif; ?>
    <img src="header.png" alt="header">
</header>

<section class="section__container plan__container">
    <p class="subheader">TRAVEL SUPPORT</p>
    <h2 class="section__header">Plan your travel with confidence</h2>
    <p class="description">Find help with your bookings and travel plans, see what to expect along your journey.</p>
    <div class="plan__grid">
        <div class="plan__content">
            <span class="number">01</span>
            <h4>Travel Requirements for Dubai</h4>
            <p>Stay informed and prepared for your trip to Dubai with essential travel requirements, ensuring a smooth and hassle-free experience in this vibrant and captivating city.</p>
            <span class="number">02</span>
            <h4>Multi-risk travel insurance</h4>
            <p>Comprehensive protection for your peace of mind, covering a range of potential travel risks and unexpected situations.</p>
            <span class="number">03</span>
            <h4>Travel Requirements by destinations</h4>
            <p>Stay informed and plan your trip with ease, as we provide up-to-date information on travel requirements specific to your desired destinations.</p>
        </div>
    </div>
</section>

<section class="subscribe">
    <div class="section__container subscribe__container">
        <h2 class="section__header">Subscribe newsletter & get latest news</h2>
        <form class="subscribe__form" method="POST">
            <input type="text" name="email" placeholder="Enter your email here" required>
            <button type="submit" name="subscribe" class="btn">Subscribe</button>
        </form>
        <?php if (isset($subscriptionMessage)): ?>
            <p><?php echo htmlspecialchars($subscriptionMessage); ?></p>
        <?php endif; ?>
    </div>
</section>

<footer class="footer">
    <div class="section__container footer__container">
        <div class="footer__col">
            <h3>Aero Reservations</h3>
            <p>Where Excellence Takes Flight. With a strong commitment to customer satisfaction and a passion for air travel, Aero Reservations offers exceptional service and seamless journeys.</p>
            <p>From friendly smiles to state-of-the-art aircraft, we connect the world, ensuring safe, comfortable, and unforgettable experiences.</p>
        </div>
        <div class="footer__col">
            <h4>INFORMATION</h4>
            <p>Home</p>
            <p>About</p>
            <p>Seats</p>
        </div>
    </div>
    <div class="section__container footer__bar">
        <p>Copyright &copy; 2024 Aero Reservations. All rights reserved.</p>
    </div>
</footer>
</body>
</html>