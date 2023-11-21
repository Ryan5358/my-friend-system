<?php 
    require_once("processpage.php"); 

    $curPage = 1;
    if(isset($_GET["unfriend_id"]) && !empty($_GET["unfriend_id"])) {
        $remove_id = $_GET["unfriend_id"];
        // execute unfriend actions

        // delete record from myfriends table
        $db["myfriends"]->delete(Table::WHERE_EQ, "friend_id1", $_SESSION["id"], "friend_id2", $remove_id);

        // update friends data arrays
        $friend_ids = get_friends_data();
        // update num_of_friends
        $db["friends"]->update_id($_SESSION["id"], "num_of_friends", count($friend_ids));
        header("Location: friendlist.php");
    }

    // update num_of_friends
    $db["friends"]->update_id($_SESSION["id"], "num_of_friends", count($friend_ids));

    $pageNum = 1;
    if(isset($_GET["page"]) && !empty($_GET["page"])) {
        $pageNum = $_GET["page"];
    }

    $break = "";
    if ( $db["myfriends"]->errMsg() || $db["friends"]->errMsg()) {
        $break = "<br>";
    }
    $err_db = $db["myfriends"]->errMsg() . $break . $db["friends"]->errMsg();
?>
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
    <section class="full content body layout-1 bg-1">
        <div class="container-fluid">
            <div class="row gx-5 d-flex justify-content-center align-items-center">
                <div class="col-md-3">
                    <div class="side left rounded-5 shadow content bg-0 bg-white">
                        <h2>Welcome back!</h2>
                        <h3 class="subtitle"><?= $_SESSION["user"] ?></h3><span class="ps-3 fw-bold font-monospace"><i class="fa-solid fa-hashtag"></i><span class="fs-4"><?= $_SESSION["id"] ?></span></span></h3>
                        <hr>
                        <ul id="user-info">
                            <li><i class="fa-solid fa-envelope"></i> <span class="ps-1 fw-bold email"><?= $user_info["friend_email"] ?></span></li>
                            <li><i class="fa-solid fa-calendar-day"></i> <span class="ps-1 fw-bold">Joined <?= date("d/m/Y" ,strtotime($user_info["date_started"])) ?></span></li>
                            <li><i class="fa-solid fa-users"></i> <span class="ps-1 fw-bold"><?= $user_info["num_of_friends"] ?> friend<?= $user_info["num_of_friends"] > 1 ? "s" : "" ?> (<?= $count_mutual ?> mutual friend<?= $count_mutual > 1 ? "s" : ""?>)</span></li>
                        </ul>
                    </div>
                </div>
                <form class="col-md-8 d-flex flex-column justify-content-center align-items-center mt-3" action="friendlist.php" method="get">
                    <div class="container glassmorphism side right rounded-4">
                        <?php if(!empty($err_db)) { ?>
                            <div class="row g-0 error"><?= $err_db ?></div>
                        <?php }?>
                        <div class="row gy-3 gx-3 justify-content-center">
                            <?php
                                // print cards
                                $pFriendList = new Paginator(5, $friend_ids);
                                $pFriendList->set_cur_page($curPage);
                                $friend_ids_page = $pFriendList->get_page();
                                $friends = array();
                                echo (empty($friend_ids_page) ? "<p class='fs-2 text-center'>You have no friends!</p>" : "");
                                foreach($friend_ids_page as $friend_id) {
                                    $friends[] = new Friend($friend_id, $db);
                                }
                                usort($friends, 'customCompare');
                                
                                foreach($friends as $friend) {
                                    $friend->print_card_friendlist();
                                }
                            ?>
                        </div>
                    </div>
                    <?= $pFriendList->print_pagination() ?>
                </form>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>