<?php

namespace MediaFoundation\Api;

/**
 * Image media (bitmap-based formats like PNG, JPEG, WebP, etc.).
 */
interface IImageMedia extends IBinaryMedia {

	/**
	 * Returns the image width in pixels.
	 */
	public function getWidth(): int;

	/**
	 * Returns the image height in pixels.
	 */
	public function getHeight(): int;

	/**
	 * Returns the image format, e.g. "png", "jpg", "webp".
	 */
	public function getFormat(): string;
}
