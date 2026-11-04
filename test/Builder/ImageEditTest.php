<?php declare(strict_types=1);

namespace MediaFoundation\Test\Builder;

use JsonSerializable;
use MediaFoundation\Builder\ImageEdit;
use PHPUnit\Framework\TestCase;

final class ImageEditTest extends TestCase {

	public function testCreateReturnsFreshSpecWithSafeExportDefaults(): void {
		$edit = ImageEdit::create();
		$spec = $edit->toArray();

		$this->assertInstanceOf(JsonSerializable::class, $edit);
		$this->assertSame(null, $spec['preset']);
		$this->assertSame(['crop' => null, 'crop_norm' => null, 'rotate' => 0, 'flip_h' => false, 'flip_v' => false], $spec['transform']);
		$this->assertSame(0.0, $spec['adjust']['brightness']);
		$this->assertSame(1.0, $spec['adjust']['gamma']);
		$this->assertSame(0.0, $spec['adjust']['color_noise_reduction']);
		$this->assertSame([
			'enabled' => false, 'path' => null, 'opacity' => 1.0, 'scale' => 1.0,
			'x' => -50, 'y' => -50, 'anchor' => 'se'
		], $spec['watermark']);
		$this->assertSame(['max_width' => null, 'max_height' => null, 'mode' => 'fit'], $spec['resize']);
		$this->assertSame(['format' => 'jpeg', 'quality' => 90, 'strip' => true, 'srgb' => true, 'progressive' => true], $spec['export']);
	}

	public function testPresetStoresExactNameAndReturnsSameInstance(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->preset('custom-preset'));
		$this->assertSame('custom-preset', $edit->toArray()['preset']);
		$this->assertSame($edit, $edit->preset('publish'));
		$this->assertSame('publish', $edit->spec()['preset']);
	}

	public function testPreviewAppliesPreviewPresetAndWidthLimit(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->preview());
		$this->assertSame('preview', $edit->spec()['preset']);
		$this->assertSame(['max_width' => 1600, 'max_height' => null, 'mode' => 'fit'], $edit->spec()['resize']);
	}

	public function testPreviewAcceptsCustomWidthAndReplacesPreviousResize(): void {
		$edit = ImageEdit::create()->resizeFill(300, 500);

		$this->assertSame($edit, $edit->preview(480));
		$this->assertSame(['max_width' => 480, 'max_height' => null, 'mode' => 'fit'], $edit->spec()['resize']);
	}

	public function testPublishChangesPresetWithoutResettingOtherEdits(): void {
		$edit = ImageEdit::create()->crop(10, 20, 30, 40)->resizeFit(320, 180);

		$this->assertSame($edit, $edit->publish());
		$this->assertSame('publish', $edit->spec()['preset']);
		$this->assertSame(['x' => 10, 'y' => 20, 'w' => 30, 'h' => 40], $edit->spec()['transform']['crop']);
		$this->assertSame(['max_width' => 320, 'max_height' => 180, 'mode' => 'fit'], $edit->spec()['resize']);
	}

	public function testPixelCropReplacesNormalizedCrop(): void {
		$edit = ImageEdit::create()->cropNormalized(0.1, 0.2, 0.3, 0.4);

		$this->assertSame($edit, $edit->crop(-2, 4, 500, 250));
		$this->assertSame(['x' => -2, 'y' => 4, 'w' => 500, 'h' => 250], $edit->spec()['transform']['crop']);
		$this->assertNull($edit->spec()['transform']['crop_norm']);
	}

	public function testNormalizedCropReplacesPixelCrop(): void {
		$edit = ImageEdit::create()->crop(0, 0, 100, 200);

		$this->assertSame($edit, $edit->cropNormalized(0.125, 0.25, 0.5, 0.75));
		$this->assertSame(['x' => 0.125, 'y' => 0.25, 'w' => 0.5, 'h' => 0.75], $edit->spec()['transform']['crop_norm']);
		$this->assertNull($edit->spec()['transform']['crop']);
	}

	public function testRotationAcceptsFractionalDegreesAndOverwritesPreviousValue(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->rotate(-12.5));
		$this->assertSame(-12.5, $edit->spec()['transform']['rotate']);
		$this->assertSame($edit, $edit->rotate(405.5));
		$this->assertSame(405.5, $edit->spec()['transform']['rotate']);
	}

	public function testRotationConveniencesSetExpectedAngles(): void {
		$edit = ImageEdit::create();
		foreach ([['rotate90', 90.0], ['rotate180', 180.0], ['rotate270', 270.0]] as [$method, $expected]) {
			$this->assertSame($edit, $edit->$method());
			$this->assertSame($expected, (float)$edit->spec()['transform']['rotate']);
		}
	}

	public function testFlipsCanBeEnabledAndDisabledIndependently(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->flipHorizontal()->flipVertical());
		$this->assertTrue($edit->spec()['transform']['flip_h']);
		$this->assertTrue($edit->spec()['transform']['flip_v']);
		$this->assertSame($edit, $edit->flipHorizontal(false));
		$this->assertFalse($edit->spec()['transform']['flip_h']);
		$this->assertTrue($edit->spec()['transform']['flip_v']);
		$this->assertSame($edit, $edit->flipVertical(false));
		$this->assertFalse($edit->spec()['transform']['flip_v']);
	}

	public function testWatermarkUsesDefaultPositionAndAnchor(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->watermark('/srv/watermark.png'));
		$this->assertSame([
			'enabled' => true, 'path' => '/srv/watermark.png', 'opacity' => 1.0,
			'scale' => 1.0, 'x' => -50, 'y' => -50, 'anchor' => 'se'
		], $edit->spec()['watermark']);
	}

	public function testWatermarkCanChangePositionOpacityAndScale(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->watermark('logo.png', 12, -18, 'nw')->watermarkOpacity(0.25)->watermarkScale(0.4));
		$this->assertSame([
			'enabled' => true, 'path' => 'logo.png', 'opacity' => 0.25,
			'scale' => 0.4, 'x' => 12, 'y' => -18, 'anchor' => 'nw'
		], $edit->spec()['watermark']);
	}

	public function testWatermarkDisableResetsAllWatermarkSettingsOnly(): void {
		$edit = ImageEdit::create()->preset('custom')->crop(1, 2, 3, 4)
			->watermark('secret-path.png', 5, 10, 'n')->watermarkOpacity(0.3)->watermarkScale(0.8);

		$this->assertSame($edit, $edit->disableWatermark());
		$this->assertSame([
			'enabled' => false, 'path' => null, 'opacity' => 1.0, 'scale' => 1.0,
			'x' => -50, 'y' => -50, 'anchor' => 'se'
		], $edit->spec()['watermark']);
		$this->assertSame('custom', $edit->spec()['preset']);
		$this->assertSame(['x' => 1, 'y' => 2, 'w' => 3, 'h' => 4], $edit->spec()['transform']['crop']);
	}

	public function testToneAdjustmentsUpdateTheirSpecificKeys(): void {
		$edit = ImageEdit::create();
		$settings = [
			'brightness' => 0.1, 'contrast' => -0.2, 'saturation' => 0.3, 'vibrance' => -0.4,
			'gamma' => 1.2, 'exposure' => -1.5, 'highlights' => 0.6, 'shadows' => -0.7,
			'whites' => 0.8, 'blacks' => -0.9, 'temperature' => 0.11, 'tint' => -0.12,
			'clarity' => 0.13, 'sharpness' => -0.14, 'dehaze' => 0.15
		];

		foreach ($settings as $method => $value) {
			$this->assertSame($edit, $edit->$method($value));
			$this->assertSame($value, $edit->spec()['adjust'][$method]);
		}
		$this->assertSame(0.0, $edit->spec()['adjust']['luminance_noise_reduction']);
	}

	public function testNoiseReductionMethodsHaveStableSpecKeys(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->luminanceNoiseReduction(0.6));
		$this->assertSame($edit, $edit->colorNoiseReduction(0.3));
		$this->assertSame(0.6, $edit->spec()['adjust']['luminance_noise_reduction']);
		$this->assertSame(0.3, $edit->spec()['adjust']['color_noise_reduction']);
	}

	public function testMoodStoresAdditionalBackendHintWithoutRewritingContrast(): void {
		$edit = ImageEdit::create()->brightness(0.2)->contrast(0.3);

		$this->assertSame($edit, $edit->mood(1.15, -0.05));
		$this->assertSame(['mul' => 1.15, 'add' => -0.05], $edit->spec()['adjust']['_mood']);
		$this->assertSame(0.2, $edit->spec()['adjust']['brightness']);
		$this->assertSame(0.3, $edit->spec()['adjust']['contrast']);
	}

	public function testResizeFitAcceptsEitherOrBothLimits(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->resizeFit(null, 768));
		$this->assertSame(['max_width' => null, 'max_height' => 768, 'mode' => 'fit'], $edit->spec()['resize']);
		$this->assertSame($edit, $edit->resizeFit(1024, 512));
		$this->assertSame(['max_width' => 1024, 'max_height' => 512, 'mode' => 'fit'], $edit->spec()['resize']);
	}

	public function testResizeFillReplacesPreviousFitLimits(): void {
		$edit = ImageEdit::create()->resizeFit(null, 600);

		$this->assertSame($edit, $edit->resizeFill(1200, 800));
		$this->assertSame(['max_width' => 1200, 'max_height' => 800, 'mode' => 'fill'], $edit->spec()['resize']);
	}

	public function testJpegOverridesQualityStripAndProgressive(): void {
		$edit = ImageEdit::create()->png(false);

		$this->assertSame($edit, $edit->jpeg(78, false, false));
		$this->assertSame('jpeg', $edit->spec()['export']['format']);
		$this->assertSame(78, $edit->spec()['export']['quality']);
		$this->assertFalse($edit->spec()['export']['strip']);
		$this->assertFalse($edit->spec()['export']['progressive']);
	}

	public function testWebpUsesOwnDefaultQualityAndStripOption(): void {
		$edit = ImageEdit::create()->jpeg(30, false, false);

		$this->assertSame($edit, $edit->webp());
		$this->assertSame('webp', $edit->spec()['export']['format']);
		$this->assertSame(85, $edit->spec()['export']['quality']);
		$this->assertTrue($edit->spec()['export']['strip']);
		$this->assertFalse($edit->spec()['export']['progressive']);
		$this->assertSame($edit, $edit->webp(60, false));
		$this->assertSame(60, $edit->spec()['export']['quality']);
		$this->assertFalse($edit->spec()['export']['strip']);
	}

	public function testPngUpdatesFormatAndMetadataStrippingWithoutResettingOtherExportKeys(): void {
		$edit = ImageEdit::create()->jpeg(65, true, false);

		$this->assertSame($edit, $edit->png(false));
		$this->assertSame('png', $edit->spec()['export']['format']);
		$this->assertFalse($edit->spec()['export']['strip']);
		$this->assertSame(65, $edit->spec()['export']['quality']);
		$this->assertFalse($edit->spec()['export']['progressive']);
	}

	public function testEnsureSrgbCanBeDisabledAndEnabled(): void {
		$edit = ImageEdit::create();

		$this->assertSame($edit, $edit->ensureSrgb(false));
		$this->assertFalse($edit->spec()['export']['srgb']);
		$this->assertSame($edit, $edit->ensureSrgb());
		$this->assertTrue($edit->spec()['export']['srgb']);
	}

	public function testJsonSerializationAndSpecExposeTheSameValues(): void {
		$edit = ImageEdit::create()->preview(640)->cropNormalized(0.1, 0.2, 0.5, 0.5)
			->watermark('a.png')->mood(1.2, 0.02)->webp(85);

		$this->assertSame($edit->toArray(), $edit->spec());
		$this->assertSame($edit->toArray(), $edit->jsonSerialize());
		$this->assertSame($edit->toArray(), json_decode((string)json_encode($edit, JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR));
	}

	public function testReturnedSnapshotDoesNotModifyBuilder(): void {
		$edit = ImageEdit::create()->crop(1, 2, 3, 4)->watermark('original.png');
		$copy = $edit->toArray();
		$copy['transform']['crop']['w'] = 999;
		$copy['watermark']['path'] = 'modified.png';

		$this->assertSame(3, $edit->spec()['transform']['crop']['w']);
		$this->assertSame('original.png', $edit->spec()['watermark']['path']);
	}

	public function testTwoCreatedBuildersDoNotShareMutableState(): void {
		$first = ImageEdit::create()->crop(1, 2, 3, 4)->jpeg(30);
		$second = ImageEdit::create();

		$this->assertNull($second->spec()['transform']['crop']);
		$this->assertSame(90, $second->spec()['export']['quality']);
		$this->assertSame(30, $first->spec()['export']['quality']);
	}
}
