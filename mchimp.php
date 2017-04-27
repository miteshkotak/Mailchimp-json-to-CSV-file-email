<?php 

/*---(0)---------asking for JSON string reply from mchimp-------------*/

session_start();

$apikey= $_SESSION["apikey"];
$list_id = $_GET['list_id'];
$chunk_size = 4096; //in bytes
$url = 'http://us1.api.mailchimp.com/export/1.0/list?apikey='.$apikey.'&id='.$list_id;
$handle = @fopen($url,'r');
$csvOutput = "";
$csvOutput2 = "";
$csvOutput_final = "";
//echo $handle;



/*---(1)---------Coversion of json input to csv String and save as file-------------*/
if (!$handle) {
  echo "failed to access url\n";
} else {
  $i = 0;
  $n = 0;
  $header = array();
  while (!feof($handle)) {
    $buffer = fgets($handle, $chunk_size);

 $obj = json_decode($buffer);
 $n_obj = count($obj) -1;
 //echo count($obj);
foreach ($obj as $key ) {

  $csvOutput .= $key.","; 
    echo "{$key} ";
     if ($i == $n_obj) 
     {
     $i= -1;
      $csvOutput .= "\n";
      }
     $i++;
 }


  }

  fclose($handle);
$filename = "list-".$list_id.".csv";
file_put_contents($filename, $csvOutput);
//$filename = mb_convert_encoding($filename, 'Windows-1252');


}

/*---(2)---------File Send Email-------------*/

require '../PHPMailerAutoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;


// $mail->isSMTP();                                      // Set mailer to use SMTP
// $mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
// $mail->SMTPAuth = true;                               // Enable SMTP authentication
// $mail->Username = 'user@example.com';                 // SMTP username
// $mail->Password = 'secret';                           // SMTP password
// $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
// $mail->Port = 587;                                    // TCP port to connect to
// Set PHPMailer to use the sendmail transport
$mail->isSendmail();
//Set who the message is to be sent from
$mail->setFrom('from@example.com', 'Mitesh Kotak');
//Set an alternative reply-to address
$mail->addReplyTo('someone@gmail.com', 'someone');
//Set who the message is to be sent to
$mail->addAddress('someone@gmail.com', 'someone');

// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');

//Set the subject line
$mail->Subject = 'CSV file ';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML('somthing');
//Replace the plain text body with one created manually
$mail->AltBody = 'something';
//Attach an image file
$mail->addAttachment($filename);

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}

?>