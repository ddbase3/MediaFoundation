<?php

namespace MediaFoundation\Api;

/**
 * Video media, e.g. MP4, WebM.
 */
interface IVideoMedia extends IBinaryMedia {

	/**
	 * Returns the duration in seconds (may be approximate).
	 */
	public function getDuration(): float;

	/**
	 * Returns the video width in pixels.
	 */
	public function getWidth(): int;

	/**
	 * Returns the video height in pixels.
	 */
	public function getHeight(): int;
}
