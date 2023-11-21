<?php  
    require_once("processpage.php"); 

    if ($_SESSION["login"])
        header("Location:friendadd.php");

    $email = $pf_name = $password = $cfm_password = "";
    $err_email = $err_pf_name = $err_password = $err_cfm_password = $err_db = "";
    $valid = true;
    
    if (isset($_POST["email"]) && !empty($_POST["email"])) {
        // Validate inputs
        $input_fields = ["email", "pf_name", "password", "cfm_password"];
        $input_requirements = ["EM", "AL", "AN", "AN"];
        foreach($input_fields as $i => $field) {
            ${$field."Validator"} = new Validator($_POST[$field], $input_requirements[$i]);
            if(!${$field."Validator"}->Result()) {
                $valid = false;
                ${"err_".$field} = "<p class='error'><i class='fa-solid fa-triangle-exclamation'></i> {${$field."Validator"}->Error()}</p>";
            } else {
                if ($field == "cfm_password") {
                    if ($_POST["cfm_password"] != $password) {
                        $valid = false;
                        $err_cfm_password = "<p class='error'><i class='fa-solid fa-triangle-exclamation'></i> Password does not match!</p>";
                        break;   
                    }
                }
                // obtain input data
                ${$field} = $_POST[$field];
            }
        }
        // record to database
        $ins_email = "'" . $email . "'";
        $ins_password = "'" . $password . "'";
        $ins_pf_name = "'" . $pf_name . "'";
        $current_date = "'" .  date('Y-m-d') . "'";
        $records = [
                        [$ins_email, $ins_password, $ins_pf_name, $current_date, 0]
                   ];

        if ($valid) {
            if(!$db["friends"]->addRecords($records, "friend_email", 0)) {
                $err_email = "<p class='error'><i class='fa-solid fa-triangle-exclamation'></i> Already exists!</p>";
            } else {
                $_SESSION["login"] = true;
                $newUser = $db["friends"]->fetch(Table::WHERE_EQ, "friend_email", $ins_email)[0];
                $_SESSION["id"] = $newUser["friend_id"];
                $_SESSION["user"] = $newUser["profile_name"];
                $db->close();
                header("Location:friendadd.php");
            }
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
        <div class="row g-0 form-sign glassmorphism rounded-5">
            <div class="col-md-5 p-0 content bg-signup bg-position-center rounded-start-5"></div>
            <div class="col-md-6 p-5 pt-4 pb-3">
                <h1 class="mb-4">Join our network!</h1>
                <form action="signup.php" method="post" autocomplete="off">
                    <div class="input-group">
                        <input type="text" name="email" class="rounded" id="inputEmail" value="<?= $email ?>" required>
                        <label for="inputEmail">Email</label>
                    </div>
                    <?= $err_email ?>
                    <div class="input-group">
                        <input type="text" name="pf_name" class="rounded" id="inputProfileName" value="<?= $pf_name ?>" required>
                        <label for="inputProfileName">Profile Name</label>
                    </div>
                    <?= $err_pf_name ?>
                    <div class="input-group">
                        <input type="password" name="password" class="rounded" id="inputPassword" required>
                        <label for="inputPassword">Password</label>
                    </div>
                    <?= $err_password ?>
                    <div class="input-group">
                        <input type="password" name="cfm_password" class="rounded" id="inputConfirmPassword" required>
                        <label for="inputConfirmPassword">Confirm Password</label>
                    </div>
                    <?= $err_cfm_password ?>
                    <div class="row ps-3">
                        <button type="submit" class="col-md-4 btn btn-s2 me-3"><i class="fa-solid fa-check" name="login"></i> Sign up</button>
                        <button type="reset" class="col-md-4 btn btn-s1 me-3"><i class="fa-solid fa-xmark"></i> Clear</button>
                    </div>
                    <p class="mt-3">Already registered? <a href="login.php">Log in</a></p>
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