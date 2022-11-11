## Laravel Mail Logger

---

A laravel package to log outgoing emails and resend. This package will store all outgoing emails inside database and will track their status if they are successfully sent or not.

### Installation

This package can be installed using composer.

```bash
composer require timedoor/mail-logger
```

It will automatically detected by Laravel, but you could register the package's service provider manually by adding the below line to `providers` array inside `config/app.php` file.

```php
    'providers' => [
        //
        \Timedoor\MailLogger\MailLoggerServiceProvider::class
    ]
```

By default, this package will use `mail_logs` as table name on database migration, but you could change it by editting `config/mail_logger.php`. Before that, you need to publish the configuration by running

```bash
php artisan vendor:publish --tag=mail-logger
```

Run migrations to create the table required to store the emails.

```
php artisan migrate
```

This will create a table `mail_logs`, or any name you gives on configuration file.

### Usage

By default this package records all the outgoing mailables and notifications.

### Ignoring Mailables And Notifications From Being Recorded

If you wish to ignore certain mailables or notifications from being recorded,
You can add them to `ignore` array in `config/mail_logger.php` file.

#### Resending Mails

You can resend any mail by using the following command

```bash
php artisan mail-logger:resend-mail 1
```

Here 1 represents the ID of the mail to resend.
The command above will send email as the logged mail sent, for example: if you send using queue, it will resend email using queue, too.
In case you want to send the mail immediately, you can add option `--now`.

```bash
php artisan mail-logger:resend-mail 1 --now
```

#### Resending All Un-Sent Mails

If you wish to resend all the mails which are unsent, You can use the following artisan command

```bash
php artisan mail-logger:resend-unsent-mail
```

or

```bash
php artisan mail-logger:resend-unsent-mail --now
```

Since this command will only resend the mails which are failed to send, You can safely schedule this command to resend your failed emails.

```php
$schedule->command('mail-logger:resend-unsent-mail')->daily();
```

#### Deleting Older Entries

Since this package records all outgoing emails, Your database table will start growing quickly. To automatically delete older entires,
This package provides an artisan command to schedule deletion of older entries.

You can schedule deletion of older entries by adding the following line to your scheduler.

```php
$schedule->command('mail-logger:prune --hours=72')->daily();
```

This will delete all entries which are older than 72 hours.

#### Run it via controller

If you want to resend or prune emails via controller, please look at the example below

```php
<?php

use Timedoor\MailLogger\Logger\MailLogger;
use Timedoor\MailLogger\Models\MailLog;

class MailController extends Controller
{
    public function resend(MailLog $email)
    {
        MailLogger::resendMailById($email->id, true); //true for --now option

        return redirect()->back();
    }
    
    public function resendAll(MailLog $email)
    {
        MailLogger::resendUnsentMails(true); //true for --now option

        return redirect()->back();
    }
    
    public function prune($x)
    {
        MailLogger::pruneMails(now()->subHours($x)); // prune emails older than $x hours

        return redirect()->back();
    }
}
```

### Contributing

![contributions-wellcome](https://user-images.githubusercontent.com/12730759/150999538-d6872478-96ab-42d6-bb58-0ae443f514c8.svg)

Contributions are always welcome!

### License

Licensed under the MIT License, see [LICENSE](LICENSE) for more information.
