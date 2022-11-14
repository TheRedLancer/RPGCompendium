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
    $profession = $_POST["profession"];
    $home_city = $_POST["home_city"];

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }

    $query = "INSERT into NPC VALUES (\"$npc_name\", $world_id, \"$profession\", \"$home_city\")";
    // echo $query;
    $result = mysqli_query($conn, $query);
    
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
        echo "<form action=\"addNPC.php\" method=\"POST\">\n
            <label for=\"npc_name\">New NPC Name: </label>\n
            <input name=\"npc_name\" type=\"text\" id=\"npc_name\"/>\n</br></br>

            <label for=\"profession\">NPC profession: </label>\n
            <input name=\"profession\" type=\"text\" id=\"profession\"/>\n</br></br>";

            echo "<label for=\"home_city\">NPC's Home City: </label>\n";
            echo "<select name=\"home_city\" id=\"home_city\">\n";
            echo "<option value=\"NULL\"></option>\n";
            $city_query = "SELECT t.city_name FROM City t WHERE t.world_id = $world_id";
            $city_result = mysqli_query($conn, $city_query);
            while ($row = mysqli_fetch_assoc($city_result)) {
                echo "<option value=\"" . $row["city_name"] . "\">" . $row["city_name"] . "</option>\n";
            }
            echo "</select>\n";

            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Add NPC\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added NPC: $npc_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>