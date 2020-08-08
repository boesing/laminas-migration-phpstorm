<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Command;

use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;
use Boesing\Laminas\Migration\PhpStorm\Service\MetadataGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function dirname;
use function extension_loaded;
use function file_put_contents;
use function ini_set;
use function is_string;
use function is_writable;
use const PHP_INT_MAX;

final class GenerateCommand extends Command
{
    private const EXIT_CODE_MISSING_VENDOR = 1;
    private const EXIT_CODE_CANNOT_WRITE_OUTPUT = 2;
    private const EXIT_CODE_NOTHING_TODO = 3;

    /**
     * @var string
     */
    protected static $defaultName = 'migration:phpstorm-extended-meta';

    /**
     * @var LaminasFileFinder
     */
    private $finder;

    /**
     * @var MetadataGenerator
     */
    private $generator;

    /**
     * @var bool
     */
    private $xdebugLoaded;

    public function __construct(LaminasFileFinder $finder, MetadataGenerator $generator)
    {
        $this->finder = $finder;
        $this->generator = $generator;
        parent::__construct(self::$defaultName);
        $this->xdebugLoaded = extension_loaded('xdebug');
    }

    /**
     * @return void
     */
    protected function configure()
    {
        if ($this->xdebugLoaded) {
            ini_set('xdebug.max_nesting_level', (string) PHP_INT_MAX);
        }

        $this
            // ...
            ->addArgument('pathToVendor', InputArgument::REQUIRED, 'Path to the composer vendor/ directory.')
            ->addArgument('output', InputArgument::REQUIRED, 'Path where to store the generate phpstorm.meta.php');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->xdebugLoaded) {
            $output->writeln(sprintf(
                '<comment>You are running "%s" with xdebug enabled.'
                . ' This is not recommended as it may consume too many resources.</comment>',
                self::$defaultName
            ));
        }

        $vendorDirectory = $input->getArgument('pathToVendor');
        assert(is_string($vendorDirectory));
        if ($vendorDirectory === '') {
            $output->writeln('<error>Missing path to vendor directory!</error>');

            return self::EXIT_CODE_MISSING_VENDOR;
        }

        /** @var string $outputFile */
        $outputFile = $input->getArgument('output');
        if(!is_writable($outputFile) && !is_writable(dirname($outputFile))) {
            $output->writeln(sprintf(
                '<error>Cannot write %s. Please check if the path exists and create directories by yourself.</error>',
                $outputFile
            ));

            return self::EXIT_CODE_CANNOT_WRITE_OUTPUT;
        }

        $laminasFiles = $this->finder->find($vendorDirectory);

        if (!$laminasFiles) {
            $output->writeln(sprintf(
                '<info>There are no files available in "%s" which needs to be aliased.</info>',
                $vendorDirectory
            ));

            return self::EXIT_CODE_NOTHING_TODO;
        }

        $metadata = $this->generator->generateMetadata($laminasFiles);

        file_put_contents($outputFile, $metadata->toString());
        $output->writeln(sprintf('<info>Done. Generated metadata in %s</info>', $outputFile));

        return 0;
    }
}
