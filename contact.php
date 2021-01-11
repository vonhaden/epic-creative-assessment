<?php

// ReCaptcha
// =================================================================
// Require ReCaptcha class
use ReCaptcha\ReCaptcha;
require ('recaptcha/autoload.php');
// ReCaptcha Secret Key
$recaptchaSecret = '6Lfs9ycaAAAAAHwI4jVBHU0r4FjBCHUehBklbczP';


// Email Configuration
// ==================================================================
// an email address that will be in the From field of the email.
$from = 'EPIC Creative Assessment <epic@assessment.com>';

// Email Subject
$subject = 'Your message was sent';

// Form fields to include in Email
// variable name => Text to appear in the email
$fields = array('firstName' => 'First Name', 'lastName' => 'Last Name', 'phone' => 'Phone', 'message' => 'Message');


// User Config
// ==================================================================
// message that will be displayed when everything is OK :)
$okMessage = 'Contact form successfully submitted. Thank you, I will get back to you soon!';

// If something goes wrong, we will display this message.
$errorMessage = 'There was an error while submitting the form. Please try again later';


try {
    if (!empty($_POST)) {

        // ReCaptcha Validation
        // ================================================================

        // Validate the ReCaptcha, if something is wrong, throw an Exception
        if (!isset($_POST['g-recaptcha-response'])) {
            throw new Exception('ReCaptcha is not set.');
        }
        $recaptcha = new ReCaptcha($recaptchaSecret, new \ReCaptcha\RequestMethod\CurlPost());

        // Validate the ReCaptcha field together with the user's IP address
        $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$response->isSuccess()) {
            throw new Exception('ReCaptcha was not validated.');
        }



        // Email User
        // ==================================================================

        // Compose the Email
        $emailText = "Thanks for contacting us!\n\n";

        // Add values from the form to the email
        foreach ($_POST as $key => $value) {
            // Only add values from the $fields array
            if (isset($fields[$key])) {
                $emailText .= "$fields[$key]: $value\n";
            }
        }

        // Email Headers
        $headers = array('Content-Type: text/plain; charset="UTF-8";',
            'From: ' . $from,
            'Reply-To: ' . $from,
            'Return-Path: ' . $from,
        );

        // Send email
        mail($_POST['email'], $subject, $emailText, implode("\n", $headers));

//        $responseArray = array('type' => 'success', 'message' => $okMessage);
    }
} catch (Exception $e) {
    $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
}
