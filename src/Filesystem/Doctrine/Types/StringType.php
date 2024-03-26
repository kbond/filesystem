<?php

/*
 * This file is part of the zenstruck/filesystem package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Filesystem\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\StringType as BaseStringType;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Filesystem\Node\File\LazyFile;
use Zenstruck\Filesystem\Node\File\PendingFile;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
abstract class StringType extends BaseStringType
{
    final public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof PendingFile) {
            throw new \LogicException('A pending file cannot be added directly to the database - use the event listener.');
        }

        if ($value instanceof File) {
            return $this->fileToData($value);
        }

        if (\class_exists(InvalidType::class)) {
            // dbal 4+
            throw InvalidType::new($value, File::class, [File::class, 'null']);
        }

        throw ConversionException::conversionFailedInvalidType($value, File::class, [File::class, 'null']); // @phpstan-ignore-line
    }

    final public function convertToPHPValue($value, AbstractPlatform $platform): ?LazyFile
    {
        return \is_string($value) ? $this->dataToFile($value) : null;
    }

    final public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    abstract protected function fileToData(File $file): string;

    abstract protected function dataToFile(string $data): LazyFile;
}
