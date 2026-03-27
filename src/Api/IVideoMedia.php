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
