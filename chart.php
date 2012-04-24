<?php
//NOTE: $db_user, $db_pass, $db_host variables in the db_connect function at the bottom of this file
$databaseName = ''; //the name of your database goes here.
$url = ''; //RSS feed goes here. Generate feed URL from: http://itunes.apple.com/rss
$chart = '';// This is the name of the MySQL table you want the data to be stored into.
$data = getURL($url);
$xml = new SimpleXMLElement($data);
$xmlArr = toArray($xml);
$entries = $xmlArr['entry'];

$appRankings = array();
for ($i=0;$i<count($entries);$i++) {
	$appName = explode("-", $entries[$i]['title']);
	$appRankings[$i]['name'] = trim($appName[0]);
	$appRankings[$i]['rank'] = $i+1;
}

$rankStr = json_encode($appRankings);

db_connect($databaseName);

for ($i=0;$i<count($appRankings);$i++) {
        $name = $appRankings[$i]['name'];
        //checking to see if app exists in database.
        $appAdded = false;
        $result = q("SELECT `id` FROM  `apps` WHERE `app_name` = '$name' AND `chart` = '$chart';");
        while($row = mysql_fetch_row($result)) {
                if ($row[0] > 0) { //app exists in DB.
                        $appRankings[$i]['id'] = $row[0];
                        $appAdded = true;
                }
        }
        if (!$appAdded) { //app does NOT exist in DB.
                $insert = q("INSERT INTO `ranktracker`.`apps` (`app_name`, `chart`) VALUES ('$name', '$chart');");
                $result = q("SELECT `id` FROM  `apps` WHERE `app_name` = '$name' AND `chart` = '$chart';");
                while($row = mysql_fetch_row($result)) {
                        $appRankings[$i]['id'] = $row[0];
                }
        }
}

$date = date('Y-m-d H:i:s', time());
for ($i=0;$i<count($appRankings);$i++) {
        $rank = $appRankings[$i]['rank'];
        $id = $appRankings[$i]['id'];
        $insert = q("INSERT INTO `ranktracker`.`$chart` (`app_id`, `rank`, `date`) VALUES ('$id', '$rank', '$date');");
}

//Functions
function getURL ($url) {	
	$opts = array('http' => array('ignore_errors' => true,'header' => 'Accept: application/xml'));
	$context = stream_context_create($opts);
	$data = file_get_contents($url, false, $context);
	
	return $data;
}
function toArray($xml) {
    $array = json_decode(json_encode($xml), TRUE);
    
    foreach ( array_slice($array, 0) as $key => $value ) {
        if ( empty($value) ) $array[$key] = NULL;
        elseif ( is_array($value) ) $array[$key] = toArray($value);
    }

    return $array;
}
function db_connect($db_name) {
        $db_user = '';
        $db_pass = '';
        $db_host = '';


        if (!($link = mysql_connect($db_host, $db_user, $db_pass))) {
                echo('Error in connecting to database<br/><br/>'.mysql_error());
        }
        if (!mysql_select_db($db_name, $link)) {
                echo('Error in connecting to database<br/><br/>'.mysql_error());
        }
}
function q($query) {
        global $debug;
        if($debug) {
                global $count;
                echo('c: '.$count.' >> '.$query.'<br/>');
                $count++;
        }
        //running query
        $result = mysql_query($query);
        return $result;
}



?>