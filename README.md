# Data Serializer for Better Storage and Exchange

[![Build Status](https://travis-ci.org/Oire/colloportus.svg?branch=master)](https://travis-ci.org/Oire/serializer)
[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/Oire/serializer/blob/master/LICENSE)

Serializes various data to different formats. There is also a possibility to additionally encode the output to URL-and filename-safe base64.  
Depends on [Oire Base64](https://github.com/Oire/base64) for encoding binary data to a storable format.

## Requirements

Requires PHP 7.1 or later with JSON support enabled.  
**Note**: If you want to use MessagePack serialization, the [MsgPack](https://pecl.php.net/package/msgpack) PECL extension is required. If you have PECL available, execute:

```bash
pecl install msgpack
```

## Installation

Via [Composer](https://getcomposer.org/):

`composer require oire/serializer`

Or manually. Note that you will need `base64.php` from [Oire Base64](https://github.com/Oire/base64/):

```php
require_once("oire/base64.php");
require_once("oire/serializer.php");
```

## Running Tests
Run `phpunit` in the projects directory.

## Usage Examples

Serialization to JSON. Note that you can either call methods consequently or chain them, like this:

```php
use \Oire\Serializer;
$data = ["fruit" => "orange", "vegetable" => "carrot", "money" => 3000, "randomArray" => [1, 2, 3, 4, 5], "Lambë" => "Українська"];
try {
	// You can pass "j", "json" or 1 to setMode() to get JSON
	$jsonSerialized = (new Serializer())->setMode("json")->serialize($data);
} catch(Exception $e) {
	// Handle errors
}
```

This will output:

```json
{"fruit":"orange","vegetable":"carrot","money":3000,"randomArray":[1,2,3,4,5],"Lambë":"Українська"}
```

Now unserializing:

```php
// Without chaining it will be like this
$s = new Serializer();
// You may wrap this also with try...catch
$s->setMode("j");
try {
	$unserialized = $s->unserialize($jsonSerialized);
} catch(Exception $e) {
	// Handle errors
}
```

This will return the original array. If you want to get an object instead, pass `false` as the third parameter to `unserialize()`.  
If you pass `true` as the second parameter to `serialize()`, the serialized data will be additionally encoded to URL-and filename-safe base64. This might be particularly useful if you choose a binary format such as MessagePack:

```php
$msgPackSerialized = (new Serializer())->setMode("mp")->serialize($data, true);
```

This will output:

```
haVmcnVpdKZvcmFuZ2WpdmVnZXRhYmxlpmNhcnJvdKVtb25lec0LuKtyYW5kb21BcnJheZUBAgMEBaZMYW1iw6u00KPQutGA0LDRl9C90YHRjNC60LA
```

## Supported Serialization Modes

Currently the following modes are supported:
* [JSON](http://json.org/). Pass `1`, `"j"` or `"json"` to `setMode()` to set this mode.
* [MessagePack](http://msgpack.org/). Pass `2`, `"m"`, `"mp"`, `"msgpack"` or `"messagepack"` to `setMode()` to set this mode. Note that the corresponding [PECL extension](https://pecl.php.net/package/msgpack) should be installed for this to work.

## Methods

The methods are documented in the source file, but their description is given below.  
We recommend to wrap every call in `try...catch` since Oirë Serializer throws exceptions in case of errors.

* Class constructor. You might provide a serialization mode when calling the constructor or directly call `setMode()`.
* `setMode(int|string $mode)`. Accepts either a numeric representation of the mode or a readable string such as `"json"`. See the Supported Modes section above. Chainable, so returns the current class instance.
* `getMode(bool $asString = false): int|string`. Gets the current serialization mode set by `setMode()`. If `$asString` is set to `true`, returns a readable mode name such as `"json"`, a numeric representation is returned otherwise (it is the default behavior). Throws an exception if the mode is not set or if it could not be found.
* `getAvailableModes($json = false): array|string`. Gets all available serialization modes. If the `$json` parameter is set to `true`, returns them as a JSON string, an associative array is returned otherwise (this is the default behavior).
* `serialize(mixed $data, bool $base64 = false): string`. Serializes given data according to the serialization mode set with `setMode()`. If `$base64` is set to `true`, additionally encodes the serialized data to URL-and filename-safe base64 (particularly useful for binary serialization formats such as MessagePack). If set to `false` (default), the serialized data is returned as a string, be it binary or not.
* `unserialize(string $data, bool $base64 = false, bool $assoc = true): mixed`. Unserializes given data according to the serialization mode set with `setMode()`. If `$base64` is set to `true`, assumes that the data had been additionally encoded to URL-safe base64 after serialization. If `$assoc` is set to `true` (default), returns an associative array, an object is returned otherwise. Note that the last parameter is applicable only for JSON serialization.

## License
Copyright © 2017, Andre Polykanine also known as Menelion Elensúlë.  
This software is licensed under an MIT license.