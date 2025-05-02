<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please login to access this page");
    exit();
}

$role = $_SESSION['role'] ?? 'user'; // Default to 'user' if not set
if ($role !== 'admin') {
    header("Location: index.php?message=You must be an admin to manage users");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        header("Location: manage_users.php?message=User deleted successfully");
        exit();
    } else {
        $error = "Error deleting user: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Library Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Library Management System</h1>
    <span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    <form method="post" style="display:inline;">
        <input type="submit" name="logout" value="Logout" class="btn">
    </form>
    <a href="index.php" class="back-btn">⬅ Back to Home</a>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="add_book.php">Add Book</a></li>
        <li><a href="view_books.php">View Books</a></li>
        <li><a href="issue_book.php">Issue Book</a></li>
        <li><a href="view_issued_books.php">Issued Books</a></li>
        <li><a href="contact_us.php">Contact Us</a></li>
        <?php if ($role === 'admin'): ?>
            <li><a href="manage_users.php">Manage Users</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="container">
    <h2>Manage Users</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($_GET['message'])) echo "<p style='color:green;'>" . htmlspecialchars($_GET['message']) . "</p>"; ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php
        $sql = "SELECT id, username, email, role FROM users";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['role']) . "</td>
                        <td>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                                <input type='submit' name='delete_user' value='Delete' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this user?\")'>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No users found</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>