<?php
 
// if the 'term' variable is not sent with the request, exit
//	exit;
// 
echo 'ran';
// connect to the database server and select the appropriate database for use
$conn = pg_pconnect("host=localhost port=5432 dbname=GISDB user=nginx password=phptest");
if (!$conn) {
  echo "Connection Failed. \n";
  exit;
}

$query = trim('12 main ');
$query_array = explode(' ', $query);
 
// loop through each zipcode returned and format the response for jQuery
try {	
	$house_no = "%" . $query_array[0] . "%";
	$address = "%" . $query . "%";
	$params = array($house_no, $address);
	echo $params;
	$result = pg_query_params($conn,'SELECT pid, concat(addr_line2,\', \',addr_line3) as address, geom FROM "Property" where addr_line2 like $1 and house_no like $2 LIMIT 10',array($house_no,$address));
	//$result = pg_query($conn, 'SELECT pid, concat(addr_line2,\', \',addr_line3) as address, geom FROM "Property" where addr_line2 like \'%12 Main%\' and house_no like \'%12%\' LIMIT 10');

	$results = pg_fetch_all($result)
	$json = json_encode($results);
	echo $json
}
 
// jQuery wants JSON data
echo json_encode($data);
flush();