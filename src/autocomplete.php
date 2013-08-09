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
$sql = "SELECT pid, concat(addr_line2,', ',addr_line3) as address, geom FROM \"Property\" where addr_line2 like '{$address}' and house_no like '{$house_no}' LIMIT 10";

$result = pg_query($conn, $sql);

$results = pg_fetch_all($result)
$json = json_encode($results);
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