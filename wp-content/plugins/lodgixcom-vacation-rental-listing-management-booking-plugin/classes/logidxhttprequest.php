<?php

if (!class_exists('LogidxHTTPRequestException')) {
    class LogidxHTTPRequestException extends Exception
    {
        public function __construct($message, $code = 0, Exception $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }

        public function __toString()
        {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }
    }
}

if (!class_exists('LogidxHTTPRequest')) {
    class LogidxHTTPRequest
    {
        var $_host;
        var $_protocol;
        var $_uri;
        var $_port;

        function __construct($url)
        {
            $pos = strpos($url, '://');
            $this->_protocol = strtolower(substr($url, 0, $pos));

            $url = substr($url, $pos + 3);
            $pos = strpos($url, '/');
            if ($pos === false) {
                $pos = strlen($url);
            }
            $host = substr($url, 0, $pos);

            if (strpos($host, ':') !== false) {
                list($this->_host, $this->_port) = explode(':', $host);
            } else {
                $this->_host = $host;
                $this->_port = ($this->_protocol == 'https') ? 443 : 80;
            }

            $this->_uri = substr($url, $pos);
            if ($this->_uri == '') {
                $this->_uri = '/';
            }
        }

        /**
         * @return string
         * @throws LogidxHTTPRequestException
         */
        function downloadToString()
        {
            $crlf = "\r\n";

            // generate request
            $req = 'GET ' . $this->_uri . ' HTTP/1.0' . $crlf . 'Host: ' . $this->_host . $crlf . $crlf;

            // fetch
            $context = stream_context_create(['ssl' => [
                'verify_peer' => false,
                'allow_self_signed' => true,
            ]]);
            $fp = stream_socket_client(
                ($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host . ':' . $this->_port,
                $errno,
                $errstr,
                60,
                STREAM_CLIENT_CONNECT,
                $context
            );
            if (!$fp || $errno > 0) {
                throw new LogidxHTTPRequestException($errstr, $errno);
            }
            stream_set_timeout($fp, 600);
            fwrite($fp, $req);
            $response = '';
            while (is_resource($fp) && $fp && !feof($fp)) {
                $response .= fread($fp, 1024);
            }
            fclose($fp);

            if (!$response) {
                throw new LogidxHTTPRequestException('Empty response', 0);
            }

            // split header and body
            $pos = strpos($response, $crlf . $crlf);
            if ($pos === false) {
                throw new LogidxHTTPRequestException('Invalid response', 0);
            }
            $header = substr($response, 0, $pos);
            $body = substr($response, $pos + 2 * strlen($crlf));

            // parse headers
            $headers = array();
            $lines = explode($crlf, $header);
            foreach ($lines as $line) {
                if (($pos = strpos($line, ':')) !== false) {
                    $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos + 1));
                }
            }

            // redirection?
            if (isset($headers['location'])) {
                return (new LogidxHTTPRequest($headers['location']))->downloadToString();
            }

            return $body;
        }
    }
}
