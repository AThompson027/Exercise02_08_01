<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignGuestBook.php</title>
    <script src="modernizr.custom.65897"></script>
</head>

<body>
    <h1>Sign Guest Book</h1>
    <?php
    // global variables
    $hostname = "localhost";
    $username = "adminer";
    $password = "sense-grass-80";
    $DBName = "guestbook";
    $tablename = "visitors";
    $firstName = "";
    $lastName = "";
    $formErrorCount = 0;
    
    // this function will connect the file to the database
    function connectToDB($hostname, $username, $password) {
    $DBConnect = mysqli_connect($hostname, $username, $password);
        // if the database does not connect then it will display an error
    if (!$DBConnect) {
        echo "<p>Connection error: " . mysqli_connect_error() . "</p>\n";
    }
    return $DBConnect;
    }
    
    // this function selects the database
    function selectDB($DBConnect, $DBName) {
        $success = mysqli_select_db($DBConnect, $DBName);
        if ($success) {
//            echo "<p>Successfully selected the \"$DBName\" database.</p>\n";
        }
        else {
            echo "<p>Could not select the \"$DBName\" database:" . mysqli_error($DBConnect) . ", creating it.<p>\n";
            // creates the database
                $sql = "CREATE DATABASE $DBName";
            if (mysqli_query($DBConnect, $sql)) {
                echo "<p>Successfully created the \"$DBConnect\" database.</p>\n";
                $success = mysqli_select_db($DBConnect, $DBName);
                if ($success) {
//                    echo "<p>Count not create the \"$DBName\" database.</p>\n";
                }
            } 
            else {
                echo "<p>Could not create the \"$DBName\" database: " . mysqli_error($DBConnect) . "</p>\n";
            }
        }
        return $success;
    
    }
    
    // this function creates the table for the data
    function createTable($DBConnect, $tablename) {
        $success = false;
        $sql = "SHOW TABLES LIKE '$tablename'";
        $result = mysqli_query($DBConnect, $sql);
        if (mysqli_num_rows($result) === 0) {
            // this will create the table with a count ID and will increment with each row
            echo "The <strong>$tablename</strong> table does not exist, creating table.<br>\n";
            $sql = "CREATE TABLE $tablename(countID SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY, lastName VARCHAR(40), firstName VARCHAR(40))";
            $result = mysqli_query($DBConnect, $sql);
            // if there is no result (table) then there will be an error
            if ($result === false) {
                $success = false;
                echo "<p>Unable to create the $tablename table.</p>";
                echo "<p>Error code " . mysqli_errno($DBConnect) . ": " ,mysqli_error($DBConnect) . "</p>";
            }
            else {
                $success = true;
                echo "<p>Successfully created the $tablename table.</p>";
            }
        }
        // indicates if there already is a table with that name
        else {
            $success = true;
            echo "The $tablename table already exists<br>\n";
        }
        return $success;
    }
    // when you press the submit button, then it will strip out and trim unnecessary characters from input to prevent bugs
    if (isset($_POST['submit'])) {
        $firstName = stripslashes($_POST['firstName']);
        $firstName = trim($firstName);
        $lastName = stripslashes($_POST['lastName']);
        $lastName = trim($lastName);
        // if the inputs are empty then there will be an error
        if (empty($firstName) || empty($lastName)) {
            echo "<p>You must enter your first and last <strong>name</strong>.</p>\n";
            ++$formErrorCount;
        }
        if ($formErrorCount === 0) {
            // this connects to the database
        $DBConnect = connectToDB($hostname , $username, $password);
            // if the database is connected then it will select the database and create the table
            if(selectDB($DBConnect, $DBName)){
                if (createTable($DBConnect, $tablename)) {
                    echo "<p>Connection successful!</p>\n";
                    $sql = "INSERT INTO $tablename VALUES(NULL, '$lastName', '$firstName')";
                    $result = mysqli_query($DBConnect, $sql);
                    if ($result === false) {
                        // if there is no result then it will post an error
                        echo "<p>Unable to execute the query.</p>";
                        echo "<p>Error code " . mysqli_errno($DBConnect) . ":" . 
                        mysqli_error($DBConnect) . "</p>";
                    }
                    else {
                        echo "<h3>Thank you for signing our guest book!</h3>";
                        $firstName = "";
                        $lastName = "";
                    }
        }
            }
            // disconnects the database
            mysqli_close($DBConnect);
        }
    }
    
    ?>
<!--    form-->
    <form action="SignGuestBook.php" method="post">
        <p><strong>First Name: </strong><br>
        <input type="text" name="firstName" value="<?php echo $firstName; ?>"></p>
        <p><strong>Last Name: </strong><br>
        <input type="text" name="lastName" value="<?php echo $lastName; ?>"></p>
        <p><input type="submit" name="submit" value="Submit"></p>        
    </form>
    <?php
    // connects to database 
    $DBConnect = connectToDB($hostname, $username, $password);
    // if the database is connected then it will select the database and create the table for the visitor log
    if ($DBConnect) {
        if (selectDB($DBConnect, $DBName)) {
            if (createTable($DBConnect, $tablename)) {
//                echo "<p>Connection successful!</p>\n";
                echo "<h2>Visitors Log</h2>";
                $sql = "SELECT * FROM $tablename";
                $result = mysqli_query($DBConnect, $sql);
                // if there are no entries in the guest book then it will indicate that
                if (mysqli_num_rows($result) == 0) {
                    echo "<p>There are no entries in the quest book!</p>";
                }
                // else will create a table for the data to be inserted 
                else {
                    echo "<table width='60%' border='1'>";
                    echo "<tr>";
                    echo "<th>Visitor</th>";
                    echo "<th>First Name</th>";
                    echo "<th>Last Name</th>";
                    echo "</tr>";
                    while ($row = mysqli_fetch_row($result)) {
                        echo "<tr>";
                        echo "<td width='10%' style='text-align: center'>$row[0]</td>";
                        echo "<td>$row[2]</td>";
                        echo "<td>$row[1]</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    // This will fetch rows from a result-set, then free the memory associated with the result
                    mysqli_free_result($result);
                }
            }
        }
        mysqli_close($DBConnect);
    }
    ?>
</body>
</html>
