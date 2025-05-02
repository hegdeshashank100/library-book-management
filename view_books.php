<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=Please login to access this page");
    exit();
}

$role = $_SESSION['role'] ?? 'user'; // Default to 'user' if not set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books - Library Management System</title>
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
    <h2>Library Books</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>ISBN</th>
            <th>Publication Date</th>
            <th>Available</th>
            <th>Action</th>
        </tr>
        <?php
        $sql = "SELECT * FROM books";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['book_id']) . "</td>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['author']) . "</td>
                        <td>" . htmlspecialchars($row['genre']) . "</td>
                        <td>" . htmlspecialchars($row['isbn']) . "</td>
                        <td>" . htmlspecialchars($row['publication_date'] ?? 'N/A') . "</td>
                        <td>" . ($row['available'] ? 'Yes' : 'No') . "</td>
                        <td>";
                if ($role === 'admin') {
                    echo "<a class='btn btn-edit' href='edit_book.php?id=" . $row['book_id'] . "'>Edit</a> ";
                    echo "<a class='btn btn-delete' href='delete_book.php?id=" . $row['book_id'] . "' onclick='return confirm(\"Are you sure you want to delete this book?\")'>Delete</a>";
                } else {
                    echo "N/A";
                }
                echo "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No books found</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>