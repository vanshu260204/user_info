<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_info";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Server-side validation function
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Delete operation (delete user data)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete user from database
    $conn->query("DELETE FROM users WHERE id=$id");

    // Redirect to the same page to avoid resubmission of the form
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Create operation (insert user data)
if (isset($_POST['submit'])) {
    $name = validate_input($_POST['name']);
    $state = validate_input($_POST['state']);
    $city = validate_input($_POST['city']);
    $age = validate_input($_POST['age']);
    $email = validate_input($_POST['email']);
    $phone = validate_input($_POST['phone']);

    // Validate inputs
    if (empty($name) || empty($state) || empty($city) || empty($age) || empty($email) || empty($phone)) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo "<script>alert('Invalid phone number format.');</script>";
    } elseif (!is_numeric($age) || $age < 1) {
        echo "<script>alert('Please enter a valid age.');</script>";
    } else {
        // Insert data into database
        $conn->query("INSERT INTO users (name, state, city, age, email, phone) VALUES ('$name', '$state', '$city', $age, '$email', '$phone')");
    }
}

// Update operation (update user data)
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = validate_input($_POST['name']);
    $state = validate_input($_POST['state']);
    $city = validate_input($_POST['city']);
    $age = validate_input($_POST['age']);
    $email = validate_input($_POST['email']);
    $phone = validate_input($_POST['phone']);

    // Validate inputs
    if (empty($name) || empty($state) || empty($city) || empty($age) || empty($email) || empty($phone)) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo "<script>alert('Invalid phone number format.');</script>";
    } elseif (!is_numeric($age) || $age < 1) {
        echo "<script>alert('Please enter a valid age.');</script>";
    } else {
        // Update data in database
        $conn->query("UPDATE users SET name='$name', state='$state', city='$city', age=$age, email='$email', phone='$phone' WHERE id=$id");
    }
}

// Read operation (fetch all users)
$users = $conn->query("SELECT * FROM users");

// Edit operation (fetch user by id)
$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM users WHERE id=$edit_id");
    $edit_row = $edit_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information Form</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        input, select {
            padding: 10px;
            margin: 8px 0;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }

        button:hover {
            background-color: #45a049;
        }

        .form-group {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        td a {
            color: #007BFF;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        .btn-action {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-action:hover {
            background-color: #e53935;
        }

    </style>
</head>
<body>

<div class="container">
    <h2><?php echo isset($edit_row) ? 'Edit' : 'Add'; ?> User Information</h2>

    <form method="POST">
        <?php if (isset($edit_row)): ?>
            <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo isset($edit_row) ? $edit_row['name'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="state">State:</label>
            <select name="state" id="state" required>
                <option value="">Select State</option>
                <option value="California" <?php echo (isset($edit_row) && $edit_row['state'] == 'California') ? 'selected' : ''; ?>>California</option>
                <option value="Texas" <?php echo (isset($edit_row) && $edit_row['state'] == 'Texas') ? 'selected' : ''; ?>>Texas</option>
                <option value="Florida" <?php echo (isset($edit_row) && $edit_row['state'] == 'Florida') ? 'selected' : ''; ?>>Florida</option>
            </select>
        </div>

        <div class="form-group">
            <label for="city">City:</label>
            <select name="city" id="city" required>
                <option value="">Select City</option>
            </select>
        </div>

        <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" name="age" id="age" value="<?php echo isset($edit_row) ? $edit_row['age'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo isset($edit_row) ? $edit_row['email'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" id="phone" value="<?php echo isset($edit_row) ? $edit_row['phone'] : ''; ?>" required>
        </div>

        <button type="submit" name="<?php echo isset($edit_row) ? 'update' : 'submit'; ?>"><?php echo isset($edit_row) ? 'Update' : 'Submit'; ?></button>
    </form>
</div>

<script>
    // Add cities based on the selected state
    const stateCities = {
        "California": ["Los Angeles", "San Francisco", "San Diego"],
        "Texas": ["Houston", "Dallas", "Austin"],
        "Florida": ["Miami", "Orlando", "Tampa"]
    };

    document.getElementById("state").addEventListener("change", function() {
        const selectedState = this.value;
        const citySelect = document.getElementById("city");
        citySelect.innerHTML = '<option value="">Select City</option>'; // Reset city options

        if (selectedState) {
            stateCities[selectedState].forEach(function(city) {
                const option = document.createElement("option");
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }

        // Preselect city if editing an existing user
        if (<?php echo isset($edit_row) ? 'true' : 'false'; ?>) {
            citySelect.value = "<?php echo isset($edit_row) ? $edit_row['city'] : ''; ?>";
        }
    });

    // Trigger the change event to populate cities if editing
    if (<?php echo isset($edit_row) ? 'true' : 'false'; ?>) {
        document.getElementById("state").dispatchEvent(new Event('change'));
    }
</script>

<div class="container">
    <h2>Users List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>State</th>
                <th>City</th>
                <th>Age</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['state']; ?></td>
                    <td><?php echo $row['city']; ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $row['id']; ?>">Edit</a> |
                        <form method="GET" action="" style="display:inline;">
                            <button type="submit" name="delete" value="<?php echo $row['id']; ?>" class="btn-action" onclick="return confirm('Are you sure you want to delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
