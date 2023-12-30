<?php
// Include your database configuration file
include "dbconfig.php";

// Function to check for a valid positive integer
function isValidQuantity($value) {
    return filter_var($value, FILTER_VALIDATE_INT) && (int)$value > 0;
}

// Check if the required GET parameters are set
if (isset($_GET['new_qty'], $_GET['pid'], $_GET['cid'], $_GET['oid'])) {
    $new_qty = $_GET['new_qty'];
    $product_id = $_GET['pid'];
    $customer_id = $_GET['cid'];
    $order_id = $_GET['oid'];

    // Establish a connection to the CPS3740 database
    $con = mysqli_connect($server, $login, $password, 'CPS3740');
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Validate if the new quantity is a positive integer
    if (!isValidQuantity($new_qty)) {
        echo "Please enter a positive integer.";
    } else {
        // Check if the order exists and belongs to the customer in CPS3740_2023F database
        $con->select_db('CPS3740_2023F');
        $order_check_query = "SELECT order_qty FROM Order_romerop WHERE oid = ? AND cid = ?";
        $order_check_stmt = $con->prepare($order_check_query);
        $order_check_stmt->bind_param("ii", $order_id, $customer_id);
        $order_check_stmt->execute();
        $order_result = $order_check_stmt->get_result();

        if ($order_result->num_rows === 0) {
            echo "The order id does not exist or does not belong to you.";
        } else {
            // Check if the new quantity does not exceed the available quantity
            $con->select_db('CPS3740');
            $product_check_query = "SELECT Quantity FROM Products WHERE P_Id = ?";
            $product_check_stmt = $con->prepare($product_check_query);
            $product_check_stmt->bind_param("i", $product_id);
            $product_check_stmt->execute();
            $product_result = $product_check_stmt->get_result();

            if ($product_row = $product_result->fetch_assoc()) {
                if ($new_qty > $product_row['Quantity']) {
                    echo "There is only {$product_row['Quantity']} quantity available.";
                } else {
                    // Update the order quantity in CPS3740_2023F database
                    $con->select_db('CPS3740_2023F');
                    $update_query = "UPDATE Order_romerop SET order_qty = ? WHERE oid = ? AND cid = ?";
                    $update_stmt = $con->prepare($update_query);
                    $update_stmt->bind_param("iii", $new_qty, $order_id, $customer_id);

                    if ($update_stmt->execute()) {
                        echo "Successfully changed the order!";
                    } else {
                        echo "Error changing the order: " . $con->error;
                    }
                }
            } else {
                echo "Product not found.";
            }
        }
    }

    // Close the connection
    $con->close();
} else {
    echo "All parameters must be provided.";
}
?>
