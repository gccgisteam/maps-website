<?php
include_once('src/database-config.php');
if ($_REQUEST['qtype'] == 'search') {
  $query = trim($_REQUEST['q']);

  $sql = 'SELECT
	  uid, address
  from dbo."WastePickup"';

  $sql .= " WHERE address LIKE '%$query%'";

  $jsonObject = array();
	$result = pg_query($dbConn, $query);
	while ($row = pg_fetch_assoc($result)) {
	  $jsonObject[] = $row;
	}
	header('Content-type: application/json');
	echo json_encode($jsonObject);
} else if ($_REQUEST['qtype'] == 'retrieve') {
  $query = trim($_REQUEST['address']);
$sql = 'SELECT
	  CONVERT(varchar(12), getdate() - 1 + [Days] - (DATEDIFF(DAY,SeedDate,getdate() - 1) % [Days]), 107) AS "Last Pickup",
	  CONVERT(varchar(12), getdate() - 1 + 2*[Days] - (DATEDIFF(DAY,SeedDate,getdate() - 1) % [Days]), 107) AS "Next Pickup",
	  ServiceID AS [Service ID],
	  ServiceCode AS [Service Code],
	  Day,
	  Week,
	  Frequency,
	  Description,
	  ContainersDescription as [Containers Description]
  from WastePickup';

  $sql .= " WHERE address LIKE '%$query%'";

  $sql .= ' GROUP BY
  "ServiceID", "Description", "Week", "Day", "Days", "ServiceCode", "SeedDate", "no_bins", "ContainersDescription", "Frequency"
  ORDER BY
	"Description" desc';
}



?>
