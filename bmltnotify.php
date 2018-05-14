<?php
include 'config.php';

date_default_timezone_set("$timezone");
$today=date("Y-m-d");
$dateminus=date('Y-m-d', strtotime("-$howmanydays days"));

// Email headers
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';
$headers[] = "To: " .$to_name. "<" .$to_email. ">";
$headers[] = "From: " .$from_name. "<" .$from_email. ">";
$subject = $email_subject;

// show beginning header 
$message = "<H2>" .$service_body_name. " CHANGES</H2>Changes for last $howmanydays day(s) from " .date("l jS \of F Y h:i A") . "<br><hr>";

$url = $bmlt_server. "/client_interface/xml/?switcher=GetChanges&start_date=" .$dateminus. "&end_date=" .$today. "&service_body_id=" .$service_body_id;

// get xml file contents
$xml = simplexml_load_file($url);

if (empty($xml)) {
  // if no changes found do nothing 
}

else {
  // loop begins
  foreach($xml->row as $row) {
   
  // begin new paragraph
  $message .= "<p>";
   
  // show Date
  $message .= "<strong>Date:</strong> ".$row->date_string." - ";
  
  // show Change Type
  $change_type=$row->change_type;
  if ($change_type == "comdef_change_type_change") {	$change_type = "Change"; }
  if ($change_type == "comdef_change_type_delete") { $change_type = "DELETE"; }
  if ($change_type == "comdef_change_type_new") {		$change_type = "New";	}
  $message .= "<strong>Change Type:</strong> ".$change_type."<br/>";
   
  // show Meeting ID
  $message .= "<strong>Meeting (ID) Name:</strong> (".$row->meeting_id.") " .$row->meeting_name."<br/>";
   
  // show User Name
  $message .= "<strong>User Name:</strong> ".$row->user_name."<br/>";
   
  // Show Service Body
  $message .= "<strong>Service Body:</strong> ".$row->service_body_name."</br><OL>";
  
  // show details
  $details=$row->details;
	 	// Remove last . at end of details
	  $details = preg_replace('/.$/',"",$details);
		// Remove the weird #@-@# from the format codes
	 $details = str_replace("#@-@#"," ",$details);
 		// protect email . from being replaced with </br> tag
	 $details = preg_replace_callback(
	 	'/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/',
			function ($email) {
				return str_replace(".","~DOT~",$email[0]);
				},
				$details
			);
	 	// Look for Latittude and Longitude, change . to ~DOT~ This must happen so we can then find the individual changes and add <li> 
	  $details = preg_replace_callback(
	 	'/from \"[0-9]+\./',
			function ($matches) {
				return str_replace(".","~DOT~",$matches[0]);
				},
				$details
			);
	  $details = preg_replace_callback(
	 	'/to \"[0-9]+\./',
			function ($matches) {
				return str_replace(".","~DOT~",$matches[0]);
				},
				$details
			);
	  $details = preg_replace_callback(
	 	'/from \"-+[0-9]+\./',
			function ($matches) {
				return str_replace(".","~DOT~",$matches[0]);
				},
				$details
			);
	  $details = preg_replace_callback(
		 '/to \"-+[0-9]+\./',
			function ($matches) {
				return str_replace(".","~DOT~",$matches[0]);
				},
				$details
			);
   
  // Change all the . to <LI>
 	$details = str_replace(".","<LI>",$details);
   
		//Change all the ~DOT~ back to .
 	$details = str_replace("~DOT~",".",$details);
  //	$details = str_replace("from \"-75</br>"," from \"-75.",$details);
  $message .= "<strong>Details:</strong><LI> ".$details."</OL>";
  $message .= "</p> <hr>";
  // end paragraph
  }
  // loop ends
  $message .= "END of BMLT Changes";
  //echo $message;  // for testing
 
  //Send Email
  mail($to, $subject, $message, implode("\r\n", $headers));
}
?>