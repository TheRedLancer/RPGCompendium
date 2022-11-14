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
    $item_name = $_POST["item_name"];
    $dungeon_name = $_POST["dungeon_name"];
    $region_name = $_POST["region_name"];

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }
    if ($item_name) {
        $query = "INSERT into DungeonItem VALUES (\"$item_name\", \"$dungeon_name\", \"$region_name\", $world_id)";
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
    if ($item_name && $result == FALSE) {
        // bad insert
        echo "There was a problem adding Magic Item " . $item_name . " to Dungeon: $dungeon_name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"addMagicItemToDungeon.php\" method=\"POST\">\n";
            echo "<label for=\"item_name\">Magic Item to add: </label>\n";
            echo "<select name=\"item_name\" id=\"item_name\">\n";
            $npc_query = "SELECT m.item_name FROM MagicItem m WHERE m.world_id = $world_id";
            $npc_result = mysqli_query($conn, $npc_query);
            while ($row = mysqli_fetch_assoc($npc_result)) {
                echo "<option value=\"" . $row["item_name"] . "\">" . $row["item_name"] . "</option>\n";
            }
            echo "</select>\n</br>";
            echo "<input name=\"dungeon_name\" id=\"dungeon_name\" type=\"hidden\" value=\"$dungeon_name\" />\n";
            echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"$region_name\" />\n";
            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Add Magic Item\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added Magic Item: $item_name to Dungeon: $dungeon_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>