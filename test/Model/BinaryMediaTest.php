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
use Base3\MediaFoundation\Api\IMedia;
use Base3\MediaFoundation\Model\BinaryMedia;
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
