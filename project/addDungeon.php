<!DOCTYPE html>

<?php
    // connection params
    $config = parse_ini_file("../../private/config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "zburnaby_DB";
    $world_id = $_POST["world_id"];
    $region_name = $_POST["region_name"];
    if (!$world_id) {
        header("Location: homepage.html");
    }
    $result = FALSE;
    $dungeon_name = $_POST["dungeon_name"];
    $dungeon_size = $_POST["dungeon_size"];
    $dungeon_type = $_POST["dungeon_type"];

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }

    $query = "INSERT into Dungeon VALUES (\"$dungeon_name\", \"$dungeon_size\", \"$dungeon_type\", \"$region_name\", $world_id )";
    // echo $query;
    $result = mysqli_query($conn, $query);
    
    ?>

<html>
<form action="selectedWorld.php" method="POST">
    <input type="hidden" name="world_id" value="<?php echo $world_id; ?>"/>
    <input type="submit" value="Go Back"/>
</form>
    <?php 
    if ($dungeon_name && $result == FALSE) {
        // bad insert
        echo "There was a problem adding Dungeon " . $dungeon_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"addDungeon.php\" method=\"POST\">\n
            <label for=\"dungeon_name\">New Dungeon Name: </label>\n
            <input name=\"dungeon_name\" type=\"text\" id=\"dungeon_name\"/>\n</br></br>";

            echo "<label for=\"dungeon_size\">Dungeon Size: </label>\n";
            echo "<select name=\"dungeon_size\" id=\"dungeon_size\">\n";
                echo "<option value=\"NULL\"></option>\n";
                echo "<option value=\"tiny\">tiny</option>\n";
                echo "<option value=\"small\">small</option>\n";
                echo "<option value=\"medium\">medium</option>\n";
                echo "<option value=\"large\">large</option>\n";
                echo "<option value=\"huge\">huge</option>\n";
                echo "<option value=\"gargantuan\">gargantuan</option>\n";
            echo "</select></br></br>";

            echo "<label for=\"dungeon_type\">Dungeon type: </label>\n
            <input name=\"dungeon_type\" type=\"text\" id=\"dungeon_type\"/>\n</br></br>";

            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n";
            echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"$region_name\" />\n
            </br>
            <input type=\"submit\" value=\"Add Dungeon\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added Dungeon: $dungeon_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>