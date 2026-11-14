<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of MediaFoundation for BASE3 Framework.
 *
 * MediaFoundation extends the BASE3 framework with a unified API
 * foundation for working with images, video, and audio.
 * It provides shared interfaces for modular media processing.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de/v/mediafoundation
 * https://github.com/ddbase3/MediaFoundation
 **********************************************************************/

namespace Base3\MediaFoundation\Test\Model;

use Base3\MediaFoundation\Api\IBinaryMedia;
use Base3\MediaFoundation\Api\IImageMedia;
use Base3\MediaFoundation\Api\IMedia;
use Base3\MediaFoundation\Model\BinaryMedia;
use Base3\MediaFoundation\Model\ImageMedia;
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
