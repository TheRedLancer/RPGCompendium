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
    $region_biome = $_POST["region_biome"];

    if ($region_name) {
        // connect to db
        $conn = mysqli_connect($server, $username, $password, $database);
        // check connection
        if (!$conn) {
            die("Connection failed :" . mysqli_connect_error());
        }

        $query = "INSERT into Region VALUES (\"$region_name\", \"$region_biome\", $world_id)";
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
        echo "There was a problem adding region " . $region_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"addRegion.php\" method=\"POST\">\n
            <label for=\"region_name\">New Region Name: </label>\n
            <input name=\"region_name\" type=\"text\" id=\"region_name\"/>\n</br></br>
            <label for=\"region_biome\">New Region Biome: </label>\n
            <input name=\"region_biome\" type=\"text\" id=\"region_biome\"/>\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            </br>
            <input type=\"submit\" value=\"Add Region\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added Region: $region_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>