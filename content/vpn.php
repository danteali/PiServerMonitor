<h4><i class="material-icons" style="color:red">vpn_key</i> VPN Status</h4>

<?php
  /*
   * Display info on my VPN status. I use PrivateInternetAccess as a VPN provider with a few cron jobs 
	 * which connect to the VPN at various times for various reasons. It's good to be able to glance at 
	 * the 'status' page and see if the NAS is currently connected.
	 * The 'TorrServicesCheckVPN.sh' script which lives elsewhere on my server runs this command:
	 * `curl -sS ipinfo.io | grep -e ip -e country`. Then tidies up the output and writes it to the file
	 * '/shared/Webpages/ApacheDocRoot/state/content/vpn.txt'
	 * I could implement this in php but its easier for me this way as I already had this script written 
	 * and I'm not too good with php yet so don't know how to do the string manipulation which I can do in 
	 * bash. 
	 * For info, the complete content of the check VPN bash script is at the bottom of this file.
   */


  //$output = shell_exec('/home/ryan/scripts/Torrenting/TorrServicesCheckVPN.sh');
  $vpn_state = shell_exec('sed \'1q;d\' /shared/Webpages/ApacheDocRoot/state/content/vpn.txt');
	$ip_addr = shell_exec('sed \'2q;d\' /shared/Webpages/ApacheDocRoot/state/content/vpn.txt');
	$vpn_country = shell_exec('sed \'3q;d\' /shared/Webpages/ApacheDocRoot/state/content/vpn.txt');
	$last_update = shell_exec('sed \'4q;d\' /shared/Webpages/ApacheDocRoot/state/content/vpn.txt');
?>

<table class="table table-striped table-hover">
  <tbody>
    <tr>
		  <td><p><?php echo $vpn_state ?></p></td>
			<td></td>
		</tr>
		<tr>
			<td><p>External IP Address: <?php echo $ip_addr ?></p></td>
			<td><p>Country: <?php echo $vpn_country ?></p></td>
		</tr>
		<tr>
		<td><p>Last Updated: <?php echo $last_update ?></p></td>
		<td></td>
		</tr>      
 </tbody>
</table>

<?php
  /*
   * #!/bin/sh
   * ipaddr=`curl -sS ipinfo.io | grep -e ip -e country`
   * isVPN=`echo "$ipaddr" | grep -e RO -e A1 -e NL`
   * cleaned_country=`echo "$ipaddr" | grep country | awk '{ print $2 }' | sed -e 's/\"//g' -e 's/\,//g'`
   * cleaned_IP=`echo "$ipaddr" | grep ip | awk '{ print $2 }' | sed -e 's/\"//g' -e 's/\,//g'`
   * 
   * # Write results to NAS status IP info text file for fancy status display page
   * IPINFOWEB="/raid/shared/Webpages/ApacheDocRoot/state/content/vpn.txt"
   * if [ "$isVPN" = "" ]; then
   * echo "NOT Connected" >> $IPINFOWEB
   * else
   *   echo "Connected" >> $IPINFOWEB
   * fi
   *   echo $cleaned_IP >> $IPINFOWEB
   *   echo $cleaned_country >> $IPINFOWEB
   *   echo `date` >> $IPINFOWEB
   */
?>