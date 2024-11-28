<?php
session_start();
include('connect.php');
include('common.php');

if (!isset($_SESSION['username'])) {

    header('Location: login.php');
    exit();
}

// Initialize session arrays if not set
if (!isset($_SESSION['book_ids'])) {
    $_SESSION['book_ids'] = [];
}

if (!isset($_SESSION['books'])) {
    $_SESSION['books'] = [];
}

// Handle adding a book to the session
if (isset($_POST['add_book'])) {
    $book_id = $_POST['book_id'];
    $book_sql = "SELECT * FROM book WHERE book_id = $book_id";
    $book_result = mysqli_query($connection, $book_sql);
    $row = mysqli_fetch_assoc($book_result);

    $book_data = [
        'book_id' => $row['book_id'],
        'book_name' => $row['book_name'],
        'author_name' => $row['author_name'],
    ];

    array_push($_SESSION['book_ids'], $row['book_id']);
    array_push($_SESSION['books'], $book_data);
    header('Location: addorder.php'); // Redirect to the same page
    exit;
}

// Handle removing a book from the session
if (isset($_GET['index'])) {
    $book_id = $_GET['index'];
    // Find the book in the session and remove it
    foreach ($_SESSION['books'] as $key => $book) {
        if ($book['book_id'] == $book_id) {
            unset($_SESSION['books'][$key]);
            break;
        }
    }
    $_SESSION['books'] = array_values($_SESSION['books']); // Re-index the array
    header('Location: addorder.php'); // Redirect to the same page
    exit;
}

// Handle placing the order

if (isset($_POST['place_order'])) {
     date_default_timezone_set('Asia/Colombo');
    $current_date = date('Y-m-d');

    $return_date = new DateTime($current_date); // Start from the current date
    $return_date->modify('+13 days'); // Add 14 days
    $return_date_str = $return_date->format('Y-m-d');

    $customer_id = $_POST['customer_id'];
    $loan = $_POST['loan'];



    $customer_sql = "SELECT * FROM customer where customer_id=$customer_id";
    $customer_result = mysqli_query($connection, $customer_sql);
    $customer_data=mysqli_fetch_assoc($customer_result);
    $customer_name=$customer_data['customer_name'];

    // Insert the order into the orders table
    $order_sql = "INSERT INTO orders (customer_id, customer_name, order_date,return_date) VALUES 
    ($customer_id, '$customer_name', '$current_date','$return_date_str')";    
    mysqli_query($connection, $order_sql);
    $order_id = mysqli_insert_id($connection); // Get the last inserted order id

    // Insert each book into the order_items table
    foreach ($_SESSION['books'] as $book) {
        $book_id = $book['book_id'];

        $sql4 = "SELECT qty FROM qty where book_id=$book_id";
        $result4 = mysqli_query($connection, $sql4);
        $qty_data=mysqli_fetch_assoc($result4);
        $availbe_qty=$qty_data['qty'];

        $balance_qty= $availbe_qty-1;
        $sql5 = "update qty set qty=$balance_qty where book_id=$book_id";
        $result5 = mysqli_query($connection, $sql5);

        $book_name=$book['book_name'];
        $author_name=$book['author_name'];
        $order_item_sql = "INSERT INTO order_detail (order_id,book_id,customer_id,book_name,author_name) VALUES ($order_id,$book_id,$customer_id,'$book_name','$author_name')";
        mysqli_query($connection, $order_item_sql);
    }

    // Clear the session
    $_SESSION['book_ids'] = [];
    $_SESSION['books'] = [];

    if($loan >0){

        $sqlloan = "insert into loan (customer_id,loan) values ($customer_id,$loan)";
        mysqli_query($connection, $sqlloan);
    }



}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>

<?php navbar();?>

    <div class="container-fluid px-4">
        <div class="card mt-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0 text-center">Create Order</h4>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-md-5 mx-3">
                            <label for="">Select Book</label>
                            <select name="book_id" required class="form-select mySelect2" id="">
                                <option value="">-- Select Book --</option>
                                <?php
                                $book_sql = "SELECT * FROM book";
                                $book_result = mysqli_query($connection, $book_sql);
                                if ($book_result) {
                                    foreach ($book_result as $book) {
                                ?>
                                        <option value="<?= $book['book_id']; ?>"><?= $book['book_name'] ?></option>
                                <?php
                                    }
                                } else {
                                    echo '<option value="">-- something wrong --</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 text-end">
                            <br />
                            <button type="submit" name="add_book" class="btn btn-primary">Add Book</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="mb-0">Books</h4>
            </div>
            <div class="card-body">
                <?php
                $count=0;
                if (!empty($_SESSION['books'])) {
                ?>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Count</th>
                                    <th>Book Name</th>
                                    <th>Author Name</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['books'] as $book) : $count++?>
                                    <tr>
                                        <td><?= $count;?></td>
                                        <td><?= $book['book_name']; ?></td>
                                        <td><?= $book['author_name']; ?></td>
                                        <td>
                                            <a href="?index=<?= $book['book_id']; ?>" class="btn btn-danger">Remove</a>
                                        </td>
                                    </tr>
                                    
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                } else {
                    echo '<h5>No books added</h5>';
                }
                ?>
            </div>
        </div>

        <div class="mt-5">
            <form action="" method="post">
                <div class="row ">

                    <div class="col-md-5 mx-3">
                <label for="loan">Loan </label>
                <input type="text" name="loan"><br><br>

                        <label for="">Select Customer</label>
                        <select name="customer_id" required class="form-select mySelect2" id="selectCustomer">
                            <option value="">-- Select Customer --</option>
                            <?php
                            $customer_sql = "SELECT * FROM customer";
                            $customer_result = mysqli_query($connection, $customer_sql);
                            if ($customer_result) {
                                foreach ($customer_result as $customer) {
                            ?>
                                    <option value="<?= $customer['customer_id']; ?>"><?= $customer['customer_name'] ?></option>
                            <?php
                                }
                            } else {
                                echo '<option value="">-- something wrong --</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 text-end">
                        <br />
                        <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
                    </div>
                </div>
                            <br><br>
       
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.mySelect2').select2();
        });
    </script>
</body>

</html>
