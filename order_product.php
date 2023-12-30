<?php
// Include your database configuration file
include "dbconfig.php";

// Start the session
session_start();

// Check if the cid GET parameter is set and not empty
if (isset($_GET['cid']) && !empty($_GET['cid'])) {
    $customer_id = $_GET['cid'];

    // Establish a connection to the database
    $con = mysqli_connect($server, $login, $password, $dbname);
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fetch products from the database
    $query = "SELECT P_Id, Name, Price, Quantity, V_Id FROM CPS3740.Products"; 
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Error fetching products: " . mysqli_error($con));
    }

    // Start the table
    echo '<table border="1">';
    echo '<tr><th>pid</th><th>Name</th><th>Price</th><th>Available QTY</th><th>vid</th><th>QTY to order</th></tr>';

    // Loop through each product and create a row
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['P_Id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Price']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Quantity']) . '</td>';
        echo '<td>' . htmlspecialchars($row['V_Id']) . '</td>';
        echo '<td>';
        echo '<form action="place_order.php" method="get">';
        echo '<input type="number" name="pid_order_qty" min="1" required>'; // Using type number to ensure positive values
        echo '<input type="submit" value="Place order">';
        echo '<input type="hidden" name="pid" value="' . htmlspecialchars($row['P_Id']) . '">';
        echo '<input type="hidden" name="cid" value="' . htmlspecialchars($customer_id) . '">';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';

    // Close the connection
    mysqli_close($con);
} else {
    echo "Customer ID is required.";
}
?>
