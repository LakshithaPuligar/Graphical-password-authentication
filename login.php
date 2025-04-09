<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gpa_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<script>alert('Database connection failed!'); window.history.back();</script>");
}

// Get form data
$email = isset($_POST['email']) ? trim($_POST['email']) : "";
$category1 = isset($_POST['category1']) ? $_POST['category1'] : "";
$category2 = isset($_POST['category2']) ? $_POST['category2'] : "";

// Validate input
if (empty($email) || empty($category1) || empty($category2)) {
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
    exit();
}

// Check if user exists and verify categories
$stmt = $conn->prepare("SELECT * FROM registeredusers WHERE email = ? AND category1 = ? AND category2 = ?");
$stmt->bind_param("sss", $email, $category1, $category2);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['user_email'] = $email;
    echo "<script>alert('Login successful!'); window.location.href='selection_image.php';</script>";
} else {
    echo "<script>alert('Invalid credentials! Please try again.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
