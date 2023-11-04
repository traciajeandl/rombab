<?php 
include '../../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['update_id']) && isset($_POST['update_value']))) {
        $update_id = $_POST['update_id'];
        $update_value = $_POST['update_value'];

        // Ensure the value is within the range of 1 to 5
        $update_value = max(1, min(5, $update_value));

        // Update the database
        $update_quantity_query = mysqli_query($connection, "UPDATE `cart` SET cart_quantity = '$update_value' WHERE cart_id = '$update_id'");
        if ($update_quantity_query) {
            // Successfully updated the database
            echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
        } else {
            // Error updating the database
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
    } else {
        // Invalid request parameters
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orderID'])) {
    $orderID = $_POST['orderID'];

    // Update the read_notif_session in the orders table
    $updateQuery = "UPDATE orders SET read_notif_session = '1' WHERE order_id = '$orderID' AND status = '1'";
    if (mysqli_query($connection, $updateQuery)) {
        // Return a success response
        echo 'Success';
    } else {
        // Handle the error if the update fails
        echo 'Error';
    }
} else {
    // Handle invalid or missing POST data
    echo 'Invalid Request';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['totalBill']) && isset($_POST['userId']) && isset($_POST['tableNo']))) {
    // Get the totalBill, userId, and tableNo from the POST request
    $bill = $_POST['totalBill'];
    $user = $_POST['userId'];
    $table = $_POST['tableNo'];

    // Perform the SQL query to insert data into the billing_history table
    $insertBillingQuery = "INSERT INTO billing_history (user_id, table_no, total_bill) VALUES ('$user', '$table', '$bill')";

        if (mysqli_query($connection, $insertBillingQuery)) {
            // Now, perform the SQL query to update summary_orders
            $updateSummaryOrders = "UPDATE summary_orders SET summary_status = '1' WHERE user_summary_id = '$user' AND summary_table_no = '$table'";

            if (mysqli_query($connection, $updateSummaryOrders)) {
                // Update was successful
                echo 'success';
            } else {
                // Update failed
                echo 'Error updating summary_orders: ' . mysqli_error($connection);
            }
        } else {
            // Insertion into billing_history failed
            echo 'Error inserting data into billing history: ' . mysqli_error($connection);
        }
} else {
    echo 'Error: Invalid Request';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['check_tableNo']) && isset($_POST['check_userId']))) {
    $check_tableNo = $_POST['check_tableNo'];
    $check_userId = $_POST['check_userId'];

    // Query the database to check if a survey record exists
    $check_query = "SELECT * FROM survey WHERE survey_table_no = $check_tableNo AND survey_user_id = $check_userId";
    $check_result = mysqli_query($connection, $check_query);

    if ($check_result) {
        if (mysqli_num_rows($check_result) > 0) {
            // If a survey record exists, send a response indicating it's "existing"
            echo 'existing';
        } else {
            // If no survey record exists, send a response indicating it's "non-existing"
            echo 'non-existing';
        }
    } else {
        // If there's an error in the query, you can log the error for debugging
        echo 'error: ' . mysqli_error($connection);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['submit_tableNo']) && isset($_POST['submit_userId']) && isset($_POST['rating']))) {
    $submit_tableNo = $_POST['submit_tableNo'];
    $submit_userId = $_POST['submit_userId'];
    $rating = $_POST['rating'];

    // Insert the user's rating into the database
    $submit_query = "INSERT INTO survey (survey_table_no, survey_user_id, survey_answer) VALUES ($submit_tableNo, $submit_userId, $rating)";
    $submit_result = mysqli_query($connection, $submit_query);

    if ($submit_result) {
        // If the insertion is successful, send a success response
        echo 'success';
    } else {
        // If there's an error, send an error response
        echo 'error';
    }
}
?>