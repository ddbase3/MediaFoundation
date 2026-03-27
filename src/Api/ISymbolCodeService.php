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
 * Service for generating symbolic codes such as QR codes, barcodes, etc.
 * The exact capabilities depend on the concrete implementation.
 */
interface ISymbolCodeService {

	/**
	 * Generates a symbolic code for the given value.
	 *
	 * Implementations may support different code types and options
	 * (e.g. "type" => "qr", "type" => "ean13", size, margin, etc.).
	 *
	 * @param string $value   The payload to encode.
	 * @param array  $options Implementation-specific options (optional).
	 *
	 * @return IBinaryMedia   The generated code as binary media (e.g. PNG, SVG).
	 */
	public function generate(string $value, array $options = []): IBinaryMedia;
}
