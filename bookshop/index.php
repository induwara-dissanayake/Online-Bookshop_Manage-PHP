
<?php
ob_start();
session_start();
include('connect.php');
include('common.php');

if (!isset($_SESSION['username'])) {

    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
    
    <style>
        body {
  background-image: url('./img/6.jpg');
  background-repeat: no-repeat;
  background-size: cover;
}
    </style>


</head>
<body>
<div class="container-fluid">
    <?php navbar(); ?>

    <div class="row row_banner">
        <div class="col-md-4 mb-4">
            <div class="banner">
                <a href="order_display.php">
                    <h2>Orders</h2>
                </a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="banner">
                <a href="book_display.php">
                    <h2>Books</h2>
                </a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="banner">
                <a href="customer_display.php">
                    <h2>Customers</h2>
                </a>
            </div>
        </div>
    </div>


    <div class="row row_banner">
        <div class="col-md-4 mb-4">
            <div class="banner">
                <a href="author_display.php">
                    <h2>Authors</h2>
                </a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="banner">
                <a href="available.php">
                    <h2>Availability</h2>
                </a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="banner">
                <a href="payment.php">
                    <h2>Finance</h2>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>    
</body>
</html>
