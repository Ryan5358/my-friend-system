<!--  https://mercury.swin.edu.au/cos30020/s103511424/assign2/index1.php -->

<?php require_once("processpage.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Web application development" />
    <meta name="keywords" content="System Development Project 2, Assignment 3" />
    <meta name="author" content="Ryan Vu" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title><?= get_current_title() ?></title>

    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Navbar -->
    <header><nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid mx-5 my-2">
            <a class="navbar-brand fs-2 fw-bold" href="index.php">ASSIGNMENT 3</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>   
            <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                <ul class="navbar-nav nav-underline mb-lg-0">
                    <?= print_nav_links() ?>
                </ul>
            </div> <!-- .navbar-collapse -->
        </div> <!-- .container-fluid -->
    </nav></header>

<!-- Content -->
    <!-- Hero Section -->
    <section class="full content hero align-items-center bg-0 bg-fixed">
        <div class="container-fluid position-fixed">
            <p class="subtitle mb-1">System Development Project 2</p>
            <h1 class="title mb-5"><span>My Friends</span> <br>Systems</h1>
            <p class="w-50 fs-4">This is a simplified social network application, and will make use of MySQL table creation, MySQL database access from PHP and PHP sessions.</p>
            <div class="d-flex">
                <a href="about.php"><button class="btn btn-s1 hover-bg-change me-3">About the project <i class="fa-solid fa-arrow-right-long"></i></button></a>
                <a href="#declaration"><button class="btn">See Declaration <i class="fa-solid fa-arrow-down-long"></i></button></a>
            </div>
        </div>
    </section>
   
    <!-- Declaration -->
    <section class="full content body layout-1 bg-white bg-1 position-relative z-1" id="declaration">
        <div class="container-fluid text-justify">
            <h1 class="title text-center"><i class="fa-regular fa-copyright"></i> Declaration</h1>
            <div class="text d-flex align-items-top mt-5">
                <div class="text body flex-start fs-4 lh-sm text-justify me-5">
                    <p>I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied from any other student’s work or from any other source.</p>
                    <?= populate_dataset() ?>
                </div>
                <div class="text signature flex-end ms-2">
                    <p class="fs-1 fw-bold mb-3 author name"><i class="fa-solid fa-signature"></i> Ryan Vu</p>
                    <p class="fw-bold mb-1 author info"><i class="fa-solid fa-hashtag"></i> 103511424</p>
                    <p class="fw-bold mb-1 author info"><i class="fa-solid fa-envelope"></i> <a class='link-offset-2 link-underline link-underline-opacity-0 email' href="mailto:103511424@student.swin.edu.au">103511424@student.swin.edu.au</a></p>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>