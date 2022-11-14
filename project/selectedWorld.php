<!DOCTYPE html>
<style>
.collapsible {
  background-color: #777;
  color: white;
  cursor: pointer;
  padding: 18px;
  width: 60%;
  border: none;
  margin: 4px;
  text-align: left;
  outline: none;
  font-size: 15px;
}

.active, .collapsible:hover {
  background-color: lightgreen;
  color: black;
}

.collapsible:after {
  content: '>';
  font-size: 16px;
  color: white;
  float: right;
  margin-left: 5px;
}

table, th, td {
  border:1px solid black;
}

.active:after {
  content: "v";
}

.content {
  padding: 18px;
  display: none;
  overflow: hidden;
  min-height: 18px;
  width: 60%;
  background-color: #f1f1f1;
}
</style>

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
    // connect to db
    $conn = mysqli_connect($server, $username, $password, $database);
    // check connection
    if (!$conn) {
        die("Connection failed :" . mysqli_connect_error());
    }

    $query = "SELECT world_name, user_name FROM World WHERE world_id = $world_id";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $login = $row["user_name"];
        $world_name = $row["world_name"];
    } else {
        header("Location: homepage.html");
    }    
    ?>

<html>
    <form action="selectedUser.php" method="POST">
        <input type="hidden" name="world_id" value="<?php echo $world_id; ?>"/>
        <input type="submit" value="Back to World Select"/>
    </form>
    </br>
    <form action="homepage.html" method="POST">
        <input type="submit" value="Log Out"/>
    </form>
    </br>
    <form action="selectedWorld.php" method="POST">
        <input type="hidden" name="world_id" value="<?php echo $world_id; ?>"/>
        <input type="submit" value="Reload"/>
    </form>
    <?php
        echo "<h1>$login's $world_name RPG World</h1>\n";
        echo "<h3>Select an item below to expand!</h3>\n";

        echo "<button type=\"button\" class=\"collapsible\">Regions</button>\n";
        echo "<div class=\"content\">\n";
        {
            $region_query = "SELECT region_name, biome FROM Region WHERE world_id = $world_id";
            $region_result = mysqli_query($conn, $region_query);
            while ($region = mysqli_fetch_assoc($region_result)) {
                echo "<button type=\"button\" class=\"collapsible\">" . $region["region_name"] . " (Biome: " . $region["biome"] . ")" . "</button>\n";
                echo "<div class=\"content\">\n";
                    {
                        echo "<button type=\"button\" class=\"collapsible\">Cities</button>\n";
                        echo "<div class=\"content\">\n";
                            {
                                //echo "SELECT city_name, population FROM City WHERE region_name = \"" . $region["region_name"] . "\"";
                                $city_query = "SELECT city_name, population 
                                                FROM City WHERE region_name = \"" . $region["region_name"] . "\" AND world_id = " . $world_id . " ORDER BY population DESC";
                                $city_result = mysqli_query($conn, $city_query);
                                while ($city = mysqli_fetch_assoc($city_result)) {
                                    echo "<button type=\"button\" class=\"collapsible\">". $city["city_name"] . "</button>\n";
                                    echo "<div class=\"content\">\n";
                                        echo "<p>Population: " . $city["population"] . "</p>\n";
                                        echo "</br><form action=\"addNPCToCity.php\" method=\"POST\">";
                                            echo "<input name=\"city_name\" id=\"city_name\" type=\"hidden\" value=\"". $city["city_name"] . "\" />\n";
                                            echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"" . $region["region_name"] . "\" />\n";
                                            echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n";
                                            echo "<input type=\"submit\" value=\"Click to add NPC to this City\"/>";
                                        echo "</form>"; 
                                        echo "</br><form action=\"deleteCity.php\" method=\"POST\">";
                                            echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                                            echo "<input name=\"city_name\" id=\"city_name\" type=\"hidden\" value=\"" . $city["city_name"] . "\" />\n";
                                            echo "<input type=\"submit\" value=\"Click to delete this city\"/>";
                                        echo "</form>";

                                    echo "</div>\n";
                                }
                                echo "</br><form action=\"addCity.php\" method=\"POST\">";
                                    echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                                    echo "<input type=\"submit\" value=\"Click here to add a City to this Region!\"/>";
                                    echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"" . $region["region_name"] . "\" />\n";
                                echo "</form>";
                            }
                        echo "</div>\n";

                        
                        echo "<button type=\"button\" class=\"collapsible\">Dungeons</button>\n";
                        echo "<div class=\"content\">\n";
                            {
                                //echo "SELECT dungeon_name, dungeon_size, dungeon_type FROM Dungeon WHERE region_name = \"" . $region["region_name"] . "\"";
                                $dungeon_query = "SELECT dungeon_name, dungeon_size, dungeon_type FROM Dungeon WHERE region_name = \"" . $region["region_name"] . "\"AND world_id = " . $world_id;
                                $dungeon_result = mysqli_query($conn, $dungeon_query);
                                while ($dungeon = mysqli_fetch_assoc($dungeon_result)) {
                                    echo "<button type=\"button\" class=\"collapsible\">". $dungeon["dungeon_name"] . "</button>\n";
                                    echo "<div class=\"content\">\n";
                                        echo "<p>Dungeon Size: " . $dungeon["dungeon_size"] . "</p>\n";
                                        echo "<p>Dungeon Type: " . $dungeon["dungeon_type"] . "</p>\n";
                                        // List all monsters found in this dungeon
                                        $monster_appearance_query = "SELECT DISTINCT dm.monster_name, dm.monster_count" . 
                                        " FROM DungeonMonster dm" .
                                        " WHERE dm.dungeon_name = \"" . $dungeon["dungeon_name"] . "\" AND dm.world_id = \"$world_id\"";
                                        $monster_appearance_result = mysqli_query($conn, $monster_appearance_query);
                                        if (mysqli_num_rows($monster_appearance_result) > 0) {
                                            echo "<p>Monsters found in this dungeon: </p><ul>";
                                            while ($monster = mysqli_fetch_assoc($monster_appearance_result)) {
                                                echo "<li>" . $monster["monster_name"] .  " : " . $monster["monster_count"] .  "</li>";
                                            }
                                            echo "</ul>";
                                        } else {
                                            echo "<p>No monsters found in this dungeon</p>";
                                        }         

                                        {

                                            echo "</br><form action=\"addMonsterToDungeon.php\" method=\"POST\">";
                                                echo "<input name=\"dungeon_name\" id=\"dungeon_name\" type=\"hidden\" value=\"". $dungeon["dungeon_name"] . "\" />\n";
                                                echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"" . $region["region_name"] . "\" />\n";
                                                echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n";
                                                echo "<input type=\"submit\" value=\"Click to add Monster to this Dungeon\"/>";
                                            echo "</form>"; 

                                            echo "</br><form action=\"addMagicItemToDungeon.php\" method=\"POST\">";
                                                echo "<input name=\"dungeon_name\" id=\"dungeon_name\" type=\"hidden\" value=\"". $dungeon["dungeon_name"] . "\" />\n";
                                                echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"" . $region["region_name"] . "\" />\n";
                                                echo "<input name=\"world_id\" id=\"world_id\" type=\"hidden\" value=$world_id />\n";
                                                echo "<input type=\"submit\" value=\"Click to add a Magic Item to this Dungeon\"/>";
                                            echo "</form>"; 

                                            echo "</br><form action=\"deleteDungeon.php\" method=\"POST\">";
                                                echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                                                echo "<input name=\"dungeon_name\" id=\"dungeon_name\" type=\"hidden\" value=\"" . $dungeon["dungeon_name"] . "\" />\n";
                                                echo "<input type=\"submit\" value=\"Click to delete this dungeon\"/>";
                                            echo "</form>";
                                        }
                                    echo "</div>\n";
                                }
                                echo "</br><form action=\"addDungeon.php\" method=\"POST\">";
                                    echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                                    echo "<input type=\"submit\" value=\"Click here to add a Dungeon to this Region!\"/>";
                                    echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"" . $region["region_name"] . "\" />\n";
                                echo "</form>";
                            }
                        echo "</div>\n";
                    }
                    echo "</br><form action=\"deleteRegion.php\" method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                        echo "<input name=\"region_name\" id=\"region_name\" type=\"hidden\" value=\"" . $city["region_name"] . "\" />\n";
                        echo "<input type=\"submit\" value=\"Click to delete this region WARNING: DELETES ALL CHILDREN ITEMS\"/>";
                    echo "</form>";
                echo "</div>\n";
            }
            echo "</br><form action=\"addRegion.php\" method=\"POST\">";
                echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                echo "<input type=\"submit\" value=\"Click here to add a Region!\"/>";
            echo "</form>";
        }
        
        echo "</div>\n";

        echo "<button type=\"button\" class=\"collapsible\">Heroes</button>\n";
        echo "<div class=\"content\">\n";
        {
            $hero_query = "SELECT hero_name, class FROM Hero WHERE world_id = \"$world_id\"";
            $hero_result = mysqli_query($conn, $hero_query);
            while ($hero = mysqli_fetch_assoc($hero_result)) {
                echo "<button type=\"button\" class=\"collapsible\">". $hero["hero_name"] . "</button>\n";
                echo "<div class=\"content\">\n";
                    echo "<p>Class: " . $hero["class"] . "</p>\n";
                echo "</br><form action=\"deleteHero.php\" method=\"POST\">";
                    echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                    echo "<input name=\"hero_name\" id=\"hero_name\" type=\"hidden\" value=\"" . $city["hero_name"] . "\" />\n";
                    echo "<input type=\"submit\" value=\"Click to delete this hero\"/>";
                echo "</form>";
                echo "</div>\n";
                
            }
            echo "</br><form action=\"addHero.php\" method=\"POST\">";
                echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                echo "<input type=\"submit\" value=\"Click here to add a Hero!\"/>";
            echo "</form>";
        }
        echo "</div>\n";

        echo "<button type=\"button\" class=\"collapsible\">Monsters</button>\n";
        echo "<div class=\"content\">\n";
        {
            $monster_query = "SELECT DISTINCT m.monster_name, m.hit_points, m.ac, m.attack, m.cr, m.monster_type" . 
                            " FROM Monster m" .
                            " WHERE m.world_id = \"$world_id\"";
            $monster_result = mysqli_query($conn, $monster_query);
            while ($monster = mysqli_fetch_assoc($monster_result)) {
                echo "<button type=\"button\" class=\"collapsible\">". $monster["monster_name"] . "</button>\n";
                echo "<div class=\"content\">\n";
                    echo "<p>Hit Points: " . $monster["hit_points"] . "</p>\n";
                    echo "<p>AC: " . $monster["ac"] . "</p>\n";
                    echo "<p>Attack: " . $monster["attack"] . "</p>\n";
                    echo "<p>CR: " . $monster["cr"] . "</p>\n";
                    echo "<p>Monster Type: " . $monster["monster_type"] . "</p>\n";
                    {
                        $dungeon_monster_query = "SELECT dm.dungeon_name, dm.region_name" .
                            " FROM DungeonMonster dm" .
                            " WHERE dm.world_id = \"$world_id\" AND dm.monster_name = \"" . $monster["monster_name"] . "\"";
                        $dungeon_monster_result = mysqli_query($conn, $dungeon_monster_query);
                        if (mysqli_num_rows($dungeon_monster_result) > 0) {
                            echo "<p>Can be found in: </p><ul>";
                            while ($item = mysqli_fetch_assoc($dungeon_monster_result)) {
                                echo "<li>" . $item["region_name"] . " > " . $item["dungeon_name"] .  "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p>Not found in any dungeon</p>";
                        }
                    }
                    echo "</br><form action=\"deleteMonster.php\" method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                        echo "<input name=\"monster_name\" id=\"monster_name\" type=\"hidden\" value=\"" . $city["monster_name"] . "\" />\n";
                        echo "<input type=\"submit\" value=\"Click to delete this Monster\"/>";
                    echo "</form>";
                echo "</div>\n";
            }
            echo "</br><form action=\"addMonster.php\" method=\"POST\">";
                echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                echo "<input type=\"submit\" value=\"Click here to add a Monster!\"/>";
            echo "</form>";
        }
        echo "</div>\n";

        echo "<button type=\"button\" class=\"collapsible\">Magic Items</button>\n";
        echo "<div class=\"content\">\n";
        {
            $item_query = "SELECT DISTINCT i.item_name, i.effect, i.item_type" . 
                            " FROM MagicItem i" .
                            " WHERE i.world_id = \"$world_id\"";
            $item_result = mysqli_query($conn, $item_query);
            while ($item = mysqli_fetch_assoc($item_result)) {
                echo "<button type=\"button\" class=\"collapsible\">". $item["item_name"] . "</button>\n";
                echo "<div class=\"content\">\n";
                    echo "<p>Effect: " . $item["effect"] . "</p>\n";
                    echo "<p>Item Type: " . $item["item_type"] . "</p>\n";
                    {
                        
                        $dungeon_item_query = "SELECT di.dungeon_name, di.region_name" . 
                                                " FROM DungeonItem di" .
                                                " WHERE di.world_id = \"$world_id\" AND di.item_name = \"" . $item["item_name"] . "\"";
                        $dungeon_item_result = mysqli_query($conn, $dungeon_item_query);
                        if (mysqli_num_rows($dungeon_item_result) > 0) {
                            echo "<p>Can be found in: </p><ul>";
                            while ($item = mysqli_fetch_assoc($dungeon_item_result)) {
                                echo "<li>" . $item["region_name"] . " > " . $item["dungeon_name"] .  "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p>Not found in any dungeon</p>";
                        }
                        
                    }
                    echo "</br><form action=\"deleteMagicItem.php\" method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                        echo "<input name=\"item_name\" id=\"item_name\" type=\"hidden\" value=\"" . $city["item_name"] . "\" />\n";
                        echo "<input type=\"submit\" value=\"Click to delete this Magic Item\"/>";
                    echo "</form>";
                echo "</div>\n";
            }
            echo "</br><form action=\"addMagicItem.php\" method=\"POST\">";
                echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                echo "<input type=\"submit\" value=\"Click here to add a Magic Item!\"/>";
            echo "</form>";
        }
        echo "</div>\n";

        echo "<button type=\"button\" class=\"collapsible\">NPCs</button>\n";
        echo "<div class=\"content\">\n";
        {
            $npc_query = "SELECT DISTINCT n.npc_name, n.profession, n.home_city" . 
                            " FROM NPC n" .
                            " WHERE n.world_id = \"$world_id\"";
            $npc_result = mysqli_query($conn, $npc_query);
            while ($npc = mysqli_fetch_assoc($npc_result)) {
                echo "<button type=\"button\" class=\"collapsible\">". $npc["npc_name"] . "</button>\n";
                echo "<div class=\"content\">\n";
                    echo "<p>Profession: " . $npc["profession"] . "</p>\n";
                    if ($npc["home_city"]) {
                        echo "<p>Home City: " . $npc["home_city"] . "</p>\n";
                    } else {
                        echo "<p>No home City</p>\n";
                    }
                    {
                        $npc_appearance_query = "SELECT na.city_name, na.region_name" . 
                                                " FROM NPCAppearance na" .
                                                " WHERE na.world_id = \"$world_id\" AND na.npc_name = \"" . $npc["npc_name"] . "\"";
                        $npc_appearance_result = mysqli_query($conn, $npc_appearance_query);
                        if (mysqli_num_rows($npc_appearance_result) > 0) {
                            echo "<p>Can be found in: </p><ul>";
                            while ($npc = mysqli_fetch_assoc($npc_appearance_result)) {
                                echo "<li>" . $npc["region_name"] . " > " . $npc["city_name"] .  "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p>Not found in other locations</p>";
                        }                        
                    }
                    echo "</br><form action=\"deleteNPC.php\" method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                        echo "<input name=\"npc_name\" id=\"npc_name\" type=\"hidden\" value=\"" . $city["npc_name"] . "\" />\n";
                        echo "<input type=\"submit\" value=\"Click to delete this NPC\"/>";
                    echo "</form>";
                echo "</div>\n";
            }
            echo "</br><form action=\"addNPC.php\" method=\"POST\">";
                echo "<input type=\"hidden\" name=\"world_id\" value=$world_id />";
                echo "<input type=\"submit\" value=\"Click here to add an NPC!\"/>";
            echo "</form>";
        }
        echo "</div>\n";

        echo "<button type=\"button\" class=\"collapsible\">All Dungeons</button>\n";
        echo "<div class=\"content\">\n";
        {
            $dungeon_query = "SELECT d.dungeon_name, d.dungeon_size, d.dungeon_type, d.region_name" .
                            " FROM Dungeon d" . 
                            " WHERE d.world_id = \"$world_id\"";
            $dungeon_result = mysqli_query($conn, $dungeon_query);
            while ($dungeon = mysqli_fetch_assoc($dungeon_result)) {
                echo "<button type=\"button\" class=\"collapsible\">". $dungeon["dungeon_name"] . "</button>\n";
                echo "<div class=\"content\">\n";
                    echo "<p>Dungeon Size: " . $dungeon["dungeon_size"] . "</p>\n";
                    echo "<p>Dungeon Type: " . $dungeon["dungeon_type"] . "</p>\n";
                    echo "<p>Found in region: " . $dungeon["region_name"] . "</p>\n";
                    // List all monsters found in this dungeon
                    $monster_appearance_query = "SELECT DISTINCT dm.monster_name, dm.monster_count" . 
                                                " FROM DungeonMonster dm" .
                                                " WHERE dm.dungeon_name = \"" . $dungeon["dungeon_name"] . "\" AND dm.world_id = \"$world_id\"";
                    $monster_appearance_result = mysqli_query($conn, $monster_appearance_query);
                    if (mysqli_num_rows($monster_appearance_result) > 0) {
                        echo "<p>Monsters found in this dungeon: </p><ul>";
                        while ($monster = mysqli_fetch_assoc($monster_appearance_result)) {
                            echo "<li>" . $monster["monster_name"] .  " : " . $monster["monster_count"] .  "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No monsters found in this dungeon</p>";
                    }         
                echo "</div>\n";
            }
        }
        echo "</div>\n";

        echo "<button type=\"button\" class=\"collapsible\">All Cities</button>\n";
        echo "<div class=\"content\">\n";
        {
            $city_query = "SELECT t.city_name, t.population, t.region_name" .
                            " FROM City t" . 
                            " WHERE t.world_id = \"$world_id\" ORDER BY t.population DESC";
            $city_result = mysqli_query($conn, $city_query);
            while ($city = mysqli_fetch_assoc($city_result)) {
                echo "<button type=\"button\" class=\"collapsible\">". $city["city_name"] . "</button>\n";
                echo "<div class=\"content\">\n";
                    echo "<p>Popultaion: " . $city["population"] . "</p>\n";
                    echo "<p>Located in region: " . $city["region_name"] . "</p>\n";
                echo "</div>\n";
            }
        }
        echo "</div>\n";

        echo "<h3>Fun and Interesting stats!!!</h3>";
        // Average monster difficulty based on region
        /*-- Get weighted average monster challenge rating of each Dungeon
            SELECT r.region_name, AVG(m.cr * dm.monster_count) as average_cr
            FROM Region r LEFT OUTER JOIN Dungeon d USING (region_name, world_id) LEFT OUTER JOIN DungeonMonster dm USING (dungeon_name, world_id) LEFT OUTER JOIN Monster m USING (monster_name, world_id)
            WHERE r.world_id = 2
            GROUP BY r.region_name; */
        $region_difficulty_query = "SELECT r.region_name, AVG(m.cr * dm.monster_count) as average_cr".
                                    " FROM Region r LEFT OUTER JOIN Dungeon d USING (region_name, world_id) LEFT OUTER JOIN DungeonMonster dm USING (dungeon_name, world_id) LEFT OUTER JOIN Monster m USING (monster_name, world_id)" . 
                                    " WHERE r.world_id = \"$world_id\"" .
                                    " GROUP BY r.region_name ORDER BY average_cr DESC";
        $result = mysqli_query($conn, $region_difficulty_query);
        echo "<table><tr><th>Region Name</th><th>Average Monster Difficulty</th></tr>";
        while ($region = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $region["region_name"] . "</td>";
            if ($region["average_cr"] != NULL) {
                echo "<td>" . round($region["average_cr"], 2) . "</td>";
            } else {
                echo "<td>No monsters in this Region</td>";
            }
            echo "</tr>";        
        }
        echo "</table>";
    ?>
    

    <script>
        // Script for collapsable HTML elemets take from https://www.w3schools.com/howto/howto_js_collapsible.asp
        var coll = document.getElementsByClassName("collapsible");
        var i;

        for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.display === "block") {
                content.style.display = "none";
                } else {
                content.style.display = "block";
                }
            });
        }
    </script>

</html>
