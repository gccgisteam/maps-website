<?php
include_once('src/database-config.php');
if ($_REQUEST['qtype'] == 'search') {
  $query = trim($_REQUEST['q']);
  $sql = 'SELECT uid,address from dbo."WastePickup"';

  $sql .= " WHERE lower(address) LIKE lower('%$query%')";
  $jsonObject = array();
	$result = pg_query($dbConn, $sql);
	while ($row = pg_fetch_object($result)) {
	  $jsonObject[] = array(
							'id' => $row->uid,
							'text' => $row->address
						   );
	}
	header('Content-type: application/json');
	echo json_encode($jsonObject);
} else if ($_REQUEST['qtype'] == 'retrieve') {
  $query = trim($_REQUEST['address']);
//$sql = 'SELECT
//	  CONVERT(varchar(12), getdate() - 1 + [Days] - (DATEDIFF(DAY,SeedDate,getdate() - 1) % [Days]), 107) AS "Last Pickup",
//	  CONVERT(varchar(12), getdate() - 1 + 2*[Days] - (DATEDIFF(DAY,SeedDate,getdate() - 1) % [Days]), 107) AS "Next Pickup",
//	  ServiceID AS [Service ID],
//	  ServiceCode AS [Service Code],
//	  Day,
//	  Week,
//	  Frequency,
//	  Description,
//	  ContainersDescription as [Containers Description]
//  from WastePickup';
//
//  $sql .= " WHERE uid = $query";
//
//  $sql .= ' GROUP BY
//  "ServiceID", "Description", "Week", "Day", "Days", "ServiceCode", "SeedDate", "no_bins", "ContainersDescription", "Frequency"
//  ORDER BY
//	"Description" desc';
  $sql = 'SELECT uid, frequency, day, week, seeddate, days, description, containersdescription, address, ST_AsGeoJSON(ST_Transform(geom, 4326), 5) AS geom FROM dbo."WastePickup"';
  $sql .= " WHERE uid = $query";
  $result = pg_query($dbConn, $sql);
  while ($row = pg_fetch_object($result)) {
	echo json_encode($row);
  }
}



?>
