<?php
/**
 * SocketEncoder - provides easy access to binary data, e.g. for networking
 *
 * @author Martok
 * @copyright Copyright (c) 2012
 */
class SocketEncoder{
	/**
	 * SocketEncoder::asRaw() - convert byte array to string
	 *
	 * @param array $array
	 * @return string
	 */
	public static function asRaw($array) {
		return call_user_func_array('pack', array_merge(array('C*'), $array));
	}

	/**
	 * SocketEncoder::asArray() - convert string to byte array
	 *
	 * @param string $string
	 * @return array
	 */
	public static function asArray($string) {
		return unpack('C*', $string);
	}

	static function chr2ord($chr) {
		return ord($chr) & 0xFF;
	}

	static function ord2chr($ord) {
		return chr($ord & 0xFF);
	}

	/**
	 * SocketEncoder::int32BE() - convert int32 into big-endian byte array
	 *
	 * @param int $int32
	 * @return array
	 */
	public static function int32BE($int32)
	{
		return array_reverse(self::int32($int32));
	}

	/**
	 * SocketEncoder::int32() - convert int32 into little-endian byte array
	 *
	 * @param int $int32
	 * @return array
	 */
	public static function int32($int32)
	{
		$x = (int) $int32;
		return array(
			($x & 0x000000FF),
			($x & 0x0000FF00) >> 8,
			($x & 0x00FF0000) >> 16,
			($x & 0xFF000000) >> 24
		);
	}

	/**
	 * SocketEncoder::getByte() - fetch one byte from $array and advance $pos
	 *
	 * @param array $array
	 * @param int &$pos
	 * @return byte
	 */
	public static function getByte($array, &$pos)
	{
		$b = $array[$pos++];
		return $b;
	}

	/**
	 * SocketEncoder::getInt16BE() - fetch signed int16 big-endian from $array and advance $pos
	 *
	 * @param array $array
	 * @param int &$pos
	 * @return int
	 */
	public static function getInt16BE($array, &$pos)
	{
		$b = $array[$pos++] & 0xFF;
		$b = ($b << 8) | $array[$pos++] & 0xFF;
		return $b;
	}

	/**
	 * SocketEncoder::getInt16BE() - fetch signed int16 little-endian from $array and advance $pos
	 *
	 * @param array $array
	 * @param int &$pos
	 * @return int
	 */
	public static function getInt16($array, &$pos)
	{
		$b = $array[$pos++] & 0xFF;
		$b = $b | ($array[$pos++] & 0xFF) << 8;
		return $b;
	}

	/**
	 * SocketEncoder::getInt16BE() - fetch signed int32 big-endian from $array and advance $pos
	 *
	 * @param array $array
	 * @param int &$pos
	 * @return int
	 */
	public static function getInt32BE($array, &$pos)
	{
		$b = $array[$pos++] & 0xFF;
		$b = ($b << 8) | $array[$pos++] & 0xFF;
		$b = ($b << 8) | $array[$pos++] & 0xFF;
		$b = ($b << 8) | $array[$pos++] & 0xFF;
		return $b;
	}

	/**
	 * SocketEncoder::getInt16BE() - fetch zero-terminated string from $array and advance $pos
	 *
	 * @param array $array
	 * @param int &$pos
	 * @return int
	 */
	public static function getPStr($array, &$pos)
	{
		$c = '';
		while(($pos <= count($array)) && ($t = $array[$pos++])){
			$c.=chr($t);
		}
		return $c;
	}
}

?>