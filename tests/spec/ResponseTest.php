<?php

use cebe\openapi\Reader;
use cebe\openapi\spec\MediaType;
use cebe\openapi\spec\Response;

/**
 * @covers \cebe\openapi\spec\Response
 */
class ResponseTest extends \PHPUnit\Framework\TestCase
{
    public function testRead()
    {
        /** @var $response Response */
        $response = Reader::readFromJson(<<<'JSON'
{
  "description": "A complex object array response",
  "content": {
    "application/json": {
      "schema": {
        "type": "array",
        "items": {
          "$ref": "#/components/schemas/VeryComplexType"
        }
      }
    }
  }
}
JSON
        , Response::class);

        $result = $response->validate();
        $this->assertEquals([], $response->getErrors());
        $this->assertTrue($result);

        $this->assertEquals('A complex object array response', $response->description);
        $this->assertArrayHasKey("application/json", $response->content);
        $this->assertInstanceOf(MediaType::class, $response->content["application/json"]);

        /** @var $response Response */
        $response = Reader::readFromJson(<<<'JSON'
{
  "content": {
    "application/json": {
      "schema": {
        "type": "array",
        "items": {
          "$ref": "#/components/schemas/VeryComplexType"
        }
      }
    }
  }
}
JSON
        , Response::class);

        $result = $response->validate();
        $this->assertEquals([
            'Response is missing required property: description',
        ], $response->getErrors());
        $this->assertFalse($result);
    }
}