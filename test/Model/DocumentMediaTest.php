<?php declare(strict_types=1);

namespace MediaFoundation\Test\Model;

use MediaFoundation\Api\IDocumentMedia;
use MediaFoundation\Api\IBinaryMedia;
use MediaFoundation\Model\BinaryMedia;
use MediaFoundation\Model\DocumentMedia;
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
