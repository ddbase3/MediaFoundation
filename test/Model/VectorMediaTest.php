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

use Base3\MediaFoundation\Api\IMedia;
use Base3\MediaFoundation\Api\IVectorMedia;
use Base3\MediaFoundation\Model\VectorMedia;
use PHPUnit\Framework\TestCase;

final class VectorMediaTest extends TestCase {

	public function testVectorPreservesOriginalDataAndOptionalXmlSeparately(): void {
		$raw = "<svg>raw\x00\xff</svg>";
		$xml = '<svg viewBox="0 0 10 10"></svg>';
		$media = new VectorMedia($raw, 'image/svg+xml', 'svg', $xml);

		$this->assertInstanceOf(IMedia::class, $media);
		$this->assertInstanceOf(IVectorMedia::class, $media);
		$this->assertSame($raw, $media->getData());
		$this->assertSame(strlen($raw), $media->getSize());
		$this->assertSame('image/svg+xml', $media->getMimeType());
		$this->assertSame('svg', $media->getFormat());
		$this->assertSame($xml, $media->getXml());
	}

	public function testUnknownXmlDefaultsToNull(): void {
		$media = new VectorMedia('eps', 'application/postscript', 'eps');

		$this->assertNull($media->getXml());
		$this->assertSame('eps', $media->getFormat());
		$this->assertSame(3, $media->getSize());
	}

	public function testEmptyXmlIsNotTreatedAsUnknown(): void {
		$media = new VectorMedia('', 'image/svg+xml', 'svg', '');

		$this->assertSame('', $media->getXml());
		$this->assertSame('', $media->getData());
		$this->assertSame(0, $media->getSize());
	}

	public function testXmlIsNotAutomaticallySanitizedOrDecoded(): void {
		$xml = '<svg><script>test()</script></svg>';
		$media = new VectorMedia($xml, 'image/svg+xml', 'svg', $xml);

		$this->assertSame($xml, $media->getXml());
		$this->assertSame($xml, $media->getData());
	}
}
