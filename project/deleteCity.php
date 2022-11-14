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
    $city_name = $_POST["city_name"];

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }
    if ($city_name) {
        $query = "DELETE FROM City WHERE world_id = $world_id AND city_name = \"$city_name\" ";
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
    if ($city_name && $result == FALSE) {
        // bad insert
        echo "There was a problem Deleting City: " . $city_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"deleteCity.php\" method=\"POST\">\n";
            echo "<label for=\"city_name\">City to delete: </label>\n";
            echo "<select name=\"city_name\" id=\"city_name\">\n";
            $city_query = "SELECT d.city_name FROM City d WHERE d.world_id = $world_id";
            $city_result = mysqli_query($conn, $city_query);
            while ($row = mysqli_fetch_assoc($city_result)) {
                echo "<option value=\"" . $row["city_name"] . "\">" . $row["city_name"] . "</option>\n";
            }
            echo "</select>\n";

            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Delete\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Deleted : $city_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>