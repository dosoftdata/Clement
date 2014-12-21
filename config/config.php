<?php
// SITE_ROOT contains the full path to the folder
define('SITE_ROOT', dirname(dirname(__FILE__)));
// Database connectivity setup  
define('DB_PERSISTENCY', 'true');
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'aguser');
define('DB_PASSWORD', 'AXcnVFzGqPy6UKTS');
define('DB_DATABASE','agcurrency');
define('DB_DRIVER','Pdo_Mysql');

// These should be true while developing the web site
define('IS_WARNING_FATAL', true);
define('DEBUGGING', true);
// The error types to be reported
define('ERROR_TYPES', 1);
// Settings about mailing the error messages to admin
define('SEND_ERROR_MAIL', false);
define('ADMIN_ERROR_MAIL', 'xxx@ghost');
define('SENDMAIL_FROM', 'xxx@ghost');
ini_set('sendmail_from', SENDMAIL_FROM);
//Admin web emails
define('ADMNEMAIL','xxx@ghost');

// By default we don't log errors to a file

define('LOG_ERRORS', true);
define('LOG_ERRORS_FILE', SITE_ROOT.'/errors_log.txt'); // Windows

//define('LOG_ERRORS_FILE', SITE_ROOT.'/errors.log'); // Linux
/* Generic error message to be displayed instead of debug info
(when DEBUGGING is false) */
define('SITE_GENERIC_ERROR_MESSAGE', '<h1>Sitename Error!</h1>');