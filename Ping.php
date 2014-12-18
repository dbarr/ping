<?php

namespace dbarr;

use \BadMethodCallException;
use \InvalidArgumentException;
use \Closure;

class Ping {
    const SUPPORTED_OPERATING_SYSTEMS = [
        'freebsd',
    ];

    protected $_binaryCmd;
    protected $_cmdOptions = [
        'count'         => '',
        'dfbit'         => '',
        'timeout'       => '',
        'packetsize'    => '',
    ];
    protected $_defaultOptions = [
    ];
    protected $_parsers = [
    ];

    static public final function getInstance($os) {
        if (array_search($os, self::SUPPORTED_OPERATING_SYSTEMS) === false) {
            throw new BadMethodCallException('Unsupported operating system');
        }

        $class = sprintf('%s\\%s', __CLASS__, $os);

        return new $class();
    }

    static public final function getSupportedOperatingSystems() {
        return self::SUPPORTED_OPERATING_SYSTEMS;
    }

    public function clearDefaultOptions() {
        $this->_defaultOptions = [];
    }

    public function setDefaultOptions($opts=[]) {
        foreach ($opts as $opt => $optval) {
            if (array_key_exists($opt, $this->_cmdOptions) === false) {
                throw new InvalidArgumentException(sprintf('Unknown option: %s', $opt));
            }
        }

        $this->_defaultOptions = array_merge($this->_defaultOptions, $opts);
    }

    private function generateCmd($host, $opts=[]) {
        $cmd = [escapeshellcmd($this->_binaryCmd)];

        $opts = array_merge($this->_defaultOptions, $opts);

        foreach ($opts as $opt => $optval) {
            if (array_key_exists($opt, $this->_cmdOptions) === false) {
                throw new InvalidArgumentException(sprintf('Unknown option: %s', $opt));
            }

            $option = $this->_cmdOptions[$opt];

            $cmd[] = escapeshellarg(call_user_func_array('sprintf', array_merge([$option], is_array($optval) ? $optval : [$optval])));
        }

        $cmd[] = escapeshellarg($host);
        $cmd[] = '2>&1';
        $cmd = implode(' ', $cmd);

        return $cmd;
    }

    public function ping($host, $opts=[]) {
        $cmd = $this->generateCmd($host, $opts);
        $out = [];
        $returncode = 0;

        exec($cmd, $out, $returncode);

        $ret = ['host' => $host];

        if ($returncode) {
            $ret['error'] = implode("\n", $out);
            return $ret;
        }

        $ret['output'] = implode("\n", $out);

        foreach ($this->_parsers as $parser_callback) {
            $callback_data = $parser_callback($out);

            if (is_array($callback_data)) {
                $ret = array_merge($ret, $callback_data);
            }
        }

        return $ret;
    }

    protected function setBinaryCmd($cmd) {
        $this->_binaryCmd = $cmd;
    }

    protected function setCmdOptions(array $opts) {
        $this->_cmdOptions = array_merge($this->_cmdOptions, $opts);
    }

    protected function registerParser($callback) {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback');
        }

        $this->_parsers[] = $callback;
    }
}
