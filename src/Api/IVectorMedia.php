<?php

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
