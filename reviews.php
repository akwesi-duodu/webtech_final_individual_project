<?php
session_start();
include 'dbconfig.php'; // Database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $comments = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_STRING);

    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($comments)) $errors[] = "Comments are required";

    // If no errors, insert the review
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reviews (name, email, comments) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $comments]);
            $_SESSION['success_message'] = "Review submitted successfully!";
            header("Location: reviews.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error submitting review: " . $e->getMessage();
        }
    }
}

// Fetch existing reviews
try {
    $reviews_stmt = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC");
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $reviews = [];
    $errors[] = "Error fetching reviews: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>App Reviews</title>
    <link rel="stylesheet" href="reviews_styling.css">
    <style>
        /* Navigation Bar Styling */
        .navbar {
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="seats.php">Seats</a>
        <a href="about_us.php">About Us</a>
    </div>

    <div class="container">
        <h1>App Reviews</h1>
        
        <!-- Success Message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Review Submission Form -->
        <form method="POST" action="reviews.php" class="review-form">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required 
                    value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" required rows="4">
                    <?php echo isset($comments) ? htmlspecialchars($comments) : ''; ?>
                </textarea>
            </div>

            <button type="submit" class="btn-submit">Submit Review</button>
        </form>

        <!-- Existing Reviews -->
        <div class="reviews-list">
            <h2>Previous Reviews</h2>
            <?php if (empty($reviews)): ?>
                <p>No reviews yet. Be the first to leave a review!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <h3><?php echo htmlspecialchars($review['name']); ?></h3>
                        </div>
                        <p class="review-date">
                            <?php echo date('F j, Y, g:i a', strtotime($review['created_at'])); ?>
                        </p>
                        <p class="review-text"><?php echo htmlspecialchars($review['comments']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
