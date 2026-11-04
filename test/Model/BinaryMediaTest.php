<?php declare(strict_types=1);

namespace MediaFoundation\Test\Model;

use MediaFoundation\Api\IBinaryMedia;
use MediaFoundation\Api\IMedia;
use MediaFoundation\Model\BinaryMedia;
use PHPUnit\Framework\TestCase;

final class BinaryMediaTest extends TestCase {

	public function testBinaryPayloadIsReturnedWithoutModification(): void {
		$data = "\x00\xff\x10\x00raw\n\xfe";
		$media = new BinaryMedia($data, 'application/octet-stream');

		$this->assertInstanceOf(IMedia::class, $media);
		$this->assertInstanceOf(IBinaryMedia::class, $media);
		$this->assertSame($data, $media->getData());
		$this->assertSame(strlen($data), $media->getSize());
		$this->assertSame('application/octet-stream', $media->getMimeType());
	}

	public function testEmptyPayloadHasZeroByteSize(): void {
		$media = new BinaryMedia('', 'application/empty');

		$this->assertSame('', $media->getData());
		$this->assertSame(0, $media->getSize());
		$this->assertSame('application/empty', $media->getMimeType());
	}

	public function testSizeCountsBytesRatherThanCharacters(): void {
		$data = "Gr\xc3\xbc\xc3\x9fe \xf0\x9f\x98\x80";
		$media = new BinaryMedia($data, 'text/plain');

		$this->assertSame(strlen($data), $media->getSize());
		$this->assertSame($data, $media->getData());
	}

	public function testMimeTypeIsPreservedExactly(): void {
		$media = new BinaryMedia('payload', 'Application/Custom; charset=binary');

		$this->assertSame('Application/Custom; charset=binary', $media->getMimeType());
		$this->assertSame(7, $media->getSize());
	}
}
