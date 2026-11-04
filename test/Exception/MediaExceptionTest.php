<?php declare(strict_types=1);

namespace MediaFoundation\Test\Exception;

use MediaFoundation\Exception\ImageRenderException;
use MediaFoundation\Exception\InvalidImageEditException;
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
