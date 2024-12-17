<?php
session_start();
require_once 'dbconfig.php';

$loginMessage = '';

// Check if user is already logged in
// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare SQL to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, password_hash FROM users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];

                // Update last login time
                $update_login_stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
                $update_login_stmt->bind_param("i", $user['user_id']);
                $update_login_stmt->execute();
                $update_login_stmt->close();

                // Redirect to index.php
                header("Location: index.php");
                exit();
            } else {
                $loginMessage = "Invalid email or password.";
            }
        } else {
            $loginMessage = "Invalid email or password.";
        }
        $stmt->close();
    } else {
        $loginMessage = "Invalid email format.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
    <title>Login - Aero Reservations</title>
    <style>
      .login__container {
        max-width: 500px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f4f4f4;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      }

      .login__form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
      }

      .login__form .input__group {
        position: relative;
        margin-bottom: 1rem;
      }

      .login__form input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
      }

      .login__form label {
        position: absolute;
        top: -10px;
        left: 10px;
        background-color: white;
        padding: 0 5px;
        font-size: 12px;
        color: #666;
      }

      .login__form .btn {
        width: 100%;
        padding: 12px;
        background-color: #4a90e2;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }

      .login__form .btn:hover {
        background-color: #357abd;
      }

      .forgot__password {
        text-align: right;
        margin-bottom: 1rem;
      }

      .signup__link {
        text-align: center;
        margin-top: 1rem;
      }

      .error__message {
        color: red;
        text-align: center;
        margin-bottom: 1rem;
      }
    </style>
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

    <section class="section__container login__container">
      <h2 class="section__header">Welcome Back to Aero Reservations</h2>
      
      <?php if (!empty($loginMessage)): ?>
        <div class="error__message">
          <?php echo htmlspecialchars($loginMessage); ?>
        </div>
      <?php endif; ?>

      <form class="login__form" method="POST" action="">
        <div class="input__group">
          <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
          <label>Email Address</label>
        </div>
        <div class="input__group">
          <input type="password" name="password" required />
          <label>Password</label>
        </div>
        <div class="forgot__password">
          <a href="#">Forgot Password?</a>
        </div>
        <button type="submit" class="btn">Login</button>
      </form>
      <div class="signup__link">
        Don't have an account? <a href="sign_up.php">Sign up here</a>
      </div>
    </section>

    <footer class="footer">
      <div class="section__container footer__container">
        <div class="footer__col">
          <h3>Aero Reservations</h3>
          <p>
            Where Excellence Takes Flight. With a strong commitment to customer
            satisfaction and a passion for air travel, Aero Reservations offers
            exceptional service and seamless journeys.
          </p>
          <p>
            From friendly smiles to state-of-the-art aircraft, we connect the
            world, ensuring safe, comfortable, and unforgettable experiences.
          </p>
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
        <div class="socials"></div>
      </div>
    </footer>
  </body>
</html>