<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Test\Service\LaminasFileFinder;

use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder\File;
use Generator;
use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    /**
     * @dataProvider namespaceProvider
     */
    public function testWillPrefixNamespace(string $laminas, string $zend): void
    {
        $file = File::create($laminas, $zend);
        self::assertEquals(sprintf('\%s', ltrim($laminas, '\\')), $file->laminas);
        self::assertEquals(sprintf('\%s', ltrim($zend, '\\')), $file->zend);
    }

    /**
     * @psalm-return Generator<string,list<string>>
     */
    public function namespaceProvider(): Generator
    {
        yield 'without prefix' => [
            'Laminas\Foo',
            'Zend\Foo',
        ];

        yield 'with prefix' => [
            '\Laminas\Foo',
            '\Zend\Foo',
        ];
        yield 'laminas prefixed' => [
            '\Laminas\Foo',
            'Zend\Foo',
        ];

        yield 'zend prefixed' => [
            'Laminas\Foo',
            '\Zend\Foo',
        ];
    }
}
