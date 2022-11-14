<!DOCTYPE html>

<?php
    // connection params
    $config = parse_ini_file("../../private/config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "zburnaby_DB";
    $login = $_POST["login"];
    if (!$login) {
        header("Location: homepage.html");
    }
    $result = FALSE;
    $worldName = $_POST["world_name"];
    if ($worldName) {
        // connect to db
        $conn = mysqli_connect($server, $username, $password, $database);
        // check connection
        if (!$conn) {
            die("Connection failed :" . mysqli_connect_error());
        }

        $query = "INSERT into World (world_name, user_name, date_created) VALUES (\"$worldName\", \"$login\", \"" . date("Y-m-d") . "\")";
        $result = mysqli_query($conn, $query);
    }
    ?>

<html>
    <?php 
    if ($worldName && $result == FALSE) {
        echo "There was a problem adding world " . $worldName . ". Please try another name.";
    }
    if ($result == FALSE) {
        // bad insert
        echo "<form action=\"addWorld.php\" method=\"POST\">\n
        <label for=\"world_name\">New World Name: </label>\n
        <input name=\"world_name\" type=\"text\" id=\"world_name\"/>\n
        <input name=\"login\" id=\"login\" type=\"hidden\" value=\"". $login . "\"/>\n
        <input type=\"submit\" value=\"Add World\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added world: ". $worldName ."</h3>\n
        <form action=\"selectedUser.php\" method=\"POST\">\n
        <input name=\"login\" id=\"login\" type=\"hidden\" value=\"". $login . "\"/>\n
        <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>