<?php
namespace Oire;

/**
 * Oirë Data Serializer
 * Copyright © 2017 Andre Polykanine also known as Menelion Elensúlë, The magical kingdom of Oirë, https://github.com/Oire
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
*/

/**
 * Class Serializer
 * Data serializer. All supported modes are listed in the $allModes variable and can be gotten by calling getAvailableModes()
 * @package Serializer
*/
class Serializer {
	CONST JSON_DEFAULT_DEPTH = 512; // As per PHP manual

	public $allModes; // All available serialization modes
	public $mode; // Current serialization mode
	private $jsonErrors; // For JSON handling

	/**
	 * Class constructor
	 * @param $mode The serialization mode. All available modes are listed in the setMode() description and can be gotten with getAvailableModes(). May be null or omitted if you prefer to call setMode() directly
	 * @throws \Exception
	*/
	public function __construct($mode = null) {
		if (!empty($mode)) {
			try {
				$this->setMode($mode);
			} catch(\InvalidArgumentException $e) {
				throw new \Exception("Unable to set mode in class constructor: ".$e->getMessage());
			}
		}
		$this->allModes = [
			"json" => 1,
			"msgpack" => 2,
			"igbinary" => 3
		];
		$this->jsonErrors = [
			JSON_ERROR_DEPTH => "Maximum stack depth exceeded.",
			JSON_ERROR_STATE_MISMATCH => "Malformed JSON.",
			JSON_ERROR_CTRL_CHAR => "Control character error.",
			JSON_ERROR_SYNTAX => "JSON Syntax error.",
			JSON_ERROR_UTF8 => "Invalid or non-UTF-8 characters.",
			JSON_ERROR_RECURSION => "Data contains recursive references and cannot be encoded.",
			JSON_ERROR_INF_OR_NAN => "Data contains infinity or NaN and cannot be encoded.",
			JSON_ERROR_UNSUPPORTED_TYPE => "The data is of unsupported type.",
			JSON_ERROR_UTF16 => "Invalid or non-UTF-16 characters."
		];
	}

	/**
	 * Sets the serialization mode.
	 * @param $mode The serialization mode: 1, "j" or "json" for JSON; 2, "m", "mp", "msgpack" or "messagepack" for MessagePack; 3, "i", "ib", "ig" or "igbinary" for Igbinary
	 * @return $this for chainability
	 * @throws \InvalidArgumentException
	*/
	public function setMode($mode) {
		if (empty($mode)) {
			throw new \InvalidArgumentException("The serialization mode cannot be empty.");
		}
		$mode = strtolower((string)$mode);
		switch ($mode) {
			case 1:
			case "j":
			case "json":
				$this->mode = $this->allModes['json'];
			break;
			case 2:
			case "m":
			case "mp":
			case "msgpack":
			case "messagepack":
				$this->mode = $this->allModes['msgpack'];
			break;
			case 3:
			case "i":
			case "ib":
			case "ig":
			case "igbinary":
				$this->mode = $this->allModes['igbinary'];
			break;
			default:
				throw new \InvalidArgumentException("Unsupported serialization mode.");
			break;
		}
		return $this;
	}

	/**
	 * Gets the current serialization mode.
	 * @param bool $asString If set to true, returns a readable string compatible with setMode(), such as "json" or "msgpack". If set to false (default), returns a numeric representation of the mode
	 * @return int|string Returns the mode being used. If $asString is set to true, returns a readable string. If set to false (default), returns a numeric representation
	 * @throws \Exception
	*/
	public function getMode($asString = false) {
		if (empty($this->mode)) {
			throw new \Exception("The mode is not set.");
		}
		if ($asString) {
			$mode = array_search($this->mode, $this->allModes);
			if (empty($mode)) {
				throw new \Exception("The mode string could not be found.");
			}
		} else {
			$mode = $this->mode;
		}
		return $mode;
	}

	/**
	 * Gets all available serialization modes.
	 * @param bool $json If set to true, returns all available modes as a JSON string, an associative array if set to false (default)
	 * @return array|string a JSON string if $json is set to true, an associative array if set to false (default)
	*/
	public function getAvailableModes($json = false) {
		return $json? $this->toJson($this->allModes): $this->allModes;
	}

	/**
	 * Wraps the built-in json_encode() method.
	 * @param mixed $data The data to be encoded into JSON
	 * @return string
	 * @throws \InvalidArgumentException if the data is empty
	 * @throws \RuntimeException if a JSON error occurs
	*/
	private function toJson($data) {
		if (empty($data)) {
			throw new \InvalidArgumentException("The data cannot be empty.");
			return "";
		}
		$encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		if ($encoded === false || $encoded === null) {
			$error = json_last_error();
			if (in_array($error, $this->jsonErrors)) {
				throw new \RuntimeException($this->jsonErrors[$error]);
			} else {
				throw new \RuntimeException("Unknown encoding error.");
			}
			return "";
		}
		return $encoded;
	}

	/**
	 * Wraps the built-in json_decode() method.
	 * @param string $data The data to be encoded into JSON
	 * @param bool $assoc If set to true (default), returns the decoded data as associative array. If set to false, an object is returned
	 * @return array|object
	* @throws \InvalidArgumentException if the data is empty
	 * @throws \RuntimeException if a JSON error occurs
	*/
	private function fromJson($data, $assoc = true) {
		if (empty($data)) {
			throw new \InvalidArgumentException("The data cannot be empty.");
			return "";
		}
		$decoded = json_decode($data, $assoc, self::JSON_DEFAULT_DEPTH, JSON_BIGINT_AS_STRING);
		if ($decoded === false || $decoded === null) {
			$error = json_last_error();
			if (in_array($error, $this->jsonErrors)) {
				throw new \RuntimeException($this->jsonErrors[$error]);
			} else {
				throw new \RuntimeException("Unknown decoding error.");
			}
			return "";
		}
		return $decoded;
	}

	/**
	 * Serializes the given data to a string.
	 * @param mixed $data The data to be serialized
	 * @param bool $base64 If set to true, the serialized string is additionally encoded to Base64 (uses Oirë Base64). If set to false (default), returns a serialized string according to the serialization mode
	 * @return string A base64-encoded (uses Oirë Base64) string, if $base64 is set to true, just serialized data otherwise. The data are serialized according to the current serialization mode
	 * @throws \InvalidArgumentException If the data is empty
	 * @throws \Exception
	*/
	public function serialize($data, $base64 = false) {
		if (empty($data)) {
			throw new \InvalidArgumentException("The data to be serialized cannot be empty.");
			return "";
		}
		switch ($this->mode) {
			case $this->allModes['json']:
				try {
					$serialized = $this->toJson($data);
				} catch(\Exception $e) {
					throw new \Exception("Serialization error: ".$e->getMessage());
					return "";
				}
			break;
			case $this->allModes['msgpack']:
				if (!function_exists("msgpack_pack")) {
					throw new \Exception("MessagePack encoding not available.");
					return "";
				}
				$serialized = msgpack_pack($data);
				if ($serialized === false || $serialized === null) {
					throw new \Exception("Encoding to MessagePack failed.");
					return "";
				}
			break;
			case $this->allModes['igbinary']:
				if (!function_exists("igbinary_serialize")) {
					throw new \Exception("Igbinary serialization not available.");
					return "";
				}
				$serialized = igbinary_serialize($data);
				if ($serialized === false || $serialized === null) {
					throw new \Exception("Serialization to Igbinary failed.");
					return "";
				}
			break;
			default:
				throw new \Exception("Unknown serialization mode.");
				return "";
			break;
		}
		if ($base64) {
			try {
				$serialized = \Oire\Base64::encode($serialized);
			} catch(\Exception $e) {
				throw new \Exception("Base64 encoding failed.");
				return "";
			}
		}
		return $serialized;
	}

	/**
	 * Unserializes the given data from a string.
	 * @param string $data The data to be unserialized
	 * @param bool $base64 If set to true, it is assumed that the data was additionally encoded to base64 using Oirë Base64. If set to false (default), unserializes the string according to the serialization mode
	 * @param bool $assoc Applies only to JSON serialization. If set to true (default), decodes the JSON string to an associative array. If set to false, an object is returned
	 * @return mixed The original data
	 * @throws \InvalidArgumentException If the data is empty or is not a string
	 * @throws \Exception
	*/
	public function unserialize($data, $base64 = false, $assoc = true) {
		if (empty($data)) {
			throw new \InvalidArgumentException("The data to be unserialized cannot be empty.");
		}
		if (!is_string($data)) {
			throw new \InvalidArgumentException("The serialized data must be a string.");
		}
		if ($base64) {
			try {
				$data = \Oire\Base64::decode($data);
			} catch(\Exception $e) {
				throw new \Exception("Base64 decoding failed.");
				return "";
			}
		}
		switch ($this->mode) {
			case $this->allModes['json']:
				try {
					$unserialized = $this->fromJson($data, $assoc);
				} catch(\Exception $e) {
					throw new \Exception("Unserialization error: ".$e->getMessage());
					return "";
				}
			break;
			case $this->allModes['msgpack']:
				if (!function_exists("msgpack_unpack")) {
					throw new \Exception("MessagePack decoding not available.");
					return "";
				}
				$unserialized = msgpack_unpack($data);
				if ($unserialized === false || $unserialized === null) {
					throw new \Exception("Decoding from MessagePack failed.");
					return "";
				}
			break;
			case $this->allModes['igbinary']:
				if (!function_exists("igbinary_unserialize")) {
					throw new \Exception("Igbinary unserialization not available.");
					return "";
				}
				$unserialized = igbinary_unserialize($data);
				if ($unserialized === false || $unserialized === null) {
					throw new \Exception("Unserialization from Igbinary failed.");
					return "";
				}
			break;
			default:
				throw new \Exception("Unknown serialization mode.");
			break;
		}
		return $unserialized;
	}
}
?>