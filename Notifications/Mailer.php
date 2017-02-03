<?php

/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 12/4/16
 * Time: 4:32 PM
 */
namespace isv\Notifications;

use isv\Helper\Logger;
use isv\IS;

class Mailer
{
    public static $errors = FALSE;

    /**
     * This class require PHPMailer
     * @param $to
     * @param $subject
     * @param $body
     * @param bool $html
     * @return bool
     */
    public static function sendMessage($to, $subject, $body, $html=true)
    {
        $mail = new \PHPMailer();
        $config = IS::app()->getConfig('notify');
        if($config['smtp'])
        {
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = $config['secure_type'];
            $mail->Port = $config['smtp_port'];
        }
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($config['senderEmail'], $config['senderName']);
        if(is_array($to))
        {
            foreach ($to as $emailAddress)
            {
                $mail->addAddress($emailAddress);     // Add a recipient
            }
        }else
        {
            $mail->addAddress($to);
        }
        $mail->isHTML($html);                                  // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
        if($mail->send()){
            return true;
        }else{
            Logger::log('global', 'Failed to send email to '.var_export($to, true). 'With message: '.$mail->ErrorInfo);
            static::$errors = $mail->ErrorInfo;
        }
    }

    public static function prepare($templateName, $data=NULL)
    {
        $templateFile = IS::app()->getConfig('config')['emailDir'].DIRSEP.$templateName.'.html';
        if(!is_file($templateFile)){
            return 'Email template file not exists';
            Logger::log('global', 'Email template file: '.$templateFile.' not exists');
        }
        $fileContent = file_get_contents($templateFile);
        if($data && count($data))
        {
            return str_replace(array_keys($data), array_values($data), $fileContent);
        }
    }
}