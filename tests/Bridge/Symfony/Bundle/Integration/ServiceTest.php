<?php

namespace Zenstruck\Filesystem\Tests\Bridge\Symfony\Bundle\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Filesystem\MultiFilesystem;
use Zenstruck\Filesystem\Test\InteractsWithFilesystem;
use Zenstruck\Filesystem\Tests\Fixture\Symfony\Service;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ServiceTest extends KernelTestCase
{
    use InteractsWithFilesystem;

    /**
     * @test
     */
    public function can_get_named_filesystems(): void
    {
        /** @var Service $service */
        $service = self::getContainer()->get(Service::class);

        $this->assertInstanceOf(MultiFilesystem::class, $service->general);
        $this->assertSame('public', $service->general->name());
        $this->assertSame('public', $service->public->name());
        $this->assertSame('private', $service->private->name());
    }

    /**
     * @test
     */
    public function can_get_prefixed_urls(): void
    {
        $file = $this->filesystem()->write('nested/file.txt', 'content')->last()->ensureFile();

        $this->assertSame('/files/nested/file.txt', $file->url()->toString());
    }

    /**
     * @test
     */
    public function can_get_route_urls(): void
    {
        $this->markTestIncomplete();
    }
}
