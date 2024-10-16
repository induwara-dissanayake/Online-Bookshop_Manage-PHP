<?php
ob_start();
session_start();
include('connect.php');
include('common.php');

if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    $customer_query="select * from customer where customer_id=$customer_id";
    $customer_result=mysqli_query($connection,$customer_query);
    $customer_data = mysqli_fetch_assoc($customer_result);

    $customer_name=$customer_data['customer_name'];
    $contact=$customer_data['contact'];


} else {
    // Redirect to a relevant page if customer ID is not provided
    header("Location: customer.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
</head>
<body>
    <div class="container-fluid">
        <?php navbar(); ?>
        <h1 class="text-center mt-3">Customer History</h1>

        <div class="row">
            <h3 class="col-md-5 mt-4 mx-3">Customer Name: <?php echo $customer_name; ?></h3>
            <h3 class="col-md-5 mt-4 mx-3">Mobile Number: <?php echo $contact; ?></h3>

        </div>




        <div class="search_box">
            <div class="row_search">
                <input class="book_search" type="text" id="input-box" placeholder="Book Name/ID" autocomplete="off">
                <button class="btn btn-primary" id="search-button"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>
        <br>
        <div id="table-container">
            <!-- The table will be dynamically updated here -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputBox = document.getElementById('input-box');
            const tableContainer = document.getElementById('table-container');

            function loadBooks(query = '') {
                fetch('search_customer_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        query: query,
                        customer_id: <?php echo $customer_id; ?>
                    })
                })
                .then(response => response.text())
                .then(data => {
                    tableContainer.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

            // Load all books when the page loads
            loadBooks();

            // Trigger search on keyup
            inputBox.addEventListener('keyup', () => {
                const query = inputBox.value;
                loadBooks(query);
            });

            // Trigger search on button click
            document.getElementById('search-button').addEventListener('click', () => {
                const query = inputBox.value;
                loadBooks(query);
            });
        });
    </script>
</body>
</html>
