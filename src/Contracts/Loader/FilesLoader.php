<?php

declare(strict_types=1);

namespace Quill\Contracts\Loader;

interface FilesLoader
{
    public function load(string ...$filenames): void;
}
