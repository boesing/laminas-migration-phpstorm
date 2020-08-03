<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;

final class File
{
    /**
     * @var string
     */
    public $laminas;

    /**
     * @var string
     */
    public $zend;

    private function __construct(string $laminas, string $zend)
    {
        $this->laminas = $laminas;
        $this->zend = $zend;
    }

    public static function create(string $laminas, string $zend): self
    {
        return new self($laminas, $zend);
    }
}
