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

namespace MediaFoundation\Builder;

use JsonSerializable;
use MediaFoundation\Api\IImageRenderer;
use MediaFoundation\Exception\InvalidImageEditException;

/**
 * Fluent edit description for "edit an image and render it".
 *
 * This class does NOT render anything by itself.
 * It only captures edit intent in a structured spec and delegates to an {@see IImageRenderer}.
 *
 * It is safe to persist this object by calling {@see toArray()} or {@see jsonSerialize()}.
 */
final class ImageEdit implements JsonSerializable
{
	/**
	 * A compact, renderer-agnostic edit spec.
	 * Renderers interpret this spec and map it to their backend (vips, imagick, etc.).
	 *
	 * @var array<string, mixed>
	 */
	private array $spec = [
		'preset' => null,          // e.g. "preview" | "publish" | "instagram-4x5" ...
		'transform' => [
			'crop' => null,        // ['x'=>int,'y'=>int,'w'=>int,'h'=>int] OR normalized crop
			'crop_norm' => null,   // ['x'=>float,'y'=>float,'w'=>float,'h'=>float] in 0..1
			'rotate' => 0,         // degrees: 0,90,180,270 (or any float, backend permitting)
			'flip_h' => false,
			'flip_v' => false,
		],
		'adjust' => [
			// All adjustments are expressed in a backend-agnostic way.
			// Ranges are guidelines; renderer may clamp.
			'brightness' => 0.0,                // -1..+1 (0 = none)
			'contrast'   => 0.0,                // -1..+1 (0 = none)
			'saturation' => 0.0,                // -1..+1
			'vibrance'   => 0.0,                // -1..+1
			'gamma'      => 1.0,                // >0, 1.0 = none
			'exposure'   => 0.0,                // stops, -5..+5 typical
			'highlights' => 0.0,                // -1..+1
			'shadows'    => 0.0,                // -1..+1
			'whites'     => 0.0,                // -1..+1
			'blacks'     => 0.0,                // -1..+1
			'temperature'=> 0.0,                // -1..+1 (cooler..warmer)
			'tint'       => 0.0,                // -1..+1 (green..magenta)
			'clarity'    => 0.0,                // -1..+1 (local contrast)
			'sharpness'  => 0.0,                // -1..+1
			'dehaze'     => 0.0,                // -1..+1
			'luminance_noise_reduction' => 0.0, // 0..+1 typical
			'color_noise_reduction'     => 0.0, // 0..+1 typical
		],
		'watermark' => [
			'enabled' => false,
			'path' => null,        // PNG recommended
			'opacity' => 1.0,      // 0..1
			'scale' => 1.0,        // relative scale (renderer-defined meaning; typically relative to image width)
			'x' => -50,            // px offset from anchor; negative = from right/bottom if anchor uses edges
			'y' => -50,
			'anchor' => 'se',      // 'nw','n','ne','w','c','e','sw','s','se'
		],
		'resize' => [
			'max_width' => null,   // int|null
			'max_height'=> null,   // int|null
			'mode' => 'fit',       // 'fit' (preserve aspect) | 'fill' (crop to fill) | 'stretch'
		],
		'export' => [
			'format' => 'jpeg',    // 'jpeg'|'webp'|'png'
			'quality' => 90,       // for jpeg/webp
			'strip' => true,       // remove metadata (EXIF/IPTC)
			'srgb' => true,        // convert/ensure sRGB output
			'progressive' => true, // jpeg progressive if supported
		],
	];

	private function __construct() {}

	/**
	 * Create a new edit description.
	 */
	public static function create(): self
	{
		return new self();
	}

	/**
	 * Convenience "preset" marker (renderer may use it for defaults).
	 */
	public function preset(string $name): self
	{
		$this->spec['preset'] = $name;
		return $this;
	}

	/**
	 * Convenience preset for UI previews.
	 */
	public function preview(int $maxWidth = 1600): self
	{
		return $this->preset('preview')->resizeFit($maxWidth, null);
	}

	/**
	 * Convenience preset for publish render.
	 */
	public function publish(): self
	{
		return $this->preset('publish');
	}

	// ----------------------------
	// Transformations
	// ----------------------------

	/**
	 * Crop in pixels relative to the current image orientation (renderer-defined, but consistent).
	 */
	public function crop(int $x, int $y, int $w, int $h): self
	{
		if ($w <= 0 || $h <= 0) {
			throw new InvalidImageEditException('Crop width/height must be > 0.');
		}
		$this->spec['transform']['crop'] = ['x' => $x, 'y' => $y, 'w' => $w, 'h' => $h];
		$this->spec['transform']['crop_norm'] = null;
		return $this;
	}

	/**
	 * Normalized crop rectangle (0..1), useful when UI operates on scaled previews.
	 */
	public function cropNormalized(float $x, float $y, float $w, float $h): self
	{
		foreach (['x'=>$x,'y'=>$y,'w'=>$w,'h'=>$h] as $k => $v) {
			if (!is_finite($v)) {
				throw new InvalidImageEditException("CropNormalized {$k} must be finite.");
			}
		}
		$this->spec['transform']['crop_norm'] = ['x' => $x, 'y' => $y, 'w' => $w, 'h' => $h];
		$this->spec['transform']['crop'] = null;
		return $this;
	}

	/**
	 * Rotate in degrees. Typical values: 0/90/180/270.
	 */
	public function rotate(float $degrees): self
	{
		if (!is_finite($degrees)) {
			throw new InvalidImageEditException('Rotate degrees must be finite.');
		}
		$this->spec['transform']['rotate'] = $degrees;
		return $this;
	}

	public function rotate90(): self  { return $this->rotate(90); }
	public function rotate180(): self { return $this->rotate(180); }
	public function rotate270(): self { return $this->rotate(270); }

	public function flipHorizontal(bool $on = true): self
	{
		$this->spec['transform']['flip_h'] = $on;
		return $this;
	}

	public function flipVertical(bool $on = true): self
	{
		$this->spec['transform']['flip_v'] = $on;
		return $this;
	}

	// ----------------------------
	// Watermark
	// ----------------------------

	/**
	 * Enable watermark overlay.
	 *
	 * @param string $pngPath Path to watermark image (PNG recommended).
	 * @param int    $x       Offset in pixels (relative to anchor).
	 * @param int    $y       Offset in pixels (relative to anchor).
	 * @param string $anchor  'nw','n','ne','w','c','e','sw','s','se'
	 */
	public function watermark(string $pngPath, int $x = -50, int $y = -50, string $anchor = 'se'): self
	{
		$this->spec['watermark']['enabled'] = true;
		$this->spec['watermark']['path'] = $pngPath;
		$this->spec['watermark']['x'] = $x;
		$this->spec['watermark']['y'] = $y;
		$this->spec['watermark']['anchor'] = $anchor;
		return $this;
	}

	public function watermarkOpacity(float $opacity): self
	{
		if (!is_finite($opacity)) {
			throw new InvalidImageEditException('Watermark opacity must be finite.');
		}
		$this->spec['watermark']['opacity'] = $opacity;
		return $this;
	}

	/**
	 * Relative scale hint for watermark (renderer interprets; typically relative to image width).
	 */
	public function watermarkScale(float $scale): self
	{
		if (!is_finite($scale) || $scale <= 0) {
			throw new InvalidImageEditException('Watermark scale must be > 0.');
		}
		$this->spec['watermark']['scale'] = $scale;
		return $this;
	}

	public function disableWatermark(): self
	{
		$this->spec['watermark'] = [
			'enabled' => false,
			'path' => null,
			'opacity' => 1.0,
			'scale' => 1.0,
			'x' => -50,
			'y' => -50,
			'anchor' => 'se',
		];
		return $this;
	}

	// ----------------------------
	// Color / tone adjustments
	// ----------------------------

	public function brightness(float $amount): self { $this->spec['adjust']['brightness'] = $amount; return $this; }
	public function contrast(float $amount): self   { $this->spec['adjust']['contrast']   = $amount; return $this; }
	public function saturation(float $amount): self { $this->spec['adjust']['saturation'] = $amount; return $this; }
	public function vibrance(float $amount): self   { $this->spec['adjust']['vibrance']   = $amount; return $this; }

	public function gamma(float $gamma): self
	{
		if (!is_finite($gamma) || $gamma <= 0) {
			throw new InvalidImageEditException('Gamma must be > 0.');
		}
		$this->spec['adjust']['gamma'] = $gamma;
		return $this;
	}

	public function exposure(float $stops): self            { $this->spec['adjust']['exposure']   = $stops; return $this; }
	public function highlights(float $amount): self         { $this->spec['adjust']['highlights'] = $amount; return $this; }
	public function shadows(float $amount): self            { $this->spec['adjust']['shadows']    = $amount; return $this; }
	public function whites(float $amount): self             { $this->spec['adjust']['whites']     = $amount; return $this; }
	public function blacks(float $amount): self             { $this->spec['adjust']['blacks']     = $amount; return $this; }
	public function temperature(float $amount): self        { $this->spec['adjust']['temperature']= $amount; return $this; }
	public function tint(float $amount): self               { $this->spec['adjust']['tint']       = $amount; return $this; }
	public function clarity(float $amount): self            { $this->spec['adjust']['clarity']    = $amount; return $this; }
	public function sharpness(float $amount): self          { $this->spec['adjust']['sharpness']  = $amount; return $this; }
	public function dehaze(float $amount): self             { $this->spec['adjust']['dehaze']     = $amount; return $this; }
	public function luminanceNoiseReduction(float $amount): self { $this->spec['adjust']['luminance_noise_reduction'] = $amount; return $this; }
	public function colorNoiseReduction(float $amount): self     { $this->spec['adjust']['color_noise_reduction'] = $amount; return $this; }

	/**
	 * Convenience: "mood punch" using two simple knobs:
	 * - $mul: contrast-ish multiplier (typical ~1.0..1.3)
	 * - $add: brightness-ish offset (small values like -0.05..0.05)
	 *
	 * Renderers can map this to their fastest primitive (e.g. vips linear).
	 */
	public function mood(float $mul, float $add): self
	{
		// Store as contrast/brightness hints plus explicit mood for backends that support it.
		$this->spec['adjust']['_mood'] = ['mul' => $mul, 'add' => $add];
		return $this;
	}

	// ----------------------------
	// Resize
	// ----------------------------

	public function resizeFit(?int $maxWidth, ?int $maxHeight): self
	{
		$this->spec['resize']['mode'] = 'fit';
		$this->spec['resize']['max_width'] = $maxWidth;
		$this->spec['resize']['max_height'] = $maxHeight;
		return $this;
	}

	public function resizeFill(int $width, int $height): self
	{
		$this->spec['resize']['mode'] = 'fill';
		$this->spec['resize']['max_width'] = $width;
		$this->spec['resize']['max_height'] = $height;
		return $this;
	}

	// ----------------------------
	// Export
	// ----------------------------

	public function jpeg(int $quality = 90, bool $strip = true, bool $progressive = true): self
	{
		$this->spec['export']['format'] = 'jpeg';
		$this->spec['export']['quality'] = $quality;
		$this->spec['export']['strip'] = $strip;
		$this->spec['export']['progressive'] = $progressive;
		return $this;
	}

	public function webp(int $quality = 85, bool $strip = true): self
	{
		$this->spec['export']['format'] = 'webp';
		$this->spec['export']['quality'] = $quality;
		$this->spec['export']['strip'] = $strip;
		return $this;
	}

	public function png(bool $strip = true): self
	{
		$this->spec['export']['format'] = 'png';
		$this->spec['export']['strip'] = $strip;
		return $this;
	}

	public function ensureSrgb(bool $on = true): self
	{
		$this->spec['export']['srgb'] = $on;
		return $this;
	}

	// ----------------------------
	// Render delegation
	// ----------------------------

	/**
	 * Delegate rendering to a renderer backend.
	 */
	public function renderWith(IImageRenderer $renderer, string $inputPath, string $outputPath): void
	{
		$renderer->render($inputPath, $this, $outputPath);
	}

	/**
	 * Export the edit spec for persistence (JSON, DB, sidecar file, etc.).
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return $this->spec;
	}

	/**
	 * Read-only access for renderers.
	 *
	 * @internal Renderers should treat the returned array as immutable.
	 * @return array<string, mixed>
	 */
	public function spec(): array
	{
		return $this->spec;
	}

	public function jsonSerialize(): mixed
	{
		return $this->spec;
	}
}
