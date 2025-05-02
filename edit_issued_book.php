<?php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM issued_books WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['user_name'];
    $new_date = $_POST['issue_date'];

    $update_sql = "UPDATE issued_books SET user_name = '$user_name', issue_date = '$new_date' WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Issued book updated successfully!'); window.location='view_issued_books.php';</script>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Issued Book</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Edit Issued Book</h2>
    <form method="post">
        <input type="text" name="user_name" value="<?php echo $row['user_name']; ?>" required>
        <input type="date" name="issue_date" value="<?php echo $row['issue_date']; ?>" required>
        <input type="submit" value="Update">
    </form>
</div>

</body>
</html>
