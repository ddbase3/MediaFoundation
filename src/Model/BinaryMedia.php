<?php

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
