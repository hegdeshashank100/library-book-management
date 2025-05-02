<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $return_date = date('Y-m-d');

    $get_book_sql = "SELECT book_id FROM issued_books WHERE id = ?";
    $get_stmt = $conn->prepare($get_book_sql);
    $get_stmt->bind_param("i", $id);
    $get_stmt->execute();
    $get_result = $get_stmt->get_result();

    if ($get_result->num_rows > 0) {
        $row = $get_result->fetch_assoc();
        $book_id = $row['book_id'];

        $update_sql = "UPDATE issued_books SET return_date = ? WHERE id = ?";
        $avail_sql = "UPDATE books SET available = TRUE WHERE book_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $avail_stmt = $conn->prepare($avail_sql);
        $update_stmt->bind_param("si", $return_date, $id);
        $avail_stmt->bind_param("i", $book_id);

        if ($update_stmt->execute() && $avail_stmt->execute()) {
            header("Location: view_issued_books.php?message=Book returned successfully");
            exit();
        } else {
            $error = "Error returning book: " . $conn->error;
        }
        $update_stmt->close();
        $avail_stmt->close();
    } else {
        $error = "Invalid issue record";
    }
    $get_stmt->close();
} else {
    $error = "Invalid request";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book - Library Management System</title>
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
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="manage_users.php">Manage Users</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="container">
    <h2>Return Book</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($_GET['message'])) echo "<p style='color:green;'>" . htmlspecialchars($_GET['message']) . "</p>"; ?>
</div>
</body>
</html>