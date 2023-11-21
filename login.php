<?php 
    require_once("processpage.php");

    if ($_SESSION["login"])
            header("Location:friendlist.php");

    $email = $password = "";
    $err_email = $err_password = $err_db = "";

    if (isset($_POST["email"]) && !empty($_POST["email"])) {
        // Validate inputs
        $input_fields = ["email", "password"];
        $table_fields = ["friend_email", "password"];
        $input_requirements = ["EM", "AN"];
        foreach($input_fields as $i => $field) {
            ${$field."Validator"} = new Validator($_POST[$field], $input_requirements[$i]);
            if(!${$field."Validator"}->Result()) {
                ${"err_".$field} = "<p class='error'><i class='fa-solid fa-triangle-exclamation'></i> {${$field."Validator"}->Error()}</p>";
            } else {
                if ($db["friends"]->isDuplicate($table_fields[$i], "'" . $_POST[$field] . "'")) 
                    ${$field} = $_POST[$field];
                else
                    ${"err_".$field} = "<p class='error'><i class='fa-solid fa-triangle-exclamation'></i> Incorrect $field</p>";
            }
        }

        if (empty($err_email) && empty($err_password)) {
            $data = $db["friends"]->fetch(Table::WHERE_EQ, "friend_email", "'".$email."'"); 
            $_SESSION["user"] = $data[0]["profile_name"];
            $_SESSION["id"] = $data[0]["friend_id"];
            $db->close(); 
            $_SESSION["login"] = true;
            header("Location:friendlist.php");
        }
        $err_db = $db["friends"]->errMsg();
    }
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
</body>
<!-- Content -->
    <section class="full content body layout-1 bg-2 flex-column">
        <div class="row g-0 form-sign glassmorphism form-log rounded-5">
            <div class="col-md-5 p-0 content bg-login bg-position-center rounded-start-5"></div>
            <div class="col-md-6 p-5">
                <h1 class="mb-5">Welcome back!</h1>
                <form action="login.php" method="post" autocomplete="off">
                    <div class="input-group">
                        <input type="text" name="email" class="rounded" id="inputEmail" value="<?= $email?>" required>
                        <label for="inputEmail">Email</label>
                    </div>
                    <?= $err_email ?>
                    <div class="input-group">
                        <input type="password" name="password" class="rounded" id="inputPassword" required>
                        <label for="inputPassword">Password</label>
                    </div>
                    <?= $err_password ?>
                    <div class="row ps-3 mt-3">
                        <button type="submit" class="col-md-4 btn btn-s2 me-3"><i class="fa-solid fa-check"></i> Log in</button>
                        <button type="reset" class="col-md-4 btn btn-s1 me-3"><i class="fa-solid fa-xmark"></i> Clear</button>
                    </div>
                    <p class="mt-4">Don't have an account? <a href="signup.php">Sign up</a></p>
                </form>
            </div>
        </div>
        <?php if(!empty($err_db)) { ?>
            <div class="row g-0 form-sign error mt-2"><?= $err_db ?></div>
        <?php }?>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html> 