<?php
session_start();

// Ensure request method is POST and required fields are present
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['selectedImage'], $_POST['selectedGridCells'], $_POST['userEmail'])) {
    die("<script>alert('Invalid request! Please try again.'); window.location.href='login.html';</script>");
}

// Retrieve form values
$selectedImage = trim($_POST['selectedImage']); // Selected image from form
$selectedGrids = explode(',', $_POST['selectedGridCells']); // Convert string to array
$selectedGrids = array_map(fn($x) => $x + 1, $selectedGrids); // 🔥 Fix: Increment indices
$userEmail = trim($_POST['userEmail']); // User email from form

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gpa_project";

$conn = new mysqli($servername, $username, "", $dbname);
if ($conn->connect_error) {
    die("<script>alert('Database connection failed!'); window.location.href='login.html';</script>");
}

// Fetch correct image & grid cue points from database
$stmt = $conn->prepare("SELECT image1, cue_points1 FROM registeredusers WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("<script>alert('User not found! Please login again.'); window.location.href='login.html';</script>");
}

// Get correct image from DB
$correctImage = trim($user['image1']);
$correctImageWithExtension = $correctImage . ".jpg"; // Append ".jpg" for validation

// Convert correct cue points into an array
$correctCuePoints = explode(',', $user['cue_points1']); 

// Debugging output
echo "<pre>";
echo "Selected Image: $selectedImage\n";
echo "Correct Image from DB: $correctImageWithExtension\n";
echo "Selected Grid Cells: ";
print_r($selectedGrids);
echo "Correct Cue Points from DB: ";
print_r($correctCuePoints);
echo "</pre>";

// Validate image selection
if (strcasecmp($selectedImage, $correctImageWithExtension) !== 0) {
    die("<script>alert('Incorrect image selection! Redirecting to login...'); window.location.href='login.html';</script>");
}

// Validate grid selection
if ($selectedGrids == $correctCuePoints) {
    echo "<h2 style='color:green;'>✅ Image & Grid Selection Verified Successfully!</h2>";
    echo "<script>window.location.href='selection_image2.php';</script>"; // Redirect to next step
} else {
    echo "<h2 style='color:red;'>❌ Invalid Grid Selection!</h2>";
    echo "<script>alert('Incorrect grid selection! Please try again.');
    // window.location.href='selection_image1.php';</script>";
}

$conn->close();
?>
