<?php
session_start();

// Ensure request method is POST and required fields are present
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['selectedImage'], $_POST['userEmail'])) {
    die("<script>alert('Invalid request! Please try again.'); window.location.href='login.html';</script>");
}

// Retrieve selected image and email from the form
$selectedImage = trim($_POST['selectedImage']); // Remove any extra spaces
$email = trim($_POST['userEmail']); 

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gpa_project";

$conn = new mysqli($servername, $username, "", $dbname);
if ($conn->connect_error) {
    die("<script>alert('Database connection failed!'); window.location.href='login.html';</script>");
}

// Fetch the correct image from the database
$stmt = $conn->prepare("SELECT image1 FROM registeredusers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Validate user and image selection
if (!$user) {
    die("<script>alert('User not found! Please login again.'); window.location.href='login.html';</script>");
}

// Get correct image from DB and ensure it matches selected one
$correctImage = trim($user['image1']); 
$correctImageWithExtension = $correctImage . ".jpg"; // Append ".jpg" for validation

// Validate image selection
if (strcasecmp($selectedImage, $correctImageWithExtension) === 0) { // Case-insensitive comparison
    echo "<script>alert('Image verified successfully!'); window.location.href='grid trail1.php';</script>";
} else {
    // Redirect directly to login.html on incorrect selection
    echo "<script>alert('Incorrect image selection! Redirecting to login...'); window.location.href='login.html';</script>";
}
?>
