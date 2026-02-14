<?php

require_once('phpmailer/class.phpmailer.php');
require_once('phpmailer/class.smtp.php');

$mail = new PHPMailer();

//$mail->SMTPDebug = 3; // Enable verbose debug output
$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = 'sandbox.smtp.mailtrap.io'; // Specify main and backup SMTP servers
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'ba556857363b8c'; // SMTP username
$mail->Password = '029d06b5757b90'; // SMTP password
$mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
$mail->SMTPAutoTLS = true; // Allow STARTTLS on supported ports
$mail->Port = 2525; // TCP port to connect to

$message = "";
$status = "false";

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
 if( $_POST['form_name'] != '' AND $_POST['form_email'] != '' AND $_POST['form_subject'] != '' ) {

 $name = $_POST['form_name'];
 $email = $_POST['form_email'];
 $subject = $_POST['form_subject'];
 $phone = $_POST['form_phone'];
 $address = isset($_POST['form_address']) ? $_POST['form_address'] : '';
 $property_type = isset($_POST['form_property_type']) ? $_POST['form_property_type'] : '';
 $budget = isset($_POST['form_budget']) ? $_POST['form_budget'] : '';
 $timeline = isset($_POST['form_timeline']) ? $_POST['form_timeline'] : '';
 $sqft = isset($_POST['form_sqft']) ? $_POST['form_sqft'] : '';
 $services = isset($_POST['form_services']) ? $_POST['form_services'] : array();
 $message = $_POST['form_message'];

 $subject = isset($subject) ? $subject : 'New Message | Contact Form';

 $botcheck = $_POST['form_botcheck'];

 $toemail = 'shabbirali2702@gmail.com'; // Your Email Address
 $toname = 'Shabbir Company'; // Your Name

 if( $botcheck == '' ) {

 $mail->SetFrom( 'no-reply@northside-renovations.test' , 'Northside Renovations' );
 $mail->AddReplyTo( $email , $name );
 $mail->AddAddress( $toemail , $toname );
 $mail->Subject = $subject;

 $name = isset($name) ? "Name: $name<br><br>" : '';
 $email = isset($email) ? "Email: $email<br><br>" : '';
 $phone = isset($phone) ? "Phone: $phone<br><br>" : '';
 $address = isset($address) ? "Project Address: $address<br><br>" : '';
 $property_type = isset($property_type) ? "Property Type: $property_type<br><br>" : '';
 $budget = isset($budget) ? "Budget Range: $budget<br><br>" : '';
 $timeline = isset($timeline) ? "Preferred Timeline: $timeline<br><br>" : '';
 $sqft = isset($sqft) ? "Approx. Square Footage: $sqft<br><br>" : '';
 $services_list = '';
 if (!empty($services) && is_array($services)) {
 $services_list = "Requested Services: " . implode(', ', $services) . "<br><br>";
 }
 $message = isset($message) ? "Message: $message<br><br>" : '';

 $referrer = $_SERVER['HTTP_REFERER'] ? '<br><br><br>This Form was submitted from: ' . $_SERVER['HTTP_REFERER'] : '';

 $body = "$name $email $phone $address $property_type $budget $timeline $sqft $services_list $message $referrer";

 $mail->MsgHTML( $body );
 $sendEmail = $mail->Send();

 if( $sendEmail == true ):
 $message = 'We have <strong>successfully</strong> received your Message and will get Back to you as soon as possible.';
 $status = "true";
 else:
 $message = 'Email <strong>could not</strong> be sent due to some Unexpected Error. Please Try Again later.<br /><br /><strong>Reason:</strong><br />' . $mail->ErrorInfo . '';
 $status = "false";
 endif;
 } else {
 $message = 'Bot <strong>Detected</strong>.! Clean yourself Botster.!';
 $status = "false";
 }
 } else {
 $message = 'Please <strong>Fill up</strong> all the Fields and Try Again.';
 $status = "false";
 }
} else {
 $message = 'An <strong>unexpected error</strong> occured. Please Try Again later.';
 $status = "false";
}

$status_array = array( 'message' => $message, 'status' => $status);
echo json_encode($status_array);
?>
