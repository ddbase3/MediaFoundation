<?php

namespace MediaFoundation\Model;

use MediaFoundation\Api\IDocumentMedia;

/**
 * Document media, e.g. PDF, DOCX, XLSX, CSV.
 */
class DocumentMedia extends BinaryMedia implements IDocumentMedia {

	protected string $format;
	protected ?int $pageCount;

	/**
	 * @param string   $data
	 * @param string   $mimeType
	 * @param string   $format     Document format ("pdf", "docx", etc.)
	 * @param int|null $pageCount  Page count if applicable (or null)
	 */
	public function __construct(string $data, string $mimeType, string $format, ?int $pageCount = null) {
		parent::__construct($data, $mimeType);
		$this->format = $format;
		$this->pageCount = $pageCount;
	}

	public function getFormat(): string {
		return $this->format;
	}

	public function getPageCount(): ?int {
		return $this->pageCount;
	}
}
