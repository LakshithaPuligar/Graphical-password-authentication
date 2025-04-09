<?php
session_start();

// Ensure request method is POST and required fields are present
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['selectedImage2'], $_POST['selectedGridCells2'], $_POST['userEmail'])) {
    die("<script>alert('Invalid request! Please try again.'); window.location.href='login.html';</script>");
}

// Retrieve form values
$selectedImage2 = trim($_POST['selectedImage2']); // Selected image from form
$selectedGrids2 = explode(',', $_POST['selectedGridCells2']); // Convert string to array
$selectedGrids2 = array_map(fn($x) => $x + 1, $selectedGrids2); // 🔥 Fix: Increment indices
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

// Fetch correct image & grid cue points for second step
$stmt = $conn->prepare("SELECT image2, cue_points2 FROM registeredusers WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("<script>alert('User not found! Please login again.'); window.location.href='login.html';</script>");
}

// Get correct image from DB
$correctImage2 = trim($user['image2']);
$correctImageWithExtension2 = $correctImage2 . ".jpg"; // Append ".jpg" for validation

// Convert correct cue points into an array
$correctCuePoints2 = explode(',', $user['cue_points2']);

// Debugging output
echo "<pre>";
echo "Selected Image 2: $selectedImage2\n";
echo "Correct Image 2 from DB: $correctImageWithExtension2\n";
echo "Selected Grid Cells 2: ";
print_r($selectedGrids2);
echo "Correct Cue Points 2 from DB: ";
print_r($correctCuePoints2);
echo "</pre>";

// Validate image selection
if (strcasecmp($selectedImage2, $correctImageWithExtension2) !== 0) {
    die("<script>alert('Incorrect image selection! Redirecting to login...'); window.location.href='login.html';</script>");
}

// Validate grid selection
if ($selectedGrids2 == $correctCuePoints2) {
    echo "<h2 style='color:green;'>✅ Image 2 & Grid Selection Verified Successfully!</h2>";
    echo "<script>
    localStorage.setItem('verificationSuccess', 'true');
    window.location.href = 'aboutpage.html';
</script>";
exit();

} else {
    echo "<h2 style='color:red;'>❌ Invalid Grid Selection!</h2>";
    echo "<script>alert('Incorrect grid selection! Please try again.');
    window.location.href='selection_image2.php';</script>";
}

$conn->close();
?>
