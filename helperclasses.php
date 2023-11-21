<?php
//////////////////////////////////////////////////////////////////////////////////////////
// HELPER CLASSES

    //Validator
    class Validator {
        private $_input;
        private $_cond_code;
        private $_errorMsg;
        private $_result;

        public function __construct($input, $condition_code) {
            $this->_input = $input;
            $this->_cond_code = $condition_code;
            $this->_result = $this->validate();
        }
        // Validate input functions
        private function validate() {
            $result = false;
            switch ($this->_cond_code) {
                case "EM":
                    $result = $this->check_email();
                    break;
                case "AL":
                    $result = $this->check_alphas_only();
                    break;
                case "AN":
                    $result = $this->check_alphanumerics_only();
                    break;
                default:
                    die("Invalid condition code: $this->_cond_code");
            }
            return $result;
        }
        private function check_email() {
            if (!filter_var($this->_input, FILTER_VALIDATE_EMAIL)) {
                $this->_errorMsg = "Invalid email format";
                return false;
            }
            return true;
        }
        private function check_alphas_only() {
            if (!preg_match("/^[a-zA-Z]*$/", $this->_input)) {
                $this->_errorMsg = "Contains letters only";
                return false;
            }
            return true;
        }
        private function check_alphanumerics_only() {
            if (!preg_match("/^[a-zA-Z0-9]*$/", $this->_input)) {
                $this->_errorMsg = "Contains alpha-numeric characters only";
                return false;
            }
            return true;
        }

        // Getter for errorMsg
        public function Error() {
            return $this->_errorMsg;
        }

        // Getter for result
        public function Result() {
            return $this->_result;
        }
    }

    // Data Generator
    class DataGenerator {
        private $_names;
        private $_relationships;

        public function __construct() {
            $this->_names = 
                [
                    "Liam", "Olivia", "Noah", "Emma", "Sophia", "Jackson", "Ava", "Lucas", "Isabella", "Aiden",
                    "Mia", "Caden", "Charlotte", "Mila", "Amelia", "Layla", "Harper", "Elijah", "Lily", "Evelyn",
                    "Benjamin", "Aria", "Abigail", "Logan", "Ella", "Carter", "Scarlett", "Sebastian", "Emily", "Michael",
                    "Grace", "Alexander", "Madison", "Oliver", "Chloe", "Daniel", "Penelope", "Henry", "Riley", "Jacob",
                    "Zoe", "Matthew", "Victoria", "Joseph", "Avery", "Samuel", "Eleanor", "David", "Hannah", "William"
                ];
        }

        // Data Component Makers
        private function makeDomain() {
            $domainExtensions = array('.com.au', '.net.au', '.org.au', '.edu.au', '.gov.au');
            $domainPrefixes = array('example', 'test', 'abc', 'xyz', 'company', 'australia', 'web', 'my', 'online');
            
            $randomExtension = $domainExtensions[array_rand($domainExtensions)];
            $randomPrefix = $domainPrefixes[array_rand($domainPrefixes)];
            $randomDomain = $randomPrefix . $randomExtension;

            return $randomDomain;
        }
        private function makeEmail($name) {
            $name = str_replace("'", "", $name);
            $domain = $this->makeDomain();
            $email = "'" . strtolower($name) . "@" . $domain . "'";

            return $email;
        }
        private function pickName() {
            if (empty($this->_names)) {
                return null; // Return null if no names are available
            }
    
            $randomKey = array_rand($this->_names);
            $randomName = $this->_names[$randomKey];
    
            // Remove the selected name from the array
            unset($this->_names[$randomKey]);
        
            return "'" . $randomName . "'";
        }
        private function makePassword() {
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $password = "'" . substr(str_shuffle($characters), 0, rand(7, 20)) . "'";
            return $password;
        }

        private function makeDate() {
            $currentTimestamp = time(); // Get current timestamp
            $randomTimestamp = rand(0, $currentTimestamp); // Generate a random timestamp
            $randomDate = "'" . date('Y-m-d', $randomTimestamp) . "'"; // Format the timestamp as a date
            return $randomDate;
        }

        private function makeRelationshipsAmong($numEntities) {
            $relationships = array();

            for ($i = 1; $i <= $numEntities; $i++) {
                for ($j = $i + 1; $j <= $numEntities; $j++) {
                    $relationships[] = array($i, $j);
                }
            }
            $this->_relationships = $relationships;
        } 


        // Generate Data
        public function generate_dataset($limit) {
            $dataset = array();
            for ($i = 0; $i < $limit; $i++) {
                $name = $this->pickName();
                $row = [$this->makeEmail($name), $this->makePassword(), $name, $this->makeDate(), 0];
                $dataset[$i] = $row;
            }
            $this->makeRelationshipsAmong($limit);
            return $dataset;
        }
        public function generate_relationships($limit) {
            $relationships = array();
            for ($i = 0; $i < $limit; $i++) {
                if (empty($this->_relationships)) {
                    return null; // Return null if no relationships are available
                }
        
                $randomKey = array_rand($this->_relationships);
                $randomRelationship = $this->_relationships[$randomKey];
                
                $relationships[] = $randomRelationship;

                // Remove the selected name from the array
                unset($this->_relationships[$randomKey]);
            }
            return $relationships;
        }
    }

    // QAChat class
    class QAChat {
        private $_question;
        private $_answer;

        public function __construct($question, $answer) {
            $this->_question = $question;
            $this->_answer = $answer;
        }

        public function print_chat($no) {
            echo "
            <div class='card d-block w-100 mb-2'>
                <div class='card-header fw-bold'>Question $no</div>
                <div class='card-body'>
                    <h5 class='card-title'>$this->_question</h5>
                    <p class='card-text'>$this->_answer</p>
                </div>
            </div>";
        }
    }

    // Friend class
    class Friend {
        private $_friendID;
        private $_profileName;
        private $_db;

        public function __construct($friendID, &$db) {
            $this->_friendID = $friendID;
            $this->_db = $db;
            $this->_profileName = $db["friends"]->fetch(Table::WHERE_EQ, "friend_id", $this->_friendID)[0]["profile_name"];
        }

        // print cards for friend list page function
        public function print_card_friendlist() {
            $count = $this->count_mutual_friends();
            echo "
            <div class='col-sm-6 mb-3 mb-sm-0'>
                <div class='card rounded-4 content bg-0'>
                    <div class='card-body'>
                        <div class='card-title d-flex justify-content-between'>
                            <h4>",  $this->_profileName, " <i class='fa-solid fa-hashtag fs-5'></i><span class='fs-5'>{$this->_friendID}</span></h4>
                            <p class='text-white px-2 py-1 bg-semi-transparent-accent rounded-pill'>$count mutual friend", $count > 1 ? "s" : "", "</p>
                        </div>
                        <button type='submit' class='btn btn-s2' name='unfriend_id' value='", $this->_friendID,"'><i class='fa-solid fa-user-xmark pe-2'></i> Unfriend</button>
                    </div>
                </div>
            </div>";
        }

        // print cards for add friend page function
        public function print_card_addfriend() {
            $count = $this->count_mutual_friends();
            echo "
            <div class='col-sm-6 mb-3 mb-sm-0'>
                <div class='card rounded-4 content bg-0'>
                    <div class='card-body'>
                        <div class='card-title d-flex justify-content-between'>
                            <h4>",  $this->_profileName, " <i class='fa-solid fa-hashtag fs-5'></i><span class='fs-5'>{$this->_friendID}</span></h4>
                            <p class='text-white px-2 py-1 bg-semi-transparent-accent rounded-pill'>$count mutual friend", $count > 1 ? "s" : "", "</p>
                        </div>
                        <button type='submit' class='btn btn-s2' name='addfriend_id' value='", $this->_friendID,"'><i class='fa-solid fa-user-plus pe-2'></i> Add as Friend</button>
                    </div>
                </div>
            </div>";
        }

        // getter for profile name
        public function get_profile_name() {
            return $this->_profileName;
        }

        public function count_mutual_friends() {
            $count = 0;
            $db = $this->_db;
            $friends = $db["myfriends"]->fetch(Table::WHERE_EQ, "friend_id1", $this->_friendID);
            foreach($friends as $row) {
                $id = $row["friend_id2"];
                $friends_of_id = $db["myfriends"]->fetch(Table::WHERE_EQ, "friend_id1", $id);

                foreach($friends_of_id as $friend_of_id) {
                    if ($this->_friendID == $friend_of_id["friend_id2"]) {
                        $count++;
                    }
                }
            }
            return $count;
        }
    }

    // Paginator class
    class Paginator {
        private $_maxNumItemsOnPg;
        private $_numPages;
        private $_items;
        private $_currentPage;

        public function __construct($maxNumItemsOnPg, $items) {
            $this->_maxNumItemsOnPg = $maxNumItemsOnPg;
            $this->_items = $items;
            $this->_currentPage = 1;
            $this->_numPages = (int) ceil(count($items) / $maxNumItemsOnPg);
        }

        public function get_page() {
            $page = array();
            $allowed = $this->_maxNumItemsOnPg;
            $myItems = $this->_items;
            $curPage = $this->_currentPage;

            $startIndex = ($curPage - 1) * $allowed;
            $endIndex = $startIndex + ($allowed - 1);
            if ($endIndex >= count($myItems)) {
                $endIndex = count($myItems) - 1;
            }
            
            for ($i = $startIndex; $i <= $endIndex; $i++) {
                $page[] = $myItems[$i];
            }
            return $page;
        }

        public function print_pagination() {
            $curPage = $this->_currentPage;
            $numPages = $this->_numPages;
            $btns = "";
            $pagination = "";

            if($curPage > 1 && $curPage <= $numPages) {
                $btns .= "<button type='submit' class='btn btn-s2 float-start' name='page' value='" . ($curPage - 1) . "'><i class='fa-solid fa-chevron-left pe-2'></i> Previous</button>";
            }
            if($curPage >= 1 && $curPage < $numPages) {
                $btns .= "<button type='submit' class='btn btn-s2 float-end' name='page' value='" . ($curPage + 1) . "'>Next <i class='fa-solid fa-chevron-right ps-2'></i></button>";
            }
            if (!empty($btns)) {
                $pagination = 
                    "
                    <div class='container mt-1 mb-5'>
                        $btns
                    </div>
                    ";
            }
            echo $pagination;
        }

        // Setter for _currentPage
        public function set_cur_page($i) {
            $this->_currentPage = $i;
        }
    }

    // Database class
    class Database implements ArrayAccess {
        private $_dbTables;
        private $_errorMsg;
        private $_dbConnect;

        public function __construct() {
            require_once("settings.php");

            // Connect to database
            $dbConnect = @new mysqli($host, $user, $pswd, $dbnm);
            if ($dbConnect->connect_error ) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables unsuccessfully created and populated, unable to connect to the database server.</p>"
                    . "<p class='font-monospace mb-0 text-start'>Error code " . $dbConnect->connect_errno
                    . ": " . $dbConnect->connect_error . "</p>";
            } else {
                $this->_dbConnect = $dbConnect;
            }
        }

        // add tables function
        public function addTables() {
            $tables = func_get_args();
            foreach($tables as &$table) {
                $table->connect($this->_dbConnect);
                if ($this->isSuccess()) {
                    $this->create($table);
                    $this->_dbTables[$table->Name()] = &$table;
                }
            }
        }

        // database operation functions
        private function create(&$table) {
            $sql = "CREATE TABLE IF NOT EXISTS ". $table->Name() . "(";
            foreach($table->Structure() as $name => $props) {
                $sql .= "$name ". str_replace(',', '', $props[0]). ", ";
            }
            $sql = substr($sql, 0, -2);
            $sql .= ")";
            $dbConnect = $this->_dbConnect;
            $queryResult = $dbConnect->query($sql);
            if (!$queryResult) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables unsuccessfully created and populated, unable to execute the query <br><code class='lh-1'>$sql</code>.</p>"
                    . "<p class='font-monospace mb-0'>Error code " . $dbConnect->errno
                    . ": " . $dbConnect->error . "</p>";
            }
        }
        public function close() {
            $this->_dbConnect->close();
        }

        // Overloading array operators
        public function offsetExists($name) {
            if(isset($this->_dbTables[$name])) {
                return true;
            } else {
                $this->_errMsg = "<p class='fw-semibold'>Tables unsuccessfully created and populated, table '$name' not exists!</p>";
                return false;
            }
        }
        public function offsetGet($name) {
            if(isset($this->_dbTables[$name])) {
                return $this->_dbTables[$name];
            } else {
                return null;
            }
        }
        public function offsetSet($name, $value) {
            $this->_dbTables[$name] = $value;
        }
    
        public function offsetUnset($name) {
            unset($this->_dbTables[$name]);
        }

        // Auxilliary functions
        public function isSuccess() {
            return empty($this->_errorMsg);
        }

        // Getter for errMsg
        public function errMsg() {
            return $this->_errorMsg;
        }
    }

    // Table class
    class Table {
        private $_tbName;
        private $_tbStructure;
        private $_expectNumValues;
        private $_dbConnect;
        private $_errorMsg;

        const WHERE_EQ = "=";
        const WHERE_NEQ = "<>";

        const ORDER_ASC = "";
        const ORDER_DESC = "DESC"; 
 
        public function __construct($name, $tb_struct) {
            $this->_tbName = $name;
            $this->_tbStructure = $tb_struct;
            foreach($tb_struct as $col_struct) {
                if ($col_struct[1]) {
                    $this->_expectNumValues++;
                }
            }
        }

        // add record(s) functions
        function addRecord($values) {
            $count = count($values);
            if ($count != $this->_expectNumValues) {
                $strRecord = ($count < 2) ? $values : implode(", ", $values);
                $this->_errorMsg = "<p class='fw-semibold'>Tables created but unsuccessfully populated, invalid number of values to insert.</p>
                                    <p class='font-monospace mb-0'>Expecting ".$this->_expectNumValues." value(s), get ". count($values)." value(s)<br>from record: '$strRecord' of table '{$this->_tbName}'";
                return;
            } else {
                if ($count < 2) {
                    return $values;
                } else {
                    return implode(", ", $values);
                }  
            }
        }
        public function addRecords($my_records, $dupField = "", $dupValIndex = -1) {
            $noDuplicates = true;
            $records = array();
            $str_records = "";
            foreach($my_records as $record) {
                if ($dupField == "all") {
                    if ($this->isCompleteDuplicate($record)) {
                        $noDuplicates = false;
                        continue;
                    }
                } elseif($dupField != "") {
                    if($this->isDuplicate($dupField, $record[$dupValIndex])) {
                        $noDuplicates = false;
                        continue;
                    }
                }
                if (empty($this->_errorMsg)) {
                    $records[] = "(". $this->addRecord($record). ")";
                } else {
                    break;
                }
            }
            $str_records = implode(", ", $records);
            if ($noDuplicates) {
                $this->populate($str_records);
            }
            return $noDuplicates;
        }
        
        // database operations
        public function connect(&$dbConnect) {
            $this->_dbConnect = $dbConnect;
        }
        private function populate($data) {
            $columns_str = "";
            foreach($this->_tbStructure as $colName => $props)
            {
                if($props[1])
                {
                    $columns_str .= "`$colName`, ";
                }
            }
            $columns_str = substr($columns_str, 0, -2);     // remove the last comma
            $sql = "INSERT INTO ". $this->_tbName . "($columns_str) VALUES ". $data;
            $dbConnect = $this->_dbConnect;
            $queryResult = $dbConnect->query($sql);
            if (!$queryResult) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables created but unsuccessfully populated, unable to execute the query: <br>'$sql'.</p>"
                    . "<p class='font-monospace mb-0'>Error code " . $dbConnect->errno
                    . ": " . $dbConnect->error . "</p>";
            }
        }
        public function update_id() {
            $numArgs = func_num_args();
            $args = func_get_args();

            if ($numArgs % 2 !== 1) {
                throw new InvalidArgumentException("Invalid number of arguments. Field-value pairs expected. Specify friend_id.");
            }

            $updates = array();
            $id = $args[0];

            for ($i = 1; $i < $numArgs; $i += 2) {
                $field = $args[$i];
                $value = $args[$i + 1];
                $updates[] = "$field = '$value'";
            }
            $sql = "UPDATE {$this->_tbName} SET " . implode(', ', $updates) . " WHERE friend_id = '$id'";
            $dbConnect = $this->_dbConnect;
            $queryResult = $dbConnect->query($sql);
            if (!$queryResult) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables created but unsuccessfully populated, unable to execute the query: <br>'$sql'.</p>"
                    . "<p class='font-monospace mb-0'>Error code " . $dbConnect->errno
                    . ": " . $dbConnect->error . "</p>";
            }
        }
        public function delete() {
            $numArgs = func_num_args();
            $args = func_get_args();
            $str_condition = "";

            if ($numArgs > 0) {
                $operator = $args[0];
                    if (!in_array($operator, [Table::WHERE_EQ, Table::WHERE_NEQ])) {
                        throw new InvalidArgumentException("Invalid operator: $operator");
                    }
                $conditions = array();
                for ($i = 1; $i < $numArgs; $i += 2) {
                    $field = $args[$i];
                    $value = $args[$i + 1];
                    $conditions[] = "$field $operator $value";
                }
                $str_condition = "WHERE " . implode(' AND ', $conditions);
            }
            
            $sql = "DELETE FROM {$this->_tbName} $str_condition";
            $dbConnect = $this->_dbConnect;
            $queryResult = $dbConnect->query($sql);
            if (!$queryResult) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables created but unsuccessfully populated, unable to execute the query: <br>'$sql'.</p>"
                    . "<p class='font-monospace mb-0'>Error code " . $dbConnect->errno
                    . ": " . $dbConnect->error . "</p>";
            }
        }

        // Auxilliary functions
        public function isSuccess() {
            return empty($this->_errorMsg);
        }
        public function isDuplicate($field, $value) {
            $result = false;
            $sql = "SELECT * FROM {$this->_tbName} WHERE $field = $value";
            $dbConnect = $this->_dbConnect;
            $queryResult = $dbConnect->query($sql);
            if (!$queryResult) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables created but unsuccessfully populated, unable to execute the query: <br>'$sql'.</p>"
                    . "<p class='font-monospace mb-0'>Error code " . $dbConnect->errno
                    . ": " . $dbConnect->error . "</p>";
            } else {
                if ($queryResult->num_rows > 0) {
                    $result = true;
                    $queryResult->free();
                }
                else
                    $result = false;
            }
            return $result;
        }
        public function isCompleteDuplicate($record) {
            $result = false;
            $conditions = array();
            $columns = array_keys($this->_tbStructure);
            foreach($columns as $i => $column) {
                $conditions[] = "$column = $record[$i]";
            }
            $sql = "SELECT * FROM {$this->_tbName} WHERE " . implode(" AND ", $conditions);
            $dbConnect = $this->_dbConnect;
            $queryResult = $dbConnect->query($sql);
            if (!$queryResult) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables created but unsuccessfully populated, unable to execute the query: <br>'$sql'.</p>"
                    . "<p class='font-monospace mb-0'>Error code " . $dbConnect->errno
                    . ": " . $dbConnect->error . "</p>";
            } else {
                if ($queryResult->num_rows > 0) {
                    $result = true;
                    $queryResult->free();
                }
                else
                    $result = false;
            }
            return $result;
        }
        public function fetch() {
            $numArgs = func_num_args();
            $args = func_get_args();
            $str_condition = "";
            $str_order = "";
            
            if ($numArgs > 0) {
                $offset = 1;

                $conditions = array();
                $operator = $orderSeq = $orderBy = "";
                if ($numArgs % 2 !== 1) {
                    $orderSeq = $args[0];
                    $orderBy = $args[1];
                    $offset += 1;
                } else {
                    $operator = $args[0];
                    if (!in_array($operator, [Table::WHERE_EQ, Table::WHERE_NEQ])) {
                        throw new InvalidArgumentException("Invalid operator: $operator");
                    }
                }
                
                if (in_array($args[2], array_keys($this->_tbStructure), true)) {
                    $orderSeq = $args[1];
                    $orderBy = $args[2];
                    if (!in_array($orderSeq, [Table::ORDER_ASC, Table::ORDER_DESC])) {
                        throw new InvalidArgumentException("Invalid use of order sequence: $orderSeq");
                    }
                    $str_order = " ORDER BY $orderBy $orderSeq";
                    $offset += 2; 
                }
                
                if (!empty($operator)) {
                    for ($i = 0 + $offset; $i < $numArgs; $i += 2) {
                        $field = $args[$i];
                        $value = $args[$i + 1];
                        $conditions[] = "$field $operator $value";
                    }
                    $str_condition = " WHERE " . implode(' AND ', $conditions);
                }
                
            }
    
            $result = array();
            $sql = "SELECT * FROM {$this->_tbName}$str_condition$str_order";
            $dbConnect = $this->_dbConnect;
            $queryResult = $dbConnect->query($sql);
            if (!$queryResult) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables created but unsuccessfully populated, unable to execute the query: <br>'$sql'.</p>"
                    . "<p class='font-monospace mb-0'>Error code " . $dbConnect->errno
                    . ": " . $dbConnect->error . "</p>";
            } else {
                $columns = array_keys($this->_tbStructure);
                $rows = array();
                while ($row = $queryResult->fetch_assoc()) {
                    // Access the columns using associative array keys
                    foreach($columns as $column) {
                        $rows[$column] = $row[$column];
                    }
                    $result[] = $rows;
                }
                $queryResult->free();
            }
            return $result;
        }
        public function count_rows() {
            $result = 0;
            $sql = "SELECT * FROM {$this->_tbName}";
            $dbConnect = $this->_dbConnect;
            $queryResult = $dbConnect->query($sql);
            if (!$queryResult) {
                $this->_errorMsg = "<p class='fw-semibold'>Tables created but unsuccessfully populated, unable to execute the query: <br>'$sql'.</p>"
                    . "<p class='font-monospace mb-0'>Error code " . $dbConnect->errno
                    . ": " . $dbConnect->error . "</p>";
            } else {
                $result = $queryResult->num_rows;
                $queryResult->free();
            }
            return $result;
        }


        // Getter for errMsg
        public function errMsg() {
            return $this->_errorMsg;
        }
        // Getter for tbName
        public function Name() {
            return $this->_tbName;
        }
        // Getter for tbStructure;
        public function Structure() {
            return $this->_tbStructure;
        }
    }
?>