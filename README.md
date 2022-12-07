## To Update Setting for sending mail

You need to update setting for sending multiple mail at the same time.

- add "php_value max_execution_time 900" in htaccess file
- update update "max_execution_time" and "max_input_time" to "900" in php.ini file

## Mail Setting in your google account

- go to "Security Setting" and "2 Step Verification On and Generate Password"
- add generated password in MAIL_PASSWORD from .env file
