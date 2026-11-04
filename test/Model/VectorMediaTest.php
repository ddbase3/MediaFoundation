<?php declare(strict_types=1);

namespace MediaFoundation\Test\Model;

use MediaFoundation\Api\IMedia;
use MediaFoundation\Api\IVectorMedia;
use MediaFoundation\Model\VectorMedia;
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
