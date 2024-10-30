<?php
ob_start();
session_start();
include('connect.php');
include('common.php');

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $availble_query="select * from book where book_id=$book_id";
    $availble_result=mysqli_query($connection,$availble_query);
    $availble_data = mysqli_fetch_assoc( result: $availble_result);

    $book_name=$availble_data['book_name'];
    $author_name=$availble_data['author_name'];
    $book_qty=$availble_data['qty'];

    $qty_query="select * from qty where book_id=$book_id";
    $qty_result=mysqli_query($connection,$qty_query);
    $qty_data = mysqli_fetch_assoc($qty_result);

    $qty=$qty_data['qty'];


    $book_query="select * from order_detail where book_id=$book_id and status=0";
    $book_result=mysqli_query($connection,$book_query);
    $num_row=mysqli_num_rows($book_result);


} else {
    // Redirect to a relevant page if availble ID is not provided
    header("Location: availble.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>availble History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
</head>
<body>

    <?php 
        echo '<div class="container-fluid">';
        navbar(); 
    
    if($num_row==0){

        echo "<h2>All books are availble in the store</h2>";
        
    }

    else{
        
        echo '        <h1 class="text-center mt-3">Availble Details</h1>
        <br>
        <table class="table table-hover" id="tables">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Book Name</th>
                <th>Author Name</th>
                <th>Order Date</th>
                <th>Return Date</th>
            </tr>
        </thead>
        <tbody>';
while ($book_result_row = mysqli_fetch_assoc($book_result)) {

    $order_id=$book_result_row['order_id'];

    $order_query="select * from orders where order_id=$order_id";
    $order_result=mysqli_query($connection,$order_query);
    $order_data = mysqli_fetch_assoc($order_result);



    echo '<tr>
            <td>' . $order_data['customer_name'] . '</td>
            <td>' . $book_name . '</td>
            <td>' . $author_name . '</td>
            <td>' . $order_data['order_date'] . '</td>
            <td>' . $order_data['return_date'] . '</td>

          </tr>';
}
echo '</tbody>
      </table>
        </div>';
    }
    
    ?>

</body>
</html>
