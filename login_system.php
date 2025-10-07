<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "login_demo_2");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Session timeout (optional)
$timeout_duration = 300; // 5 minutes
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

// ---------------------------
// LOGIN LOGIC
// ---------------------------
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['status'] = $row['status'];

        // Check if user is inactive
        if ($row['status'] == 'inactive') {
            $error = "Your account is inactive. Contact admin.";
            session_unset();
            session_destroy();
        } else {
            // Redirect based on role
            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                header("Location: login_system.php?page=dashboard");
                exit();
            }
        }
    } else {
        $error = "Invalid username or password!";
    }
}

// ---------------------------
// LOGOUT LOGIC
// ---------------------------
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: login_system.php");
    exit();
}

// ---------------------------
// USER DASHBOARD (Protected)
// ---------------------------
if (isset($_GET['page']) && $_GET['page'] == 'dashboard') {
    if (!isset($_SESSION['username'])) {
        header("Location: login_system.php");
        exit();
    }

    echo "<h2>Welcome, " . htmlspecialchars($_SESSION['username']) . "!</h2>";
    echo "<p>Role: " . htmlspecialchars($_SESSION['role']) . "</p>";
    echo "<p>Status: " . htmlspecialchars($_SESSION['status']) . "</p>";
    echo "<p>This is your user dashboard.</p>";
    echo "<br><a href='login_system.php?action=logout'>Logout</a>";
    exit();
}
?>

<!-- ---------------------------
LOGIN PAGE (HTML)
---------------------------- -->
<!DOCTYPE html>
<html>
<head>
    <title>PHP Login System</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
        }
        .container {
            width: 300px;
            margin: 100px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #999;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
        }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Login System</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" action="">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>
