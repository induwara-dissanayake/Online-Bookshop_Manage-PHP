<?php
session_start();
include('connect.php');
include('common.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT 
            MONTHNAME(return_date) AS payment_month, 
            YEAR(return_date) AS payment_year, 
            SUM(payment) AS total_payment, 
            COUNT(*) AS total_transactions 
        FROM payment 
        GROUP BY payment_year, payment_month 
        ORDER BY payment_year DESC, MONTH(return_date) DESC";
$result = mysqli_query($connection, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
</head>
<body>
    <div class="container-fluid">
        <?php navbar(); ?>
        <h1  class="text-center mt-3">Payments by Month</h1>
        <table class="table table-hover mt-4">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Year</th>
                    <th>Total Transactions</th>
                    <th>Total Payment (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['payment_month']; ?></td>
                        <td><?php echo $row['payment_year']; ?></td>
                        <td><?php echo $row['total_transactions']; ?></td>
                        <td><?php echo $row['total_payment']; ?></td>
                        <td>
    <a href="display_payment_details.php?month=<?php echo $row['payment_month']; ?>&year=<?php echo $row['payment_year']; ?>" 
       class="btn btn-primary">
       Details
    </a>
</td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
