<?php
ob_start();
session_start();
include('connect.php');
include('common.php');

if (!isset($_SESSION['username'])) {

    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $customer_old_id = $_GET['id'];

    $select_query = "SELECT * FROM customer WHERE customer_id = $customer_old_id";
    $result_select = mysqli_query($connection, $select_query);
    $row_data = mysqli_fetch_assoc($result_select);
    $customer_old_name = $row_data['customer_name'];
    $customer_old_contact = $row_data['contact'];
    $customer_old_registation_no = $row_data['registation_no'];

}

if (isset($_POST['update_customer'])) {
    $book_new_name = $_POST['title'];
    $new_contact = $_POST['contact'];
    $new_registation_no = $_POST['member'];


    $sql1 = "UPDATE customer SET customer_name = '$book_new_name',
    contact='$new_contact',registation_no= '$new_registation_no' WHERE customer_id =$customer_old_id";
    $result = mysqli_query($connection, $sql1);
    if ($result) {
        echo "<script type='text/javascript'>window.location.href = 'customer.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Error');</script>";
    }
    exit;
} elseif (isset($_POST['delete_customer'])) {

    $delete_order = "DELETE FROM orders WHERE customer_id=$customer_old_id";
    $order_result = mysqli_query($connection, $delete_order);

    if ($order_result) {
        $delete_query = "DELETE FROM customer WHERE customer_id=$customer_old_id";
        $delete_result = mysqli_query($connection, $delete_query);
        if ($delete_result) {
            echo "<script type='text/javascript'>window.location.href = 'customer.php';</script>";
        } else {
            echo "<script type='text/javascript'>alert('Error');</script>";
        }
        exit;
    }else {
        echo "<script type='text/javascript'>alert('Error');</script>";
    }
    exit;


}


$sql1 = "SELECT customer_name FROM customer";
$result1 = mysqli_query($connection, $sql1);
$num_row1 = mysqli_num_rows($result1);
$bookTitles = array();

if ($num_row1 > 0) {
    while ($row1 = $result1->fetch_assoc()) {
        $bookTitles[] = $row1['customer_name'];
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
    <style>
        .result_box {
            position: absolute;
            z-index: 1000;
            background: #fff;
            border: 1px solid #ccc;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
        }

        .result_box ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }

        .result_box li {
            cursor: pointer;
            padding: 5px;
            border-bottom: 1px solid #ccc;
        }

        .result_box li:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <?php navbar(); ?>
    <div class="container">
        <h2 class="m-5 text-center">Edit Customer</h2>

        <form action="" method="post" id="editBookForm">
            <div class="mb-3">
                <label for="title" class="form-label">Customer Name</label>
                <input type="text" name="title" required class="form-control" id="title" placeholder="Name" autocomplete="off" value="<?php echo $customer_old_name; ?>">
                <div class="result_box"></div>
            </div>
            <div class="mb-3">
                
                <label for="contact" class="form-label">Mobile No</label>
                <input type="text" name="contact" required class="form-control" id="contact" placeholder="Contact" autocomplete="off" value="0<?php echo $customer_old_contact; ?>">
            </div>

            <div class="mb-3">
                
                <label for="member" class="form-label">Registation No</label>
                <input type="text" name="member" required class="form-control" id="member" placeholder="Registation index" autocomplete="off" value="<?php echo $customer_old_registation_no; ?>">
            </div>


            <button type="submit" name="update_customer" class="btn btn-primary">Update</button>
            <button type="button" name="delete_customer" class="btn btn-danger" onclick="confirmDelete()">Remove</button>
        </form>

    </div>

    <script>
        const resultbox = document.querySelector(".result_box");
        const inputbox = document.getElementById("title");
        let availableBooks = <?php echo json_encode($bookTitles); ?>;

        inputbox.onkeyup = function () {
            let result = [];
            let input = inputbox.value;
            if (input.length) {
                result = availableBooks.filter((keyword) => {
                    return keyword.toLowerCase().includes(input.toLowerCase());
                });
            }
            displayBookResults(result);
        }

        function displayBookResults(result) {
            const content = result.map((list) => {
                return "<li onclick=selectBook(this)>" + list + "</li>";
            });
            resultbox.innerHTML = "<ul>" + content.join('') + "</ul>";
        }

        function selectBook(list) {
            inputbox.value = list.innerHTML;
            resultbox.innerHTML = '';
        }

        function confirmDelete() {
            if (confirm('Are you sure you want to delete this customer?')) {
                const form = document.getElementById('editBookForm');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_customer';
                form.appendChild(input);
                form.submit();
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Bzd0TB/lt9Lg6w1+tGA89y5a00cCT1ls5FOD6GVk32hMop" crossorigin="anonymous"></script>
</body>

</html>
