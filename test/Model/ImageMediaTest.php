<?php declare(strict_types=1);

namespace MediaFoundation\Test\Model;

use MediaFoundation\Api\IBinaryMedia;
use MediaFoundation\Api\IImageMedia;
use MediaFoundation\Api\IMedia;
use MediaFoundation\Model\BinaryMedia;
use MediaFoundation\Model\ImageMedia;
use PHPUnit\Framework\TestCase;

final class ImageMediaTest extends TestCase {

	public function testImagePreservesDimensionsFormatAndBinaryPayload(): void {
		$data = "\x89PNG\r\n\x1a\n\x00\xff";
		$image = new ImageMedia($data, 'image/png', 800, 600, 'png');

		$this->assertInstanceOf(IMedia::class, $image);
		$this->assertInstanceOf(IBinaryMedia::class, $image);
		$this->assertInstanceOf(IImageMedia::class, $image);
		$this->assertInstanceOf(BinaryMedia::class, $image);
		$this->assertSame($data, $image->getData());
		$this->assertSame(strlen($data), $image->getSize());
		$this->assertSame('image/png', $image->getMimeType());
		$this->assertSame(800, $image->getWidth());
		$this->assertSame(600, $image->getHeight());
		$this->assertSame('png', $image->getFormat());
	}

	public function testMetadataDoesNotDependOnDecodingOrValidatingImage(): void {
		$image = new ImageMedia('not a real image', 'image/jpeg', 1, 4096, 'jpeg');

		$this->assertSame('not a real image', $image->getData());
		$this->assertSame(1, $image->getWidth());
		$this->assertSame(4096, $image->getHeight());
		$this->assertSame('jpeg', $image->getFormat());
	}

	public function testZeroDimensionAndEmptyPayloadRemainCallerSuppliedMetadata(): void {
		$image = new ImageMedia('', 'image/webp', 0, 0, 'webp');

		$this->assertSame(0, $image->getSize());
		$this->assertSame(0, $image->getWidth());
		$this->assertSame(0, $image->getHeight());
		$this->assertSame('', $image->getData());
	}
}
