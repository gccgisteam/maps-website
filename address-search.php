<?php
include_once('src/database-config.php');
if ($_REQUEST['qtype'] == 'search') {
  $query = trim($_REQUEST['q']);
  $limit = $_REQUEST['page_limit'];
  $offset = ($_REQUEST['page'] - 1) * $_REQUEST['page_limit'];
  $sql = 'SELECT pid,address from dbo."WastePickup"';

  $sql .= " WHERE lower(address) LIKE lower('%$query%')
  GROUP BY address, pid
  ORDER BY address ASC LIMIT $limit OFFSET $offset";

  $jsonObject = array();
	$result = pg_query($dbConn, $sql);
	while ($row = pg_fetch_object($result)) {
	  $jsonObject[] = array(
							'id' => $row->pid,
							'text' => $row->address
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
  //$sql = 'SELECT uid, frequency, day, week, seeddate, days, description, containersdescription, address, ST_AsGeoJSON(ST_Transform(geom, 4326), 5) AS geom FROM dbo."WastePickup"';
  //$sql .= " WHERE uid = $query";
  $result = pg_query($dbConn, $sql);
  $addressObj = array();
  $servicesObj = array();
  while ($row = pg_fetch_object($result)) {
	if (!isset($addressObj['address'])) {
	  $addressObj['address'] = $row->address;
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



?>
