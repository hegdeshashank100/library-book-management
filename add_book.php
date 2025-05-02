<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please login to access this page");
    exit();
}

// Debug: Check session role
$role = $_SESSION['role'] ?? 'user'; // Default to 'user' if not set
if ($role !== 'admin') {
    header("Location: index.php?message=You must be an admin to add books");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $isbn = trim($_POST['isbn']);
    $publication_date = trim($_POST['publication_date']);

    if (empty($title) || empty($author) || empty($isbn)) {
        $error = "Title, author, and ISBN are required";
    } else {
        // Check for duplicate ISBN
        $check_sql = "SELECT book_id FROM books WHERE isbn = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $isbn);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "A book with this ISBN already exists";
        } else {
            $sql = "INSERT INTO books (title, author, genre, isbn, publication_date, available) VALUES (?, ?, ?, ?, ?, TRUE)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $title, $author, $genre, $isbn, $publication_date);
            if ($stmt->execute()) {
                header("Location: index.php?message=Book added successfully");
                exit();
            } else {
                $error = "Error adding book: " . $conn->error;
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
    <title>Add Book - Library Management System</title>
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
    <h2>Add New Book</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($_GET['message'])) echo "<p style='color:green;'>" . htmlspecialchars($_GET['message']) . "</p>"; ?>
    <form method="post">
        <input type="text" name="title" placeholder="Book Title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
        <input type="text" name="author" placeholder="Author" value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>" required>
        <input type="text" name="genre" placeholder="Genre" value="<?php echo isset($_POST['genre']) ? htmlspecialchars($_POST['genre']) : ''; ?>">
        <input type="text" name="isbn" placeholder="ISBN" value="<?php echo isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : ''; ?>" required>
        <input type="date" name="publication_date" value="<?php echo isset($_POST['publication_date']) ? htmlspecialchars($_POST['publication_date']) : ''; ?>">
        <button type="submit" class="btn">Add Book</button>
    </form>
</div>
</body>
</html>