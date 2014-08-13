<?php
include_once('src/database-config.php');
if ($_REQUEST['qtype'] == 'search') {
  $query = trim($_REQUEST['q']);
  $limit = $_REQUEST['page_limit'];

  $sql = buildQuery($query, false);
  $jsonObject = array();
  $result = queryPostgres($sql);
  if (!$result) {
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
  $sql = "SELECT
                TO_CHAR(now() - ((1 + Days) * '1 day'::INTERVAL) - (((EXTRACT(DAY FROM AGE(SeedDate,now() - (1 * '1 Day'::INTERVAL)))::INTEGER % Days))*'1 day'::INTERVAL),'Day Month FMDD, YYYY') AS \"lastpickup\",
                TO_CHAR(now() - ((1 + 2*Days) * '1 day'::INTERVAL) - (((EXTRACT(DAY FROM AGE(SeedDate,now() - (1 * '1 Day'::INTERVAL)))::INTEGER % Days))*'1 day'::INTERVAL),'Day Month FMDD, YYYY') AS \"nextpickup\",
				address,
				ST_AsGeoJSON(ST_Transform(geom, 4326), 5) AS geom,
                servicecode,
                day,
                week,
                frequency,
                containersdescription
FROM dbo.\"WastePickup\"
WHERE PID = $query";
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
	$houseNum = array_shift($addressParts);
	$sql .= "houseno = '$houseNum' AND lower(fullstreet) LIKE lower('%". implode(' ', $addressParts) ."%')";
  } else {
	$sql .= "lower(address) LIKE lower('%$query%')";
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
