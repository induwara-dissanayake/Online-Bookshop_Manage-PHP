<?php
include('connect.php');
include('common.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
$query = isset($_POST['query']) ? $_POST['query'] : '';
$query = mysqli_real_escape_string($connection, $query);

// Prepare SQL query based on search input
if ($query != '') {
    $sql = "SELECT * FROM order_detail 
            WHERE (book_id LIKE '%$query%' OR book_name LIKE '%$query%' OR author_name LIKE '%$query%') 
            AND customer_id = $customer_id";
} else {
    $sql = "SELECT * FROM order_detail WHERE customer_id = $customer_id";
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
                    <th>Book ID</th>
                    <th>Book Name</th>
                    <th>Author Name</th>
                </tr>
            </thead>
            <tbody>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>' . $row['book_id'] . '</td>
                <td>' . $row['book_name'] . '</td>
                <td>' . $row['author_name'] . '</td>
              </tr>';
    }
    echo '</tbody>
          </table>';
} else {
    echo '<br><center><h2><b><i>No Results</i></b></h2></center>';
}
?>
