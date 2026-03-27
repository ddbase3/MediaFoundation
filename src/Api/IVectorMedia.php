<?php

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

/**
 * Vector-based media, e.g. SVG, EPS.
 */
interface IVectorMedia extends IMedia {

	/**
	 * Returns the vector format, e.g. "svg", "eps".
	 */
	public function getFormat(): string;

	/**
	 * For XML-based vector formats (like SVG), this may return the XML string.
	 * For other formats, this may return null or an empty string.
	 */
	public function getXml(): ?string;
}
