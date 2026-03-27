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
