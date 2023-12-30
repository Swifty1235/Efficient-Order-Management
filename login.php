<?php
// Include your database configuration file
include "dbconfig.php";

// Start the session
session_start();

// Establish a connection to the database
$con = mysqli_connect($server, $login, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form data is submitted
if (isset($_POST['username']) && isset($_POST['password'])) {
    $browser_username = mysqli_real_escape_string($con, $_POST['username']);
    $browser_password = mysqli_real_escape_string($con, $_POST['password']);

    // Query the database for the user
    $stmt = $con->prepare("SELECT * FROM CPS3740.Customers WHERE login = ?");
    $stmt->bind_param("s", $browser_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Validate the password
        if ($browser_password == $row['password']) {
            // Set session variables with the user's details
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_dob'] = $row['DOB'];
            $_SESSION['user_gender'] = $row['gender'];
            $_SESSION['user_address'] = $row['street'] . ', ' . $row['city'] . ', ' . $row['state'] . ', ' . $row['zipcode'];
            $_SESSION['user_img'] = 'data:image/jpeg;base64,' . base64_encode($row['img']);

            // Redirect to user home page with cid parameter
            header("Location: Userhomepage.php?cid=" . $row['id']);
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "Username not found.";
    }
} else {
    echo "Login form data not set.";
}

// Close the connection
mysqli_close($con);
?>

