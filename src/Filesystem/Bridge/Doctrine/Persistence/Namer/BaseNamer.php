<?php

namespace Zenstruck\Filesystem\Bridge\Doctrine\Persistence\Namer;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Zenstruck\Filesystem\Bridge\Doctrine\Persistence\Namer;
use Zenstruck\Filesystem\Node;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Filesystem\Node\PendingNode;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class BaseNamer implements Namer
{
    private static AsciiSlugger $asciiSlugger;

    public function __construct(protected ?SluggerInterface $slugger = null)
    {
        if (!$this->slugger && \interface_exists(LocaleAwareInterface::class) && \class_exists(AsciiSlugger::class)) {
            $this->slugger = self::$asciiSlugger ??= new AsciiSlugger();
        }
    }

    final protected static function extensionWithDot(Node $node): string
    {
        if ($node instanceof PendingNode) {
            return '.'.\mb_strtolower((string) $node->originalExtension());
        }

        return $node instanceof File ? '.'.\mb_strtolower((string) $node->extension()) : '';
    }

    final protected static function nameWithoutExtension(Node $node): string
    {
        if ($node instanceof PendingNode) {
            return $node->originalNameWithoutExtension();
        }

        return $node instanceof File ? $node->nameWithoutExtension() : $node->name();
    }

    final protected function slugify(string $value): string
    {
        return $this->slugger ? $this->slugger->slug($value) : \mb_strtolower(\str_replace(' ', '-', $value)); // quick and dirty
    }
}
