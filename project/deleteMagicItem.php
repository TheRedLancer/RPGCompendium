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

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }
    if ($item_name) {
        $query = "DELETE FROM MagicItem WHERE world_id = $world_id AND item_name = \"$item_name\" ";
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
        echo "There was a problem Deleting Magic Item: " . $item_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"deleteMagicItem.php\" method=\"POST\">\n";
            echo "<label for=\"item_name\">Magic Item to delete: </label>\n";
            echo "<select name=\"item_name\" id=\"item_name\">\n";
            $item_query = "SELECT d.item_name FROM MagicItem d WHERE d.world_id = $world_id";
            $item_result = mysqli_query($conn, $item_query);
            while ($row = mysqli_fetch_assoc($item_result)) {
                echo "<option value=\"" . $row["item_name"] . "\">" . $row["item_name"] . "</option>\n";
            }
            echo "</select>\n";

            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Delete\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Deleted : $item_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>