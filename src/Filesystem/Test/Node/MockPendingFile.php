<?php

/*
 * This file is part of the zenstruck/filesystem package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Filesystem\Test\Node;

use Zenstruck\Filesystem\Node\File\PendingFile;
use Zenstruck\TempFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class MockPendingFile extends PendingFile
{
    use MockMethods;

    /**
     * @param string|resource|\SplFileInfo|null $content
     */
    public function __construct(string $filename, mixed $content = null)
    {
        $this->filename = static fn() => TempFile::withName($filename, $content);
    }
}
