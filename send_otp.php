<?php
// PHPMailer via composer autoload — ya direct include
// Download PHPMailer: https://github.com/PHPMailer/PHPMailer
// Place in /phpmailer/ folder
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function sendOTP($toEmail, $toName, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shram0610@gmail.com';
        $mail->Password   = 'uhnrjrocoecdeizv';  // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;


        $mail->setFrom('shram0610@gmail.com', 'Explore India');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP - Explore India';
        $mail->Body    = '
        <div style="font-family:Arial,sans-serif;max-width:480px;margin:0 auto;background:#0a0a0a;border-radius:16px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#1a0533,#2d1b69);padding:32px;text-align:center;">
                <h2 style="color:#fff;margin:0;font-size:24px;">🇮🇳 Explore India</h2>
            </div>
            <div style="padding:32px;">
                <p style="color:#ccc;font-size:15px;margin-bottom:8px;">Hello <b style="color:#fff;">'.$toName.'</b>,</p>
                <p style="color:#aaa;font-size:14px;">Your One-Time Password (OTP) is:</p>
                <div style="background:#1a1a1a;border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:24px;text-align:center;margin:20px 0;">
                    <span style="font-size:42px;font-weight:800;letter-spacing:10px;color:#f5a623;">'.$otp.'</span>
                </div>
                <p style="color:#888;font-size:13px;">This OTP is valid for <b style="color:#fff;">10 minutes</b>. Do not share it with anyone.</p>
                <hr style="border-color:rgba(255,255,255,0.08);margin:24px 0;">
                <p style="color:#555;font-size:12px;text-align:center;">© 2024 Explore India. All Rights Reserved.</p>
            </div>
        </div>';
        $mail->send();
        return true;
    } catch (Exception $e) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
    }