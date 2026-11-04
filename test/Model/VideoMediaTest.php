<?php declare(strict_types=1);

namespace MediaFoundation\Test\Model;

use MediaFoundation\Api\IBinaryMedia;
use MediaFoundation\Api\IVideoMedia;
use MediaFoundation\Model\BinaryMedia;
use MediaFoundation\Model\VideoMedia;
use PHPUnit\Framework\TestCase;

final class VideoMediaTest extends TestCase {

	public function testVideoPreservesDurationDimensionsAndBinaryPayload(): void {
		$data = "\x00\x00\x00\x20ftypmp42\xff";
		$video = new VideoMedia($data, 'video/mp4', 13.5, 1920, 1080);

		$this->assertInstanceOf(BinaryMedia::class, $video);
		$this->assertInstanceOf(IBinaryMedia::class, $video);
		$this->assertInstanceOf(IVideoMedia::class, $video);
		$this->assertSame($data, $video->getData());
		$this->assertSame(strlen($data), $video->getSize());
		$this->assertSame('video/mp4', $video->getMimeType());
		$this->assertSame(13.5, $video->getDuration());
		$this->assertSame(1920, $video->getWidth());
		$this->assertSame(1080, $video->getHeight());
	}

	public function testEmptyPayloadAndZeroMetadataArePreserved(): void {
		$video = new VideoMedia('', 'video/webm', 0.0, 0, 0);

		$this->assertSame('', $video->getData());
		$this->assertSame(0, $video->getSize());
		$this->assertSame(0.0, $video->getDuration());
		$this->assertSame(0, $video->getWidth());
		$this->assertSame(0, $video->getHeight());
	}

	public function testFractionalDurationIsNotRounded(): void {
		$video = new VideoMedia('x', 'video/mp4', 0.001, 1, 2);

		$this->assertSame(0.001, $video->getDuration());
		$this->assertSame(1, $video->getWidth());
		$this->assertSame(2, $video->getHeight());
	}
}
