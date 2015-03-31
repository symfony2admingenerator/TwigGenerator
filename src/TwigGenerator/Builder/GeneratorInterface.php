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
 * This interface defines the structure of generators.
 *
 * @author Cédric Lombardot
 */
interface GeneratorInterface
{
    /**
     * Init a new generator and automatically define the base of temp directory.
     * 
     * @param string $baseTempDir    Existing base directory for temporary template files
     */
    public function __construct($baseTempDir);

    public function setAutoRemoveTempDir($autoRemoveTempDir);

    public function setMustOverwriteIfExists($status);

    public function setTemplateDirs(array $templateDirs);

    /**
     * @param string The temporary directory path
     */
    public function setTempDir($tempDir);

    /**
     * @return string   The temporary directory.
     */
    public function getTempDir();

    /**
     * Set Twig extensions to load before parsing
     * templates.
     *
     * @param array $twigExtensions
     */
    public function setTwigExtensions(array $twigExtensions);

    /**
     * Set Twig filters to load before parsing
     * templates.
     *
     * @param array $twigFilters
     */
    public function setTwigFilters(array $twigFilters);

    /**
     * @return array    The list of builders.
     */
    public function getBuilders();

    /**
     * Add a builder.
     *
     * @param \TwigGenerator\Builder\BuilderInterface $builder  A builder.
     *
     * @return \TwigGenerator\Builder\BuilderInterface  The builder
     */
    public function addBuilder(BuilderInterface $builder);

    /**
     * Add an array of variables to pass to builders.
     *
     * @param array $variables  A set of variables.
     */
    public function setVariables(array $variables);

    /**
     * Generate and write classes to disk.
     *
     * @param string $outputDirectory   An output directory.
     */
    public function writeOnDisk($outputDirectory);

    /**
     * Remove a directory.
     *
     * @param string $target    A directory.
     */
    private function removeDir($target);
}
