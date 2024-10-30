<?php
include('connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get query from POST request
$query = isset($_POST['query']) ? $_POST['query'] : '';
$query = mysqli_real_escape_string($connection, $query);

// Prepare SQL query based on search input
$sql = "SELECT * FROM book";
if ($query != '') {
    $sql .= " WHERE book_id LIKE '%$query%' OR book_name LIKE '%$query%' OR author_name LIKE '%$query%' OR isbn LIKE '%$query%'";
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
                    <th>ISBN</th>
                    <th>Availble QTY</th>
             
                </tr>
            </thead>
            <tbody>';
    while ($row = mysqli_fetch_assoc($result)) {
        $book_id=$row['book_id'];
        $sql5 = "select * from qty where book_id=$book_id";
        $result5 = mysqli_query($connection, $sql5);
        $qty_data=mysqli_fetch_assoc($result5);
       
        echo '<tr>
                <td>' . $row['book_id'] . '</td>
                <td>' . $row['book_name'] . '</td>
                <td>' . $row['author_name'] . '</td>
                <td>' . $row['isbn'] . '</td>
                <td>' . $qty_data['qty'] . '</td>
                <td>
                    <center>
                        <a href="availble_history.php?id=' . $row['book_id'] . '" class="btn btn-primary">Details</a>
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
