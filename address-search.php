<?php
include_once('src/database-config.php');
if ($_REQUEST['qtype'] == 'search') {
  $rawquery = trim($_REQUEST['q']);
  $limit = $_REQUEST['page_limit'];
  $query = $rawquery;
  while (preg_match('/  /', $rawquery)) {
	$query = preg_replace('/  /', ' ', $rawquery);
	$rawquery = $query;
  }

  $sql = buildQuery($query, false);

  $jsonObject = array();
  $result = queryPostgres($sql);
  if (pg_num_rows($result) < 1) {
	$sql = buildQuery($query, true);
	$result = queryPostgres($sql);
  }
  while ($row = pg_fetch_object($result)) {
	$address = preg_replace('/~/', ' ', $row->address);
	$jsonObject[] = array(
						  'id' => $row->pid,
						  'text' => $address
						 );
  }
  header('Content-type: application/json');
  echo json_encode($jsonObject);
} else if ($_REQUEST['qtype'] == 'retrieve') {
  $query = trim($_REQUEST['address']);
  $sql = "select
                to_char((now() - '1 day'::INTERVAL) - ((now()::date - SeedDate::date - 1)%days)*'1 day'::INTERVAL,'Day Mon FMDD, YYYY') as \"lastpickup\",
                to_char((now() - '1 day'::INTERVAL) + ((Days) * '1 day'::INTERVAL)  - ((now()::date - SeedDate::date - 1)%days)*'1 day'::INTERVAL,'Day Mon FMDD, YYYY') as \"nextpickup\",
				address,
				ST_AsGeoJSON(ST_Transform(geom, 4326), 5) AS geom,
                servicecode,
                day,
                week,
                frequency,
                containersdescription
from dbo.\"WastePickup\"
where PID = $query";
  $result = pg_query($dbConn, $sql);
  $addressObj = array();
  $servicesObj = array();
  while ($row = pg_fetch_object($result)) {
	if (!isset($addressObj['address'])) {
	  $address = preg_replace('/~/', '<br /> ', $row->address);
	  $addressObj['address'] = $address;
	  $addressObj['geom'] = $row->geom;
	}
	$servicesObj[] = array(
	  serviceType 	=> $row->servicecode,
	  container 	=> $row->containersdescription,
	  lastpickup 	=> $row->lastpickup,
	  nextpickup 	=> $row->nextpickup,
	  pickupday 	=> $row->day,
	  frequency 	=> $row->frequency,
	);
  }
  $addressObj['services'] = $servicesObj;
  header('Content-type: application/json');
  echo json_encode($addressObj);
}

function buildQuery ($query, $nohouse) {
  global $limit;
  $sql = 'SELECT address, pid from dbo."WastePickup" WHERE ';
  if (preg_match('/^[0-9]/', $query) && $nohouse != true) {
	$addressParts = explode(' ', $query);
	$houseNum = strtoupper(array_shift($addressParts));
	$percent = '%';
	$sql .= "houseno = '$houseNum' AND lower(fullstreet) LIKE lower('%". implode($percent, $addressParts) ."%')";
  } else {
	$searchStr = preg_replace('/ /', '%', $query);
	$sql .= "lower(address) LIKE lower('%$searchStr%')";
  }
  $sql .= "GROUP BY address, pid
  ORDER BY address ASC LIMIT $limit";
  return $sql;
}

function queryPostgres ($queryString) {
  global $dbConn;
  $queryResult =  pg_query($dbConn, $queryString);
  return $queryResult;
}

?>
