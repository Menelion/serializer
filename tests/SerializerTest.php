<?php
use Oire\Serializer;
use PHPUnit\Framework\TestCase;

/**
 @requires php 7.1
*/

class SerializerTest extends TestCase {
	protected $rawData;
	protected $jsonData;
	protected $base64JsonData;
	protected $msgPackData;
	protected $base64MsgPackData;
	protected $igbinaryData;
	protected $base64IgbinaryData;
	protected $s; // Serializer instance

	protected function setUp() {
		$this->rawData = ["fruit" => "orange", "vegetable" => "carrot", "money" => 3000, "randomArray" => [1, 2, 3, 4, 5], "Lambë" => "Українська"];
		$this->jsonData = '{"fruit":"orange","vegetable":"carrot","money":3000,"randomArray":[1,2,3,4,5],"Lambë":"Українська"}';
		$this->base64JsonData = "eyJmcnVpdCI6Im9yYW5nZSIsInZlZ2V0YWJsZSI6ImNhcnJvdCIsIm1vbmV5IjozMDAwLCJyYW5kb21BcnJheSI6WzEsMiwzLDQsNV0sIkxhbWLDqyI6ItCj0LrRgNCw0ZfQvdGB0YzQutCwIn0";
		$this->msgPackData = hex2bin("85a56672756974a66f72616e6765a9766567657461626c65a6636172726f74a56d6f6e6579cd0bb8ab72616e646f6d4172726179950102030405a64c616d62c3abb4d0a3d0bad180d0b0d197d0bdd181d18cd0bad0b0"); // Binary safe
		$this->base64MsgPackData = "haVmcnVpdKZvcmFuZ2WpdmVnZXRhYmxlpmNhcnJvdKVtb25lec0LuKtyYW5kb21BcnJheZUBAgMEBaZMYW1iw6u00KPQutGA0LDRl9C90YHRjNC60LA";
		$this->igbinaryData = hex2bin("0000000214051105667275697411066f72616e67651109766567657461626c651106636172726f7411056d6f6e6579080bb8110b72616e646f6d41727261791405060006010601060206020603060306040604060511064c616d62c3ab1114d0a3d0bad180d0b0d197d0bdd181d18cd0bad0b0"); // Binary safe
		$this->base64IgbinaryData = "AAAAAhQFEQVmcnVpdBEGb3JhbmdlEQl2ZWdldGFibGURBmNhcnJvdBEFbW9uZXkIC7gRC3JhbmRvbUFycmF5FAUGAAYBBgEGAgYCBgMGAwYEBgQGBREGTGFtYsOrERTQo9C60YDQsNGX0L3RgdGM0LrQsA";
		$this->s = new Serializer();
	}

	public function testJsonSerialization() {
		$this->s->setMode("json");
		$this->assertSame($this->s->getMode(), 1);
		$this->assertSame($this->s->getMode(true), "json");
		$this->assertSame($this->s->serialize($this->rawData), $this->jsonData);
		$this->assertNotSame($this->s->serialize($this->rawData), json_encode($this->rawData));
		$this->assertSame($this->s->serialize($this->rawData, true), $this->base64JsonData);
		$this->assertNotSame($this->s->serialize($this->rawData, true), $this->jsonData);
		$this->assertSame($this->s->unserialize($this->jsonData), $this->rawData);
		$this->assertSame($this->s->unserialize($this->base64JsonData, true), $this->rawData);
	}

	public function testMessagePackSerialization() {
		$this->s->setMode("msgpack");
		$this->assertSame($this->s->getMode(), 2);
		$this->assertSame($this->s->getMode(true), "msgpack");
		$this->assertSame($this->s->serialize($this->rawData), $this->msgPackData);
		$this->assertSame($this->s->serialize($this->rawData, true), $this->base64MsgPackData);
		$this->assertNotSame($this->s->serialize($this->rawData, true), $this->msgPackData);
		$this->assertSame($this->s->unserialize($this->msgPackData), $this->rawData);
		$this->assertSame($this->s->unserialize($this->base64MsgPackData, true), $this->rawData);
	}

	public function testIgbinarySerialization() {
		$this->s->setMode("igbinary");
		$this->assertSame($this->s->getMode(), 3);
		$this->assertSame($this->s->getMode(true), "igbinary");
		$this->assertSame($this->s->serialize($this->rawData), $this->igbinaryData);
		$this->assertSame($this->s->serialize($this->rawData, true), $this->base64IgbinaryData);
		$this->assertNotSame($this->s->serialize($this->rawData, true), $this->igbinaryData);
		$this->assertSame($this->s->unserialize($this->igbinaryData), $this->rawData);
		$this->assertSame($this->s->unserialize($this->base64IgbinaryData, true), $this->rawData);
	}
}
?>