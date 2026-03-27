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

use MediaFoundation\Api\IBinaryMedia;

/**
 * Generic binary media implementation.
 * Represents any binary content, such as PNG, PDF, ZIP, etc.
 */
class BinaryMedia implements IBinaryMedia {

	protected string $data;
	protected string $mimeType;

	public function __construct(string $data, string $mimeType) {
		$this->data = $data;
		$this->mimeType = $mimeType;
	}

	public function getMimeType(): string {
		return $this->mimeType;
	}

	public function getSize(): int {
		return strlen($this->data);
	}

	public function getData(): string {
		return $this->data;
	}
}
