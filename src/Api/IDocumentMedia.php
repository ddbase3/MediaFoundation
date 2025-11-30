<?php

namespace MediaFoundation\Api;

/**
 * Document media, e.g. PDF, DOCX, XLSX, CSV.
 */
interface IDocumentMedia extends IBinaryMedia {

	/**
	 * Returns the document format, e.g. "pdf", "docx", "xlsx", "csv".
	 */
	public function getFormat(): string;

	/**
	 * Returns the page count if the format supports pages (e.g. PDF),
	 * or null if not applicable or unknown.
	 */
	public function getPageCount(): ?int;
}
