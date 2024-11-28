<?php
session_start();
include('connect.php');
include('common.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['date'])) {
    $date = $_GET['date'];

    // Fetch payment details for the specified date
    $sql = "SELECT * FROM payment 
            WHERE DATE(return_date) = '$date'
            ORDER BY return_date DESC";
    $result = mysqli_query($connection, $sql);

    // Calculate the total payment for the specific date
    $total_sql = "SELECT SUM(payment) AS total_payment 
                  FROM payment 
                  WHERE DATE(return_date) = '$date'";
    $total_result = mysqli_query($connection, $total_sql);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_payment = $total_row['total_payment'];
} else {
    echo "Invalid parameters.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details for <?php echo $date; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="./index.css">
</head>
<body>
    <div class="container-fluid">
        <?php navbar(); ?>
        <h1 class="text-center mt-3">Payment Details for <?php echo $date; ?></h1>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Payment Amount (Rs.)</th>
                    <th>Order Date</th>
                    <th>Return Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo $row['customer_name']; ?></td>
                        <td><?php echo $row['payment']; ?></td>
                        <td><?php echo $row['order_date']; ?></td>
                        <td><?php echo $row['return_date']; ?></td>
                    </tr>
                <?php } ?>
                <!-- Total Payment Row -->
                <tr>
                    <td colspan="5" class="text-end"><strong>Total Payment:</strong></td>
                    <td><strong><?php echo number_format($total_payment, 2); ?> Rs.</strong></td>
                </tr>
            </tbody>
        </table>
        <a href="payment_details.php" class="btn btn-secondary mt-3">Back to Payments</a>
    </div>
</body>
</html>
