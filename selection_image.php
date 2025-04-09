<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_email'])) {
    die("<script>window.location.href='login.html';</script>");
}

$email = $_SESSION['user_email']; // Store email from session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gpa_project";

$conn = new mysqli($servername, $username, "", $dbname);
if ($conn->connect_error) {
    die("<script>window.history.back();</script>");
}

// Fetch user details
$stmt = $conn->prepare("SELECT category1 FROM registeredusers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("<script>window.location.href='login.html';</script>");
}

$category1 = $user['category1'];
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Image - Step 1</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .selectable {
            width: 100px;
            height: 100px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border 0.3s;
        }
        .selected {
            border: 2px solid blue;
        }
    </style>
</head>
<body>
    <h2>Select an Image from: <?php echo ucfirst($category1); ?></h2>

    <form id="imageSelectionForm" action="verify_image1.php" method="POST">
        <div class="image-container">
            <?php
            for ($i = 1; $i <= 9; $i++) {
                echo "<img src='images/$category1/{$category1}$i.jpg' class='selectable' onclick='selectImage(this, \"$category1$i.jpg\")'>";
            }
            ?>
        </div>
        <!-- Hidden fields for selected image and email -->
        <input type="hidden" name="selectedImage" id="selectedImage" required>
        <input type="hidden" name="userEmail" value="<?php echo htmlspecialchars($email); ?>">

        <button type="submit">Verify Selection</button>
    </form>

    <script>
        function selectImage(imgElement, imageName) {
            // Remove 'selected' class from all images
            document.querySelectorAll('.selectable').forEach(img => img.classList.remove('selected'));

            // Add 'selected' class to the clicked image
            imgElement.classList.add('selected');

            // Store the selected image name in the hidden input field
            document.getElementById("selectedImage").value = imageName;
        }
    </script>
</body>
</html>
