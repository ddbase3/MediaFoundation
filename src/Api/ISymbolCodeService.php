<?php

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
