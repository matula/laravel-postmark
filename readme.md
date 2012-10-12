# Postmark Bundle for Laravel #

This package is a simple wrapper for the [Postmark API](http://developer.postmarkapp.com/developer-build.html).  All error checking should occur before using this bundle.

## Install ##

```bash
php artisan bundle:install postmark
```

In ``application/bundles.php`` add:

```php
'postmark' => array('auto' => true),
```

### Config ###

Add your API Key and From email address to the config file in ``bundles/postmark/config/option.php``.

## Usage ##

This is the most basic usage

```php
$postmark = new Postmark();
$postmark->to('racerx@mach5speedraceremail.com');
$postmark->subject('Chim chim on the loose again');
$postmark->txt_body('Hey Speed, Please keep Spritle and Chim chim in line. Love, Racer X.');
$response = $postmark->send();

if ($response['error']) 
{
	// There was a problem.
	echo $response['message'];
}
```

In Postmark, you have to keep your registered email address for the 'From' field, but you can customize the name and the reply to address.

```php
$postmark->from_name('Rex Racer');
$postmark->reply('yourbrother@mach5speedraceremail.com');
```

Other methods

```php
$postmark->cc();
$postmark->bcc();
$postmark->html_body();
$postmark->tag();
```

Attachments need a file name, the base64 encoded files, and the mime type. Multiple attachments are accepted, but Postmark has some restrictions on type and size. This bundle doesn't yet validate any of those.

```php
$postmark->attachment($_FILES['userfile']['name'], base64_encode($_FILES['userfile']['tmp_name'], $_FILES['userfile']['type']));
$postmark->attachment('trixie_hot.jpg','R28gU3BlZWQhIGh0dHA6Ly9iaXQubHkvT2xTSGhz','image/jpeg');
``` 