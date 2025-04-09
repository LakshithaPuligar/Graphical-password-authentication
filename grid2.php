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
$stmt = $conn->prepare("SELECT category2 FROM registeredusers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("<script>window.location.href='login.html';</script>");
}

$category2 = $user['category2'];
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Image - Step 2</title>
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
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            width: 300px;
            height: 300px;
            gap: 2px;
            margin-top: 20px;
        }
        .grid-item {
            width: 100px;
            height: 100px;
            background-size: 300px 300px;
            border: 1px solid black;
            cursor: pointer;
            transition: border 0.3s;
        }
        .grid-item.selected-grid {
            border: 2px solid red;
        }
    </style>
</head>
<body>
    <h2>Select an Image from: <?php echo ucfirst($category2); ?></h2>

    <form id="imageSelectionForm" action="final_verification.php" method="POST">
        <div class="image-container">
            <?php
            for ($i = 1; $i <= 9; $i++) {
                echo "<img src='images/$category2/{$category2}$i.jpg' class='selectable' onclick='selectImage(this, \"$category2$i.jpg\")'>";
            }
            ?>
        </div>
        <!-- Hidden fields for selected image, grid selections, and email -->
        <input type="hidden" name="selectedImage2" id="selectedImage2" required>
        <input type="hidden" name="selectedGridCells2" id="selectedGridCells2" required>
        <input type="hidden" name="userEmail" value="<?php echo htmlspecialchars($email); ?>">

        <h3>3x3 Grid Preview (Select Grid Cells)</h3>
        <div class="grid-container" id="gridContainer"></div>

        <button type="submit">Verify Selection</button>
    </form>

    <script>
        let selectedCells2 = []; // Store selected grid cell indexes

        function selectImage(imgElement, imageName) {
            document.querySelectorAll('.selectable').forEach(img => img.classList.remove('selected'));
            imgElement.classList.add('selected');

            document.getElementById("selectedImage2").value = imageName;
            createGrid(imgElement.src);
        }

        function createGrid(imageSrc) {
            const gridContainer = document.getElementById("gridContainer");
            gridContainer.innerHTML = ""; // Clear previous grid
            selectedCells2 = []; // Reset selected grid parts

            for (let row = 0; row < 3; row++) {
                for (let col = 0; col < 3; col++) {
                    let gridItem = document.createElement("div");
                    gridItem.classList.add("grid-item");
                    gridItem.style.backgroundImage = `url('${imageSrc}')`;
                    gridItem.style.backgroundPosition = `${-col * 100}px ${-row * 100}px`;
                    gridItem.dataset.index = row * 3 + col; // Assign index
                    gridItem.onclick = () => toggleGridSelection(gridItem);
                    gridContainer.appendChild(gridItem);
                }
            }
        }

        function toggleGridSelection(gridItem) {
            const index = gridItem.dataset.index;

            if (selectedCells2.includes(index)) {
                selectedCells2 = selectedCells2.filter(i => i !== index);
                gridItem.classList.remove("selected-grid");
            } else {
                selectedCells2.push(index);
                gridItem.classList.add("selected-grid");
            }

            document.getElementById("selectedGridCells2").value = selectedCells2.join(",");
        }
    </script>
</body>
</html>
