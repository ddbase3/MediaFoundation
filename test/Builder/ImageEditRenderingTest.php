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

namespace Base3\MediaFoundation\Test\Builder;

use Base3\MediaFoundation\Api\IImageRenderer;
use Base3\MediaFoundation\Builder\ImageEdit;
use Base3\MediaFoundation\Exception\ImageRenderException;
use PHPUnit\Framework\TestCase;

final class ImageEditRenderingTest extends TestCase {

	public function testRenderWithDelegatesExactlyOnceToInjectedRenderer(): void {
		$edit = ImageEdit::create()->crop(10, 20, 100, 200)->webp(80);
		$renderer = $this->createStub(IImageRenderer::class);
		$calls = [];
		$renderer->method('render')->willReturnCallback(
			static function(string $input, ImageEdit $actualEdit, string $output) use (&$calls): void {
				$calls[] = [$input, $actualEdit, $output];
			}
		);

		$this->assertNull($edit->renderWith($renderer, '/tmp/input.png', '/tmp/output.webp'));
		$this->assertSame([['/tmp/input.png', $edit, '/tmp/output.webp']], $calls);
	}

	public function testRenderWithDoesNotModifyEditSpecification(): void {
		$edit = ImageEdit::create()->watermark('/tmp/logo.png')->resizeFit(600, 400)->jpeg(78);
		$before = $edit->toArray();
		$renderer = $this->createStub(IImageRenderer::class);
		$renderer->method('render')->willReturnCallback(static function(string $input, ImageEdit $received, string $output): void {});

		$edit->renderWith($renderer, 'source', 'target');
		$this->assertSame($before, $edit->toArray());
	}

	public function testRenderWithPreservesInputAndOutputPathStrings(): void {
		$input = '/path with spaces/source.png';
		$output = '../relative-result.webp';
		$edit = ImageEdit::create();
		$renderer = $this->createStub(IImageRenderer::class);
		$paths = [];
		$renderer->method('render')->willReturnCallback(static function(string $source, ImageEdit $editSpec, string $destination) use (&$paths): void {
			$paths = [$source, $destination];
		});

		$edit->renderWith($renderer, $input, $output);
		$this->assertSame([$input, $output], $paths);
	}

	public function testRenderExceptionIsPropagatedWithoutWrapping(): void {
		$edit = ImageEdit::create()->png();
		$renderer = $this->createStub(IImageRenderer::class);
		$exception = new ImageRenderException('Backend failed');
		$renderer->method('render')->willThrowException($exception);

		try {
			$edit->renderWith($renderer, '/tmp/source.png', '/tmp/result.png');
			$this->fail('Renderer error was swallowed.');
		} catch (ImageRenderException $caught) {
			$this->assertSame($exception, $caught);
			$this->assertSame('Backend failed', $caught->getMessage());
		}
	}

	public function testRendererContractDoesNotDependOnConcreteBackend(): void {
		$renderer = $this->createStub(IImageRenderer::class);
		$edit = ImageEdit::create()->publish();
		$invoked = false;
		$renderer->method('render')->willReturnCallback(
			static function(string $input, ImageEdit $received, string $output) use (&$invoked, $edit): void {
				$invoked = true;
				if ($received !== $edit) throw new \LogicException('Unexpected edit object.');
			}
		);

		$edit->renderWith($renderer, 'in', 'out');
		$this->assertTrue($invoked);
	}
}
