<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please login to access this page");
    exit();
}

// Restrict to admin role
$role = $_SESSION['role'] ?? 'user'; // Default to 'user' if not set
if ($role !== 'admin') {
    header("Location: index.php?message=You must be an admin to issue books");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST['book_id']);
    $user_name = trim($_POST['user_name']);
    $issue_date = date('Y-m-d');

    if (empty($user_name)) {
        $error = "User name is required";
    } else {
        // Check if book exists and is available
        $check_sql = "SELECT available FROM books WHERE book_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            if ($row['available']) {
                $sql = "INSERT INTO issued_books (book_id, user_name, issue_date, return_date) VALUES (?, ?, ?, NULL)";
                $update_sql = "UPDATE books SET available = FALSE WHERE book_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $book_id, $user_name, $issue_date);
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $book_id);

                if ($stmt->execute() && $update_stmt->execute()) {
                    header("Location: view_issued_books.php?message=Book issued successfully");
                    exit();
                } else {
                    $error = "Error issuing book: " . $conn->error;
                }
                $stmt->close();
                $update_stmt->close();
            } else {
                $error = "Book is already issued";
            }
        } else {
            $error = "Book not found";
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Book - Library Management System</title>
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
    <h2>Issue a Book</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($_GET['message'])) echo "<p style='color:green;'>" . htmlspecialchars($_GET['message']) . "</p>"; ?>
    <form method="post">
        <input type="text" name="user_name" placeholder="Enter User Name" required>
        <select name="book_id" required>
            <option value="">Select Book</option>
            <?php
            $sql = "SELECT book_id, title, available FROM books WHERE available = TRUE";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['book_id']) . "'>" . htmlspecialchars($row['title']) . " (Available)</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn">Issue Book</button>
    </form>
</div>
</body>
</html>