# Snelstart B2B API
Snelstart api PHP wrapper class

`composer require avancecommunicatie/snelstart-b2b-api`

Simple example

```
require 'vendor/autoload.php';

$snelstart = new \Snelstart\Snelstart();
$snelstart->setApiKey([your api key here]);
$snelstart->generateAccessToken([your connection key here]);

print_r($snelstart->send('artikelen'));
```

Have fun, feel free to contribute.
