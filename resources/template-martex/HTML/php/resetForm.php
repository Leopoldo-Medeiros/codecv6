<?
$email = $_REQUEST["email"];
$to    = "you@yourdomain.com"; // ENTER YOUR EMAIL ADDRESS
if (isset($email)) {
    $email_subject = "This message was sent via YOUR SITE NAME to reset a login for $email (Reset Form)";
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/plain; charset=UTF-8" . "\r\n";
	$headers .= "Email: ".$email.">\r\n"."Reply-To: ".$email."\r\n" ;
	$msg     = "Email: $email";
	
	$mail =  mail($to, $email_subject, $msg, $headers);

if($mail)
	{
		echo 'success';
	}

else
	{
		echo 'failed';
	}
}

?>