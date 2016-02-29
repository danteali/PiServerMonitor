<h4><i class="material-icons">insert_drive_file</i> Files in Use</h4>

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


  $output1 = shell_exec('sudo smbstatus -b | col -b');
	$table_rows1 = preg_split ('/$\R?^/m', $output1);
	$table_rows1 = array_splice($table_rows1, 4);
	
?>

<h5><i class="material-icons" style="font-size:18px;">device_hub</i> Connected Devices</h5>
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


<?php	
	$output2 = shell_exec('sudo smbstatus -L | col -b');
	$table_rows2 = preg_split ('/$\R?^/m', $output2);
	$table_rows2 = array_splice($table_rows2, 4);
?>

<h5><i class="fa fa-lock" style="font-size:18px;"></i> Locked Files</h5>
<table class="table table-striped table-hover">
	<thead>
    <tr>
      <th><p>Process ID</p></th>
      <th><p>User ID</p></th>
      <th><p>Deny Mode</p></th>
			<th><p>Access</p></th>
			<th><p>Read/Write?</p></th>
			<th><p>Oplock</p></th>
			<th><p>Share Path</p></th>
			<th><p>File Name</p></th>
			<th><p>Time</p></th>
    </tr>
  </thead>
	
  <tbody>
		<?php
      foreach($table_rows2 as $row) {
        echo "<tr>";
        //$items = explode(' ', $row);
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