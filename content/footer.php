<?php
  /*
   * Footer area. Using a bunch of inline echos to find out 
   * information about this system.
   *
   * The array $_SERVER[] contains a bunch of server and execution
   * environment information.
   *
   * Reading /proc/cpuinfo to find out the type of processor this
   * is running on.
   */
?>

<p>
  <?php echo $_SERVER['SERVER_NAME']; ?> 
   - 
  <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
</p>
<p>
 <?php
    $name_full = shell_exec('cat /proc/cpuinfo | grep name | head -1');
    $name = explode (': ', $name_full);
    echo $name[1];
 ?>
</p>
<hr />
<p>
  &copy; 2016 <a href="http://colinwaddell.com/">Colin Waddell</a> under the terms of the <a href="LICENSE.txt">MIT License</a> - Customised by Ryan McGuinness
	<a href="https://github.com/ColinWaddell/CurrantPi">Source</a>
</p>
