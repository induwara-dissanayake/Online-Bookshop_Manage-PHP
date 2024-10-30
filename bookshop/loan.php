<?php
ob_start();
session_start();
include('connect.php');
include('common.php');

    $loan_query="select * from loan";
    $loan_result=mysqli_query($connection,$loan_query);
    $num_row=mysqli_num_rows($loan_result)

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

        echo "<h2>No Loans are Recorded</h2>";
        
    }

    else{
        
        echo '        <h1 class="text-center mt-3">Loan Details</h1>
        <br>
        <table class="table table-hover" id="tables">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Loan Amount</th>
   
            </tr>
        </thead>
        <tbody>';
while ($loan_data = mysqli_fetch_assoc($loan_result)) {

    $customer_id=$loan_data['customer_id'];

    $customer_query="select * from customer where customer_id=$customer_id";
    $customer_result=mysqli_query($connection,$customer_query);
    $customer_data = mysqli_fetch_assoc($customer_result);



    echo '<tr>
            <td>' . $customer_data['customer_name'] . '</td>
            <td> Rs.' . $loan_data['loan'] . '</td>

          </tr>';
}
echo '</tbody>
      </table>
        </div>';
    }
    
    ?>

</body>
</html>
