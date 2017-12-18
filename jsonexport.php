<?php

error_reporting(E_ALL);

/*
  13    1499283935043   5789.65         -       WP HT Zählerstand
 14     1499283935794   3531.334        -       WP NT Zählerstand
 15     1499283934021   0.008           -       WP Verbrauch
 10     1499283937037   16772.885       -       Haus Zählerstand
 12     1499283935950   0.615           -       Haus Verbrauch
 */

$assoc_channels = array(
    10 => 'haus_zaehlerstand',
    12 => 'haus_verbrauch',
    13 => 'wp_ht_zaehlerstand',
    14 => 'wp_nt_zaehlerstand',
    15 => 'wp_verbrauch',
);



$mysqli = new mysqli('localhost', 'root', 'raspberry', 'volkszaehler');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}


$aktuelle_stunde = date("h");
//echo($aktuelle_stunde);

if($aktuelle_stunde == 10)
{
$relevant_date = time()-(60*60*24);


$relevant_date .= "000";

$sql = "INSERT INTO data_archive SELECT * FROM data WHERE timestamp < '".$relevant_date."'";
$result = $mysqli->query($sql);

$sql = "DELETE FROM data WHERE timestamp < '".$relevant_date."'";
$result = $mysqli->query($sql);
}





$return_array = array();


$sql = "SELECT tt.*
FROM data tt
INNER JOIN
(SELECT channel_id, MAX(id) AS id
    FROM data
    GROUP BY channel_id) groupedtt
ON tt.channel_id = groupedtt.channel_id
AND tt.id = groupedtt.id";

$result = $mysqli->query($sql);
if($result){
    while ($row = $result->fetch_object()){

       $channel_id = $row->channel_id;
       $value = $row->value;

       $return_array[$assoc_channels[$channel_id]] = floatval($value);
 }

    #var_dump($return_array);

    // Free result set
    $result->close();
    $mysqli->next_result();
}
else {
    echo "Fehler";
}


header('Content-Type: application/json');
?>