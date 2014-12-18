ping
====

Example:

```php
<?php
    $ping = dbarr\Ping::getInstance('freebsd');
    $ping->setDefaultOptions(['packetsize' => 56, 'count' => 4]);
    
    var_dump($ping->ping("127.0.0.1"));
    var_dump($ping->ping("8.8.8.8"));
```
