<?php

namespace Zenstruck\Filesystem\Flysystem\Adapter;

use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use Zenstruck\Filesystem\Exception\UnsupportedFeature;
use Zenstruck\Filesystem\Feature\All;
use Zenstruck\Filesystem\Feature\FileChecksum;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class WrappedAdapter implements FilesystemAdapter, All
{
    public function __construct(private FilesystemAdapter $next)
    {
    }

    public function md5Checksum(string $path): string
    {
        return $this->ensureSupports(FileChecksum::class)->md5Checksum($path); // @phpstan-ignore-line
    }

    public function sha1Checksum(string $path): string
    {
        return $this->ensureSupports(FileChecksum::class)->sha1Checksum($path); // @phpstan-ignore-line
    }

    public function fileExists(string $path): bool
    {
        return $this->next->fileExists($path);
    }

    public function directoryExists(string $path): bool
    {
        return $this->next->directoryExists($path);
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->next->write($path, $contents, $config);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->next->writeStream($path, $contents, $config);
    }

    public function read(string $path): string
    {
        return $this->next->read($path);
    }

    public function readStream(string $path)
    {
        return $this->next->readStream($path);
    }

    public function delete(string $path): void
    {
        $this->next->delete($path);
    }

    public function deleteDirectory(string $path): void
    {
        $this->next->deleteDirectory($path);
    }

    public function createDirectory(string $path, Config $config): void
    {
        $this->next->createDirectory($path, $config);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $this->next->setVisibility($path, $visibility);
    }

    public function visibility(string $path): FileAttributes
    {
        return $this->next->visibility($path);
    }

    public function mimeType(string $path): FileAttributes
    {
        return $this->next->mimeType($path);
    }

    public function lastModified(string $path): FileAttributes
    {
        return $this->next->lastModified($path);
    }

    public function fileSize(string $path): FileAttributes
    {
        return $this->next->fileSize($path);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        return $this->next->listContents($path, $deep);
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->next->move($source, $destination, $config);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $this->next->copy($source, $destination, $config);
    }

    public function supports(string $feature): bool
    {
        return $this->next instanceof self ? $this->next->supports($feature) : $this->next instanceof $feature;
    }

    /**
     * @return FilesystemAdapter The "real" adapter
     */
    private function adapter(): FilesystemAdapter
    {
        return $this->next instanceof self ? $this->next->adapter() : $this->next;
    }

    private function ensureSupports(string $feature): FilesystemAdapter
    {
        if (!$this->supports($feature)) {
            throw new UnsupportedFeature(\sprintf('The "%s" adapter does not support "%s".', \get_class($this->adapter()), $feature));
        }

        return $this->next;
    }
}
