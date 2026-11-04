<?php declare(strict_types=1);

namespace MediaFoundation\Test\Model;

use MediaFoundation\Api\IAudioMedia;
use MediaFoundation\Api\IBinaryMedia;
use MediaFoundation\Model\AudioMedia;
use MediaFoundation\Model\BinaryMedia;
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
