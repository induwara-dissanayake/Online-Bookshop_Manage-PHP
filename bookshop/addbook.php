<?php
ob_start();
session_start();
include('connect.php');
include('common.php');

if (!isset($_SESSION['username'])) {

    header('Location: login.php');
    exit();
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
    } elseif (isset($_POST['add'])) {
        $title = $_POST['title'];
        $author_name = $_POST['author_name'];
        $author_id = $_POST['author_id'];
        $isbn = $_POST['isbn'];
        $price = $_POST['price'];
    
        // First INSERT query
        $sql1 = "INSERT INTO book (book_name, author_id, author_name, isbn, price, qty) VALUES ('$title', '$author_id', '$author_name', '$isbn', '$price', 1)";
        $result = mysqli_query($connection, $sql1);
    
        // Check if the first INSERT query was successful
        if ($result) {
            // Get the ID of the last inserted row
            $book_id = mysqli_insert_id($connection);
    
            // Second INSERT query using the retrieved book_id
            $sql2 = "INSERT INTO qty (book_id, qty) VALUES ('$book_id', 1)";
            $result2 = mysqli_query($connection, $sql2);
    
            if ($result2) {
                echo "<script type='text/javascript'>alert('Success'); window.location.href = 'addbook.php';</script>";
            } else {
                echo "<script type='text/javascript'>alert('Error in inserting quantity');</script>";
            }
        } else {
            echo "<script type='text/javascript'>alert('Error in inserting book');</script>";
        }
        exit;
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
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
    <style>
        .author_result,.result_box ul {
            list-style-type: none;
            padding-left: 0;
        }
        .author_result,.result_box li {
            cursor: pointer;
            padding: 5px;
            border-bottom: 1px solid #ccc;
        }
        .author_result,.result_box li:hover {
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
        <h2 class="m-5">Add New Book</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" name="title" class="form-control" id="title" placeholder="Title" required autocomplete="off">
                <div class="result_box"></div>
            </div>
            <div class="mb-3">
                <label for="author_name" class="form-label">Author Name</label>
                <input type="text" name="author_name" class="form-control" id="author_name" placeholder="Author" required autocomplete="off">
                <input type="hidden" name="author_id" id="author_id">
                <div class="author_result"></div>
            </div>

            <div class="mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" name="isbn" class="form-control" id="isbn" placeholder="ISBN" required autocomplete="off">

            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" name="price" class="form-control" id="price" placeholder="Rs." required autocomplete="off">

            </div>
            <button type="submit" name="add" class="btn btn-primary">Submit</button>
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
            fetch('addbook.php', {
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Bzd0TB/lt9Lg6w1+tGA89y5a00cCT1ls5FOD6GVk32hMop" crossorigin="anonymous"></script>
</body>
</html>
