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

use MediaFoundation\Api\IVectorMedia;

/**
 * Vector-based media such as SVG or EPS.
 */
class VectorMedia implements IVectorMedia {

	protected string $mimeType;
	protected string $format;
	protected string $data;
	protected ?string $xml;

	/**
	 * @param string      $data
	 * @param string      $mimeType
	 * @param string      $format  Format such as "svg"
	 * @param string|null $xml     Optional XML representation (for SVG)
	 */
	public function __construct(string $data, string $mimeType, string $format, ?string $xml = null) {
		$this->data = $data;
		$this->mimeType = $mimeType;
		$this->format = $format;
		$this->xml = $xml;
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

	public function getFormat(): string {
		return $this->format;
	}

	public function getXml(): ?string {
		return $this->xml;
	}
}
