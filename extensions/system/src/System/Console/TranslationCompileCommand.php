<?php

namespace Pagekit\System\Console;

use Pagekit\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class TranslationCompileCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translation:compile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compiles translation files from .po -> .mo format';

    /**
     * The core extensions.
     *
     * @var array
     */
    protected $extensions;

    /**
     * Node visitors.
     *
     * @var NodeVisitor[]
     */
    protected $visitors;

    /**
     * The xgettext command availability.
     *
     * @var bool
     */
    protected $xgettext;

    /**
     * The .po file loader.
     *
     * @var PoFileLoader
     */
    protected $loader;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addArgument('extension', InputArgument::OPTIONAL, 'Extension name');
    }

    /**
     * Initialize the console command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->extensions = $this->pagekit['config']['extension.core'];
        $this->xgettext = !defined('PHP_WINDOWS_VERSION_MAJOR') && (bool)exec('which xgettext');
    }

    /**
     * Execute the console command.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $extension = $this->argument('extension') ? : 'system';
        $path = $this->getPath($extension);
        $languages = "$path/languages";

        $this->line("Looking for language files in extension '$extension'");

        chdir($this->pagekit['path']);

        if (!is_dir($languages)) {
            mkdir($languages, 0777, true);
        }

        foreach (Finder::create()->files()->in($languages)->name("*.po") as $file) {

            $this->line("Compiling " . $file->getRelativePathname() . " -> " . preg_replace('/\.po$/', '.mo', $file->getFilename()));
            exec('msgfmt -o  ' . preg_replace('/\.po$/', '.mo', $file->getPathname()) . ' ' . $file->getPathname());

        }

    }

    /**
     * Returns the extension path.
     *
     * @param  string $path
     * @return array
     */
    protected function getPath($path)
    {
        $root = $this->pagekit['path.extensions'];

        if (!is_dir($path = "$root/$path")) {
            $this->error("Can't find extension in '$path'");
            exit;
        }

        return $path;
    }

}
