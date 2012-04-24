<?php
//NOTE: $db_user, $db_pass, $db_host variables in the db_connect function at the bottom of this file

$databaseName = ''; //the name of your database goes here.
$chart = ''; // This is the name of the MySQL table you want the data to be pulled from.
$start = '2012-04-24 00:00:00'; //Start date. Keep this format
$end = '2012-04-25 15:00:03'; //End date. Keep this format

$rank = array();

db_connect($databaseName);
$result = q("SELECT `id`, `app_name` FROM  `apps` WHERE `chart` = '$chart';");
$count = 0;
$apps = array();
while($row = mysql_fetch_row($result)) {
	$apps[$count]['id'] = $row[0];
	$apps[$count]['name'] = $row[1];
	$count++;
}

$topCount = 0;
for ($i=0;$i<count($apps);$i++) {
	$appID = $apps[$i]['id'];
	$result = q("SELECT `rank` FROM  `$chart` WHERE `date` > '$start' AND `date` < '$end' AND `app_id` = '$appID';");
	$sum = 0;
	$count = 0;
	while($row = mysql_fetch_row($result)) {
		$sum = $sum+$row[0];
		$count++;
	}
	if ($count > $topCount) {
		$topCount = $count;
	}
	$apps[$i]['sum'] = $sum;
	$apps[$i]['count'] = $count;
}

for ($i=0;$i<count($apps);$i++) {
	if ($apps[$i]['count'] < $topCount) {
		$apps[$i]['sum'] = $apps[$i]['sum']+(($topCount-$apps[$i]['count'])*100);
	}

	$apps[$i]['avg_rank'] = ($apps[$i]['sum']/$topCount);
}


echo '<pre>';
usort($apps, "my_sort");
print_r($apps);


//functions
function my_sort($a, $b) {
	if ($a['avg_rank'] == $b['avg_rank']) return 0;
	return ($a['avg_rank'] < $b['avg_rank']) ? -1 : 1;
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