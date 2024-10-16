<?php
include('connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get query from POST request
$query = isset($_POST['query']) ? $_POST['query'] : '';
$query = mysqli_real_escape_string($connection, $query);

// Prepare SQL query based on search input
$sql = "SELECT * FROM orders";
if ($query != '') {
    $sql .= " WHERE order_id LIKE '%$query%' OR customer_name LIKE '%$query%'";
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
        if ($row['status']==0) {
            echo '<tr>
            <td>' . $row['order_id'] . '</td>
            <td>' . $row['customer_name'] . '</td>
            <td>' . $row['order_date'] . '</td>
            <td>Not Recived</td>
            <td><b>Pending</b></td>';
        }
        else{

            echo '<tr>
            <td>' . $row['order_id'] . '</td>
            <td>' . $row['customer_name'] . '</td>
            <td>' . $row['order_date'] . '</td>
            <td>' . $row['return_date'] . '</td>
            <td><b>Complete</b></td>';
            
        }
      
                echo'
              </tr>';
    }
    echo '</tbody>
          </table>';
} else {
    echo '<br><center><h2><b><i>No Results</i></b></h2></center>';
}
?>
