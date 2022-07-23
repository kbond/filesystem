<?php

namespace Zenstruck\Filesystem\Tests\Test;

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\AdapterFilesystem;
use Zenstruck\Filesystem\Test\TestFilesystem;
use Zenstruck\Filesystem\Tests\FilesystemTest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TestFilesystemTest extends FilesystemTest
{
    protected function createFilesystem(): Filesystem
    {
        return new TestFilesystem(new AdapterFilesystem(self::TEMP_DIR));
    }
}
