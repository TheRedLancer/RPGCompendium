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
    $region_name = $_POST["region_name"];

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }
    if ($region_name) {
        $query = "DELETE FROM Region WHERE world_id = $world_id AND region_name = \"$region_name\" ";
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
    if ($region_name && $result == FALSE) {
        // bad insert
        echo "There was a problem Deleting Region: " . $region_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"deleteRegion.php\" method=\"POST\">\n";
            echo "<label for=\"region_name\">Region to delete: </label>\n";
            echo "<select name=\"region_name\" id=\"region_name\">\n";
            $region_query = "SELECT d.region_name FROM Region d WHERE d.world_id = $world_id";
            $region_result = mysqli_query($conn, $region_query);
            while ($row = mysqli_fetch_assoc($region_result)) {
                echo "<option value=\"" . $row["region_name"] . "\">" . $row["region_name"] . "</option>\n";
            }
            echo "</select>\n";

            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Delete\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Deleted : $region_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>