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
