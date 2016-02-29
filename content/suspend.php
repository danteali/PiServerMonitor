<h4><i class="material-icons">airline_seat_individual_suite</i> Suspend on Idle Status</h4>

<?php
  /*
   * Display info on my suspend on idle state. My computer runs a separate script every minute via a 
	 * cron jon which check whether anyone has files open or whether any active downloads are taking place. 
	 * If no files have been open for 10 min and no dl's ongoing then the NAS suspends itself. It can later 
	 * be woken using a WoL packet. All the computers in the house are set to send a WoL packet to the NAS 
	 * when they turn on. 
	 * It's quite a nice system that saves power and disk wear. I've included a copy of this python script 
	 * in this directory in case anyone wants it (suspend_on_idle.py).
   */


  $output = shell_exec('cat /shared/Webpages/ApacheDocRoot/state/content/suspend.txt');
	$table_rows = preg_split ('/$\R?^/m', $output);
?>

<table class="table table-striped table-hover">
		<?php
			foreach($table_rows as $row) {
				echo "<tr>";
					echo "<td><p>$row</p></td>";
				echo "</tr>";
			}
		?>
 </tbody>
</table>
