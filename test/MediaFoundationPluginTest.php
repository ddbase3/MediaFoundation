<?php declare(strict_types=1);

namespace MediaFoundation\Test;

use Base3\Api\IContainer;
use Base3\Api\IPlugin;
use MediaFoundation\MediaFoundationPlugin;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MediaFoundationPluginTest extends TestCase {

	public function testPluginHasStableDiscoverableNameAndContract(): void {
		$container = $this->createStub(IContainer::class);
		$plugin = new MediaFoundationPlugin($container);

		$this->assertInstanceOf(IPlugin::class, $plugin);
		$this->assertSame('mediafoundationplugin', MediaFoundationPlugin::getName());
	}

	public function testInitRegistersSameInstanceAsSharedService(): void {
		$container = $this->createStub(IContainer::class);
		$registrations = [];
		$container->method('set')->willReturnCallback(
			static function(string $name, mixed $service, int $flags = 0) use ($container, &$registrations): IContainer {
				$registrations[] = [$name, $service, $flags];
				return $container;
			}
		);
		$plugin = new MediaFoundationPlugin($container);

		$this->assertNull($plugin->init());
		$this->assertSame([['mediafoundationplugin', $plugin, IContainer::SHARED]], $registrations);
	}

	public function testInitPropagatesRegistrationFailures(): void {
		$container = $this->createStub(IContainer::class);
		$container->method('set')->willThrowException(new RuntimeException('Registration unavailable'));
		$plugin = new MediaFoundationPlugin($container);

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Registration unavailable');
		$plugin->init();
	}
}
