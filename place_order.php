<?php
// Include the database configuration file
include "dbconfig.php";

// Function to check for a valid positive integer
function isValidQuantity($value) {
    return filter_var($value, FILTER_VALIDATE_INT) && (int)$value > 0;
}

// Check if the required GET parameters are set
if (isset($_GET['cid'], $_GET['pid'], $_GET['pid_order_qty'])) {
    $customer_id = $_GET['cid'];
    $product_id = $_GET['pid'];
    $order_quantity = $_GET['pid_order_qty'];

    // Establish a connection to the database
    $con = mysqli_connect($server, $login, $password, 'CPS3740'); // Connect to the database that has Customers and Products
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Validate if the input quantity is a positive integer
    if (!isValidQuantity($order_quantity)) {
        echo "The order quantity must be a positive integer. The order has not been successfully placed.";
    } else {
        // Fetch the available quantity of the product from the CPS3740 database
        $stmt = $con->prepare("SELECT Quantity FROM CPS3740.Products WHERE P_Id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $available_qty = $row['Quantity'];
            
            // Check if the requested order quantity exceeds the available quantity
            if ($order_quantity > $available_qty) {
                echo "There is only $available_qty quantity available. The order is not successfully placed.";
            } else {
                // Now connect to the CPS3740_2023F database for inserting into Order_romerop
                $con->select_db('CPS3740_2023F');
                
                // Insert the order into the Order_romerop table with the current timestamp
                $insert_query = "INSERT INTO Order_romerop (cid, pid, order_qty, order_datetime) VALUES (?, ?, ?, NOW())";
                $insert_stmt = $con->prepare($insert_query);
                $insert_stmt->bind_param("iii", $customer_id, $product_id, $order_quantity);
                
                if ($insert_stmt->execute()) {
                    echo "Order placed successfully!";
                } else {
                    echo "Error placing the order: " . $con->error;
                }
            }
        } else {
            echo "Product not found.";
        }
    }
    // Close the connection
    $con->close();
} else {
    echo "The required parameters are not set.";
}
?>

