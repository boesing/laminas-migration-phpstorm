<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service;

use Boesing\Laminas\Migration\PhpStorm\Metadata;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\File;

final class MetadataGenerator
{

    /**
     * @param array<int,File> $laminasFiles
     *
     * @psalm-param list<File> $laminasFiles
     */
    public function generateMetadata(array $laminasFiles): Metadata
    {
        $metadata = Metadata::create();
        foreach ($laminasFiles as $file) {
            $metadata = $metadata->withAlias(sprintf('class_alias(%s::class, %s::class);', $file->laminas, $file->zend));
        }

        return $metadata;
    }
}
