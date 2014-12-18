<?php

namespace dbarr\Ping;

use dbarr\Ping;

class freebsd extends Ping {
    function __construct() {
        $this->setBinaryCmd('/sbin/ping');
        $this->setCmdOptions([
            'count'         => '-c %d',
            'dfbit'         => '-D',
            'timeout'       => '-t %d',
            'packetsize'    => '-s %d',
        ]);
        $this->setDefaultOptions([
            'count'         => 1,
        ]);
        $this->registerParser([$this, 'ParsePacketCounts']);
        $this->registerParser([$this, 'ParseRoundTripTimes']);
        $this->registerParser([$this, 'ParseFirstPing']);
    }

    static function ParsePacketCounts($output) {
        foreach ($output as $line) {
            if (sscanf($line, '%d packets transmitted, %d packets received', $transmitted, $received) == 2) {
                return ['packets_transmitted' => $transmitted, 'packets_received' => $received];
            }
        }
    }

    static function ParseRoundTripTimes($output) {
        foreach ($output as $line) {
            if (sscanf($line, 'round-trip min/avg/max/stddev = %f/%f/%f/%f ms', $min, $avg, $max, $stddev) == 4) {
                return ['rtt_min_ms' => $min, 'rtt_avg_ms' => $avg, 'rtt_max_ms' => $max, 'rtt_stddev_ms' => $stddev];
            }
        }
    }

    static function ParseFirstPing($output) {
        foreach ($output as $line) {
            if (sscanf($line, '%d bytes from %s icmp_seq=%d ttl=%d time=%f ms', $packetsize, $host, $seq, $ttl, $rtt) == 5) {
                return ['packetsize' => $packetsize, 'ttl' => $ttl];
            }
        }
    }
}

?>