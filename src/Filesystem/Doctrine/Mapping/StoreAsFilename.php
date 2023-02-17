<?php

/*
 * This file is part of the zenstruck/filesystem package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Filesystem\Doctrine\Mapping;

use Zenstruck\Filesystem\Node\Path\Namer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class StoreAsFilename extends StoreAsPath
{
    public string|Namer $prefix;

    public function __construct(
        string $filesystem,
        string|Namer $prefix,
        string|Namer|null $namer = null,
        bool $deleteOnRemove = true,
        bool $deleteOnUpdate = true,
        array $column = []
    ) {
        parent::__construct($filesystem, $namer, $deleteOnRemove, $deleteOnUpdate, $column);

        try {
            $this->prefix = self::parseNamer($prefix) ?? $prefix;
        } catch (\InvalidArgumentException) {
            // use plain string as the prefix
            $this->prefix = $prefix;
        }
    }
}
