<h4><i class="demo-icon icon-server"></i> Hardware</h4>

<?php
  /*
   * Using the onboard temperature sensor and the command 'uptime'
   * to pull in information about how hot the Raspberry Pi is and
   * how long it's been switched on for.
   */
  //$output = shell_exec('cat /sys/class/thermal/thermal_zone0/temp');
  //$temp = round(($output)/1000, 1);
	//$temp = shell_exec('sensors | grep temp1 | head -n 1 | awk '{print $2}'');
	$temp= shell_exec('sensors | grep temp1 | head -n 1 | awk \'{print $2}\'');

  $output = shell_exec('echo "$(</proc/uptime awk \'{print $1}\')"');
  $time_alive = seconds_to_time(intval($output));
?>

<table class="table table-striped table-hover">
  <tbody>
  <tr>
    <td><p>Uptime: </p></td>
    <td><p class="text-right"><?php echo "$time_alive";?></p></td>
  </tr>
  <tr>
    <td><p>Board Temperature: </p></td>
    <td><p class="text-right"><?php echo "$temp&deg;C";?></p></td>
  </tr>
 </tbody>
</table>
