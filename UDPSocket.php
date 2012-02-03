<?php
class SocketException extends Exception {}

/**
 * UDPSocket - provides support for sending & receiving UDP packets
 *
 * @author Martok
 * @copyright Copyright (c) 2012
 */

class UDPSocket {
	var $fp = null;

	/**
	 * UDPSocket
	 *
	 * @param mixed $host: hostname or address
	 * @param mixed $port: numerical port
	 */
	function __construct($host, $port) {
		$host = "udp://$host";
		$fp = fsockopen($host, $port, $errno, $errstr);
		if (!$fp) {
			throw new SocketException("Network Error $errno: $errstr");
		} else {
			stream_set_timeout($fp, 2);
			$this->fp = $fp;
		}
	}

	function __destruct(){
		fclose($this->fp);
	}

	/**
	 * UDPSocket::send() - send raw data
	 *
	 * @param mixed $raw: data
	 */
	function send($raw){
		fwrite($this->fp, $raw);
	}

	/**
	 * UDPSocket::receive() - receive up to $bytes bytes from the network
	 *
	 * @param int $bytes:
	 * @return string: raw data
	 */
	function receive($bytes) {
		$buf = fread($this->fp, $bytes);
		return $buf;
	}

	function eof(){
		return feof($this->fp);
	}
}

?>