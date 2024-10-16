<?php 

function navbar()
{

    global $connection;
    echo " <section id='header'>
            <a href=''><img src='1.png' alt='' class='logo'></a>
            <a class='navbar-brand' href='#'></a>
            <div>
                <ul id='navbar'>
                    <a href='index.php'><img src='img/logo.jpg' alt='' class='logo'></a>
                    <li><a href='index.php'>Home</a></li>
                    <li><a href='order_display.php'>Orders</a></li>
                    <li><a href='book_display.php'>Books</a></li>
                      <li><a href='customer_display.php'>Customers</a></li>
                    <li><a href='author_display.php'>Authors</a></li>
                    <li class='mx-3'><a href='logout.php'>Logout</a></li>

                  

                    ";

                    
                  
                         echo"


                </ul>
            </div>

        </section>
        ";
                    

        
}


function calculateDateDifference($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
}





?>