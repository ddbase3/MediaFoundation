<?php

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
