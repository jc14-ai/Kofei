<?php
session_start();
include('dbcon.php');

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require __DIR__ . '/../../vendor/autoload.php';

function send_email_verification($username, $email, $verify_token)
{
    $mail = new PHPMailer(true);
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->SMTPAuth = true;

    $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through                              //Enable SMTP authentication
    $mail->Username = 'jestalycastillo15@gmail.com';                     //SMTP username
    $mail->Password = 'dyzc sruc fjud dhrd';
    //SMTP password
    $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
    $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('from@example.com', "Kofai");
    $mail->addAddress($email);     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Verify your email from Kofei';
    $email_template = "
        <h2>Registration from Kofei</h2>
        <h5>Verify your Email Address with the given link below.</h5>
        <br/><br/>
        <a href='http://127.0.0.1:8000/site/backend/verify-email.php?token=$verify_token'>Verify my email</a>
    ";
    $mail->Body = $email_template;

    $mail->send();
}

if (isset($_POST['sign-up-button'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = $_POST['password'];
    $verify_token = md5(rand());

    $check_username_query = "SELECT COUNT(*) AS usernames FROM user WHERE username = '$username';";
    $check_username_query_run = mysqli_query($conn, $check_username_query);
    $user_row = mysqli_fetch_assoc($check_username_query_run);
    // if ($user_row['usernames'] != null) {
    //     $_SESSION['sign-up-status'] = 'Registration Failed.';
    //     header('Location: /site/php/index/body.php');
    //     exit;
    // }
    // // USERNAME VERIFICATION
    // $username_query = "SELECT username FROM user WHERE username = '$username';";
    // $username_query_run = mysqli_query($conn, $username_query);

    // if (mysqli_num_rows($username_query_run) > 0) {
    //     $_SESSION['sign-up-status'] = 'Username already exists.';
    //     header('Location: /site/php/index/body.php');
    //     return;
    // }

    // // CONTACT VERIFICATION
    // $contact_query = "SELECT contact FROM user WHERE contact = '$contact';";
    // $contact_query_run = mysqli_query($conn, $contact_query);

    // if (mysqli_num_rows($contact_query_run) > 0) {
    //     $_SESSION['sign-up-status'] = 'Contact Number already exists.';
    //     header('Location: /site/php/index/body.php');
    //     return;
    // }


    // EMAIL LITERAL VERIFICATION 
    $email_query = "SELECT email FROM user WHERE email = '$email' LIMIT 1;";

    $email_query_run = mysqli_query($conn, $email_query);

    if (mysqli_num_rows($email_query_run) > 0) {
        $verify_email_query = "SELECT verified FROM user WHERE email = '$email' LIMIT 1;";
        $verify_email_query_run = mysqli_query($conn, $verify_email_query);
        $row = mysqli_fetch_assoc($verify_email_query_run);

        if ($row['verified'] === 'yes') {
            $_SESSION['sign-up-status'] = 'Email Address already exists.';
        } else {
            $token_query = "SELECT token FROM user WHERE email = '$email' LIMIT 1;";
            $token_query_run = mysqli_query($conn, $token_query);
            $row = mysqli_fetch_assoc($token_query_run);
            $verify_token = $row['token'];

            send_email_verification($username, $email, $verify_token);
        }
        header('Location: /site/php/index/body.php');
        // header('Location: /site/php/index/body.php?signup=true');
        exit(0);
    } else {
        $user_query = "INSERT INTO user (username,email,contact,password,token) VALUES ('$username', '$email','$contact','$password','$verify_token');";
        $user_query_run = mysqli_query($conn, $user_query);

        $role_query = "INSERT INTO roles (email) VALUES ('$email');";
        $role_query_run = mysqli_query($conn, $role_query);

        if ($user_query_run && $role_query_run) {
            send_email_verification($username, $email, $verify_token);
            $_SESSION['sign-up-status'] = 'Registered Successfully!. Verify your email.';
            header('Location: /site/php/index/body.php');

        } else {
            $_SESSION['sign-up-status'] = 'Registration Failed.';
            header('Location: /site/php/index/body.php');
        }
    }
}
?>