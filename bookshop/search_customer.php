<?php
include('connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get query from POST request
$query = isset($_POST['query']) ? $_POST['query'] : '';
$query = mysqli_real_escape_string($connection, $query);

// Prepare SQL query based on search input
$sql = "SELECT * FROM customer";
if ($query != '') {
    $sql .= " WHERE customer_id LIKE '%$query%' OR customer_name LIKE '%$query%' OR contact LIKE '%$query%' OR registation_no LIKE '%$query%'";
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
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Contact</th>
                    <th>Regostation No</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>' . $row['customer_id'] . '</td>
                <td>' . $row['customer_name'] . '</td>
                <td>' . $row['contact'] . '</td>
                <td>' . $row['registation_no'] . '</td>
                <td>' . $row['date'] . '</td>
                <td>
                    <center>
                        <a href="customer_history.php?id=' . $row['customer_id'] . '" class="btn btn-primary">Details</a>
                        <a href="customer_details.php?id=' . $row['customer_id'] . '" class="btn btn-success">Edit</a>
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
