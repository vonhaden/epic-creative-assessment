<?php

// ReCaptcha
// =====================================================================================================================
// Require ReCaptcha class
use ReCaptcha\ReCaptcha;
require ('recaptcha/autoload.php');
// ReCaptcha Secret Key
$recaptchaSecret = '6Lfs9ycaAAAAAHwI4jVBHU0r4FjBCHUehBklbczP';



// =====================================================================================================================
// Email Configuration
// =====================================================================================================================
// an email address that will be in the From field of the email.
$from = 'EPIC Creative Assessment <epic@assessment.com>';

// Email Subject
$subject = 'Your message was sent';

// Form fields to include in Email
// variable name => Text to appear in the email
$fields = array('firstName' => 'First Name', 'lastName' => 'Last Name', 'phone' => 'Phone', 'message' => 'Message');




// =====================================================================================================================
// DB Config
// =====================================================================================================================
require_once ('includes/database.php');



// =====================================================================================================================
// User Config
// =====================================================================================================================
// message that will be displayed when everything is OK :)
$okMessage = 'Thank you for contacting us!';

// If something goes wrong, we will display this message.
$errorMessage = 'There was an error while submitting the form. Please try again later';



try {
    if (!empty($_POST)) {
        // =============================================================================================================
        // ReCaptcha Validation
        // =============================================================================================================

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



        // =============================================================================================================
        // Email User
        // =============================================================================================================

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



        // =============================================================================================================
        // Add form data to DB
        // =============================================================================================================

        // Set up Variables
        $firstName = isset($_POST['firstName']) ? strip_tags($_POST['firstName']) : '';
        $lastName = isset($_POST['lastName']) ? strip_tags($_POST['lastName']) : '';
        $email = isset($_POST['email']) ? strip_tags($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? strip_tags($_POST['phone']) : '';
        $address1 = isset($_POST['address1']) ? strip_tags($_POST['address1']) : '';
        $address2 = isset($_POST['address2']) ? strip_tags($_POST['address2']) : '';
        $city = isset($_POST['city']) ? strip_tags($_POST['city']) : '';
        $state = isset($_POST['state']) ? strip_tags($_POST['state']) : '';
        $zip = isset($_POST['zip']) ? strip_tags($_POST['zip']) : '';
        $message = isset($_POST['message']) ? strip_tags($_POST['message']) : '';

        // Set up the insert query for use in a prepared statement
        $query = "INSERT INTO `Messages`
                 (`MessageID`, `FirstName`, `LastName`, `Email`, `Phone`, `Address1`, `Address2`, `City`, `State`, `Zip`, `Message`, `Date`)
                 VALUES
                 (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";

        // Create the prepared statement and run against database
        $stmt = mysqli_prepare($db, $query) or die('Error in query.');

        // Bind the variables to the query
        mysqli_stmt_bind_param($stmt, "ssssssssss", $firstName, $lastName, $email, $phone, $address1, $address2, $city, $state, $zip, $message);

        // Execute the query
        $result = mysqli_stmt_execute($stmt);



        // =============================================================================================================
        // Set response
        // =============================================================================================================
        $responseArray = array(
            'type' => 'success',
            'message' => $okMessage
        );
    }
} catch (Exception $e) {
    $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
}


// =====================================================================================================================
// Send response to the client
// ====================================================================================================================
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
} else {
    echo $responseArray['message'];
}
