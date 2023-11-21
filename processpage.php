<?php
//
//  START SESSION
//
    session_start();
    if(!isset($_SESSION["login"])) {
        $_SESSION["login"] = false;
        $_SESSION["user"] = "";
        $_SESSION["id"] = 0;
    }

    require_once("helperclasses.php");

//
// FOR ALL PAGES
//
    $db = new Database();
    set_up_db();

    $pages = 
        array(// [filename] => [page-title]
                "index.php" => "Home", 
                "about.php" => "About", 
                "login.php" => "Sign in", 
                "signup.php" => "Sign up"
            );
    
    $sub_pages =
        array(// [filename]      => array([page-title], [icon])
                "friendlist.php" => array("Friend List", "<i class='fa-solid fa-users'></i>"),
                "friendadd.php"  => array("Add Friend", "<i class='fa-solid fa-user-plus'></i>"),
                "logout.php"     => array("Log out", "<i class='fa-solid fa-arrow-right-from-bracket'></i>")
            );
    
    // redirecting and pre-process
    if(!$_SESSION["login"]) {
        $active_page = str_replace([" Page", "{$_SESSION["user"]}'s "], "", get_current_title());
        if(Exists($active_page, $sub_pages)) {
            header("Location: login.php");
        }
    } else {
        $user = $db["friends"]->fetch(Table::WHERE_EQ, "friend_id", $_SESSION["id"]);
        if (!$user) {
            // Logs out inactive user
            header("Location: logout.php");
        }
        // User Info Data
        $user_info = $user[0];
        $me = new Friend($_SESSION["id"], $db);
        $count_mutual = $me->count_mutual_friends(); 
        // All Friends Data
        $all_friends = $db["friends"]->fetch();
        $all_friend_ids = extract_data("friend_id", $all_friends);
        $all_friend_ids = array_values(array_diff($all_friend_ids, array($user_info["friend_id"])));

        // Friends Data
        $friend_ids = get_friends_data();
        $nonfriend_ids = array_values(array_diff($all_friend_ids, $friend_ids));
    }

    // get current page title function
    function get_current_title() {
        global $pages, $sub_pages;
        $currentFilename = basename($_SERVER['PHP_SELF']);
        if (isset($pages[$currentFilename])) {
            $currentTitle = $pages[$currentFilename];
        } else {
            $currentTitle = $_SESSION["user"] . "'s " . $sub_pages[$currentFilename][0];
        }
        return post_process_title($currentTitle);
    }

    // post-process page title function
    function post_process_title($title) {
        $to_append = 
            array( 
                    "Home",
                    "About",
                    $_SESSION["user"] . "'s " . "Friend List",
                    $_SESSION["user"] . "'s " .  "Add Friend"
                );
        return (in_array($title, $to_append)?$title." Page":$title);
    }

    // print nav-links function
    function print_nav_links() {
        global $pages;
        $hidden_when_login = 
            array(
                    "Sign in",
                    "Sign up"
            );
        foreach ($pages as $filename => $page_title) {
            if ( ($_SESSION["login"] && !in_array($page_title, $hidden_when_login)) ||
                 !$_SESSION["login"]) {
                echo "<li class='nav-item me-5'>";
                    if ($page_title == get_current_title() || $page_title . " Page" == get_current_title())
                        echo "<div class='nav-link active' aria-current='page'>$page_title</div>";
                    else 
                        echo "<a class='nav-link inactive' href='$filename'>$page_title</a>";
                }
                echo "</li>";
            }
        if ($_SESSION["login"])
            print_dropdown();
    }

    // print dropdown
    function print_dropdown() {
        global $sub_pages;
        $active_page = str_replace([" Page", "{$_SESSION["user"]}'s "], "", get_current_title());
        echo "
        <li class='nav-item dropdown'>
          <div class='nav-link ";
        if ( Exists($active_page, $sub_pages))
        {
            echo "active";
        }
        echo " dropdown-toggle' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
            <i class='fa-regular fa-circle-user'></i> ", $_SESSION["user"], 
          "</div>
          <ul class='dropdown-menu'>";
        foreach ($sub_pages as $filename => $page_props) {
            if ($page_props[0] == "Log out") {
                echo "<li><hr class='dropdown-divider'></li>";
                echo "<li><a class='dropdown-item text-danger' href='$filename'>{$page_props[1]} {$page_props[0]}</a></li>";
            }
            else {
                echo "<li class='dropdown-item'>";
                if ($page_props[0] == $active_page)
                    echo "<div class='nav-link active' aria-current='page'>{$page_props[1]} {$page_props[0]}</div>";
                else 
                    echo "<a class='dropdown-item' href='$filename'>{$page_props[1]} {$page_props[0]}</a>";
                echo "</li>";
            }
        }
        echo " 
          </ul>
        </li>";
    }

    // check title exists (support function for sub_pages)
    function Exists($pageTitle, $pageArray) {
        foreach ($pageArray as $page => $details) {
            if ($details[0] === $pageTitle) {
                return true;
            }
        }
        return false;
    }

    // extract data from field 
    function extract_data($field, $array) {
        $result = array();
        foreach($array as $row) {
            $result[] = $row[$field];
        }
        return $result;
    }
 
//
// about.php
//
    // feature page function
    function feature_page($title, $link, $path_to_img) {
        echo "
        <div class='card mb-3 rounded'>
            <div class='row g-0'>
                <div class='col-md-4'>
                    <img src='$path_to_img' class='img-fluid rounded-start' alt='", basename($path_to_img), "'>
                </div>
                <div class='col-md-8 d-flex align-items-center'>
                    <div class='card-body p-1 ps-4'>
                        <h5 class='card-title'>$title</h5>
                        <a href='$link'><button class='btn btn-s1 mt-3 mb-0'>Go to Page <i class='fa-solid fa-arrow-right-long'></i></button></a>
                    </div>
                </div>
            </div>
        </div>
        ";
    }
//
// index.php
//
    $msg = $msg = $db->errMsg();
    $is_passed = $db->isSuccess();
    // Set up, preparing database function
    function set_up_db()
    {
        global $db, $msg, $is_passed;

        // Build Table's structure
        // Syntax: 
        //          [column_name] => array([properties], [value_is_insertable?])

        $friends_struct = 
            array ( 
                    "friend_id"      => array("INT, NOT NULL, AUTO_INCREMENT, PRIMARY KEY", false),
                    "friend_email"   => array("VARCHAR(50), NOT NULL", true),
                    "password"       => array("VARCHAR(20), NOT NULL", true),
                    "profile_name"   => array("VARCHAR(30), NOT NULL", true),
                    "date_started"   => array("DATE, NOT NULL", true),
                    "num_of_friends" => array("INT, UNSIGNED", true)
                );

        $myfriends_struct =
            array (
                    "friend_id1" => array("INT, NOT NULL", true),
                    "friend_id2" => array("INT, NOT NULL", true)
                );

        // Create and add tables to database db
        $db->addTables(
                        new Table("friends", $friends_struct),
                        new Table("myfriends", $myfriends_struct)
                      );

    }

    // Populate dataset function
    function populate_dataset()
    {
        global $db;
        $is_passed = $db->isSuccess();
        $msg = $db->errMsg();
        $generator = new DataGenerator();
        $newFriend_ids = array();
        // Populate datasets
        // for 'friends' table (10 records)

        if ($is_passed) {
            $limit = 10;
            $num_rows = $db["friends"]->count_rows();
            if ($num_rows < $limit) {
                $db["friends"]->delete();
                $friends_recs = $generator->generate_dataset($limit);
                $db["friends"]->addRecords($friends_recs, "friend_email", 0);
                $newFriends = $db["friends"]->fetch();
                $newFriend_ids = extract_data("friend_id", $newFriends);
            }
            $is_passed = $db["friends"]->isSuccess();
            $msg = $db["friends"]->errMsg();
        }

        // Populate datasets
        // for 'myfriends' table (20 records) and update num_of_friends of 'friends' table

        if ($is_passed) {
            $limit = 20;
            $num_rows = $db["myfriends"]->count_rows();
            if ($num_rows < $limit) {
                $db["myfriends"]->delete();
                $myfriend_recs = $generator->generate_relationships($limit);
          
                $db["myfriends"]->addRecords($myfriend_recs, "all");

                // update num_of_friends per friend_id
                foreach($newFriend_ids as $friend_id) {
                    $friends = get_friends_data($friend_id); 
                    $db["friends"]->update_id($friend_id, "num_of_friends", count($friends));
                }
            }
            $is_passed = $db["myfriends"]->isSuccess();
            $msg = $db["myfriends"]->errMsg();
        }

        print_alert($msg, $is_passed);
        $db->close();
    }

    // function print
    function print_alert($msg, $is_success)
    {   
        // check if there is no error message
        if (empty($msg)) {
            $msg = "<p class='font-monospace mb-0'>Tables successfully created and populated.</p>";
        }
        // Alert Elements
        $heading = "";
        $type = "";
        $icon = "";

        if ($is_success) {
            $heading = "Success!";
            $type = "alert-success";
            $icon = "fa-circle-check";
        } else {
            $heading = "Failure!";
            $type = "alert-warning";
            $icon = "fa-circle-xmark";
        }

        echo "
        <div class='alert $type mt-2' role='alert'>
            <h4 class='alert-heading mb-3'><i class='fa-solid $icon'></i> $heading</h4>
            <div class='mb-0 fs-5'>$msg</div>
        </div>
        ";
    }
//
// friendadd.php, friendlist.php
//
    function get_friends_data($id = null) 
    {
        if ($id === null) {
            $id = $_SESSION["id"];
        }
        global $db;
        $friends = $db["myfriends"]->fetch(Table::WHERE_EQ, "friend_id1", $id);
        return extract_data("friend_id2", $friends);
    }

    function customCompare($a, $b) {
        // Custom comparison logic
        if ($a->get_profile_name() > $b->get_profile_name()) {
            return 1;
        } elseif ($a->get_profile_name() < $b->get_profile_name()) {
            return -1;
        } else {
            return 0;
        }
    }
?>