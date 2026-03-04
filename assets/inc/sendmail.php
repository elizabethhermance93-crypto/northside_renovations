<?php
/**
 * Contact / Estimate form mailer — configured for InMotion Hosting.
 * InMotion requires SMTP auth; PHP mail() is disabled on their servers.
 *
 * SETUP: Create an email account in cPanel (e.g. noreply@yourdomain.com).
 * Then set the 4 config values below to match that account and your domain.
 */

require_once('phpmailer/class.phpmailer.php');
require_once('phpmailer/class.smtp.php');

$mail = new PHPMailer();

// --- InMotion Hosting SMTP (Secure SSL/TLS - Recommended) ---
$mail->isSMTP();
$mail->Host       = 'mail.northsiderenovations.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'noreply@northsiderenovations.com';
$mail->Password   = '3PEMSqFwyHfafEc';
$mail->SMTPSecure = 'ssl';   // SSL for port 465
$mail->Port       = 465;     // Outgoing SMTP (SSL)

//$mail->SMTPDebug = 2; // uncomment to troubleshoot

$message = "";
$status = "false";

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
 // Verify Cloudflare Turnstile (sign up at dash.cloudflare.com with any email, e.g. Yahoo)
 $turnstile_secret = '0x4AAAAAACmZTqhqy7A9K1YeW_JCOtUM9Hg';
 $turnstile_response = isset($_POST['cf-turnstile-response']) ? trim((string) $_POST['cf-turnstile-response']) : '';
 $turnstile_ok = false;
 if ($turnstile_response !== '') {
     $ctx = stream_context_create(['http' => [
         'method'  => 'POST',
         'header'  => 'Content-Type: application/x-www-form-urlencoded',
         'content' => http_build_query([
             'secret'   => $turnstile_secret,
             'response' => $turnstile_response,
             'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''
         ])
     ]]);
     $verify = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $ctx);
     if ($verify !== false) {
         $verify = json_decode($verify, true);
         $turnstile_ok = !empty($verify['success']);
     }
 }
 if (!$turnstile_ok) {
     $message = 'Please complete the security check. If you did, try again or use a supported browser.';
     $status = "false";
     echo json_encode(array( 'message' => $message, 'status' => $status));
     exit;
 }

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

 $toemail = 'jeremyaylesworth@yahoo.com'; // Where estimate requests are sent
 $toname  = 'Northside Renovations';

 if( $botcheck == '' ) {

 $mail->SetFrom( $mail->Username , 'Northside Renovations' );
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
