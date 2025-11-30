<?php

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
