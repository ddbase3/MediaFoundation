<?php declare(strict_types=1);

namespace MediaApi;

use Base3\Api\IContainer;
use Base3\Api\IPlugin;

class MediaApiPlugin implements IPlugin {

	public function __construct(private readonly IContainer $container) {}

	// Implementation of IBase

	public static function getName(): string {
		return 'mediaapiplugin';
	}

	// Implementation of IPlugin

	public function init() {
		$this->container
			->set(self::getName(), $this, IContainer::SHARED);
	}
}
