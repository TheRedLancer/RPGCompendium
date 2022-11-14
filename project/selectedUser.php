<!DOCTYPE html>

<?php
    // connection params
    $config = parse_ini_file("../../private/config.ini");
    $server = $config["servername"];
    $username = $config["username"];
    $password = $config["password"];
    $database = "zburnaby_DB";
    $login = $_POST["login"];
    if (!$login) {
        header("Location: homepage.html");
    }
    
    
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }

    $query = "SELECT user_name FROM User WHERE user_name = \"$login\"";
    $result = mysqli_query($conn, $query);
    ?>

<html>
    <form action="selectedUser.php" method="POST">
        <input type="hidden" name="login" value="<?php echo $login; ?>"/>
        <input type="submit" value="Reload"/>
    </form>
    </br>
    
    <?php 
        if (mysqli_fetch_assoc($result)) {
            echo "<form action=\"homepage.html\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Log Out\"/>";
            echo "</form>";
            // There is a username with that name
            echo "<h1>$login's RPG Worlds</h1>";
            $query = "SELECT world_name, date_created, world_id FROM World WHERE user_name =\"". $login . "\" ORDER BY date_created DESC";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                echo "<h3>Select a world from the drop down list to view/edit</h3>";
                echo "<form action=\"selectedWorld.php\" method=\"POST\">";
                    echo "<select name=\"world_id\" id=\"world_id\">\n";
                    echo "<option value=\"empty\"></option>\n";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value=\"" . $row["world_id"] . "\">" . $row["world_name"] . " (" . $row["date_created"] . ")" . "</option>\n";
                    }
                    echo "</select>\n";
                    echo "<input type=\"submit\" value=\"Select World\"/>";
                echo "</form>";
            } else {
                echo "<h3>Could not find any worlds!</h3>";
            }
            echo "</br>";
            echo "<form action=\"addWorld.php\" method=\"POST\">";
                echo "<input type=\"hidden\" name=\"login\" value=\"$login\" />";
                echo "<input type=\"submit\" value=\"Click here to add a New World!\"/>";
            echo "</form>";
        } else {
            // There is not a username with that name
            echo "Invalid Username :" . $login;
            echo "</br></br><form action=\"homepage.html\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Go Back\"/>";
            echo "</form>";

            echo "</br></br><form action=\"addUser.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Add User\"/>";
            echo "</form>";
        }


    ?>
</html>