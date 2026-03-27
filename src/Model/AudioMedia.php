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

use MediaFoundation\Api\IAudioMedia;

/**
 * Audio media, e.g. MP3, WAV, OGG.
 * Metadata may be best-effort depending on how the media was created.
 */
class AudioMedia extends BinaryMedia implements IAudioMedia {

	protected float $duration;
	protected int $bitrate;

	/**
	 * @param string $data
	 * @param string $mimeType
	 * @param float  $duration In seconds
	 * @param int    $bitrate  kbps
	 */
	public function __construct(string $data, string $mimeType, float $duration, int $bitrate) {
		parent::__construct($data, $mimeType);
		$this->duration = $duration;
		$this->bitrate = $bitrate;
	}

	public function getDuration(): float {
		return $this->duration;
	}

	public function getBitrate(): int {
		return $this->bitrate;
	}
}
