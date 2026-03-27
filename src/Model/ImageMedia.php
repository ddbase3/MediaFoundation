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

use MediaFoundation\Api\IImageMedia;

/**
 * Bitmap-based image media (PNG, JPEG, WebP, etc.).
 */
class ImageMedia extends BinaryMedia implements IImageMedia {

	protected int $width;
	protected int $height;
	protected string $format;

	/**
	 * @param string $data     Raw image data
	 * @param string $mimeType MIME type of the image
	 * @param int    $width    Image width in pixels
	 * @param int    $height   Image height in pixels
	 * @param string $format   Image format (e.g. "png", "jpg")
	 */
	public function __construct(string $data, string $mimeType, int $width, int $height, string $format) {
		parent::__construct($data, $mimeType);
		$this->width = $width;
		$this->height = $height;
		$this->format = $format;
	}

	public function getWidth(): int {
		return $this->width;
	}

	public function getHeight(): int {
		return $this->height;
	}

	public function getFormat(): string {
		return $this->format;
	}
}
