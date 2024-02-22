<?php
// Connect to your MySQL database
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "table3";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the Accept button is clicked
if(isset($_POST['accept_button']) && !empty($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Retrieve order details from orders table
    $sql_select_order = "SELECT * FROM orders WHERE id = $order_id";
    $result = $conn->query($sql_select_order);

    if ($result->num_rows > 0) {
        // Insert order details into checks table
        $row = $result->fetch_assoc();
        $customer_name = $row['customer_name'];
        $order_details = $row['order_details'];

        $sql_insert_check = "INSERT INTO checks (customer_name, order_details) VALUES ('$customer_name', '$order_details')";
        if ($conn->query($sql_insert_check) === TRUE) {
            // Delete order from orders table
            $sql_delete_order = "DELETE FROM orders WHERE id = $order_id";
            if ($conn->query($sql_delete_order) === TRUE) {
                echo "Order accepted successfully.";
            } else {
                echo "Error deleting order: " . $conn->error;
            }
        } else {
            echo "Error inserting into checks table: " . $conn->error;
        }
    } else {
        echo "Order not found.";
    }
}
?>

<!-- Display orders -->
<table>
    <tr>
        <th>Customer Name</th>
        <th>Order Details</th>
        <th>Action</th>
    </tr>
    <?php
    $sql_select_orders = "SELECT * FROM orders";
    $result = $conn->query($sql_select_orders);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['customer_name'] . "</td>";
            echo "<td>" . $row['order_details'] . "</td>";
            echo "<td><form method='post'><input type='hidden' name='order_id' value='" . $row['id'] . "'><input type='submit' name='accept_button' value='Accept'></form></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No orders found.</td></tr>";
    }
    ?>
</table>

<?php
// Close database connection
$conn->close();
?>
