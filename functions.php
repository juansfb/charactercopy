<?php
ini_set('display_errors', 'Off');

function Error($Error)
{
	echo "<br /><br /><br /><br /><br /><br /><center><b><span style='color:#cd0000;'> " . $Error . "</span></b></center>";
}

function check_name($name){
        include('configs.php');

        $url = "https://classic.warcraftlogs.com/v1/parses/character/{$name}/{$wcl_server}/{$wcl_region}?zone={$wcl_zone}&encounter={$wcl_encounter}&api_key={$wcl_api_key}";
        ini_set("allow_url_fopen", 1);
        $json = file_get_contents($url);
        $array = json_decode($json, true);

        $link = new mysqli($mysql_host,$mysql_user,$mysql_pass,$mysql_db);
        if ($link -> connect_errno) {
             echo "Failed to connect to MySQL: " . $link -> connect_error;
             exit();
        }

        $qry = mysqli_query($link,"SELECT * FROM characters WHERE name = '".$name."'");
        $check = mysqli_num_rows($qry);
        if($check) {
            if(strcasecmp($name,$array[0][characterName]) !== 0){
                echo '<script type="text/javascript">window.location = "index.php?error=PTR and Retail character names do not match.";</script>';
                exit();
            }
            else{
                unset($qry);
                $link -> close();
                return;
            }
        }
        else{
            echo '<script type="text/javascript">window.location = "index.php?error=PTR character does not exist with that name.";</script>';
            exit();
        }
}

function get_char_guid($ptr_name){
		include('configs.php');

		// mysqli oos connection
		$link = new mysqli($mysql_host,$mysql_user,$mysql_pass,$mysql_db);

		// Check connection
		if ($link -> connect_errno) {
			echo "Failed to connect to MySQL: " . $link -> connect_error;
			exit();
		}

		//GET CHAR ID FROM characters:characters
		$qry = mysqli_query($link,"SELECT * FROM characters WHERE name = '".$ptr_name."'");
		$guid = $qry -> fetch_row();
		unset($qry);
		$link -> close();

		return $guid[0];
}

function get_char_items($name){
        include('configs.php');

        $url = "https://classic.warcraftlogs.com/v1/parses/character/{$name}/{$wcl_server}/{$wcl_region}?zone={$wcl_zone}&encounter={$wcl_encounter}&api_key={$wcl_api_key}";
        ini_set("allow_url_fopen", 1);
        $json = file_get_contents($url);
        $array = json_decode($json, true);

        $items = array();

        for($i=0;$i<18;$i++){
            if($array[0][gear][$i][id]!==0){
                array_push($items,$array[0][gear][$i][id]);
            }
        }

        return $items;
}

function unique_guids($name,$items){
    //UNIQUE SEED FOR EACH ITEM FOR EACH PERSON
    //NAME_ASCII_VALUES_SUM x100 + ITEM_IDS_SUM x100 + EACH ITEM ID VALUE
    $sum1=0;
    $sum2=0;
    for($i=0; $i<strlen($name); $i++){
        $byte = substr($str,$pos);
        $sum1+=ord($byte);
    }
    for($i=0; $i<count($items); $i++){
        $sum2+=$items[$i];
    }
    $sum1*=100;
    $sum2*=100;
    $guids = array();
    for($i=0;$i<count($items);$i++){
        $temp = $sum1+$sum2+$items[$i];
        array_push($guids,$temp);
    }

    return $guids;
}

function char_copy()
{
		include('configs.php');

		// mysqli oos connection
		$link = new mysqli($mysql_host,$mysql_user,$mysql_pass,$mysql_db);

		// Check connection
		if ($link -> connect_errno) {
			echo "Failed to connect to MySQL: " . $link -> connect_error;
			exit();
		}
		
        if (empty($_POST["name"])) {
				echo '<script type="text/javascript">window.location = "index.php?error=You did not enter all the required information.";</script>';
        } else {
                $c_name = $_POST["name"];
				
				//comprobaciones minimas del input
				$user_chars = "#[^a-zA-Z0-9_\-\p{Letter}]#";
				if (preg_match($user_chars,$c_name)) {
						echo '<script type="text/javascript">window.location = "index.php?error=Please only use A-Z and 0-9.";</script>';
						exit();
                };
				
				//escape inputs
                $name = $link->real_escape_string($c_name);

                //STEP 0: BIG CHECK
				check_name($c_name);

				//STEP 1: GET CHAR GUID FROM characters:characters
				$ptrguid = get_char_guid($name);

				//STEP 2: GET ITEMS FROM WCL
                $items = get_char_items($name);

//                //escape items
//                for($i=0; $i<18; $i++){
//                    $items[i] = $link->real_escape_string($items[i]);
//                }


                //STEP 3: GET UNIQUE IDS FOR ITEMS
                $guids = unique_guids($name,$items);

                //STEP 4: CREATE ITEM INSTANCES INTO characters:item_instance
                for($i=0; $i<count($guids); ){
                    $qry = "INSERT INTO item_instance (guid,itemEntry,owner_guid,charges,flags,enchantments) VALUES ('{$guids[$i]}', '{$items[$i]}', '{$ptrguid}','0 0 0 0 0 ', '1', '0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 ')";
                    if (mysqli_query($link,$qry)){
                        $i++;
                    } else {
                        echo "Error updating record: " . mysqli_error($link);
                        exit();
                    }
                }

				//STEP 5: INSERT ITEMS INTO characters:mail_items
                for($i=0; $i<count($guids); ){
                    $qry = "INSERT INTO mail_items (mail_id,item_guid,item_template,receiver) VALUES ('{$guids[$i]}', '{$guids[$i]}', '{$items[$i]}', '{$ptrguid}')";
                    if (mysqli_query($link, $qry)) {
                        $i++;
                    } else {
//                            echo '<script type="text/javascript">window.location = "index.php?error= Copy failed. PTR Character has empty gear slots.";</script>';
                        echo "Error updating record: " . mysqli_error($link);
                        exit();

                    }
                }

                //STEP 5: INSERT ITEMS INTO characters:mail_items
                for($i=0; $i<count($guids); ){
                    $time = time();
                    $end = $time+250000;
                    $qry = "INSERT INTO mail (id,stationery,receiver,subject,has_items,expire_time,deliver_time) VALUES ('{$guids[$i]}', '61', '{$ptrguid}', 'Character copy', '1', '{$end}', '{$time}')";
                    if (mysqli_query($link, $qry)) {
                        $i++;
                    } else {
//                            echo '<script type="text/javascript">window.location = "index.php?error= Copy failed. PTR Character has empty gear slots.";</script>';
                        echo "Error updating record: " . mysqli_error($link);
                        exit();

                    }
                }

//            $qry = "UPDATE character_inventory SET item='{$guids[$i]}' WHERE guid='{$ptrguid}' AND bag='0' AND slot='{$i}'";



				echo '<br /><br /><br /><br /><br /><br /><center><span style="color:#41d600;">Your Character was successfully copied to the PTR!<br /></span></center>';
				$link -> close();
        };
	

}
?>