<h4><i class="fa fa-exchange"></i> Connections</h4>

<?php
  /*
   * Display info on connections to the NAS 
   */


  $output1 = shell_exec('sudo smbstatus -b | col -b');
	$table_rows1 = preg_split ('/$\R?^/m', $output1);
	$table_rows1 = array_splice($table_rows1, 4);
	

?>

<h5><i class="material-icons" style="font-size:18px;">call_merge</i> NAS Connections</h5>
<table class="table table-striped table-hover">
	<thead>
    <tr>
      <th><p>Process ID</p></th>
      <th><p>User/Group</p></th>
      <th><p>Machine</p></th>
			<th><p>IP:port</p></th>
    </tr>
  </thead>
	
  <tbody>
		<?php
      foreach($table_rows1 as $row) {
        echo "<tr>";
        $items = explode(' ', $row);
        $col_count = 0;
        foreach($items as $item){
          if ($item!==""){
            switch ($col_count) {
              // Everything, left aligned
              default:
                  echo "<td><p>$item</p></td>";
            }
            $col_count++;
          }
        }
        echo "</tr>";
      }
    ?>  
 </tbody>
</table>



<h5><i class="material-icons" style="font-size:18px;">devices</i> Active Network Devices</h5>
<p><?php echo $output2 ?></p>
<p><?php
passthru ('sudo -u www-data /usr/bin/arp-scan --interface=eth0 --localnet');
?></p>
<table class="table table-striped table-hover">
	<thead>
    <tr>
      <th><p>LAN IP</p></th>
      <th><p>MAC Address</p></th>
      <th><p>Info</p></th>
    </tr>
  </thead>
	
  <tbody>
		<?php
      foreach($table_rows2 as $row) {
        echo "<tr>";
        $items = preg_split('/[\s]+/', $row);
        $col_count = 0;
        foreach($items as $item){
          if ($item!==""){
            switch ($col_count) {
              // Everything, left aligned
              default:
                  echo "<td><p>$item</p></td>";
            }
            $col_count++;
          }
        }
        echo "</tr>";
      }
    ?>  
 </tbody>
</table>