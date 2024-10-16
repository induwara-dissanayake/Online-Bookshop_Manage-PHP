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
    <title>Book List</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./index.css">
</head>

<body>
    <div class="container-fluid">
        <?php navbar(); ?>
        <h1 class="text-center mt-3">Authors</h1>


        <div class="search_box">
            <div class="row_search">
                <input class="book_search" type="text" id="input-box" placeholder="Author Name/ID" autocomplete="off">
                <button class="btn btn-primary" id="search-button"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>

        <br>
        <div id="table-container">
            <!-- The table will be dynamically updated here -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputBox = document.getElementById('input-box');
            const tableContainer = document.getElementById('table-container');

            function loadBooks(query = '') {
                fetch('search_author.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        query: query
                    })
                })
                .then(response => response.text())
                .then(data => {
                    tableContainer.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

            // Load all books when the page loads
            loadBooks();

            // Trigger search on keyup
            inputBox.addEventListener('keyup', () => {
                const query = inputBox.value;
                loadBooks(query);
            });

            // Trigger search on button click
            document.getElementById('search-button').addEventListener('click', () => {
                const query = inputBox.value;
                loadBooks(query);
            });
        });
    </script>
</body>

</html>
