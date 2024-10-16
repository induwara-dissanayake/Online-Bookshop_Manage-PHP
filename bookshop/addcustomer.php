<?php
ob_start();
session_start();
include('connect.php');
include('common.php');

if (!isset($_SESSION['username'])) {

    header('Location: login.php');
    exit();
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

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $registation_no= $_POST['member'];

    $sql1 = "INSERT INTO customer (customer_name,contact,registation_no,date) VALUES ('$name','$contact','$registation_no',NOW())";
    $result = mysqli_query($connection, $sql1);
    if ($result) {
        echo "<script type='text/javascript'>alert('Success'); window.location.href = 'addcustomer.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Error');</script>";
    }
    exit;
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
        .result_box ul {
            list-style-type: none;
            padding-left: 0;
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
        <h2 class="m-5">Add New Customer</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Customer Name</label>
                <input type="text" name="name" class="form-control" id="name" required placeholder="Name" autocomplete="off">
                <div class="result_box">

                </div>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Mobile No</label>
                <input type="text" name="contact" class="form-control" id="contact" required placeholder="Mobile No" autocomplete="off">

               
            </div>

            <div class="mb-3">
                <label for="member" class="form-label">Registration No</label>
                <input type="text" name="member" class="form-control" id="member" required placeholder="Registration index" autocomplete="off">
               
            </div>

            <button type="submit" name="add" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
        const resultbox = document.querySelector(".result_box");
        const inputbox = document.getElementById("name");
        let availableBooks = <?php echo json_encode($bookTitles); ?>;

        inputbox.onkeyup = function() {
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

  
        
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Bzd0TB/lt9Lg6w1+tGA89y5a00cCT1ls5FOD6GVk32hMop" crossorigin="anonymous"></script>
</body>

</html>