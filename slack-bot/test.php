<?php
    /******************************
     * 
     *  KeystoneKeeper Slack Bot
     *  Version 1.0.0
     *  Date: 09/06/2018
     *  Description: Keeps track of Warcraft Mythic Keys 
     * 
     */


     //Cron Job - reset Keystone Name, Keystone Level at 9am West Coast Time
    

    http_response_code(200);
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        
        if(!empty($_REQUEST['text'])){
            
            $passed_argument = sanatize_input($_REQUEST['text']);
            if(preg_match("/about/", $passed_argument)){
                echo "*Keystone Keeper* v1.0.0 \n Author: Megan Davis - 2018 \n License: MIT \n Github:https://github.com/Davis24/slack_keystone_bot \n Submit Issues &amp; Feature Requests: https://github.com/Davis24/slack_keystone_bot/issues";
            }
            elseif(preg_match("/help/", $passed_argument)){
                echo "*Keystone Keeper Commands:* \n `/keystonekeeper status` - displays all current character keys \n `/keystonekeeper affixs` - displays the affix for the current week \n `/keystonekeeper update <character_name> <instance_name> <key_level>` - adds/updates character key information \n `/keystonekeeper about` - displays slackbot information including links on submitting bugs and feature requests";
            }
            elseif(preg_match("/affixs/", $passed_argument)){
                echo "*Keystone Keeper Commands:* \n `/keystonekeeper status` - displays all current character keys \n `/keystonekeeper affixs` - displays the affix for the current week \n `/keystonekeeper update <character_name> <instance_name> <key_level>` - adds/updates character key information \n `/keystonekeeper about` - displays slackbot information including links on submitting bugs and feature requests";
            }
            elseif(preg_match("/update\s+(.*)\s+(waycrest manor|manor|waycrest|siege|siege of boralus|underrot|motherlode|the motherlode|freehold|tol dagor|toldagor|dagor|shrine of the storm|shrine|kings|kings rest|king's rest|atal|atal dazar|atal'dazar|ataldazar|temple|temple of sethraliss|sethraliss)\s+(\d+)/i", $passed_argument, $results)){
                $db = db_connect();
                //results [1] = character name
                //results [2] = dungeon name
                //results [3] = key level
                //echo "Returning: $results[1], $results[2], $results[3]";
                $user_id = $_REQUEST['user_id'];
                $query_results =mysql_query("SELECT FROM users WHERE user_id = $user_id");
                if(mysql_num_rows($query_results) == 1){

                    
                    update_entry($db, $c_id, $results[2], $results[3]);
                }
                //Check if the name exists in the DB
                    //if true
                        //Update entry to include keystone
                    //if false
                        //Please Add Character first by using command '/keystone keeper character <Char_Name> <Char_Class>
            }
            elseif(preg_match("/character\s+([a-z]+)\s+(.*)/i", $passed_argument, $results)){ 
                //Adds character to database
                //Output Array - 0, 

                //Variables needed
                $user_id = $_REQUEST['user_id'];
                $db = db_connect();

                //Check to see if user_id exists
                $query_results = mysql_query("SELECT FROM user_char WHERE user_id = $user_id");
                if(mysql_num_rows($query_results) > 1)
                {
                    //If it does we need to check if this character already exists
                    $char_found = FALSE;
                    while($row = mysql_fetch_assoc($results)){
                        if($row['name'] == $output_array[1]){
                            $char_found = TRUE;
                            break;
                        }
                    }

                    //If it does exist WHY ARE THEY TRYING TO ADD IT, otherwise create the character
                    if($char_found = TRUE){
                        echo "This character already exists!";
                    }
                    else
                    {
                        create_character($db, $user_id, $results[1], $results[2]);
                    }
                }
                else
                {

                }
                //Check if character name exists
                    //if it does
                        //This character already exists!
                    //if it does not
            }
            else{
                echo "This command is not recgonized. Please use `/keystonekeeper help` if you need additional assistance.: $passed_argument";
            }  
        }
        else{
            echo "Oh no, you didn't pass in any text to Keystone Keeper. Please use `/keystonekeeper help` if you need additional assistance.";
        }

    }

    function update_entry($db,$character_id, $keystone_name, $keystone_level){
        $keystone_name = correct_dungeon_name($keystone_name);
        $mysql = "UPDATE character SET keystone= $keystone_name,level=$keystone_level WHERE char_id = $character_id";
        if(mysqli_query($db,$mysql))
        {
            echo "Updated";
        }
        else
        {
            echo "Oh no something went wrong try again."
        }
    }

    function create_character($db,$user, $character_name, $character_class){
        create_user($user);
        //check if character exists and is assigned to that user
            //if true 
                //Character already exists
            //if false
                //Create character

        
    }

    //Take user_id and check user table to see if it exists, if not add new user
    function create_user($db, $user_id)
    {
        $query_results = mysql_query("SELECT FROM user_char WHERE user_id = $user_id");
        if(mysql_num_rows($query_results) < 1)
        {
            "INSERT INTO users (user_id, team_id) VALUES ('$user_id', '')";
        }
    }

    function sanatize_input($data){
        $data = trim($data);
        $data = stripcslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function correct_dungeon_name($data){
        if(preg_match("/waycrest manor|manor|waycrest/i", $data))
        {
            return "Waycrest Manor";
        }
        elseif(preg_match("/siege|siege of boralus/i", $data))
        {
            return "Siege of Boralus";
        }
        elseif(preg_match("/underrot/i", $data))
        {
            return "Underrot";
        }
        elseif(preg_match("/motherlode|the motherlode/i", $data))
        {
            return "THE MOTHERLODE!!!";
        }
        elseif(preg_match("/freehold/i", $data))
        {
            return "Freehold";
        }
        elseif(preg_match("/tol dagor|toldagor|dagor/i", $data))
        {
            return "Tol Dagor";
        }
        elseif(preg_match("/shrine of the storm|shrine/i", $data))
        {
            return "Shrine of the Storm";
        }
        elseif(preg_match("/kings|kings rest|king's rest/i", $data))
        {
            return "King's Rest";
        }
        elseif(preg_match("/atal|atal dazar|atal'dazar|ataldazar/i", $data))
        {
            return "Atal'Dazar";
        }
        elseif(preg_match("/temple|temple of sethraliss|sethraliss/i", $data))
        {
            return "Temple of Sethraliss";
        }
        else
        {
            return 0;
        }
    }

    Affix 1	Affix 2	Affix 3
Fortified	Sanguine	Necrotic
Tyrannical	Bursting	Skittish
Fortified	Teeming	Quaking
Tyrannical	Raging	Necrotic
Fortified	Bolstering	Skittish
Tyrannical	Teeming	Volcanic
Fortified	Sanguine	Grievous
Tyrannical	Bolstering	Explosive
Fortified	Bursting	Quaking
Tyrannical	Raging	Volcanic
Fortified	Teeming	Explosive
Tyrannical	Bolstering	Grievous

    function get_affixis(){
        $affixs = array (
            array("Fortified, Sanguine, Necrotic, Infest"),
            array

        );
    }


    function db_connect() {
        // Define connection as a static variable, to avoid connecting more than once 
        
        // Try and connect to the database, if a connection has not been established yet
        if(!isset($db)) {
            // Load configuration as an array. Use the actual location of your configuration file
            $config = parse_ini_file('../../../private/config.ini'); 
            $db = mysqli_connect($config['servername'],$config['username'],$config['password'],$config['dbname']);
        }
        else
        {
        	#print "already connected <br>";
        }
        // If connection was not successful, handle the error
        if($db === false) {
            // Handle error - notify administrator, log to a file, show an error screen, etc.
            //return mysqli_connect_error(); 
        }
        #print "connect <br>";
        return $db;
    }


?>