<?php require_once("processpage.php") ?>
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
    <article class="content bg-1 bg-fixed">
        <section class="full content body layout-1">
            <div class="container-fluid d-flex justify-content-center align-items-center mt-3">
                <h1 class="title d-block w-50 text-center mb-5 me-5"><i class="fa-regular fa-comments"></i><br>Q&A</h1>
                <div class="container-fluid ms-5">
                    <?php
                        $chats = 
                            array(
                                    new QAChat(
                                                "What tasks you have not attempted or not completed?", 
                                                "I have completed all the tasks (including extra challenge)."
                                              ),
                                    new QAChat(
                                                "What special features have you done, or attempted, in creating the site that we should know about?", 
                                                "The website implemnted with object-oriented programming style as script executions heavily rely on objects. I have implemeted many object classes which are defined in 'helperclasses.php'"
                                              ),
                                    new QAChat(
                                                "Which parts did you have trouble with?", 
                                                "At first I was having trouble with how to implement user's log in and sign up, but after doing some small research, I have resolved my problems and come up with this solution."
                                              ),
                                    new QAChat(
                                                "What would you like to do better next time?", 
                                                "I think I have did my best in this assignment. The only thing I regret is having submitted this assignment late."
                                              ),
                                    new QAChat(
                                                "What additional features did you add to the assignment? (if any)", 
                                                "I have included additional features (e.g. user profile, mutual friends count in user profile and their friends)"
                                              )
                                );
                            
                        foreach($chats as $no => $chat) {
                            $chat->print_chat($no + 1);
                        }
                    ?>
                </div>
            </div>
        </section>
        <section class="full content body layout-1">
            <div class="container-fluid d-flex justify-content-center align-items-center">
                <h1 class="title d-block w-50 text-center mb-5 me-5"><i class="fa-solid fa-link"></i><br>Other Links</h1>
                <div class="container-fluid ms-5">
                    <?php
                        feature_page("Friend List (Log in Required)", "friendlist.php", "style/bg-2.png");
                        feature_page("Add Friends (Log in Required)", "friendadd.php", "style/bg-2.png");
                        feature_page("Home Page", "index.php", "style/bg-0.png"); 
                    ?>
                </div>
        </section>
        <section class="full content body layout-1">
            <div class="container-fluid d-flex justify-content-center align-items-center">
                <h1 class="d-block w-50 text-center mb-5 me-5"><i class="fa-solid fa-reply title"></i><br><span class="title">Discussion</span> <span class="subtitle mt-3">Response</span></h1>
                <div class="container-fluid ms-5">
                    <p class="fs-3">It's a shame I was not able to join any discussion as I was busy with this assignment.</p>
                </div>
        </section>
    </article>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>