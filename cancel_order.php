<?php
// Include the database configuration file
include "dbconfig.php";

// Check if the required GET parameters are set
if (isset($_GET['oid']) && isset($_GET['cid'])) {
    $order_id = $_GET['oid'];
    $customer_id = $_GET['cid'];

    // Establish a connection to the database
    $con = mysqli_connect($server, $login, $password, 'CPS3740_2023F');
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare the DELETE statement to cancel the order
    $stmt = $con->prepare("DELETE FROM Order_romerop WHERE oid = ? AND cid = ?");
    $stmt->bind_param("ii", $order_id, $customer_id);

    // Execute the statement and check if the order was successfully deleted
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Successfully deleted the order!";
        } else {
            echo "No order found with the specified ID for this customer, or the order could not be canceled.";
        }
    } else {
        echo "Error cancelling the order: " . $con->error;
    }

    // Close the statement and the connection
    $stmt->close();
    $con->close();
} else {
    echo "Order ID and Customer ID must be provided.";
}
?>
