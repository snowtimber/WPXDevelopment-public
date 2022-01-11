<?php
if (isset($_GET["debug"])) {
						echo "<table><tr>";
						echo "<th>Pad Name</th>";
						echo "<th>FirstOfWellname</th>";
						echo "<th>RIG</th>";
						echo "<th>WELLS_PER_VISIT</th>";
						echo "<th>RIG_ON</th>";
						echo "<th>RIG_OFF</th>";
						echo "<th>APD_MIN_STATUS</th>";
						echo "<th>APD_MAX_STATUS</th>";
						echo "<th>FED_MIN_STATUS</th>";
						echo "<th>FED_MAX_STATUS</th>";
						}
						$rows = 0;
						
						while (odbc_fetch_row($rs)) {
							$PAD_NAME=odbc_result($rs,"PAD_NAME");
							$FirstOfWellname=odbc_result($rs,"FirstOfWellname");
							$RIG=odbc_result($rs,"RIG");
							$RIG_ON=odbc_result($rs,"RIG_ON");
							$RIG_OFF=odbc_result($rs,"RIG_OFF");
							$WELLS_PER_VISIT=odbc_result($rs,"WELLS_PER_VISIT");
							$APD_MIN_STATUS=odbc_result($rs,"APD_MIN_STATUS");
							$APD_MAX_STATUS=odbc_result($rs,"APD_MAX_STATUS");
							$STATE_MIN_EXP=substr(odbc_result($rs,"STATE_MIN_EXP"), 0 , 10);
							$FED_MIN_STATUS=odbc_result($rs,"FED_MIN_STATUS");
							$FED_MAX_STATUS=odbc_result($rs,"FED_MAX_STATUS");
							$FED_MIN_EXP=substr(odbc_result($rs,"FED_MIN_EXP"), 0 , 10);
							$LAND=odbc_result($rs,"LAND");  
							$PAD_FIELD_NAME=odbc_result($rs,"PAD_FIELD_NAME");  
							$rows = $rows +1;
							if($rows == 0) {
								echo "No Matches found for this query";
							}
						}
						
						if (isset($_GET["debug"])) {
						  echo "<tr><td>--->$PAD_NAME</td>";
						  echo "<td>$FirstOfWellname</td>";
						  echo "<td>$RIG</td>";
						  echo "<td>$WELLS_PER_VISIT</td>";
						  echo "<td>$RIG_ON</td>";
						  echo "<td>$RIG_OFF</td>";
						  echo "<td>$APD_MIN_STATUS</td>";
						  echo "<td>$APD_MAX_STATUS</td>";
						  echo "<td>$FED_MIN_STATUS</td>";
						  echo "<td>$FED_MAX_STATUS</td></tr>";
						}
						
						if (isset($_GET["debug"])) {
							echo "</table>";
						}
?>
