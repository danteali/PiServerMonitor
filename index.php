<?php
  /**
   * index.php
   *
   *
   * @originaauthor     Colin Waddell
	 * @edittedby				Ryan McGuinness
   * @license    https://opensource.org/licenses/MIT  The MIT License (MIT)
   * @link       https://github.com/ColinWaddell/CurrantPi
   */

   /*
    * Libraries and helper function
   */
  include ('lib/string_helpers.php');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Header -->
    <?php include ('content/header.php'); ?>
  </head>

  <body>

    <div class="container">
        <!-- Banner -->
      <div class="header clearfix title-area">
        <?php include ('content/banner.php'); ?>
      </div>

      <div class="row">
        <!-- Hardware -->
        <div class="col-lg-6 widget-padding">
          <?php include ('content/hardware.php'); ?>
        </div>
        <!-- Network -->
        <div class="col-lg-6 widget-padding">
          <?php include ('content/network.php'); ?>
        </div>
      </div>

      <div class="row">
        <!-- Load Average -->
        <div class="col-lg-6 widget-padding">
          <?php include ('content/load_average.php'); ?>
        </div>
        <div class="col-lg-6 widget-padding">
          <!-- Memory -->
          <?php include ('content/memory.php'); ?>
        </div>
      </div>

      <div class="row">
        <!-- Storage -->
        <div class="col-lg-12 widget-less-padding">
          <?php include ('content/storage.php'); ?>
				</div>
      </div>

		  <?php /*
      <div class="row">
        <!-- VPN Info -->
        <div class="col-lg-12 widget-less-padding">
          <?php include ('content/vpn.php'); ?>
        </div> -->
      </div>
      */ ?>
    	
      <?php /*      
		  <div class="row">
        <!-- Suspend on idle status -->
        <div class="col-lg-12 widget-less-padding">
          <?php include ('content/suspend.php'); ?>
        </div>
      </div>
      */ ?>
          
			<div class="row">
        <!-- Files in use -->
        <div class="col-lg-12 widget-less-padding">
          <?php include ('content/files_in_use.php'); ?>
        </div>
      </div>
      						
      <!-- Footer -->
      <footer class="footer">
        <?php include ('content/footer.php'); ?>
      </footer>

    </div> <!-- /container -->

  </body>
</html>
