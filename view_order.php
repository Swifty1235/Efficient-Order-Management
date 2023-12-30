<?php
// Include the database configuration file
include "dbconfig.php";

// Start the session
session_start();

// Check if the cid GET parameter is set or if there is a logged-in user
$customer_id = isset($_GET['cid']) ? $_GET['cid'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

if ($customer_id) {
    // Establish a connection to the database
    $con = mysqli_connect($server, $login, $password, 'CPS3740_2023F');
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fetch all the orders for the customer
    $stmt = $con->prepare("SELECT o.oid, p.Name as product_name, p.Price, p.Quantity as available_qty, o.order_qty, o.order_datetime, p.V_Id FROM Order_romerop AS o INNER JOIN CPS3740.Products AS p ON o.pid = p.P_Id WHERE o.cid = ?");
    if (false === $stmt) {
        die("Prepare failed: " . htmlspecialchars($con->error));
    }

    $stmt->bind_param("i", $customer_id);
    if (!$stmt->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }

    $result = $stmt->get_result();
    if (!$result) {
        die("Getting result set failed: " . htmlspecialchars($stmt->error));
    }

    // Output the table header
    echo '<table border="1">';
    echo '<tr><th>Order ID</th><th>Product Name</th><th>Price</th><th>Available Quantity</th><th>Order Quantity</th><th>Vendor ID</th><th>Date Time</th><th>Actions</th></tr>';

    // Output each order row
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['oid']) . '</td>';
        echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Price']) . '</td>';
        echo '<td>' . htmlspecialchars($row['available_qty']) . '</td>';
        echo '<td>';
        // Input for changing the quantity
        echo '<form action="change_order_quantity.php" method="get">';
        echo '<input type="number" name="new_order_qty" value="' . htmlspecialchars($row['order_qty']) . '" min="1">';
        echo '<input type="hidden" name="oid" value="' . htmlspecialchars($row['oid']) . '">';
        echo '<input type="hidden" name="cid" value="' . htmlspecialchars($customer_id) . '">';
        echo '<input type="submit" value="Change Quantity">';
        echo '</form>';
        echo '</td>';
        echo '<td>' . htmlspecialchars($row['V_Id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['order_datetime']) . '</td>';
        echo '<td>';
        echo '<a href="cancel_order.php?oid=' . htmlspecialchars($row['oid']) . '&cid=' . htmlspecialchars($customer_id) . '">Cancel Order</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';

    // Close the statement and the connection
    $stmt->close();
    $con->close();
} else {
    echo "No customer ID provided or not logged in.";
}
?>
