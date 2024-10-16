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
    $book_old_id = $_GET['id'];

    $select_query = "SELECT * FROM book WHERE book_id = $book_old_id";
    $result_select = mysqli_query($connection, $select_query);
    $row_data = mysqli_fetch_assoc($result_select);
    $book_old_name = $row_data['book_name'];
    $author_old_name = $row_data['author_name'];
    $author_old_id = $row_data['author_id'];
    $old_isbn = $row_data['isbn'];
    $old_price = $row_data['price'];
    $old_qty = $row_data['qty'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_author'])) {
        $author_name = $_POST['author_name'];
        $author_insert = "INSERT INTO author (author_name) VALUES ('$author_name')";
        $insert_result = mysqli_query($connection, $author_insert);
        if ($insert_result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
        exit;
    } elseif (isset($_POST['update_book'])) {
        $book_new_name = $_POST['title'];
        $author_name = $_POST['author_name'];
        $author_id = $_POST['author_id'];
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
        $qty = $_POST['qty'];

        $sql4 = "SELECT qty FROM qty where book_id=$book_old_id";
        $result4 = mysqli_query($connection, $sql4);
        $qty_data=mysqli_fetch_assoc($result4);
        $availbe_qty=$qty_data['qty'];
        if($qty>$old_qty){
            $balance_qty= $availbe_qty+$qty-$old_qty;
            $sql5 = "update qty set qty=$balance_qty where book_id=$book_old_id";
            $result5 = mysqli_query($connection, $sql5);

        }
        if($qty<$old_qty){
            $balance_qty=$availbe_qty-($old_qty-$qty);
            $sql5 = "update qty set qty=$balance_qty where book_id=$book_old_id";
            $result5 = mysqli_query($connection, $sql5);
        }

                else{
            $result5=true;
        }
    

        if ($result5) {

            $sql1 = "UPDATE book SET book_name = '$book_new_name', author_id = '$author_id',
            author_name = '$author_name',isbn=$isbn,price=$price,qty=$qty WHERE book_id = $book_old_id";
            $result = mysqli_query($connection, $sql1);
            if ($result) {
                echo "<script type='text/javascript'>window.location.href = 'book.php';</script>";
            } else {
                echo "<script type='text/javascript'>alert('Error');</script>";
            }
            exit;

            
        } else {
            echo "<script type='text/javascript'>alert('Error');</script>";
        }
   
    } elseif (isset($_POST['delete_book'])) {
        $delete_query1 = "DELETE FROM qty WHERE book_id = $book_old_id";
        $delete_result1 = mysqli_query($connection, $delete_query1);

        if ($delete_result1) {

            $delete_query = "DELETE FROM book WHERE book_id = $book_old_id";
            $delete_result = mysqli_query($connection, $delete_query);
            if ($delete_result) {
                echo "<script type='text/javascript'>window.location.href = 'book.php';</script>";
            } else {
                echo "<script type='text/javascript'>alert('Error');</script>";
            }
            exit;
                } else {
            echo "<script type='text/javascript'>alert('Error');</script>";
        }

       
    }
}

$sql1 = "SELECT book_name FROM book";
$result1 = mysqli_query($connection, $sql1);
$num_row1 = mysqli_num_rows($result1);
$bookTitles = array();

if ($num_row1 > 0) {
    while ($row1 = $result1->fetch_assoc()) {
        $bookTitles[] = $row1['book_name'];
    }
}

$sql2 = "SELECT author_id, author_name FROM author";
$result2 = mysqli_query($connection, $sql2);
$num_row2 = mysqli_num_rows($result2);
$authors = array();

if ($num_row2 > 0) {
    while ($row2 = $result2->fetch_assoc()) {
        $authors[] = $row2;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
    <style>
        .result_box,
        .author_result {
            position: absolute;
            z-index: 1000;
            background: #fff;
            border: 1px solid #ccc;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
        }

        .result_box ul,
        .author_result ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }

        .result_box li,
        .author_result li {
            cursor: pointer;
            padding: 5px;
            border-bottom: 1px solid #ccc;
        }

        .result_box li:hover,
        .author_result li:hover {
            background-color: #f0f0f0;
        }

        .add-author-button {
            display: block;
            margin: 10px 0 0 15px;
        }
    </style>
</head>

<body>
    <?php navbar(); ?>
    <div class="container">
        <h2 class="m-5">Edit Book</h2>

        <form action="" method="post" id="editBookForm">
            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" name="title" required class="form-control" id="title" placeholder="Title" autocomplete="off" value="<?php echo $book_old_name; ?>">
                <div class="result_box"></div>
            </div>
            <div class="mb-3">
                <label for="author_name" class="form-label">Author Name</label>
                <input type="text" name="author_name" class="form-control" id="author_name" placeholder="Author" autocomplete="off" value="<?php echo $author_old_name; ?>">
                <input type="hidden" name="author_id" id="author_id" value="<?php echo isset($author_old_id) ? $author_old_id : ''; ?>">
                <div class="author_result"></div>
            </div>

            <div class="mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" name="isbn" required class="form-control" id="isbn" placeholder="ISBN" autocomplete="off" value="<?php echo $old_isbn; ?>">

            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" name="price" required class="form-control" id="price" placeholder="Price" autocomplete="off" value="<?php echo $old_price; ?>">

            </div>

            <div class="mb-3">
                <label for="qty" class="form-label">QTY</label><br>
                <input type="number" min="0" name="qty" required class="form-outline" id="qty" placeholder="QTY" autocomplete="off" value="<?php echo $old_qty; ?>">

            </div>
            <button type="submit" name="update_book" class="btn btn-primary">Update</button>
            <button type="button" name="delete_book" class="btn btn-danger" onclick="confirmDelete()">Remove</button>
        </form>

    </div>

    <script>
        const resultbox = document.querySelector(".result_box");
        const inputbox = document.getElementById("title");
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

        const author_result = document.querySelector(".author_result");
        const author_input = document.getElementById("author_name");
        const author_id_input = document.getElementById("author_id");
        let availableAuthors = <?php echo json_encode($authors); ?>;

        author_input.onkeyup = function() {
            let result1 = [];
            let input1 = author_input.value;
            if (input1.length) {
                result1 = availableAuthors.filter((author) => {
                    return author.author_name.toLowerCase().includes(input1.toLowerCase());
                });
            }
            if (result1.length === 0) {
                author_result.innerHTML = `<button type="button" class="btn btn-primary add-author-button" onclick="addNewAuthor()">+ Enter new author</button>`;
            } else {
                displayAuthorResults(result1);
            }
        }

        function displayAuthorResults(result1) {
            const content = result1.map((author) => {
                return "<li onclick=selectAuthor(this) data-id='" + author.author_id + "'>" + author.author_name + "</li>";
            });
            author_result.innerHTML = "<ul>" + content.join('') + "</ul>";
        }

        function selectAuthor(list) {
            author_input.value = list.innerHTML;
            author_id_input.value = list.getAttribute('data-id');
            author_result.innerHTML = '';
        }

        function addNewAuthor() {
            let newAuthorName = author_input.value;
            if (newAuthorName.trim() === '') {
                alert('Author name cannot be empty');
                return;
            }
            fetch('bookdetails.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `add_author=true&author_name=${newAuthorName}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Author added successfully');
                        location.reload(); // Reload to update the author list
                    } else {
                        alert('Error adding author');
                    }
                });
        }

        function confirmDelete() {
            if (confirm('Are you sure you want to delete this book?')) {
                const form = document.getElementById('editBookForm');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_book';
                form.appendChild(input);
                form.submit();
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Bzd0TB/lt9Lg6w1+tGA89y5a00cCT1ls5FOD6GVk32hMop" crossorigin="anonymous"></script>
</body>

</html>