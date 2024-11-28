<?php
ini_set('memory_limit', '1G'); // Increase memory limit to 1GB
ob_start();
session_start();
include('connect.php');
include('common.php');

// Redirect to login page if not authenticated
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Fetch order details
    $sql = "SELECT * FROM orders WHERE order_id=$order_id";
    $result = mysqli_query($connection, $sql);
    $order_data = mysqli_fetch_assoc($result);
    $customer_name = $order_data['customer_name'];
    $customer_id = $order_data['customer_id'];

    // Fetch loan details
    $sqlloan = "SELECT * FROM loan WHERE customer_id=$customer_id";
    $resultloan = mysqli_query($connection, $sqlloan);
    $loandata = mysqli_fetch_assoc($resultloan);
    $num_row_loan = mysqli_num_rows($resultloan);

    if ($num_row_loan > 0) {
        $loan = $loandata['loan'];
    }

    // Calculate payment details
    $order_date = $order_data['order_date'];
    $sql1 = "SELECT * FROM order_detail WHERE order_id=$order_id AND status=0";
    $result1 = mysqli_query($connection, $sql1);
    $num_row1 = mysqli_num_rows($result1);

    date_default_timezone_set('Asia/Colombo');
    $current_date = date('Y-m-d');
    $dateDifference = calculateDateDifference($order_date, $current_date);

    $weeks = floor($dateDifference / 7);
    $days = ($dateDifference + 2) % 7;

    if ($weeks == 0 || $weeks == 1) {
        $payment = $num_row1 * 50;
    } elseif ($weeks == 2 && $days == 2) {
        $payment = $num_row1 * 50;
    } else {
        $payment = $num_row1 * 50 + (30 * ($weeks - 2) + 30) * $num_row1;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Complete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="./index.css">
    <script>
   document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.getElementsByClassName('book-checkbox');
    const selectAllCheckbox = document.querySelector('input[type="checkbox"][onclick="toggleSelectAll(this)"]');
    const currentPaymentElement = document.getElementById('current-payment');
    const totalPayment = <?php echo $payment; ?>;
    const currentPaymentInput = document.getElementById('currentPaymentInput'); // Hidden input for current payment

    function updateCurrentPayment() {
        let selectedCount = 0;
        for (let checkbox of checkboxes) {
            if (checkbox.checked) {
                selectedCount++;
            }
        }
        const currentPayment = selectedCount > 0 ? ((totalPayment / <?php echo $num_row1; ?>) * selectedCount).toFixed(2) : 0;
        currentPaymentElement.textContent = `Current Payment: Rs. ${currentPayment}`;
        currentPaymentInput.value = currentPayment; // Update the hidden input value
    }

    // Event listener for individual checkboxes
    for (let checkbox of checkboxes) {
        checkbox.addEventListener('change', updateCurrentPayment);
    }

    // Event listener for "Select All" checkbox
    selectAllCheckbox.addEventListener('change', function () {
        const isChecked = this.checked;
        for (let checkbox of checkboxes) {
            checkbox.checked = isChecked;
        }
        updateCurrentPayment();
    });
});

    </script>
</head>
<body>
    <div class="container-fluid">
        <?php navbar(); ?>
        <div class="row">
            <h3 class="col-md-3 mt-4">Customer Name: <?php echo $customer_name; ?></h3>
            <h3 class="col-md-3 mt-4">Order Date: <?php echo $order_date; ?></h3>
            <h3 class="col-md-3 mt-4">Total Books: <?php echo $num_row1; ?></h3>
            <div class="col-md-3 mt-4">
                <h3>Total Payment: Rs. <?php echo $payment; ?>.00</h3>
                <h3 id="current-payment" class="mt-3">Current Payment: Rs. 0</h3>
            </div>
        </div>

        <form action="" method="post">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="hidden" name="currentPayment" id="currentPaymentInput" value="0"> <!-- Hidden input for current payment -->
          

            <?php 
            if ($num_row_loan > 0) {
                echo '<h3 class="col-md-3 mt-4">Loan Payment: Rs. ' . $loan . '</h3>';
                echo '<h3 class="col-md-3 mt-4">Total: Rs. ' . ($loan + $payment) . '</h3>';
                echo '<div class="form-check">';
                echo '<input class="form-check-input" type="checkbox" name="loan_paid" id="loanPaid">';
                echo '<label class="form-check-label" for="loanPaid">Loan Paid</label>';
                echo '</div>';
            }
            ?>

            <table class="table table-hover mt-5" id="tables">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleSelectAll(this)" id="selectAllCheckbox"> Select All</th>
                        <th>Book ID</th>
                        <th>Book Name</th>
                        <th>Author Name</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order_detail_data = mysqli_fetch_assoc($result1)) { ?>
                        <tr>
                            <td>
                                <input 
                                    type="checkbox" 
                                    name="selected_books[]" 
                                    value="<?php echo $order_detail_data['book_id']; ?>" 
                                    class="book-checkbox"
                                >
                            </td>
                            <td><?php echo $order_detail_data['book_id']; ?></td>
                            <td><?php echo $order_detail_data['book_name']; ?></td>
                            <td><b><?php echo $order_detail_data['author_name']; ?></b></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Complete Payment</button>
        </form>
    </div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $selected_books = $_POST['selected_books'];
    date_default_timezone_set('Asia/Colombo');
    $current_date = date('Y-m-d');

    $loan_paid = isset($_POST['loan_paid']) ? true : false;
    $loan_payment = $loan_paid ? $loan : 0;
    $current_payment = isset($_POST['currentPayment']) ? floatval($_POST['currentPayment']) : 0; // Use current payment from form   
    if ($loan_paid) {
        $sql_update_loan = "DELETE FROM loan WHERE customer_id=$customer_id";
        mysqli_query($connection, $sql_update_loan);
    }

    if (!empty($selected_books)) {
        $selected_books_str = implode(',', $selected_books);
        $sql = "UPDATE order_detail SET status=1 WHERE order_id=$order_id AND book_id IN ($selected_books_str)";
        if (mysqli_query($connection, $sql)) {
            foreach ($selected_books as $book_id) {
                $sql_update_qty = "UPDATE qty SET qty=qty+1 WHERE book_id=$book_id";
                mysqli_query($connection, $sql_update_qty);
            }

            $sql_check_all = "SELECT COUNT(*) as count FROM order_detail WHERE order_id=$order_id AND status=0";
            $result_check_all = mysqli_query($connection, $sql_check_all);
            $row_check_all = mysqli_fetch_assoc($result_check_all);

            if ($row_check_all['count'] == 0) {
                $sql_update_order = "UPDATE orders SET status=1, return_date='$current_date' WHERE order_id=$order_id";
                mysqli_query($connection, $sql_update_order);
            }

            $sql_insert_payment = "INSERT INTO payment VALUES ($order_id,$customer_id,'$customer_name',$current_payment,'$order_date','$current_date')";
            mysqli_query($connection, $sql_insert_payment);

            echo "<script>window.open('order_pending.php','_self')</script>";
        } else {
            echo "Error updating records: " . mysqli_error($connection);
        }
    } else {
        echo "<script>alert('No books are selected');</script>";
    }
}
?>
