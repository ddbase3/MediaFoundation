<?php

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
