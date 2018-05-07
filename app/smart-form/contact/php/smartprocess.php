<?php

	if (!isset($_SESSION)) session_start();

	include dirname(__FILE__).'/settings/settings.php';
	include dirname(__FILE__).'/functions/emailValidation.php';


	/* Current Date Year
	------------------------------- */
	$currYear = date("Y");

	/*	---------------------------------------------------------------------------
	: Register all form field variables here
	--------------------------------------------------------------------------- */
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

	$sendername = strip_tags(trim($request->sendername));
	$emailaddress = strip_tags(trim($request->emailaddress));
	$sendersubject = strip_tags(trim($request->sendersubject));
	$sendermessage = strip_tags(trim($request->sendermessage));

	/*	----------------------------------------------------------------------
	: Prepare form field variables for CSV export
	----------------------------------------------------------------------- */
	if($generateCSV == true){
		$csvFile = $csvFileName;
		$csvData = array(
			"$sendername",
			"$emailaddress",
			"$sendersubject"
		);
	}


	include dirname(__FILE__).'/phpmailer/PHPMailerAutoload.php';
	include dirname(__FILE__).'/templates/smartmessage.php';

	$mail = new PHPMailer();
	$mail->IsHTML(true);
	$mail->setFrom($emailaddress,$sendername);
	$mail->CharSet = "UTF-8";
	$mail->Encoding = "base64";
	$mail->Timeout = 200;
	$mail->ContentType = "text/html";
	$mail->addAddress($receiver_email, $receiver_name);
	$mail->Subject = $receiver_subject;
	$mail->Body = $message;
	$mail->AltBody = "Use an HTML compatible email client";

	// For multiple email recepients from the form
	// Simply change recepients from false to true
	// Then enter the recipients email addresses
	// echo $message;
	$recipients = false;
	if($recipients == true){
		$recipients = array(
			"address@example.com" => "Recipient Name",
			"address@example.com" => "Recipient Name"
		);

		foreach($recipients as $email => $name){
			$mail->AddBCC($email, $name);
		}
	}

	if($mail->Send()) {
		/*	-----------------------------------------------------------------
			: Generate the CSV file and post values if its true
			----------------------------------------------------------------- */
			if($generateCSV == true){
				if (file_exists($csvFile)) {
					$csvFileData = fopen($csvFile, 'a');
					fputcsv($csvFileData, $csvData );
				} else {
					$csvFileData = fopen($csvFile, 'a');
					$headerRowFields = array(
						"Guest Name",
						"Email Address",
						"Subject"
					);
					fputcsv($csvFileData,$headerRowFields);
					fputcsv($csvFileData, $csvData );
				}
				fclose($csvFileData);
			}

		/*	---------------------------------------------------------------------
			: Send the auto responder message if its true
			--------------------------------------------------------------------- */
			if($autoResponder == true){

				include dirname(__FILE__).'/templates/autoresponder.php';

				$automail = new PHPMailer();
				$automail->setFrom($receiver_email,$receiver_name);
				$automail->isHTML(true);
				$automail->CharSet = "UTF-8";
				$automail->Encoding = "base64";
				$automail->Timeout = 200;
				$automail->ContentType = "text/html";
				$automail->AddAddress($emailaddress, $sendername);
				$automail->Subject = "Thank you for contacting us";
				$automail->Body = $automessage;
				$automail->AltBody = "Use an HTML compatible email client";
				$automail->Send();
			}

			if($redirectForm == true){
				echo '<script>setTimeout(function () { window.location.replace("'.$redirectForm_url.'") }, 8000); </script>';
			}

			echo '<div class="alert notification alert-success">Message has been sent successfully!</div>';
			}
			else {
				echo '<div class="alert notification alert-error">Message not sent - server error occured!</div>';
			}

?>