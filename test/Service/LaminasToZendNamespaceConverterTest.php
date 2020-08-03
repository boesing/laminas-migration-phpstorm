<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Test\Service;

use Boesing\Laminas\Migration\PhpStorm\Service\LaminasToZendNamespaceConverter;
use Boesing\Laminas\Migration\PhpStorm\Service\LaminasToZendNamespaceConverterFactory;
use PHPUnit\Framework\TestCase;

final class LaminasToZendNamespaceConverterTest extends TestCase
{
    /**
     * @var LaminasToZendNamespaceConverter
     */
    private $converter;

    /**
     * @psalm-return list<list<string>>
     */
    public function classProvider(): array
    {
        return [
            // phpcs:disable Generic.Files.LineLength.TooLong
            // Expressive
            ['Zend\Expressive\Application', 'Mezzio\Application'],
            ['Zend\Expressive\Authentication\Authentication', 'Mezzio\Authentication\Authentication'],
            [
                'Zend\Expressive\Authentication\ZendAuthentication\AuthenticationAdapter',
                'Mezzio\Authentication\LaminasAuthentication\AuthenticationAdapter',
            ],
            [
                'Zend\Expressive\Authentication\ZendAuthentication\ZendAuthentication',
                'Mezzio\Authentication\LaminasAuthentication\LaminasAuthentication',
            ],
            ['Zend\Expressive\Authorization\Authorization', 'Mezzio\Authorization\Authorization'],
            ['Zend\Expressive\Authorization\Acl\ZendAclFactory', 'Mezzio\Authorization\Acl\LaminasAclFactory'],
            ['Zend\Expressive\Authorization\Rbac\ZendRbac', 'Mezzio\Authorization\Rbac\LaminasRbac'],
            ['Zend\Expressive\Router\Router', 'Mezzio\Router\Router'],
            ['Zend\Expressive\Router\ZendRouter', 'Mezzio\Router\LaminasRouter'],
            ['Zend\Expressive\Router\ZendRouter\RouterAdapter', 'Mezzio\Router\LaminasRouter\RouterAdapter'],
            ['Zend\Expressive\ZendView\ZendViewRenderer', 'Mezzio\LaminasView\LaminasViewRenderer'],
            ['Zend\ProblemDetails\ProblemDetails', 'Mezzio\ProblemDetails\ProblemDetails'],
            ['Zend\Expressive\Hal\LinkGenerator\ExpressiveUrlGenerator', 'Mezzio\Hal\LinkGenerator\MezzioUrlGenerator'],
            // phpcs:enable

            // Laminas
            ['Zend\Cache\Storage\Adapter\AbstractZendServer', 'Laminas\Cache\Storage\Adapter\AbstractZendServer'],
            ['Zend\Cache\Storage\Adapter\ZendServerDisk', 'Laminas\Cache\Storage\Adapter\ZendServerDisk'],
            ['Zend\Cache\Storage\Adapter\ZendServerShm', 'Laminas\Cache\Storage\Adapter\ZendServerShm'],
            ['Zend\Expressive', 'Laminas\Mezzio'],
            ['Zend\Log\Writer\ZendMonitor', 'Laminas\Log\Writer\ZendMonitor'],
            ['Zend\Main', 'Laminas\Main'],
            ['Zend\Psr7Bridge\Psr7Bridge', 'Laminas\Psr7Bridge\Psr7Bridge'],
            ['Zend\Psr7Bridge\ZendBridge', 'Laminas\Psr7Bridge\LaminasBridge'],
            ['Zend\Psr7Bridge\Zend\Psr7Bridge', 'Laminas\Psr7Bridge\Laminas\Psr7Bridge'],
            ['Zend\Psr7Bridge\Zend\ZendBridge', 'Laminas\Psr7Bridge\Laminas\LaminasBridge'],
            ['ZendService\ReCaptcha\MyClass', 'Laminas\ReCaptcha\MyClass'],
            ['ZendService\Twitter\MyClass', 'Laminas\Twitter\MyClass'],
            ['ZendXml\XmlService', 'Laminas\Xml\XmlService'],
            ['ZendOAuth\OAuthService', 'Laminas\OAuth\OAuthService'],
            ['ZendDiagnostics\Tools', 'Laminas\Diagnostics\Tools'],
            ['ZendDeveloperTools\Tools', 'Laminas\DeveloperTools\Tools'],
            ['ZF\ComposerAutoloading\Autoloading', 'Laminas\ComposerAutoloading\Autoloading'],
            ['ZF\DevelopmentMode\DevelopmentMode', 'Laminas\DevelopmentMode\DevelopmentMode'],

            // Apigility
            // phpcs:disable Generic.Files.LineLength.TooLong
            ['ZF\Apigility\BaseModule', 'Laminas\ApiTools\BaseModule'],
            [
                'ZF\Apigility\Admin\Controller\ApigilityVersionController',
                'Laminas\ApiTools\Admin\Controller\ApiToolsVersionController',
            ],
            ['ZF\Apigility\ApigilityModuleInterface', 'Laminas\ApiTools\ApiToolsModuleInterface', true],
            [
                'ZF\Apigility\Provider\ApigilityProviderInterface',
                'Laminas\ApiTools\Provider\ApiToolsProviderInterface',
                true,
            ],
            // phpcs:enable
        ];
    }

    /**
     * @dataProvider classProvider
     */
    public function testWillConvert(string $zend, string $laminas): void
    {
        self::assertSame($zend, $this->converter->convertToZendNamespace($laminas));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = (new LaminasToZendNamespaceConverterFactory())->__invoke();
    }
}
