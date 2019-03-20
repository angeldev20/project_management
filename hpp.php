<?php
include_once './constants.php';

define('ENVIRONMENT', isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'development');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */

if (defined('ENVIRONMENT'))
{
    switch (ENVIRONMENT)
    {
        case 'release':
        case 'testing':
        case 'development':
            error_reporting(E_ALL);
            break;
        case 'production':
            error_reporting(E_ERROR);
            break;

        default:
            exit('The application environment is not set correctly.');
    }
}

?>
<html>
<head>
    <style>
        .hppFrame {
            width: 100%;
            border: none;
            height: 389px;
        }
    </style>
    <script src="assets/blueline/js/propay/jquery-3.2.1.js" type="text/javascript"></script>
    <script src="assets/blueline/js/propay/jquery.signalR-3.2.1.min.js" type="text/javascript"></script>
    <script src="assets/blueline/js/propay/hpp-1.1.js" type="text/javascript"></script>
    <script type="text/javascript">

        /*=======================================================================================================
         The following functions are referenced by the hpp.js file and should be included on your checkout page
         ========================================================================================================*/
        //Submit Button Function
        function btnSubmitForm_Click() {
            signalR_SubmitForm();
        }
        //This function is invoked when the Hosted Payment Page and the Checkout Page are connected and the Hosted Payment Page is ready for submission
        function formIsReadyToSubmit() {
            //Do not allow the user to submit the Hosted Payment Page until this Method has been invoked
            //document.getElementById('btnSubmit').disabled = false;
            $("#btnSubmit",parent.document).removeAttr('disabled');
        }

        function loadHPP() {
            var HID = window.parent.HID;
            if (HID == null) {
                alert("nothing from parent");
            }

            hpp_Load(HID, false); //HostedTransactionIdentifier, Debug Mode
        }

        $( document ).ready(function() {
            loadHPP();
        });
    </script>
</head>
<body>
<iframe scrolling="no" class="hppFrame" id="hppFrame" name="hppFrame" class="iFrame"></iframe>
<input style="display: none;" type="button" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-info pull-left" onclick="btnSubmitForm_Click()" disabled="disabled" />
</body>
</html>
