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

namespace Base3\MediaFoundation\Test\Exception;

use Base3\MediaFoundation\Exception\ImageRenderException;
use Base3\MediaFoundation\Exception\InvalidImageEditException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MediaExceptionTest extends TestCase {

	public function testImageRenderExceptionPreservesMessageAndCode(): void {
		$exception = new ImageRenderException('Unable to decode media', 42);

		$this->assertInstanceOf(RuntimeException::class, $exception);
		$this->assertSame('Unable to decode media', $exception->getMessage());
		$this->assertSame(42, $exception->getCode());
	}

	public function testInvalidImageEditExceptionPreservesMessageAndCode(): void {
		$exception = new InvalidImageEditException('Invalid crop rectangle', 400);

		$this->assertInstanceOf(RuntimeException::class, $exception);
		$this->assertSame('Invalid crop rectangle', $exception->getMessage());
		$this->assertSame(400, $exception->getCode());
	}

	public function testExceptionTypesRemainDistinct(): void {
		$renderError = new ImageRenderException('render');
		$editError = new InvalidImageEditException('edit');

		$this->assertFalse($renderError instanceof InvalidImageEditException);
		$this->assertFalse($editError instanceof ImageRenderException);
	}

	public function testImageExceptionsPreservePreviousException(): void {
		$previous = new RuntimeException('Native backend error');
		$renderError = new ImageRenderException('Failed', 0, $previous);
		$editError = new InvalidImageEditException('Invalid', 0, $previous);

		$this->assertSame($previous, $renderError->getPrevious());
		$this->assertSame($previous, $editError->getPrevious());
	}
}
