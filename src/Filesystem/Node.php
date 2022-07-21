<?php

namespace Zenstruck\Filesystem;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Zenstruck\Filesystem\Exception\NodeTypeMismatch;
use Zenstruck\Filesystem\Flysystem\Operator;
use Zenstruck\Filesystem\Node\Directory;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Filesystem\Node\File\Image;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
abstract class Node
{
    private string $path;
    private \DateTimeImmutable $lastModified;
    private string $visibility;

    /**
     * @internal
     */
    public function __construct(StorageAttributes $attributes, protected Operator $operator)
    {
        $this->path = $attributes->path();

        if ($lastModified = $attributes->lastModified()) {
            $this->lastModified = self::parseDateTime($lastModified);
        }

        if ($visibility = $attributes->visibility()) {
            $this->visibility = $visibility;
        }
    }

    final public function __toString(): string
    {
        return $this->path;
    }

    final public function path(): string
    {
        return $this->path;
    }

    /**
     * Returns the file or directory name (with extension if applicable).
     *
     * @example If $path is "foo/bar/baz.txt", returns "baz.txt"
     * @example If $path is "foo/bar/baz", returns "baz"
     */
    final public function name(): string
    {
        return \pathinfo($this->path(), \PATHINFO_BASENAME);
    }

    /**
     * Returns the "parent" directory path.
     *
     * @example If $path is "foo/bar/baz", returns "foo/bar"
     */
    final public function dirname(): string
    {
        return \pathinfo($this->path(), \PATHINFO_DIRNAME);
    }

    /**
     * @return \DateTimeImmutable In the PHP default timezone
     */
    final public function lastModified(): \DateTimeImmutable
    {
        return $this->lastModified ??= self::parseDateTime($this->operator->lastModified($this->path()));
    }

    /**
     * @see FilesystemOperator::visibility()
     */
    final public function visibility(): string
    {
        return $this->visibility ??= $this->operator->visibility($this->path());
    }

    /**
     * Check if the node still exists.
     */
    final public function exists(): bool
    {
        return $this->operator->has($this->path());
    }

    abstract public function mimeType(): string;

    /**
     * Clear any cached metadata.
     */
    public function refresh(): static
    {
        unset($this->visibility, $this->lastModified);

        return $this;
    }

    /**
     * @throws NodeTypeMismatch If not a file
     */
    final public function ensureFile(): File
    {
        return $this instanceof File ? $this : throw NodeTypeMismatch::expectedFileAt($this->path());
    }

    /**
     * @return Directory<Node>
     *
     * @throws NodeTypeMismatch If not a directory
     */
    final public function ensureDirectory(): Directory
    {
        return $this instanceof Directory ? $this : throw NodeTypeMismatch::expectedDirectoryAt($this->path());
    }

    /**
     * @throws NodeTypeMismatch If not an image file
     */
    final public function ensureImage(): Image
    {
        if ($this instanceof Image) {
            return $this;
        }

        if (!$this->isImage()) {
            throw NodeTypeMismatch::expectedImageAt($this->path(), $this->mimeType());
        }

        return $this->castTo(new Image());
    }

    final public function isFile(): bool
    {
        return $this instanceof File;
    }

    final public function isDirectory(): bool
    {
        return $this instanceof Directory;
    }

    final public function isImage(): bool
    {
        return $this instanceof Image || ($this instanceof File && \str_contains($this->mimeType(), 'image/'));
    }

    final protected static function parseDateTime(\DateTimeInterface|int|string $timestamp): \DateTimeImmutable
    {
        if (\is_numeric($timestamp)) {
            $timestamp = \DateTimeImmutable::createFromFormat('U', (string) $timestamp);
        }

        if (\is_string($timestamp)) {
            $timestamp = new \DateTimeImmutable($timestamp);
        }

        if ($timestamp instanceof \DateTime) {
            $timestamp = \DateTimeImmutable::createFromMutable($timestamp);
        }

        if (!$timestamp instanceof \DateTimeImmutable) {
            throw new \RuntimeException('Unable to parse datetime.');
        }

        // ensure in the PHP default timezone
        return $timestamp->setTimezone(new \DateTimeZone(\date_default_timezone_get()));
    }

    /**
     * @template T of Node
     *
     * @param T $to
     *
     * @return T
     */
    protected function castTo(self $to): self
    {
        $to->operator = $this->operator;
        $to->path = $this->path;

        if (isset($to->visibility)) {
            $to->visibility = $this->visibility;
        }

        if (isset($to->lastModified)) {
            $to->lastModified = $this->lastModified;
        }

        return $to;
    }
}
