<?php
declare(strict_types=1);

namespace Boesing\Laminas\Migration\PhpStorm\Command;

use Boesing\Laminas\Migration\PhpStorm\Service\LaminasFileFinder;
use Boesing\Laminas\Migration\PhpStorm\Service\MetadataGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function file_put_contents;
use function is_string;

final class GenerateCommand extends Command
{
    private const EXIT_CODE_MISSING_VENDOR = 1;
    private const EXIT_CODE_NOTHING_TODO = 2;

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

    public function __construct(LaminasFileFinder $finder, MetadataGenerator $generator)
    {
        $this->finder = $finder;
        $this->generator = $generator;
        parent::__construct(self::$defaultName);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            // ...
            ->addArgument('pathToVendor', InputArgument::REQUIRED, 'Path to the composer vendor/ directory.')
            ->addArgument('output', InputArgument::OPTIONAL, 'Path where to store the generate phpstorm.meta.php');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vendorDirectory = $input->getArgument('pathToVendor');
        assert(is_string($vendorDirectory));
        if ($vendorDirectory === '') {
            $output->writeln('<error>Missing path to vendor directory!</error>');

            return self::EXIT_CODE_MISSING_VENDOR;
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

        $out = $input->getArgument('output');
        if (!is_string($out)) {
            $output->writeln($metadata->toString());

            return 0;
        }

        file_put_contents($out, $metadata->toString());

        return 0;
    }
}
