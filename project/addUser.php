<!DOCTYPE html>
<?php
    // connection params
    $config = parse_ini_file("../../private/config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "zburnaby_DB";
    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }

    $usernameExists = false;
    $insertResult = false;
    $recievedLoginName = $_POST["login"];
    if ($recievedLoginName) {
        $query = "SELECT user_name FROM User WHERE user_name = \"$recievedLoginName\"";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $usernameExists = true;
        } else {
            // echo "Add user" . $recievedLoginName;
            $query = "INSERT into User VALUES (\"$recievedLoginName\")";
            $insertResult = mysqli_query($conn, $query);
        }
    }
    ?>
<html>
<form action="selectedWorld.php" method="POST">
    <input type="hidden" name="world_id" value="<?php echo $world_id; ?>"/>
    <input type="submit" value="Go Back"/>
</form>
    <?php 
        if ($usernameExists) {
            echo "Username: " . $recievedLoginName . " already exists, please enter a different name";
        }
        if ($insertResult == FALSE) {
            echo "<form action=\"addUser.php\" method=\"POST\">
            <label for=\"login\">New Username: </label>
            <input name=\"login\" type=\"text\" id=\"login\"/>
            <input type=\"submit\" value=\"Add User\">
            </form>";
        } else {
            echo "<h3>Added user: ". $recievedLoginName ."</h3>
            <form action=\"selectedUser.php\" method=\"POST\">
            <input name=\"login\" id=\"login\" type=\"hidden\" value=\"". $recievedLoginName . "\"/>
            <input type=\"submit\" value=\"Click to continue\">
            </form>";
        }

    ?>
    
</html>