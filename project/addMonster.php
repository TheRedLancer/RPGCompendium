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
    $monster_name = $_POST["monster_name"];
    $hit_points = $_POST["hit_points"];
    $ac = $_POST["ac"];
    $attack = $_POST["attack"];
    $cr = $_POST["cr"];
    $monster_type = $_POST["monster_type"];

    if ($monster_name) {
        // connect to db
        $conn = mysqli_connect($server, $username, $password, $database);
        // check connection
        if (!$conn) {
            die("Connection failed :" . mysqli_connect_error());
        }

        $query = "INSERT into Monster VALUES (\"$monster_name\", $world_id, $hit_points, $ac, \"$attack\", $cr, \"$monster_type\")";
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
    if ($monster_name && $result == FALSE) {
        // bad insert
        echo "There was a problem adding Monster " . $monster_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"addMonster.php\" method=\"POST\">\n
            <label for=\"monster_name\">New Monster Name: </label>\n
            <input name=\"monster_name\" type=\"text\" id=\"monster_name\"/>\n</br></br>

            <label for=\"hit_points\">Hit points: </label>\n
            <input name=\"hit_points\" type=\"text\" id=\"hit_points\"/>\n</br></br>

            <label for=\"ac\">Armor Class: </label>\n
            <input name=\"ac\" type=\"text\" id=\"ac\"/>\n</br></br>

            <label for=\"attack\">Attack ex: (2d4 Slashing): </label>\n
            <input name=\"attack\" type=\"text\" id=\"attack\"/>\n</br></br>

            <label for=\"cr\">Challenge Rating: </label>\n
            <input name=\"cr\" type=\"text\" id=\"cr\"/>\n</br></br>

            <label for=\"monster_type\">Monster Type: </label>\n
            <input name=\"monster_type\" type=\"text\" id=\"monster_type\"/>\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Add Monster\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added Monster: $monster_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>