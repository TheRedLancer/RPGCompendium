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
    $dungeon_name = $_POST["dungeon_name"];

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }
    if ($dungeon_name) {
        $query = "DELETE FROM Dungeon WHERE world_id = $world_id AND dungeon_name = \"$dungeon_name\" ";
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
    if ($dungeon_name && $result == FALSE) {
        // bad insert
        echo "There was a problem Deleting Dungeon: " . $dungeon_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"deleteDungeon.php\" method=\"POST\">\n";
            echo "<label for=\"dungeon_name\">Dungeon to delete: </label>\n";
            echo "<select name=\"dungeon_name\" id=\"dungeon_name\">\n";
            $dungeon_query = "SELECT d.dungeon_name FROM Dungeon d WHERE d.world_id = $world_id";
            $dungeon_result = mysqli_query($conn, $dungeon_query);
            while ($row = mysqli_fetch_assoc($dungeon_result)) {
                echo "<option value=\"" . $row["dungeon_name"] . "\">" . $row["dungeon_name"] . "</option>\n";
            }
            echo "</select>\n";

            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Delete\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Deleted : $dungeon_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>