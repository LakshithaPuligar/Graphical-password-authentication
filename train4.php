<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "gpa_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<script>alert('Connection failed!');</script>");
}

// Get form data and trim spaces
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : "";
$email = isset($_POST['email']) ? trim($_POST['email']) : "";
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : "";
$category1 = isset($_POST['category1']) ? $_POST['category1'] : "";
$image1 = isset($_POST['imageId1']) ? $_POST['imageId1'] : "";
$cuePoints1 = isset($_POST['cuePoints1']) ? $_POST['cuePoints1'] : "";
$category2 = isset($_POST['category2']) ? $_POST['category2'] : "";
$image2 = isset($_POST['imageId2']) ? $_POST['imageId2'] : "";
$cuePoints2 = isset($_POST['cuePoints2']) ? $_POST['cuePoints2'] : "";

// Validate inputs
if (empty($first_name) || empty($email) || empty($phone) || empty($category1) || empty($image1) || empty($cuePoints1) || empty($category2) || empty($image2) || empty($cuePoints2)) {
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format!'); window.history.back();</script>";
    exit();
}

if (!preg_match("/^[0-9]{10}$/", $phone)) {
    echo "<script>alert('Phone number must be exactly 10 digits!'); window.history.back();</script>";
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT * FROM registeredusers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('Email is already registered!'); window.history.back();</script>";
    exit();
}
// Hash the sensitive fields
$hashed_category1 = hash('sha256', $category1);
$hashed_image1 = hash('sha256', $image1);
$hashed_cuePoints1 = hash('sha256', $cuePoints1);
$hashed_category2 = hash('sha256', $category2);
$hashed_image2 = hash('sha256', $image2);
$hashed_cuePoints2 = hash('sha256', $cuePoints2);

// Insert into database using prepared statement
$stmt = $conn->prepare("INSERT INTO registeredusers (first_name, email, phone, category1, image1, cue_points1, category2, image2, cue_points2) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
//$stmt->bind_param("sssssssss", $first_name, $email, $phone, $category1, $image1, $cuePoints1, $category2, $image2, $cuePoints2);

$stmt->bind_param("sssssssss", $first_name, $email, $phone, $hashed_category1, $hashed_image1, $hashed_cuePoints1, $hashed_category2, $hashed_image2, $hashed_cuePoints2);


if ($stmt->execute()) {
    echo "<script>
    localStorage.setItem('signupSuccess', 'true');
    window.location.href = 'home.html';
</script>";

} else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
