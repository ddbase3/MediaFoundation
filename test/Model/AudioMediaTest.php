<?php declare(strict_types=1);

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

namespace Base3\MediaFoundation\Test\Model;

use Base3\MediaFoundation\Api\IAudioMedia;
use Base3\MediaFoundation\Api\IBinaryMedia;
use Base3\MediaFoundation\Model\AudioMedia;
use Base3\MediaFoundation\Model\BinaryMedia;
use PHPUnit\Framework\TestCase;

final class AudioMediaTest extends TestCase {

	public function testAudioPreservesDurationBitrateAndPayload(): void {
		$data = "ID3\x00\xffaudio";
		$audio = new AudioMedia($data, 'audio/mpeg', 125.75, 320);

		$this->assertInstanceOf(BinaryMedia::class, $audio);
		$this->assertInstanceOf(IBinaryMedia::class, $audio);
		$this->assertInstanceOf(IAudioMedia::class, $audio);
		$this->assertSame($data, $audio->getData());
		$this->assertSame(strlen($data), $audio->getSize());
		$this->assertSame('audio/mpeg', $audio->getMimeType());
		$this->assertSame(125.75, $audio->getDuration());
		$this->assertSame(320, $audio->getBitrate());
	}

	public function testUnknownBitrateAndZeroDurationAreAccepted(): void {
		$audio = new AudioMedia('', 'audio/wav', 0.0, 0);

		$this->assertSame(0.0, $audio->getDuration());
		$this->assertSame(0, $audio->getBitrate());
		$this->assertSame(0, $audio->getSize());
	}

	public function testFractionalDurationIsNotRounded(): void {
		$audio = new AudioMedia('a', 'audio/ogg', 0.125, 192);

		$this->assertSame(0.125, $audio->getDuration());
		$this->assertSame(192, $audio->getBitrate());
	}
}
