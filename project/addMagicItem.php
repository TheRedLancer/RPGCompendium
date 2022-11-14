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
    $effect = $_POST["effect"];
    $item_type = $_POST["item_type"];

    if ($item_name) {
        // connect to db
        $conn = mysqli_connect($server, $username, $password, $database);
        // check connection
        if (!$conn) {
            die("Connection failed :" . mysqli_connect_error());
        }

        $query = "INSERT into MagicItem VALUES (\"$item_name\", $world_id, \"$effect\", \"$item_type\")";
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
        echo "There was a problem adding Item " . $item_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"addMagicItem.php\" method=\"POST\">\n
            <label for=\"item_name\">New Item Name: </label>\n
            <input name=\"item_name\" type=\"text\" id=\"item_name\"/>\n</br></br>

            <label for=\"effect\">Item Effect: </label>\n
            <input name=\"effect\" type=\"text\" id=\"effect\"/>\n</br></br>

            <label for=\"item_type\">Item Type: </label>\n
            <input name=\"item_type\" type=\"text\" id=\"item_type\"/>\n</br></br>

            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Add Item\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added Item: $item_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>