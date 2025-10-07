<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "login_demo_2");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Restrict access to admin only
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login_system.php");
    exit();
}

// Delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}

// Toggle user status (active/inactive)
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $result = mysqli_query($conn, "SELECT status FROM users WHERE id=$id");
    $row = mysqli_fetch_assoc($result);
    $newStatus = ($row['status'] == 'active') ? 'inactive' : 'active';
    mysqli_query($conn, "UPDATE users SET status='$newStatus' WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all users
$users = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; margin: 0; }
        h2 { background: #007bff; color: white; padding: 10px; text-align: center; }
        table { width: 90%; margin: 30px auto; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f0f0f0; }
        a.btn { text-decoration: none; padding: 5px 10px; border-radius: 5px; }
        .delete { background: #dc3545; color: white; }
        .toggle { background: #28a745; color: white; }
        .logout { display: block; width: 100px; margin: 20px auto; text-align: center;
                  background: #ff9800; color: white; padding: 8px; border-radius: 5px; }
    </style>
</head>
<body>
<h2>Admin Dashboard - Manage Users</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($users)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars($row['role']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td>
            <a class="btn delete" href="?delete=<?php echo $row['id']; ?>"
               onclick="return confirm('Delete this user?')">Delete</a>
            <a class="btn toggle" href="?toggle=<?php echo $row['id']; ?>">
               Toggle Status
            </a>
        </td>
    </tr>
    <?php } ?>
</table>

<a class="logout" href="login_system.php?action=logout">Logout</a>
</body>
</html>
