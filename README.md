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

Returns something similar to:

```
array(10) {
  ["host"]=>
  string(9) "127.0.0.1"
  ["packets_transmitted"]=>
  int(4)
  ["packets_received"]=>
  int(4)
  ["rtt_min_ms"]=>
  float(0.014)
  ["rtt_avg_ms"]=>
  float(0.032)
  ["rtt_max_ms"]=>
  float(0.044)
  ["rtt_stddev_ms"]=>
  float(0.012)
  ["packetsize"]=>
  int(64)
  ["ttl"]=>
  int(64)
}

array(10) {
  ["host"]=>
  string(7) "8.8.8.8"
  ["packets_transmitted"]=>
  int(4)
  ["packets_received"]=>
  int(4)
  ["rtt_min_ms"]=>
  float(14.384)
  ["rtt_avg_ms"]=>
  float(14.551)
  ["rtt_max_ms"]=>
  float(14.731)
  ["rtt_stddev_ms"]=>
  float(0.123)
  ["packetsize"]=>
  int(64)
  ["ttl"]=>
  int(57)
}
```
