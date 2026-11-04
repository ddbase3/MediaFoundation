<?php declare(strict_types=1);

namespace MediaFoundation\Test\Builder;

use MediaFoundation\Builder\ImageEdit;
use MediaFoundation\Exception\InvalidImageEditException;
use PHPUnit\Framework\TestCase;

final class ImageEditValidationTest extends TestCase {

	public function testPixelCropRejectsZeroOrNegativeWidth(): void {
		foreach ([0, -1, -300] as $width) {
			$this->assertInvalid(
				static fn() => ImageEdit::create()->crop(1, 2, $width, 50),
				'Crop width/height must be > 0.'
			);
		}
	}

	public function testPixelCropRejectsZeroOrNegativeHeight(): void {
		foreach ([0, -1, -300] as $height) {
			$this->assertInvalid(
				static fn() => ImageEdit::create()->crop(1, 2, 50, $height),
				'Crop width/height must be > 0.'
			);
		}
	}

	public function testPixelCropSupportsNegativeOffsetsAndMinimumValidSize(): void {
		$edit = ImageEdit::create()->crop(-5, -7, 1, 1);

		$this->assertSame(['x' => -5, 'y' => -7, 'w' => 1, 'h' => 1], $edit->spec()['transform']['crop']);
	}

	public function testInvalidPixelCropDoesNotDiscardExistingNormalizedCrop(): void {
		$edit = ImageEdit::create()->cropNormalized(0.2, 0.1, 0.4, 0.4);
		$before = $edit->toArray();

		$this->assertInvalid(static fn() => $edit->crop(0, 0, 0, 10), 'Crop width/height must be > 0.');
		$this->assertSame($before, $edit->toArray());
	}

	public function testNormalizedCropRejectsEveryNonFiniteCoordinate(): void {
		foreach (['x', 'y', 'w', 'h'] as $key) {
			foreach ([INF, -INF, NAN] as $value) {
				$crop = ['x' => 0.1, 'y' => 0.2, 'w' => 0.3, 'h' => 0.4];
				$crop[$key] = $value;
				$this->assertInvalid(
					static fn() => ImageEdit::create()->cropNormalized($crop['x'], $crop['y'], $crop['w'], $crop['h']),
					'CropNormalized ' . $key . ' must be finite.'
				);
			}
		}
	}

	public function testInvalidNormalizedCropDoesNotClearExistingPixelCrop(): void {
		$edit = ImageEdit::create()->crop(1, 2, 3, 4);
		$before = $edit->toArray();

		$this->assertInvalid(static fn() => $edit->cropNormalized(0.1, NAN, 0.2, 0.2), 'CropNormalized y must be finite.');
		$this->assertSame($before, $edit->toArray());
	}

	public function testNormalizedCropSupportsFiniteBoundaryCoordinates(): void {
		$edit = ImageEdit::create()->cropNormalized(0.0, 0.0, 1.0, 1.0);

		$this->assertSame(['x' => 0.0, 'y' => 0.0, 'w' => 1.0, 'h' => 1.0], $edit->spec()['transform']['crop_norm']);
	}

	public function testRotationRejectsNonFiniteAngles(): void {
		foreach ([INF, -INF, NAN] as $value) {
			$this->assertInvalid(static fn() => ImageEdit::create()->rotate($value), 'Rotate degrees must be finite.');
		}
	}

	public function testInvalidRotationPreservesPreviouslyConfiguredAngle(): void {
		$edit = ImageEdit::create()->rotate(45.25);

		$this->assertInvalid(static fn() => $edit->rotate(INF), 'Rotate degrees must be finite.');
		$this->assertSame(45.25, $edit->spec()['transform']['rotate']);
	}

	public function testWatermarkOpacityRejectsNonFiniteValues(): void {
		foreach ([INF, -INF, NAN] as $value) {
			$this->assertInvalid(static fn() => ImageEdit::create()->watermarkOpacity($value), 'Watermark opacity must be finite.');
		}
	}

	public function testValidWatermarkOpacityIsStoredWithoutClamping(): void {
		$edit = ImageEdit::create()->watermarkOpacity(0.0);

		$this->assertSame(0.0, $edit->spec()['watermark']['opacity']);
		$this->assertSame($edit, $edit->watermarkOpacity(1.0));
		$this->assertSame(1.0, $edit->spec()['watermark']['opacity']);
	}

	public function testInvalidOpacityPreservesPreviouslyConfiguredValue(): void {
		$edit = ImageEdit::create()->watermarkOpacity(0.35);

		$this->assertInvalid(static fn() => $edit->watermarkOpacity(NAN), 'Watermark opacity must be finite.');
		$this->assertSame(0.35, $edit->spec()['watermark']['opacity']);
	}

	public function testWatermarkScaleRejectsZeroNegativeOrNonFiniteValues(): void {
		foreach ([0.0, -0.1, INF, -INF, NAN] as $value) {
			$this->assertInvalid(static fn() => ImageEdit::create()->watermarkScale($value), 'Watermark scale must be > 0.');
		}
	}

	public function testWatermarkScaleAcceptsPositiveFractionalValue(): void {
		$edit = ImageEdit::create()->watermarkScale(0.001);

		$this->assertSame(0.001, $edit->spec()['watermark']['scale']);
	}

	public function testInvalidScalePreservesPreviouslyConfiguredScale(): void {
		$edit = ImageEdit::create()->watermarkScale(2.5);

		$this->assertInvalid(static fn() => $edit->watermarkScale(0.0), 'Watermark scale must be > 0.');
		$this->assertSame(2.5, $edit->spec()['watermark']['scale']);
	}

	public function testGammaRejectsZeroNegativeOrNonFiniteValues(): void {
		foreach ([0.0, -1.0, INF, -INF, NAN] as $value) {
			$this->assertInvalid(static fn() => ImageEdit::create()->gamma($value), 'Gamma must be > 0.');
		}
	}

	public function testGammaAcceptsPositiveFractionalValue(): void {
		$edit = ImageEdit::create()->gamma(0.1);

		$this->assertSame(0.1, $edit->spec()['adjust']['gamma']);
	}

	public function testInvalidGammaPreservesPreviouslyConfiguredGamma(): void {
		$edit = ImageEdit::create()->gamma(2.2);

		$this->assertInvalid(static fn() => $edit->gamma(-1.0), 'Gamma must be > 0.');
		$this->assertSame(2.2, $edit->spec()['adjust']['gamma']);
	}

	private function assertInvalid(callable $operation, string $expectedMessage): void {
		try {
			$operation();
			$this->fail('Invalid image edit was accepted.');
		} catch (InvalidImageEditException $exception) {
			$this->assertSame($expectedMessage, $exception->getMessage());
		}
	}
}
