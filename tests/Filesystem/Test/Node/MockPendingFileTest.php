<?php

/*
 * This file is part of the zenstruck/filesystem package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Tests\Filesystem\Test\Node;

use PHPUnit\Framework\TestCase;
use Zenstruck\Filesystem\Test\Node\MockPendingFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MockPendingFileTest extends TestCase
{
    /**
     * @test
     */
    public function is_lazily_created(): void
    {
        $file = new MockPendingFile('some-file.txt');

        $this->assertFileDoesNotExist(\sys_get_temp_dir().'/some-file.txt');
        $this->assertFileExists($file);
        $this->assertFileExists(\sys_get_temp_dir().'/some-file.txt');
        $this->assertSame('', \file_get_contents($file));
    }

    /**
     * @test
     */
    public function can_create_with_content(): void
    {
        $file = new MockPendingFile('some-file.txt', 'content');

        $this->assertSame('content', \file_get_contents($file));
    }
}
