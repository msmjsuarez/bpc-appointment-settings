<?php
if(!session_id()) {
	session_start();
}


/**
 * Plugin Name:  BPC Appointment Settings
 * Plugin URI:  
 * Description: This will control the bpc appointment settings
 * Version: 1.0
 * Author: Jesus Erwin Suarez
 * Author URI: 
 * License:   
 */

define('bpc_as_plugin_url', get_site_url() . '/wp-content/plugins/bpc-appointment-settings');
register_activation_hook(__FILE__, 'bpc_as_install_table');
add_shortcode("bpc_as_opening_hours", "bpc_as_opening_hours_func");
add_shortcode("bpc_as_calendar_google_apple", "bpc_as_calendar_google_apple_func");
add_shortcode("bpc_as_opening_hours_func_custom", "bpc_as_opening_hours_func_custom_func");
add_action("admin_menu", "bpc_as_admin_menu");

require_once("includes/helper.php");
require_once('includes/db/Bpc_As_Calendar.php');
require_once("includes/db/wpdb_queries.class.php");
require_once("includes/db/bpc_as_db.php");
require_once("includes/db/bpc_appointment_setting_breaks.php");
require_once("includes/db/bpc_appointment_setting_standard.class.php");

require_once("includes/db/bpc_user_api.php");

if(bpc_as_is_localhost()) {
	require_once("E:/xampp/htdocs/wp-load.php");
} else {
	require $_SERVER['DOCUMENT_ROOT'] .'/wp-load.php';
}

use App\Bpc_Appointment_Settings_Breaks;
use App\BPC_AS_DB;
use App\Bpc_As_Calendar;
use App\Bpc_User_Api;
use App\bpc_appointment_setting_standard;

function bpc_as_admin_menu()
{

    add_menu_page('BPC Appointment Settings', 'BPC Appointment Settings', 'manage_options', "pbc-as-admin", 'bpc_as_admin'); 
}

function bpc_as_admin () 
{  
	$posts = $_POST;  
	// print "<pre>"; 
	// 	print_r($posts);
	// print "</pre>";  
	foreach($posts as $key => $post) { 
		// print "$key , $post<br>";
		update_option( $key , $post);
	}  
 	?>  

		<!-- To update url, allow you need to visit this page https://www.ephox.com/my-account/ -->
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	    <script src="http://cloud.tinymce.com/stable/tinymce.min.js?apiKey=o2rim480e9ixjtiuyes05u9iu2930pqx4xow0tg25vta8k2t"></script>
	    <script>tinymce.init({ selector:'textarea' });</script>
	   
		<div style="width:50%; margin:0px auto;" > 
 		<br> 
 		<label class="label label-success">Reminder:</label><br> 
		1. Add short code post or page <b>[bpc_as_opening_hours]</b> in order to display the partner schedule calendar<br>
		2. Add short code post or page <b>[bpc_as_calendar_google_apple]</b> in order to display partner calendar from google or apple calendar<br>
		3. Add short code post or page <b>[bpc_as_google_calendar_settings]</b> google calendar settings<br>
		<br>	 
		   
	
	 		<form action="" name="helpNotesForm" method="POST" >
				<br><br>
				 <label class="label label-success">Call back length help notes </label>  <br> 
				<textarea name="bpc_call_back_length_standard" style="resize:none;height: 200px;width: 300px;" ><?php print get_option('bpc_call_back_length_standard'); ?></textarea>
				<br><br>
				 <label class="label label-success">Call back delay help notes</label><br> 
				<textarea name="bpc_call_back_delay_standard" style="resize:none;height: 200px;width: 300px;"  ><?php print get_option('bpc_call_back_delay_standard'); ?></textarea>
				<!-- <p>Custom Call back length help notes </p> 
				<textarea name="bpc_call_back_length_custom" style="resize:none;height: 100px;width: 300px;"  ><?php print get_option('bpc_call_back_length_custom'); ?></textarea>
				<p>Custom Call back delay help notes </p> 
				<textarea name="bpc_call_back_delay_custom" style="resize:none;height: 100px;width: 300px;"  ><?php print get_option('bpc_call_back_delay_custom'); ?></textarea> -->
				<br><br> 
				<input type="submit" value="Update" class="btn btn-default"/>
			</form>
		</div>

 	<?php
}

/**
 * Custom
 */
function bpc_as_opening_hours_func_custom_func()
{

	$bpc_User_Api 	 = new Bpc_User_Api();
	$accessToken  	 = $bpc_User_Api->getGoogleCalendarAccessToken();
 
	/**
	 * generate standard settings for specific user
	 */
	$standard = new bpc_appointment_setting_standard();
	$standard->generateSpecificUserWithDefaultStandarSettings();


	ob_start();

	bpc_as_calendar_google_apple_authenticate();

	print "<input type='hidden' value='". get_site_url() ."' id='bpc_as_rool_url' />";

	print "<div onload='bpc_init()'>";

	$book_exact_time = 'checked';

	$book_exact_day = '';

	bpc_as_header();

	echo "<form method='POST' id='testform' >";

	require_once('includes/pages/date-picker.php');

	print "<div id='bpc-as-schedule-settings-content-and-type'>";

	print "</div>";

	echo "</form>";

	require_once('includes/pages/dashboard-settings-options-save.php'); 

	if (bpc_as_calendar_googl_apple_function_is_authenticated()  == true) {
				//print "token is authenticated"; 
			print "</div>";
		print "</div>";
	} else {
		//print "token is not authenticated";
	} 
	print "</div>"; 
	ob_flush();
}

/**
 * Standard
 */
function bpc_as_opening_hours_func() 
{
	error_reporting(0);
	ob_start();


	/**
	 * generate standard settings for specific user
	 */
	$standard = new bpc_appointment_setting_standard();
	$standard->generateSpecificUserWithDefaultStandarSettings();





	print "<input type='hidden' id='bpc_kind_of_page' value='standard' />";
    // 	bpc_as_calendar_google_apple_authenticate();
	print "<input type='hidden' value='". get_site_url() ."' id='bpc_as_rool_url' />";
	print "<div onload='bpc_init()'>";  

		//		print '<div id="standard-settings-loader" >';
		//		print '<i class=" fa fa-spinner fa-spin"    ></i>';
		//		print '<p>Please wait..</p>';
		//		print '</div>';

		$book_exact_time = 'checked';
		$book_exact_day = ''; 
			bpc_as_header();
			echo "<form method='POST' id='testform' >";
				print "<div>";
				 require_once('includes/pages/standard/standard-date-picker.php');
				print "</div>";
				print "<div id='bpc-as-schedule-settings-content-and-type'>";
				print "</div>";
			echo "</form>"; 
		require_once('includes/ajax/standard/pages/standard-dashboard-settings-options-save.php');
	print "</div>"; 
 	ob_flush();
}

function bpc_as_install_table()
{
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'bpc_appointment_settings';
	
	$charset_collate = $wpdb->get_charset_collate();

   $sql1 = "CREATE TABLE $table_name   (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        partner_id bigint(20) NOT NULL,  	
        open_from varchar(50) NOT NULL,  
        open_to varchar(50) NOT NULL,  
		call_back_length varchar(50) NOT NULL,
		call_back_delay varchar(50) NOT NULL, 
		morning varchar(50) NOT NULL,
		afternoon varchar(50) NOT NULL,
		evening varchar(50) NOT NULL,
		close varchar(50) NOT NULL,
		book_time_type varchar(50) NOT NULL,
		day varchar(50) NOT NULL,
		date varchar(50) NOT NULL,
		updated_at datetime NOT NULL,
        created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

	$table_name = $wpdb->prefix . 'bpc_appointment_setting_breaks';
	$sql2 = "CREATE TABLE $table_name   (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        appointment_setting_id bigint(20) NOT NULL,
        break_from varchar(50) NOT NULL,
      	break_to varchar(50) NOT NULL,
        created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
   
	/**
	 * example input
	 * [access_token] => ya29.GlzZA_oQrsou7xDzCqQuslKTTdE9qqaXvrH1QnLrMEhaWmwoEBSpragLQCnPXqR7uyp1WE_3ScK5lUlGL6skuPTVfjFdtYYtI58aOqQJg5HE4j3y7eqVbpkT58YDtQ
	 * [token_type] => Bearer
	 * [expires_in] => 3598
	 * [created] => 1484899159
	 */

	$table_name = $wpdb->prefix . 'bpc_user_api';
	$sql3 = "CREATE TABLE $table_name   (
        id int(11) NOT NULL AUTO_INCREMENT,
        user_id int(11) NOT NULL,
        name varchar(50) NOT NULL,
        access_token varchar(500) NOT NULL,
      	token_type varchar(50) NOT NULL,
        expires_in varchar(50) NOT NULL,
        created varchar(50) NOT NULL,
        created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";



	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql1 );
	dbDelta( $sql2 );
	dbDelta( $sql3 );


	add_option( 'jal_db_version', $jal_db_version );
	//when install also add talble
}


function bpc_as_calendar_googl_apple_function_is_authenticated() {
	    //	unset($_SESSION['access_token']); 
	ob_start();   
	// print " type " . $_SESSION['type'];  


	/**
	 * generate standard settings for specific user
	 */
	$standard = new bpc_appointment_setting_standard();
	$standard->generateSpecificUserWithDefaultStandarSettings();


	//	print "type " . $_SESSION['type']	;
	if($_SESSION['type'] == 'auth') {
	?>
		<style>
			.authenticate-google-api, body{
				display:none !important;
			}
		</style> <?php 
		print "Connecting...";
	} 
	unset($_SESSION['type']); 
	?>
 
	<style>
		#page-content {
			width:1024px !important;
		}
	</style>
	<?php
	bpc_as_header();
	$bpc_AS_DB					      = new BPC_AS_DB('wp_bpc_appointment_settings');
	$bpc_Appointment_Settings_Breaks  = new Bpc_Appointment_Settings_Breaks();
	require_once __DIR__.'/includes/api/google-api/vendor/autoload.php';
	require_once __DIR__.'/includes/api/google-api/helper.php';

			// print "<pre>";
			// print_r($_SESSION);
			// print "</pre>";
	// google calendar connect

	$bpc_User_Api		 			  = new Bpc_User_Api();
 
	// execute new insert for google authentication
	if(!empty($_SESSION['access_token'])) {
				// print "session is not emopty";
		$_SESSION['access_token']['name'] = 'google calendar';
		$bpc_User_Api->addOrUpdate($_SESSION['access_token']);
	} else {
				// print "sesssion is empty";
	} 
	$client = new Google_Client();
	$client->setAuthConfig( __DIR__ . '/includes/api/google-api/client_secret.json');
	$client->addScope(Google_Service_Calendar::CALENDAR);
	$bpc_As_Calendar = new Bpc_As_Calendar();
 
	$accessToken  = $bpc_User_Api->getGoogleCalendarAccessToken();
 
 
	if (!empty($accessToken)) {  
		try { 
			$_SESSION['token_authenticated'] = true;
			$client->setAccessToken($accessToken);
			$service = new Google_Service_Calendar($client); 
			$calendarId = 'primary';
			$optParams = array(
					'maxResults' => 100,
					'orderBy' => 'startTime',
					'singleEvents' => TRUE,
					'timeMin' => date("c", strtotime($bpc_As_Calendar->getCurrentDate()))
					// 'timeMax' => '2017-03-28T23:59:59-04:00'
			); 
			$results = $service->events->listEvents($calendarId, $optParams); 
			if (count($results->getItems()) == 0) { 
			} else { 
			}   


			return true;
		}catch (Exception $e){ 
			return false; 
		} 
	} else {
	 	return false; 
	}  
}

function bpc_as_calendar_google_apple_func()
{
    //	unset($_SESSION['access_token']); 
	ob_start();   
	// print " type " . $_SESSION['type'];  


	/**
	 * generate standard settings for specific user
	 */
	$standard = new bpc_appointment_setting_standard();
	$standard->generateSpecificUserWithDefaultStandarSettings();


	//	print "type " . $_SESSION['type']	;
	if($_SESSION['type'] == 'auth') {
	?>
		<style>
			.authenticate-google-api, body{
				display:none !important;
			}
		</style> <?php 
		print "Connecting...";
	} 
	unset($_SESSION['type']); 
	?>
 
	<style>
		#page-content {
			width:1024px !important;
		}
	</style>
	<?php
	bpc_as_header();
	$bpc_AS_DB					      = new BPC_AS_DB('wp_bpc_appointment_settings');
	$bpc_Appointment_Settings_Breaks  = new Bpc_Appointment_Settings_Breaks();
	require_once __DIR__.'/includes/api/google-api/vendor/autoload.php';
	require_once __DIR__.'/includes/api/google-api/helper.php';

			// print "<pre>";
			// print_r($_SESSION);
			// print "</pre>";
	// google calendar connect

	$bpc_User_Api		 			  = new Bpc_User_Api();
 
	// execute new insert for google authentication
	if(!empty($_SESSION['access_token'])) {
				// print "session is not emopty";
		$_SESSION['access_token']['name'] = 'google calendar';
		$bpc_User_Api->addOrUpdate($_SESSION['access_token']);
	} else {
				// print "sesssion is empty";
	}


	$client = new Google_Client();
	$client->setAuthConfig( __DIR__ . '/includes/api/google-api/client_secret.json');
	$client->addScope(Google_Service_Calendar::CALENDAR);
	$bpc_As_Calendar = new Bpc_As_Calendar();

	// execute insert to wp_bpc_user_api
	$accessToken  = $bpc_User_Api->getGoogleCalendarAccessToken();
		// print "token $accessToken";
	// set if not empty, meaning its already authenticated



	print "<div style='width:90%' class='authenticate-google-api'>"; 
	if (!empty($accessToken)) { 
		print "<div style='width: 96%;' class='alert alert-success'>Authenticated with google calendar..</div>";
		// allow try ang catch functions
		try {

			$_SESSION['token_authenticated'] = true;
			$client->setAccessToken($accessToken);
			$service = new Google_Service_Calendar($client);
			// Print "the next 10 events on the user's calendar"; 
			$calendarId = 'primary';
			$optParams = array(
					'maxResults' => 100,
					'orderBy' => 'startTime',
					'singleEvents' => TRUE,
					'timeMin' => date("c", strtotime($bpc_As_Calendar->getCurrentDate()))
					// 'timeMax' => '2017-03-28T23:59:59-04:00'
			);

			$results = $service->events->listEvents($calendarId, $optParams);

			// print "<br>results:"; 
			// bpc_as_print_r_pre($results ); 

			if (count($results->getItems()) == 0) {
				print "<div style='width: 96%;' class='alert alert-danger'> No upcoming events found. </div>";
			} else {
				print "<div style='width: 96%;' class='alert alert-info'> Change your schedule in <a href='/custom-opening-hours'> custom opening hours </a> </div>";
				$googleSchedule = [];
				foreach ($results->getItems() as $index => $event) {
					$bpc_As_Calendar->setEventResult([
							'event'=>$event,
							'summary'=>$event->getSummary(),
					]);
					// print "<br>";
					// print $index  . ' .)  ' . " date " . $bpc_As_Calendar->getEventDate() . ' time from ' . $bpc_As_Calendar->getEventTimeStart() . ' time to ' . $bpc_As_Calendar->getEventTimeEnd();
					$googleSchedule[$bpc_As_Calendar->getEventDate()][] = ["break_from"=>$bpc_As_Calendar->getEventTimeStart(),"break_to"=>$bpc_As_Calendar->getEventTimeEnd(), 'description'=>$bpc_As_Calendar->getDescription()];
				}

				// bpc_as_print_r_pre($googleSchedule);
				$date = '';
				$break_from = '';
				$break_to = '';
				$counter = 0;

		print '
			<table id="example" class="display" cellspacing="0" width="100%">
		        <thead>
		            <tr>
		                <th>Event Date</th>
		                <th>Event Times</th>
		                <th>Event Name</th> 
		            </tr>
		        </thead>
		        <tfoot>
		         <tr>
		            <th>Event Date</th>
		            <th>Event Times</th>
		            <th>Event Name</th> 
		        </tr>
		        </tfoot>
		        <tbody>
		        	'; 
					foreach($googleSchedule as $date => $breaks) {
						if(!empty($date)) {
							$appointment_setting_id = $bpc_AS_DB->InsertGetOrGetPhoneCallSettings($date)[0]['id'];
							// print "<br> appointment id $appointment_setting_id";
							 $bpc_Appointment_Settings_Breaks->deleteAllAppointmentSettingBreakByAppointmentId($appointment_setting_id);
							foreach ($breaks as $break) {
								$counter ++;
								$break_from = $break['break_from'];
								$break_to   = $break['break_to'];
								$description   = $break['description'];
								if(!empty($break_from) and !empty($break_to)) { 
									print '<tr>'; 
									print "<td>" . bpc_as_set_date_as_uk_format($date) . "</td>";
									print "<td>$break_from - $break_to</td>";
									print "<td>$description</td>";  
									print "</tr>";     
									$bpc_Appointment_Settings_Breaks->addNewAppointmentBreakIndividual($appointment_setting_id, $break_from, $break_to);
								}
							}
						}
					}
				// print '<div class="list-group">';
				        print ' 
			        </tbody>
			    </table> '; 

			}  
			// print disconnect button
			bpc_as_google_calendar_print_disconnect_button();

			unset($_SESSION['access_token']);

		}catch (Exception $e){
			// print "auto load google calendar!";
			bpc_as_google_calendar_auto_connect_with_popup(bpc_as_google_calendar_get_path_call_back_file());
			bpc_as_google_calendar_print_connect_button(bpc_as_google_calendar_get_path_call_back_file());
		}

	} else {
		$_SESSION['token_authenticated'] = false;
		//		print " print button to authenticate in to google calendar";
		print "<div style='width: 96%;' class='alert alert-info'> Click <a href='/phone-appointment-settings'>here</a> to visit phone appointment settings  </div>";
		bpc_as_google_calendar_print_connect_button(bpc_as_google_calendar_get_path_call_back_file());
	} 
	print "</div>"; 


	ob_flush();
}

function bpc_as_calendar_google_apple_authenticate()
{



	ob_start();

	bpc_as_header();

	?>
	<style>
		#page-content {
			width:1024px !important;
		}
	</style>


	<?php

	$bpc_AS_DB = new BPC_AS_DB('wp_bpc_appointment_settings');
	$bpc_Appointment_Settings_Breaks  = new Bpc_Appointment_Settings_Breaks();

	require_once __DIR__.'/includes/api/google-api/vendor/autoload.php';
	require_once __DIR__.'/includes/api/google-api/helper.php';

	$client = new Google_Client();
	$client->setAuthConfig( __DIR__ . '/includes/api/google-api/client_secret.json');
	$client->addScope(Google_Service_Calendar::CALENDAR);

	$bpc_As_Calendar = new Bpc_As_Calendar();
	$bpc_User_Api 	 = new Bpc_User_Api();
	$accessToken  	 = $bpc_User_Api->getGoogleCalendarAccessToken();

	if (!empty($accessToken)) {

		print "<div style='width: 96%;' class='alert alert-info'>Synced with google calendar.. click <a href='/google-calendar-settings'>here</a> to visit google calendar settings </div>";

		try {

			$_SESSION['token_authenticated'] = true; 

			/** Set access token */
			$client->setAccessToken($accessToken);

			/** Set google celendar service instance */
			$service = new Google_Service_Calendar($client);

			/** Print the next 10 events on the user's calendar. */ 
			$calendarId = 'primary'; 

			/** Set parameter for calendar query */
			$optParams = array(
				'maxResults' => 100,
				'orderBy' => 'startTime',
				'singleEvents' => TRUE,
				'timeMin' => date("c", strtotime($bpc_As_Calendar->getCurrentDate()))
			);

			/** Query calendar ang get results */
			$results = $service->events->listEvents($calendarId, $optParams);
 			
			/** If result is zero then do nothing */
			if (count($results->getItems()) == 0) {
			} 

			/** Else result is greater than zero then execute results do filter and display */
			else {

				$googleSchedule = [];

				foreach ($results->getItems() as $index => $event) {

					$bpc_As_Calendar->setEventResult([
							'event'=>$event,
							'summary'=>$event->getSummary(),
					]);

					$googleSchedule[$bpc_As_Calendar->getEventDate()][] = ["break_from"=>$bpc_As_Calendar->getEventTimeStart(),"break_to"=>$bpc_As_Calendar->getEventTimeEnd(), 'description'=>$bpc_As_Calendar->getDescription()];

				}

				$date = '';
				$break_from = '';
				$break_to = '';
				$counter = 0;
				
				print '<div style="width:102%" >';
					foreach($googleSchedule as $date => $breaks) {
						if(!empty($date)) {
							$appointment_setting_id = $bpc_AS_DB->InsertGetOrGetPhoneCallSettings($date)[0]['id'];
							 $bpc_Appointment_Settings_Breaks->deleteAllAppointmentSettingBreakByAppointmentId($appointment_setting_id);
							foreach ($breaks as $break) {
								$counter ++;
								$break_from 	= $break['break_from'];
								$break_to   	= $break['break_to'];
								$description   	= $break['description'];
								if(!empty($break_from) and !empty($break_to)) {
									//									print '<button  style="width:94%" type="button" class="list-group-item"> '. $counter .'
									// 									 	date ' . $date . ' break from ' . $break_from . '  ' . $break_to . ' - <b> ' . $description . '</b>' . ' - <span style=\'color:green\'>Break successfully added</span>
									// 									</button>';
									$bpc_Appointment_Settings_Breaks->addNewAppointmentBreakIndividual($appointment_setting_id, $break_from, $break_to);
								}
							}
						}
					}
				print '<div>';
			}
		}catch (Exception $e){
			// bpc_as_google_calendar_auto_connect_with_popup(bpc_as_google_calendar_get_path_call_back_file());
			// bpc_as_google_calendar_print_connect_button(bpc_as_google_calendar_get_path_call_back_file()); 
		}
	} else {

		$_SESSION['token_authenticated'] = false;

		print "<div style='width: 96%;' class='alert alert-info'> Click <a href='/google-calendar-settings'>here</a> to visit google calendar settings </div>";
 
	}
	ob_flush();
}

function bpc_as_google_calendar_settings_func()
{
	?>
		<h1>This is the google calendar settings</h1>
	<?php
}

function bpc_as_header()
{	?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="<?php print bpc_as_plugin_url; ?>/assets/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php print bpc_as_plugin_url; ?>/assets/css/bootstrap-select.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php print bpc_as_plugin_url; ?>/assets/css/font-awesome.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php print bpc_as_plugin_url; ?>/assets/css/bootstrap-theme.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php print bpc_as_plugin_url; ?>/assets/css/style.css" />

		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

		<script src="<?php print bpc_as_plugin_url; ?>/assets/js/jquery-3.1.1.min.js"></script>
		<script src="<?php print bpc_as_plugin_url; ?>/assets/js/angular-1.6.1.js"></script>
		<script src="<?php print bpc_as_plugin_url; ?>/assets/js/bootstrap.min.js"></script>
		<script src="<?php print bpc_as_plugin_url; ?>/assets/js/bootstrap-select.min.js"></script>

		<script src="<?php print bpc_as_plugin_url; ?>/assets/js/my_js.js"></script>
		<script src="<?php print bpc_as_plugin_url; ?>/assets/js/my_jquery.js"></script>
		<script src="<?php print bpc_as_plugin_url; ?>/assets/js/my_angular.js"></script> 

			
		<!-- <script src="//code.jquery.com/jquery-1.12.4.js" > </script> -->
		<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js" ></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
	 
		<script>
		   	$.noConflict();
		  	jQuery( document ).ready(function( $ ) {
				  $('#example').DataTable(); 
			}); 
		</script>
	<?php
}


/**
 * sample query
 *
 * $content = file_get_contents('https://testing.umbrellasupport.co.uk/wp-json/bpc/api/v1/partner/77514');
 * $content = json_decode($content, true);
 * print "<pre>";
 * print_r($content);
 *
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'bpc/api/v1', '/partner/(?P<partner_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'my_bpc_api_func',
    ) );
} );

function my_bpc_api_func( $data ) {
    $partner_id = $data['partner_id']; 
    // print "test";

    global $wpdb;
    $settings = $wpdb->get_results( 'SELECT * FROM wp_bpc_appointment_settings WHERE partner_id = ' . $partner_id, ARRAY_A  );


    $content = [];

    foreach($settings as $setting) {

        $appointment_setting_id = $setting['id'];
        $date = $setting['date'];

        $breaks = $wpdb->get_results( 'SELECT * FROM wp_bpc_appointment_setting_breaks WHERE partner_id = ' . $appointment_setting_id, ARRAY_A  );

        $content[$date]['bpc_appointment_settings'] = $setting;
        $content[$date]['bpc_appointment_setting_breaks'] = $breaks;

    }

    $content['standard'] = $wpdb->get_results( 'SELECT * FROM wp_bpc_appointment_setting_standard WHERE partner_id = ' . $partner_id, ARRAY_A  );

    return $content;

}
