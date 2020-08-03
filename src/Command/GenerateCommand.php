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

final class GenerateCommand extends Command
{
    private const EXIT_CODE_NOTHING_TODO = 1;

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

    protected function configure()
    {
        $this
            // ...
            ->addArgument('pathToVendor', InputArgument::REQUIRED, 'Path to the composer vendor/ directory.')
            ->addArgument('output', InputArgument::OPTIONAL, 'Path where to store the generate phpstorm.meta.php');
    }

    /**
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vendorDirectory = $input->getArgument('pathToVendor');

        $laminasFiles = $this->finder->find($vendorDirectory);

        if (!$laminasFiles) {
            $output->writeln(sprintf(
                'There are no files available in "%s" which needs to be aliased.',
                $vendorDirectory
            ));

            return self::EXIT_CODE_NOTHING_TODO;
        }

        $metadata = $this->generator->generateMetadata($laminasFiles);

        $out = $input->getArgument('output');
        if (!$out) {
            $output->writeln($metadata->toString());
            return 0;
        }

        file_put_contents($out, $metadata->toString());

        return 0;
    }
}
