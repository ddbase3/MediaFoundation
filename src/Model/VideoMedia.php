<?php

namespace MediaFoundation\Model;

use MediaFoundation\Api\IVideoMedia;

/**
 * Video media, e.g. MP4, WebM.
 */
class VideoMedia extends BinaryMedia implements IVideoMedia {

	protected float $duration;
	protected int $width;
	protected int $height;

	/**
	 * @param string $data
	 * @param string $mimeType
	 * @param float  $duration In seconds
	 * @param int    $width
	 * @param int    $height
	 */
	public function __construct(string $data, string $mimeType, float $duration, int $width, int $height) {
		parent::__construct($data, $mimeType);
		$this->duration = $duration;
		$this->width = $width;
		$this->height = $height;
	}

	public function getDuration(): float {
		return $this->duration;
	}

	public function getWidth(): int {
		return $this->width;
	}

	public function getHeight(): int {
		return $this->height;
	}
}
