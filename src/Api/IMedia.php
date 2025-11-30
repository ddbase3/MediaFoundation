<?php

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
