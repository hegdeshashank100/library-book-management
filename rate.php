<?php
include 'connect.php'; // Ensure this file connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = intval($_POST["rating"]);
    $comment = trim($_POST["comment"]);

    if ($rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO feedback (rating, comment) VALUES (?, ?)");
        $stmt->bind_param("is", $rating, $comment);

        if ($stmt->execute()) {
            echo "<script>alert('Feedback submitted successfully!'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Error submitting feedback!'); window.location.href='index.php';</script>";
        }
        
        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Invalid rating! Please select a valid rating.'); window.location.href='index.php';</script>";
    }
}
?>
