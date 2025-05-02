<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>BookHive</h1>
    <span class="welcome">Welcome, <?php echo htmlspecialchars($username); ?></span>
    <form method="post" style="display:inline;">
        <input type="submit" name="logout" value="Logout" class="btn">
    </form>
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
    <h2>Welcome to the Library Management System</h2>
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    <p>This system helps you to manage books, issue them to users, and track issued books efficiently.</p>

    <h3>About Our System</h3>
    <p>The Library Management System automates the book borrowing and tracking process in libraries. It provides features like easy book management, quick search, issuing and returning books, and a user-friendly interface.</p>

    <h3>Rate Our Website</h3>
    <form action="rate.php" method="post">
        <div class="star-rating">
            <span class="star" onclick="setRating(1)">★</span>
            <span class="star" onclick="setRating(2)">★</span>
            <span class="star" onclick="setRating(3)">★</span>
            <span class="star" onclick="setRating(4)">★</span>
            <span class="star" onclick="setRating(5)">★</span>
        </div>
        <input type="hidden" name="rating" id="rating-value" required>
        <br><br>
        <label for="comment">Leave a Comment:</label><br>
        <textarea name="comment" id="comment" rows="4" cols="50" placeholder="Write your feedback here..." required></textarea>
        <br><br>
        <button type="submit" class="btn">Submit Feedback</button>
    </form>
</div>

<script>
    function setRating(value) {
        document.getElementById("rating-value").value = value;
        let stars = document.querySelectorAll(".star");
        stars.forEach((star, index) => {
            if (index < value) {
                star.classList.add("selected");
            } else {
                star.classList.remove("selected");
            }
        });
    }
</script>
</body>
</html>