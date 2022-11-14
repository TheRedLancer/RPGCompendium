<!DOCTYPE html>

<?php
    // connection params
    $config = parse_ini_file("../../private/config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "zburnaby_DB";
    $world_id = $_POST["world_id"];
    if (!$world_id) {
        header("Location: homepage.html");
    }
    $result = FALSE;
    $hero_name = $_POST["hero_name"];
    $class = $_POST["class"];

    if ($hero_name) {
        // connect to db
        $conn = mysqli_connect($server, $username, $password, $database);
        // check connection
        if (!$conn) {
            die("Connection failed :" . mysqli_connect_error());
        }

        $query = "INSERT into Hero VALUES (\"$hero_name\", \"$class\", $world_id)";
        // echo $query;
        $result = mysqli_query($conn, $query);
    }
    ?>

<html>
<form action="selectedWorld.php" method="POST">
    <input type="hidden" name="world_id" value="<?php echo $world_id; ?>"/>
    <input type="submit" value="Go Back"/>
</form>
    <?php 
    if ($hero_name && $result == FALSE) {
        // bad insert
        echo "There was a problem adding hero " . $hero_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"addHero.php\" method=\"POST\">\n
            <label for=\"hero_name\">New Hero Name: </label>\n
            <input name=\"hero_name\" type=\"text\" id=\"hero_name\"/>\n</br></br>
            <label for=\"class\">New Class: </label>\n
            <input name=\"class\" type=\"text\" id=\"class\"/>\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Add Hero\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added Hero: $hero_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>