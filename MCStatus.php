<?php
require_once("UDPSocket.php");
require_once("SocketEncoder.php");
define('MCStatus_DEFAULT_PORT',25565);

/**
 * MCStatus - provides an easy interface for querying minecraft (beta 1.9+) servers
 * See http://wiki.vg/Query for more info on the internals
 * Additionally, this class parses Bukkit's plugin item as suggested by Dinnerbone.
 *
 * @author Martok
 * @copyright Copyright (c) 2012
 */
class MCStatus{
	public static $DEFAULT_PORT = MCStatus_DEFAULT_PORT;
	var $host;
	var $port;
	var $socket;
	function __construct($host,$port = MCStatus_DEFAULT_PORT){
		$this->host = $host;
		$this->port = $port;
	}

	private function getToken($sessid)
	{
		$packet = array_merge(
			array(0xFE, 0xFD, 0x09),
			SocketEncoder::int32BE($sessid)
		);
		trigger_error("Requesting token for session $sessid");
		$this->socket->send(SocketEncoder::asRaw($packet));
		$str = $this->socket->receive(0xFFFF);
		$data = SocketEncoder::asArray($str);

		$pos = 1;
		if (0x09 == SocketEncoder::getByte($data, $pos)) {
			$rsess = SocketEncoder::getInt32BE($data, $pos);
			trigger_error("Reply for session $rsess");
			if ($sessid == $rsess) {
				$rtoken = SocketEncoder::getPStr($data, $pos);
				$rtoken = is_numeric($rtoken) ? (int)$rtoken : null;
				trigger_error("Got Token $rtoken");
				return $rtoken;
			}
		}
	}

	/**
	 * MCStatus::getFull() - get complete server info
	 *
	 * @return array
	 */
	public function getFull()
	{
		$this->socket = new UDPSocket($this->host, $this->port);
		$sessid = self::makeSessionID();
		$token = $this->getToken($sessid);
		if (!$token) {
			throw new Exception('Did not receive request token');
		}

		$packet = array_merge(
			array(0xFE, 0xFD, 0x00),
			SocketEncoder::int32BE($sessid),
			SocketEncoder::int32BE($token),
			SocketEncoder::int32BE($token)
		);
		$this->socket->send(SocketEncoder::asRaw($packet));
		$str = $this->socket->receive(0xFFFFFF);
		$data = SocketEncoder::asArray($str);

		$result = array('info' => array(), 'players' => array());

		$pos = 1;
		if (0x00 == SocketEncoder::getByte($data, $pos)) {
			$rsess = SocketEncoder::getInt32BE($data, $pos);
			if ($sessid == $rsess) {
				if ('splitnum' !== SocketEncoder::getPStr($data, $pos)) return;
				if (0x8000 !== SocketEncoder::getInt16BE($data, $pos)) return;
				$save = $pos;
				while(0x0001 !== SocketEncoder::getInt16BE($data, $pos)){
					$pos = $save;
					$key = SocketEncoder::getPStr($data, $pos);
					$value = SocketEncoder::getPStr($data, $pos);
					$result['info'][$key] = $value;
					$save = $pos;
				}
				if (isset($result['info']['plugins'])) {
					$p = $result['info']['plugins'];
					list($server, $pl) = array_map('trim',explode(':',$p));
					$plugins = array_map('trim',explode(';',$pl));
					$result['info']['bukkit-server'] = $server;
					$result['info']['bukkit-plugins'] = $plugins;
				}
				if ('player_' !== SocketEncoder::getPStr($data, $pos)) return;
				if (0x00 !== SocketEncoder::getByte($data, $pos)) return;
				while('' !== ($p = SocketEncoder::getPStr($data, $pos))){
					$result['players'][] = $p;
				}
				return $result;
			}
		}
	}

	/**
	 * MCStatus::getBasic() - get short server info
	 *
	 * @return array
	 */
	public function getBasic()
	{
		$this->socket = new UDPSocket($this->host, $this->port);
		$sessid = self::makeSessionId();
		$token = $this->getToken($sessid);
		if (!$token) {
			throw new Exception('Did not receive request token');
		}

		$packet = array_merge(
			array(0xFE, 0xFD, 0x00),
			SocketEncoder::int32BE($sessid),
			SocketEncoder::int32BE($token)
			);
		$this->socket->send(SocketEncoder::asRaw($packet));
		$str = $this->socket->receive(0xFFFFFF);
		$data = SocketEncoder::asArray($str);

		$pos = 1;
		if (0x00 == SocketEncoder::getByte($data, $pos)) {
			$rsess = SocketEncoder::getInt32BE($data, $pos);
			if ($sessid == $rsess) {
				$result = array(
					"motd" => SocketEncoder::getPStr($data, $pos),
					"gametype" => SocketEncoder::getPStr($data, $pos),
					"map" => SocketEncoder::getPStr($data, $pos),
					"numplayers" => SocketEncoder::getPStr($data, $pos),
					"maxplayers" => SocketEncoder::getPStr($data, $pos),
					"hostport" => SocketEncoder::getInt16($data, $pos),
					"hostip" => SocketEncoder::getPStr($data, $pos)
				);

				return $result;
			}
		}
	}

	/**
	 * MCStatus::makeSessionId()
	 *
	 * Always use this to make sure the ID is 7-bit-ASCII.
	 * The server will choke on everything else and reply with garbage!
	 *
	 * @return int32 suitable as session ID
	 */
	public static function makeSessionId($sample = NULL)
	{
		if (is_null($sample)) {
			$sample = (microtime(true) * 10000);
		}
		$k = (int)$sample & 0x7F7F7F7F;
		return $k;
	}
}
?>
