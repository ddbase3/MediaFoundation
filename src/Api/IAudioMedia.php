<?php

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
