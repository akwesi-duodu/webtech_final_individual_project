<?php
// Start the session (optional)
session_start();
require_once 'dbconfig.php';            
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define variables for form data
$name = $email = $message = "";
$name_err = $email_err = $message_err = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Name is required.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Email is required.";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        }
    }

    // Validate message
    if (empty(trim($_POST["message"]))) {
        $message_err = "Message cannot be empty.";
    } else {
        $message = trim($_POST["message"]);
    }

    // If there are no errors, insert data into the database
    if (empty($name_err) && empty($email_err) && empty($message_err)) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO reviews (name, email, comments) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<p>Thank you for your feedback, $name. Your message has been recorded.</p>";
            // Clear the form values
            $name = $email = $message = "";
        } else {
            echo "<p>Sorry, something went wrong. Please try again later.</p>";
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aero Reservations</title>
    <link rel="stylesheet" href="aboutus_css.css" />
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="seats.php">Destinations</a>
        <a href="seats.php">Seat Selection</a>
        <a href="about_us.php">About Us</a>
    </nav>

    <div id="about" class="container">
        <h1>About Aero Reservations</h1>
        <p>Welcome to Aero Reservations, where your journey becomes our passion. Since our establishment, we've been committed to providing exceptional air travel experiences while maintaining the highest standards of safety and customer service.</p>

        <!-- Company Stats -->
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">150+</div>
                <div>Destinations</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">200</div>
                <div>Aircrafts</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">20M+</div>
                <div>Passengers Yearly</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">98%</div>
                <div>Satisfaction Rate</div>
            </div>
        </div>

        <!-- Company History -->
        <h2>Our Journey</h2>
        <div class="timeline">
            <div class="timeline-item">
                <h3>1995</h3>
                <p>Founded with just 3 aircraft serving domestic routes</p>
            </div>
            <div class="timeline-item">
                <h3>2000</h3>
                <p>Expanded to international destinations with 20 aircraft</p>
            </div>
            <div class="timeline-item">
                <h3>2010</h3>
                <p>Launched our premium class service and loyalty program</p>
            </div>
            <div class="timeline-item">
                <h3>2020</h3>
                <p>Achieved carbon neutrality and introduced sustainable aviation initiatives</p>
            </div>
            <div class="timeline-item">
                <h3>2024</h3>
                <p>Celebrating our continuous growth with 200+ aircraft and global presence</p>
            </div>
        </div>

        <!-- Leadership Team -->
        <h2>Our Core Staff</h2>
        <div class="team-grid">
            <div class="team-member">
                <img src="CEO.jpg" alt="CEO">
                <h3>Jonny Dones</h3>
                <p>Reservations Officer</p>
            </div>
            <div class="team-member">
                <img src="COO.jpg" alt="COO">
                <h3>Sarah Hughes</h3>
                <p>Heads of Operations</p>
            </div>
            <div class="team-member">
                <img src="CTO.jpg" alt="CTO">
                <h3>Michael Alonso</h3>
                <p>Chief Technology Officer</p>
            </div>
        </div>

        <!-- Mission and Values -->
        <h2>Our Mission</h2>
        <p>To connect people and places with comfort, reliability, and sustainability, while delivering exceptional value to our customers, employees, and stakeholders.</p>

        <h2>Our Values</h2>
        <ul>
            <li>Safety First: Unwavering commitment to passenger and crew safety</li>
            <li>Customer Focus: Delivering exceptional service at every touchpoint</li>
            <li>Innovation: Embracing new technologies and solutions</li>
            <li>Sustainability: Committed to reducing our environmental impact</li>
            <li>Integrity: Operating with transparency and ethical standards</li>
        </ul>

        <!-- Certifications -->
        <h2>Our Certifications</h2>
        <p>We maintain the highest industry standards with certifications from:</p>
        <ul>
            <li>International Air Transport Association (IATA)</li>
            <li>Federal Aviation Administration (FAA)</li>
            <li>European Union Aviation Safety Agency (EASA)</li>
            <li>ISO 9001:2015 Quality Management</li>
        </ul>

        <!-- Contact Section -->
        <h2>Connect With Us</h2>
        <p>Have questions or feedback? We'd love to hear from you!</p>

        <!-- Contact Form -->
        <form method="POST" action="about_us.php">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>
            <span class="error"><?php echo $name_err; ?></span><br><br>

            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            <span class="error"><?php echo $email_err; ?></span><br><br>

            <label for="message">Your Message:</label><br>
            <textarea id="message" name="message" rows="4" cols="50" required><?php echo $message; ?></textarea><br><br>
            <span class="error"><?php echo $message_err; ?></span><br><br>

            <button type="submit">Send Message</button>
        </form>
    </div>
    <script>
        // Hide the success message after 10 seconds
        const successMessage = document.getElementById("success-message");
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = "none";
            }, 10000); // 10000 milliseconds = 10 seconds
        }
    </script>
  <footer class="footer">
    <style>
        .footer {
            background-color: #334c99; /* Deep navy blue background */
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




