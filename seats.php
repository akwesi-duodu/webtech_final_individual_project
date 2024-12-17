<?php
// Start the session
session_start();

// Include the database configuration file
require_once 'dbconfig.php'; // dbconfig.php contains the database connection setup

// Ensure user is logged in and has a valid user_id
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user_id from session

// Initialize variables
$booking_status = '';
$is_booking_successful = false; // Flag to track booking success
$total_seats = 18; // Total seats per flight (3 rows * 6 seats)

// Handle Seat Booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $flight_id = $_POST['flight'];
    $seat_number = $_POST['seat_number'];
    $passenger_name = $_POST['passenger_name'];
    $passenger_email = $_POST['passenger_email'];
    $booking_class = $_POST['booking_class'];

    // Check if the seat is already booked for the selected flight
    $check_seat_sql = "SELECT COUNT(*) AS seat_count 
                       FROM bookings 
                       WHERE flight_id = ? AND seat_number = ?";
    $stmt = $conn->prepare($check_seat_sql);
    $stmt->bind_param("is", $flight_id, $seat_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row['seat_count'] > 0) {
        // Seat is already booked
        $booking_status = "Sorry, seat {$seat_number} is already taken. Please choose another seat.";
    } else {
        // Proceed to book the seat
        $insert_booking_sql = "INSERT INTO bookings 
            (user_id, flight_id, booking_date, seat_number, passenger_name, passenger_email, booking_status, total_price, booking_class)
            VALUES (?, ?, CURRENT_TIMESTAMP, ?, ?, ?, 'confirmed', 
                (SELECT ticket_price FROM flights WHERE flight_id = ? LIMIT 1), ?)";
        
        $stmt = $conn->prepare($insert_booking_sql);
        $stmt->bind_param("iisssis", $user_id, $flight_id, $seat_number, $passenger_name, $passenger_email, $flight_id, $booking_class);
        
        if ($stmt->execute()) {
            $is_booking_successful = true;
            $booking_status = "Congratulations! Your seat ({$seat_number}) has been successfully booked for flight {$flight_id}.";
            
            // Update available seats for the flight
            $update_seats_sql = "UPDATE flights 
                                 SET available_seats = available_seats - 1 
                                 WHERE flight_id = ?";
            $update_stmt = $conn->prepare($update_seats_sql);
            $update_stmt->bind_param("i", $flight_id);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            $booking_status = "An error occurred while booking. Please try again.";
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
    <title>Aero Reservations</title>
    <link rel="stylesheet" href="seats_css.css" />
</head>

<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="about_us.php">About Us</a>
        <a href="seats.php">Seat Selection</a>
    </nav>

    <!-- Seat Selection -->
    <div id="seats" class="container">
        <h1>Select Your Seat</h1>
        <form method="POST">
            <!-- Flight Selection -->
            <select name="flight" required>
                <option value="">Select Flight Number</option>
                <option value="1">SK101 - Paris</option>
                <option value="2">SK102 - Tokyo</option>
                <option value="3">SK103 - New York</option>
            </select>

            <!-- Passenger Details -->
            <input type="text" name="passenger_name" placeholder="Enter Passenger Name" required>
            <input type="email" name="passenger_email" placeholder="Enter Passenger Email" required>

            <!-- Booking Class -->
            <select name="booking_class" required>
                <option value="economy">Economy</option>
                <option value="business">Business</option>
                <option value="first_class">First Class</option>
            </select>

            <!-- Seat Map -->
            <div class="seat-map">
                <?php 
                for ($i = 1; $i <= 3; $i++) { // 3 Rows
                    for ($j = 'A'; $j <= 'F'; $j++) { // 6 Columns (A to F)
                        echo "<div class='seat'>
                                <input type='radio' name='seat_number' value='{$i}{$j}' required> {$i}{$j}
                              </div>";
                    }
                }
                ?>
            </div>

            <button type="submit">Confirm Seat Selection</button>
        </form>

        <!-- Booking Status -->
        <?php if (!empty($booking_status)) : ?>
            <p style="color: <?php echo $is_booking_successful ? 'green' : 'red'; ?>; font-weight: bold;">
                <?php echo htmlspecialchars($booking_status); ?>
            </p>
        <?php endif; ?>
    </div>
</body>

<footer class="footer">
    <div class="section__container footer__container">
        <div class="footer__col">
            <h3>Aero Reservations</h3>
            <p>Where Excellence Takes Flight. With a strong commitment to customer satisfaction...</p>
        </div>
        <div class="footer__col">
            <h4>INFORMATION</h4>
            <p>Home</p>
            <p>About</p>
            <p>Offers & Destinations</p>
        </div>
        <div class="footer__col">
            <p>Socials</p>
        </div>
    </div>
    <div class="section__container footer__bar">
        <p>Copyright © 2024 Aero Reservations. All rights reserved.</p>
    </div>
</footer>
</html>

<?php
$conn->close();
?>
