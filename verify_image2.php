<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_email'])) {
    die("<script>window.location.href='login.html';</script>");
}

$email = $_SESSION['user_email'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gpa_project";

$conn = new mysqli($servername, $username, "", $dbname);
if ($conn->connect_error) {
    die("<script>window.history.back();</script>");
}

// Ensure an image was selected
if (!isset($_POST['selectedImage']) || empty($_POST['selectedImage'])) {
    die("<script>alert('Please select an image.'); window.history.back();</script>");
}

$selectedImage = $_POST['selectedImage'];

// Retrieve the correct image for verification
$stmt = $conn->prepare("SELECT image2 FROM registeredusers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("<script>alert('User not found. Please log in again.'); window.location.href='login.html';</script>");
}

// Append ".jpg" to match selected image format
$correctImage = trim($user['image2']) . ".jpg";

if ($selectedImage === $correctImage) {
    echo "<script>alert('Image verification successful!'); window.location.href='grid2.php';</script>";
} else {
    echo "<script>alert('Image verification failed!'); window.history.back();</script>";
}

$conn->close();
?>
