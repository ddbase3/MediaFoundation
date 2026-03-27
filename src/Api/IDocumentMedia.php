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
