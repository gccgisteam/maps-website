<?php
 
// if the 'term' variable is not sent with the request, exit
if ( !isset($_REQUEST['term']) )
	exit;
// connect to the database server and select the appropriate database for use
$conn = pg_pconnect("host=localhost port=5432 dbname=GISDB user=nginx password=phptest");
if (!$conn) {
  echo "Connection Failed. \n";
  exit;
}

$query = trim($_REQUEST['term']);
$query_array = explode(' ', $query);

$house_no = "%" . $query_array[0] . "%";
$address = "%" . $query . "%";
$sql = "SELECT pid, concat(addr_line2,', ',addr_line3) as address, ST_AsGeoJson(ST_Transform(ST_Centroid(geom),4326)) as latlon FROM \"Property\" where lower(addr_line2) like lower('{$address}') and lower(house_no) like lower('{$house_no}') LIMIT 10";

$result = pg_query($conn, $sql);

$results = pg_fetch_all($result);
$json = json_encode($results);

// modify your http header to json, to help browsers to naturally handle your response with
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo $json;
 
flush();
/*
if($result !== FALSE)
{
  $row = @pg_num_rows($result);
echo $row;
  if ( $row !== 0 )
         echo "hurray: " . $row;
  else
         echo "argh";
}
else
{
  echo "Seems, your query is bad.";
}
*/
?>