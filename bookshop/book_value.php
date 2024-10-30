<?php
ob_start();
session_start();
include('connect.php');
include('common.php');


$total=0;
$sql = "SELECT price, book_name FROM book ORDER BY price DESC";
$result = mysqli_query($connection, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">

</head>
<body>


<div class="container-fluid">
        <?php navbar(); ?>
        <h1 class="text-center mt-3">Total Book Values</h1>

        <?php 
        
        while ($row = mysqli_fetch_assoc($result)) {

            $total=$total+$row['price'];
            echo"<h2>".$row["book_name"]."=".$row["price"]."</h2><br>";
        
        }
        
        ?>
<h2>Total book value= <?php echo $total;?></h2>

    </div>


</body>
</html>