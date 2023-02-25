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

use Zenstruck\Filesystem;
use Zenstruck\Filesystem\Node\File\Image\PendingImage;
use Zenstruck\Filesystem\Node\Path;
use Zenstruck\Stream;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
trait MockMethods
{
    private \Closure|string $filename;

    public function __toString(): string
    {
        $this->construct();

        return parent::__toString();
    }

    public function saveTo(Filesystem $filesystem, callable|string|null $path = null): static
    {
        $this->construct();

        return parent::saveTo($filesystem, $path);
    }

    public function path(): Path
    {
        $this->construct();

        return parent::path();
    }

    public function lastModified(): \DateTimeImmutable
    {
        $this->construct();

        return parent::lastModified();
    }

    public function exists(): bool
    {
        $this->construct();

        return parent::exists();
    }

    public function mimeType(): string
    {
        $this->construct();

        return parent::mimeType();
    }

    public function refresh(): static
    {
        $this->construct();

        return parent::refresh();
    }

    public function guessExtension(): ?string
    {
        $this->construct();

        return parent::guessExtension();
    }

    public function size(): int
    {
        $this->construct();

        return parent::size();
    }

    public function contents(): string
    {
        $this->construct();

        return parent::contents();
    }

    public function read()
    {
        $this->construct();

        return parent::read();
    }

    public function stream(): Stream
    {
        $this->construct();

        return parent::stream();
    }

    public function checksum(?string $algo = null): string
    {
        $this->construct();

        return parent::checksum($algo);
    }

    public function tempFile(): \SplFileInfo
    {
        $this->construct();

        return parent::tempFile();
    }

    public function visibility(): string
    {
        $this->construct();

        return parent::visibility();
    }

    public function ensureExists(): static
    {
        $this->construct();

        return parent::ensureExists();
    }

    public function ensureImage(): PendingImage
    {
        $this->construct();

        return parent::ensureImage();
    }

    private function construct(): void
    {
        if ($this->filename instanceof \Closure) {
            parent::__construct(($this->filename)());
        }
    }
}
