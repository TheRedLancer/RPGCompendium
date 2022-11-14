<!DOCTYPE html>

<?php
    // connection params
    $config = parse_ini_file("../../private/config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "zburnaby_DB";
    $world_id = $_POST["world_id"];
    $region_name = $_POST["region_name"];
    if (!$world_id) {
        header("Location: homepage.html");
    }
    $result = FALSE;
    $city_name = $_POST["city_name"];
    $population = $_POST["population"];

    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }

    $query = "INSERT into City VALUES (\"$city_name\", \"$population\", \"$region_name\", $world_id )";
    // echo $query;
    $result = mysqli_query($conn, $query);
    
    ?>

<html>
<form action="selectedWorld.php" method="POST">
    <input type="hidden" name="world_id" value="<?php echo $world_id; ?>"/>
    <input type="submit" value="Go Back"/>
</form>
    <?php 
    if ($city_name && $result == FALSE) {
        // bad insert
        echo "There was a problem adding City " . $city_name . ". Please try another name.";
    }
    if ($result == FALSE) {
        // or no insert
        echo "<form action=\"addCity.php\" method=\"POST\">\n
            <label for=\"city_name\">New City Name: </label>\n
            <input name=\"city_name\" type=\"text\" id=\"city_name\"/>\n</br></br>

            <label for=\"population\">City population: </label>\n
            <input name=\"population\" type=\"text\" id=\"population\"/>\n</br></br>";

            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n";
            echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"$region_name\" />\n
            </br>
            <input type=\"submit\" value=\"Add City\">\n
        </form>\n";
    } else {
        // good insert
        echo "<h3>Added City: $city_name</h3>\n
        <form action=\"selectedWorld.php\" method=\"POST\">\n
            <input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n
            <input type=\"submit\" value=\"Click to continue\">\n
        </form>\n";
    }
    
    ?>
</html>