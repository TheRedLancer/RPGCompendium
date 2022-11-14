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
    $npc_name = $_POST["npc_name"];
    $city_name = $_POST["city_name"];
    $region_name = $_POST["region_name"];

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }
    if ($npc_name) {
        $query = "INSERT into NPCAppearance VALUES (\"$npc_name\", \"$city_name\", \"$region_name\", $world_id)";
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
    if ($npc_name && $result == FALSE) {
        // bad insert
        echo "There was a problem adding NPC " . $npc_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"addNPCToCity.php\" method=\"POST\">\n";
            echo "<label for=\"npc_name\">NPC to add: </label>\n";
            echo "<select name=\"npc_name\" id=\"npc_name\">\n";
            $npc_query = "SELECT n.npc_name FROM NPC n WHERE n.world_id = $world_id";
            $npc_result = mysqli_query($conn, $npc_query);
            while ($row = mysqli_fetch_assoc($npc_result)) {
                echo "<option value=\"" . $row["npc_name"] . "\">" . $row["npc_name"] . "</option>\n";
            }
            echo "</select>\n";
            echo "<input name=\"city_name\" id=\"city_name\" type=\"hidden\" value=\"$city_name\" />\n";
            echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"$region_name\" />\n";
            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Add NPC\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added NPC: $npc_name to City: $city_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>