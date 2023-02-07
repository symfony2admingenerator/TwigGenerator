<?php

/**
 * This file is part of the TwigGenerator package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace TwigGenerator\Builder;

/**
 * @author Cédric Lombardot
 */
class Generator
{
    final protected const TEMP_DIR_PREFIX = 'TwigGenerator';

    protected string $tempDir;

    protected array $twigExtensions = [];

    protected array $twigFilters = [];

    protected array $builders = [];

    protected bool $mustOverwriteIfExists = false;

    /**
     * @var string[]
     */
    protected array $templateDirectories = [];

    protected array $variables = [];

    protected bool $autoRemoveTempDir = true;

    /**
     * Init a new generator and automatically define the base of temp directory.
     *
     * @param string|null $baseTempDir Existing base directory for temporary template files
     */
    public function __construct(string $baseTempDir = null)
    {
        if (null === $baseTempDir) {
            $baseTempDir = sys_get_temp_dir();
        }

        $this->tempDir = realpath($baseTempDir).DIRECTORY_SEPARATOR.self::TEMP_DIR_PREFIX;

        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    public function setAutoRemoveTempDir($autoRemoveTempDir = true): void
    {
        $this->autoRemoveTempDir = $autoRemoveTempDir;
    }

    public function setMustOverwriteIfExists($status = true): void
    {
        $this->mustOverwriteIfExists = $status;
    }

    public function setTemplateDirs(array $templateDirs): void
    {
        $this->templateDirectories = $templateDirs;
    }

    /**
     * Ensure to remove the temp directory.
     */
    public function __destruct()
    {
        if ($this->tempDir && is_dir($this->tempDir) && $this->autoRemoveTempDir) {
            $this->removeDir($this->tempDir);
        }
    }

    public function setTempDir(string $tempDir): void
    {
        $this->tempDir = $tempDir;
    }

    public function getTempDir(): string
    {
        return $this->tempDir;
    }

    /**
     * Set Twig extensions to load before parsing
     * templates.
     */
    public function setTwigExtensions(array $twigExtensions): void
    {
        $this->twigExtensions = $twigExtensions;
    }

    /**
     * Set Twig filters to load before parsing
     * templates.
     */
    public function setTwigFilters(array $twigFilters): void
    {
        $this->twigFilters = $twigFilters;
    }

    public function getBuilders(): array
    {
        return $this->builders;
    }

    public function addBuilder(BuilderInterface $builder): BuilderInterface
    {
        $builder->setGenerator($this);
        $builder->addTwigExtensions($this->twigExtensions);
        $builder->addTwigFilters($this->twigFilters);
        $builder->setTemplateDirs($this->templateDirectories);
        $builder->setMustOverwriteIfExists($this->mustOverwriteIfExists);
        $builder->setVariables(array_merge($this->variables, $builder->getVariables()));   

        $this->builders[$builder->getSimpleClassName()] = $builder;

        return $builder;
    }

    public function setVariables(array $variables = []): void
    {
        $this->variables = $variables;
    }

    public function writeOnDisk(string $outputDirectory): void
    {
        foreach ($this->getBuilders() as $builder) {
            $builder->writeOnDisk($outputDirectory);
        }
    }

    private function removeDir(string $target): void
    {
        $fp = opendir($target);
        while (false !== $file = readdir($fp)) {
            if (in_array($file, array('.', '..'))) {
                continue;
            }

            if (is_dir($target.'/'.$file)) {
                self::removeDir($target.'/'.$file);
            } else {
                unlink($target.'/'.$file);
            }
        }
        closedir($fp);
        rmdir($target);
    }
}
