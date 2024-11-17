<?php
include('connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get query from POST request
$query = isset($_POST['query']) ? $_POST['query'] : '';
$query = mysqli_real_escape_string($connection, $query);

// Prepare SQL query based on search input
if ($query != '') {
    $sql = "
    SELECT * 
    FROM orders 
    WHERE status = 0 
    AND (
        order_id LIKE '%$query%' 
        OR customer_name LIKE '%$query%' 
        OR REPLACE(return_date, '-', '/') LIKE '%$query%' 
        OR customer_name IN (
            SELECT customer_name 
            FROM customer 
            WHERE contact LIKE '%$query%'
        )
    )
";
} else {
    $sql = "SELECT * FROM orders WHERE status = 0";
}

$result = mysqli_query($connection, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($connection);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    echo '<table class="table table-hover" id="tables">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>
            <td>' . $row['order_id'] . '</td>
            <td>' . $row['customer_name'] . '</td>
            <td>' . $row['order_date'] . '</td>
            <td>' . str_replace('-', '/', $row['return_date']) . '</td>
            <td><b>Pending</b></td>
            <td>
                <center>
                    <a href="orderdetails.php?id=' . $row['order_id'] . '" class="btn btn-primary">Details</a>
                </center>
            </td>
          </tr>';
    }
    echo '</tbody>
          </table>';
} else {
    echo '<br><center><h2><b><i>No Results</i></b></h2></center>';
}
?>
