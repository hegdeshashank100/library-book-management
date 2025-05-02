<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please login to access this page");
    exit();
}

$role = $_SESSION['role'] ?? 'user'; // Default to 'user' if not set
if ($role !== 'admin') {
    header("Location: index.php?message=You must be an admin to edit books");
    exit();
}

$error = '';
$book = null;

if (isset($_GET['id'])) {
    $book_id = intval($_GET['id']);
    $sql = "SELECT * FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST['book_id']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $isbn = trim($_POST['isbn']);
    $publication_date = trim($_POST['publication_date']);

    if (empty($title) || empty($author) || empty($isbn)) {
        $error = "Title, author, and ISBN are required";
    } else {
        $check_sql = "SELECT book_id FROM books WHERE isbn = ? AND book_id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $isbn, $book_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "A book with this ISBN already exists";
        } else {
            $sql = "UPDATE books SET title = ?, author = ?, genre = ?, isbn = ?, publication_date = ? WHERE book_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $title, $author, $genre, $isbn, $publication_date, $book_id);
            if ($stmt->execute()) {
                header("Location: view_books.php?message=Book updated successfully");
                exit();
            } else {
                $error = "Error updating book: " . $conn->error;
            }
            $stmt->close();
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
    <title>Edit Book - Library Management System</title>
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
    <h2>Edit Book</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($_GET['message'])) echo "<p style='color:green;'>" . htmlspecialchars($_GET['message']) . "</p>"; ?>
    <?php if ($book): ?>
        <form method="post">
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
            <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            <input type="text" name="genre" value="<?php echo htmlspecialchars($book['genre'] ?? ''); ?>">
            <input type="text" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
            <input type="date" name="publication_date" value="<?php echo htmlspecialchars($book['publication_date'] ?? ''); ?>">
            <button type="submit" class="btn">Update Book</button>
        </form>
    <?php else: ?>
        <p>Book not found.</p>
    <?php endif; ?>
</div>
</body>
</html>