<?php

/*
 * This file is part of the zenstruck/filesystem package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\Filesystem\Attribute\PendingUploadedFile;
use Zenstruck\Filesystem\Attribute\UploadedFile;
use Zenstruck\Filesystem\Node\File;
use Zenstruck\Filesystem\Node\File\Image;

/**
 * @author Jakub Caban <kuba.iluvatar@gmail.com>
 */
class ArgumentResolverController
{
    #[Route('/multiple-files', name: 'multiple-files')]
    public function multipleFiles(
        #[PendingUploadedFile]
        array $files
    ): Response {
        return new Response((string) \count($files));
    }

    #[Route('/multiple-images', name: 'multiple-images')]
    public function multipleImages(
        #[PendingUploadedFile(image: true)]
        array $images
    ): Response {
        return new Response((string) \count($images));
    }

    #[Route('/multiple-files-with-path', name: 'multiple-files-with-path')]
    public function multipleFilesWithPath(
        #[PendingUploadedFile('data[files]')]
        array $files
    ): Response {
        return new Response((string) \count($files));
    }

    #[Route('/no-injection', name: 'no-injection')]
    public function noInjection(array $file = []): Response
    {
        return new Response((string) \count($file));
    }

    #[Route('/single-file', name: 'single-file')]
    public function singleFile(?File $file): Response
    {
        return new Response($file?->contents() ?? '');
    }

    #[Route('/single-stored-file', name: 'single-stored-file')]
    public function singleStoredFile(
        #[UploadedFile('public')]
        File $file
    ): Response {
        return new Response(
            \sprintf(
                '%s:%s',
                $file->dsn(),
                $file->contents()
            )
        );
    }

    #[Route('/single-image', name: 'single-image')]
    public function singleImage(Image $image): Response
    {
        return new Response((string) $image->dimensions()->width());
    }

    #[Route('/single-file-with-path', name: 'single-file-with-path')]
    public function singleFileWithPath(
        #[PendingUploadedFile('data[file]')]
        ?File $file
    ): Response {
        return new Response($file?->contents() ?? '');
    }
}
