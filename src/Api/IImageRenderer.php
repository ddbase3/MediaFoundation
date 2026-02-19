<?php
declare(strict_types=1);

namespace MediaFoundation\Api;

use MediaFoundation\Builder\ImageEdit;
use MediaFoundation\Exception\ImageRenderException;

/**
 * Image renderer backend interface.
 *
 * Implementations take an input image file on disk and produce an output image file on disk,
 * applying the edits described by {@see ImageEdit}.
 *
 * Design goals:
 * - Input is immutable: never modify $inputPath.
 * - Deterministic output: same input + same edits => same output.
 * - File-based: implementations should operate on file paths (no PHP image resources required).
 * - Fast: implementations are free to use native libraries / binaries (e.g. libvips, ImageMagick).
 */
interface IImageRenderer
{
    /**
     * Render an edited derivative of an image.
     *
     * @param string   $inputPath  Absolute path to the source image on local disk.
     * @param ImageEdit $edit      Edit description (crop/rotate/color/watermark/export).
     * @param string   $outputPath Absolute path to the output file to be written.
     *
     * @throws ImageRenderException If rendering fails for any reason (decode, invalid options, IO, tooling).
     */
    public function render(string $inputPath, ImageEdit $edit, string $outputPath): void;
}
