# Notifier app

How to start the app:
1. Install PHP, MySQL, and composer
2. Register in Twilio and AWS to create a sandbox account.
3. Insert authenticate keys to the .env file
4. In the .env file type database credentials
5. In the console run these commands one by one:
    1. `php bin/console doctrine:migrations:migrate`
    2. `symfony server:start`
    3. `bin/console messenger:consume async`
6. In the browser type: [127.0.0.1:8000/send](http://127.0.0.1:8000/send)

**Enjoy!**

## Features

* Send emails through the AWS email
* Send SMS through  TWILIO
