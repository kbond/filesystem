<?php

namespace Zenstruck\Filesystem\Tests;

use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Zenstruck\Filesystem;
use Zenstruck\Filesystem\MultiFilesystem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class MultiFilesystemTest extends FilesystemTest
{
    /**
     * @test
     */
    public function can_nest_multi_filesystems(): void
    {
        $filesystem = $this->createMultiFilesystem(
            [
                'first' => (new Filesystem\AdapterFilesystem(new InMemoryFilesystemAdapter()))->write('file1.txt', 'content 1'),
                'second' => $this->createMultiFilesystem([
                    'third' => (new Filesystem\AdapterFilesystem(new InMemoryFilesystemAdapter()))->write('file2.txt', 'content 2'),
                ]),
                'fourth' => $this->createMultiFilesystem([
                    'fifth' => (new Filesystem\AdapterFilesystem(new InMemoryFilesystemAdapter()))->write('file3.txt', 'content 3'),
                ]),
            ],
        );

        $this->assertSame('content 1', $filesystem->file('first://file1.txt')->contents());
        $this->assertSame('content 2', $filesystem->file('third://file2.txt')->contents());
        $this->assertSame('content 3', $filesystem->file('fifth://file3.txt')->contents());

        $filesystem = $this->createMultiFilesystem(
            [
                '_default_' => $this->createMultiFilesystem([
                    'public' => (new Filesystem\AdapterFilesystem(new InMemoryFilesystemAdapter()))->write('file1.txt', 'content 1'),
                    'private' => (new Filesystem\AdapterFilesystem(new InMemoryFilesystemAdapter()))->write('file2.txt', 'content 2'),
                ], 'public'),
                'fixtures' => (new Filesystem\AdapterFilesystem(new InMemoryFilesystemAdapter()))->write('file3.txt', 'content 3'),
            ],
            '_default_'
        );

        $this->assertSame('content 1', $filesystem->file('file1.txt')->contents());
        $this->assertSame('content 2', $filesystem->file('private://file2.txt')->contents());
        $this->assertSame('content 3', $filesystem->file('fixtures://file3.txt')->contents());
    }

    /**
     * @test
     */
    public function can_copy_files_across_filesystems(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('first://foo/bar.txt', 'contents');

        $this->assertTrue($filesystem->has('first://foo/bar.txt'));
        $this->assertFalse($filesystem->has('second://baz/bar.txt'));

        $filesystem->copy('first://foo/bar.txt', 'second://baz/bar.txt');

        $this->assertTrue($filesystem->has('first://foo/bar.txt'));
        $this->assertTrue($filesystem->has('second://baz/bar.txt'));
    }

    /**
     * @test
     */
    public function can_move_files_across_filesystems(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('first://foo/bar.txt', 'contents');

        $this->assertTrue($filesystem->has('first://foo/bar.txt'));
        $this->assertFalse($filesystem->has('second://baz/bar.txt'));

        $filesystem->move('first://foo/bar.txt', 'second://baz/bar.txt');

        $this->assertFalse($filesystem->has('first://foo/bar.txt'));
        $this->assertTrue($filesystem->has('second://baz/bar.txt'));
    }

    /**
     * @test
     */
    public function can_copy_directories_across_filesystems(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('first://foo/bar.txt', 'contents');
        $filesystem->write('first://foo/nested/bar.txt', 'contents');

        $this->assertTrue($filesystem->has('first://foo/bar.txt'));
        $this->assertTrue($filesystem->has('first://foo/nested/bar.txt'));
        $this->assertFalse($filesystem->has('second://baz/bar.txt'));
        $this->assertFalse($filesystem->has('second://baz/nested/bar.txt'));

        $filesystem->copy('first://foo', 'second://baz');

        $this->assertTrue($filesystem->has('first://foo/bar.txt'));
        $this->assertTrue($filesystem->has('first://foo/nested/bar.txt'));
        $this->assertTrue($filesystem->has('second://baz/bar.txt'));
        $this->assertTrue($filesystem->has('second://baz/nested/bar.txt'));
    }

    /**
     * @test
     */
    public function can_move_directories_across_filesystems(): void
    {
        $filesystem = $this->createFilesystem();
        $filesystem->write('first://foo/bar.txt', 'contents');
        $filesystem->write('first://foo/nested/bar.txt', 'contents');

        $this->assertTrue($filesystem->has('first://foo/bar.txt'));
        $this->assertTrue($filesystem->has('first://foo/nested/bar.txt'));
        $this->assertFalse($filesystem->has('second://baz/bar.txt'));
        $this->assertFalse($filesystem->has('second://baz/nested/bar.txt'));

        $filesystem->move('first://foo', 'second://baz');

        $this->assertFalse($filesystem->has('first://foo/bar.txt'));
        $this->assertFalse($filesystem->has('first://foo/nested/bar.txt'));
        $this->assertTrue($filesystem->has('second://baz/bar.txt'));
        $this->assertTrue($filesystem->has('second://baz/nested/bar.txt'));
    }

    /**
     * @test
     */
    public function can_get_last(): void
    {
        $filesystem = $this->createFilesystem()->write('foo.txt', 'content');

        $this->assertSame('content', $filesystem->last()->ensureFile()->contents());

        $filesystem->write('second://bar.txt', 'bar');

        $this->assertSame('bar', $filesystem->last()->ensureFile()->contents());
    }

    /**
     * @test
     */
    public function last_with_no_operation(): void
    {
        $filesystem = $this->createFilesystem();

        $this->expectException(\LogicException::class);

        $filesystem->last();
    }

    final protected function createFilesystem(?array $filesystems = null, ?string $default = null): Filesystem
    {
        if (!$filesystems) {
            $filesystems = [
                'first' => new Filesystem\AdapterFilesystem(new InMemoryFilesystemAdapter()),
                'second' => new Filesystem\AdapterFilesystem(new InMemoryFilesystemAdapter()),
            ];

            $default = 'first';
        }

        return $this->createMultiFilesystem($filesystems, $default);
    }

    abstract protected function createMultiFilesystem(array $filesystems, ?string $default = null): MultiFilesystem;
}
