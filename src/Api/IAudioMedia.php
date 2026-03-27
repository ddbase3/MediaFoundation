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
 * Audio media, e.g. MP3, WAV, OGG.
 */
interface IAudioMedia extends IBinaryMedia {

	/**
	 * Returns the duration in seconds (may be approximate).
	 */
	public function getDuration(): float;

	/**
	 * Returns the bitrate in kbps if available, otherwise 0 or a best-effort value.
	 */
	public function getBitrate(): int;
}
