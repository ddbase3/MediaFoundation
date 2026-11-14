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

use Base3\MediaFoundation\Api\IDocumentMedia;
use Base3\MediaFoundation\Api\IBinaryMedia;
use Base3\MediaFoundation\Model\BinaryMedia;
use Base3\MediaFoundation\Model\DocumentMedia;
use PHPUnit\Framework\TestCase;

final class DocumentMediaTest extends TestCase {

	public function testDocumentPreservesFormatPageCountAndBinaryData(): void {
		$data = "%PDF-1.7\n\x00\xff";
		$document = new DocumentMedia($data, 'application/pdf', 'pdf', 45);

		$this->assertInstanceOf(BinaryMedia::class, $document);
		$this->assertInstanceOf(IBinaryMedia::class, $document);
		$this->assertInstanceOf(IDocumentMedia::class, $document);
		$this->assertSame($data, $document->getData());
		$this->assertSame(strlen($data), $document->getSize());
		$this->assertSame('application/pdf', $document->getMimeType());
		$this->assertSame('pdf', $document->getFormat());
		$this->assertSame(45, $document->getPageCount());
	}

	public function testUnknownPageCountDefaultsToNull(): void {
		$document = new DocumentMedia('csv,data', 'text/csv', 'csv');

		$this->assertNull($document->getPageCount());
		$this->assertSame('csv', $document->getFormat());
		$this->assertSame(8, $document->getSize());
	}

	public function testExplicitNullAndZeroPageCountRemainDistinct(): void {
		$unknown = new DocumentMedia('', 'text/plain', 'txt', null);
		$zero = new DocumentMedia('', 'application/pdf', 'pdf', 0);

		$this->assertNull($unknown->getPageCount());
		$this->assertSame(0, $zero->getPageCount());
		$this->assertSame(0, $zero->getSize());
	}
}
