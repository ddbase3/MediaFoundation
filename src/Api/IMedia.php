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
 * Base interface for all media objects.
 * Represents an in-memory media payload with basic metadata.
 */
interface IMedia {

	/**
	 * Returns the MIME type of this media, e.g. "image/png" or "application/pdf".
	 */
	public function getMimeType(): string;

	/**
	 * Returns the size of the media in bytes.
	 */
	public function getSize(): int;

	/**
	 * Returns the raw binary data of this media.
	 */
	public function getData(): string;
}
