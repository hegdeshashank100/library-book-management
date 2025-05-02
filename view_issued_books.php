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
    <title>Issued Books - Library Management System</title>
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
    <h2>Issued Books</h2>
    <?php if (isset($_GET['message'])) echo "<p style='color:green;'>" . htmlspecialchars($_GET['message']) . "</p>"; ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Book Title</th>
            <th>User</th>
            <th>Issue Date</th>
            <th>Return Date</th>
            <th>Action</th>
        </tr>
        <?php
        $sql = "SELECT ib.id, b.title, ib.user_name, ib.issue_date, ib.return_date 
                FROM issued_books ib 
                INNER JOIN books b ON ib.book_id = b.book_id 
                ORDER BY ib.return_date IS NULL DESC, ib.issue_date DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['user_name']) . "</td>
                        <td>" . htmlspecialchars($row['issue_date']) . "</td>
                        <td>" . htmlspecialchars($row['return_date'] ?? 'N/A') . "</td>
                        <td>";
                if ($role === 'admin') {
                    echo "<a class='btn btn-edit' href='edit_issued_book.php?id=" . $row['id'] . "'>Edit</a> ";
                    echo "<a class='btn btn-delete' href='return_book.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to return this book?\")'>Return</a>";
                } else {
                    echo "N/A";
                }
                echo "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No books issued</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>