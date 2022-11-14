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

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }
    if ($hero_name) {
        $query = "DELETE FROM Hero WHERE world_id = $world_id AND hero_name = \"$hero_name\" ";
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
        echo "There was a problem Deleting Hero: " . $hero_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"deleteHero.php\" method=\"POST\">\n";
            echo "<label for=\"hero_name\">Hero to delete: </label>\n";
            echo "<select name=\"hero_name\" id=\"hero_name\">\n";
            $hero_query = "SELECT d.hero_name FROM Hero d WHERE d.world_id = $world_id";
            $hero_result = mysqli_query($conn, $hero_query);
            while ($row = mysqli_fetch_assoc($hero_result)) {
                echo "<option value=\"" . $row["hero_name"] . "\">" . $row["hero_name"] . "</option>\n";
            }
            echo "</select>\n";

            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Delete\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Deleted : $hero_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>