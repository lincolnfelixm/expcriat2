<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'PHPMailer-master/src/Exception.php'; 
    require 'PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/src/SMTP.php';

    $mail = new PHPMailer();

    // Configuração
    $mail->Mailer = "smtp";
    $mail->IsSMTP(); 
	$mail->CharSet = 'UTF-8';   
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth = true;     
	$mail->SMTPSecure = 'ssl'; 
    $mail->Host = 'smtp.gmail.com'; 
	$mail->Port = 465;

    // Detalhes do envio de E-mail
	$mail->Username = 'lincoln.felixm@gmail.com'; 
	$mail->Password = "cekwdxubuczixrrq";
	$mail->SetFrom('noreply@wecare.com', 'WeCare Health');
    $mail->addAddress($decryptedEmail);
	$mail->Subject = 'Email Confirmation';


    // Mensagem
    $mensagem = "<h1> Token </h1>";
    $mensagem .= 'Your token to recover your account is <b>' . $token . '</b>';


    $mail->msgHTML($mensagem);
    $mail->send();

?>