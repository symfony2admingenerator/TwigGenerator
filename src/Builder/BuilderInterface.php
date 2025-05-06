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
 * This interface defines the structure of builders.
 *
 * @author Cédric Lombardot
 */
interface BuilderInterface
{
    function setGenerator(Generator $generator): void;

    function getGenerator(): Generator;

    function addTemplateDir(string $templateDir): void;

    /**
     * @param string[] $templateDirs
     */
    function setTemplateDirs(array $templateDirs): void;

    /**
     * @return string[]
     */
    function getTemplateDirs(): array;

    /**
     * @return string[]
     */
    function getDefaultTemplateDirs(): array;

    function setTemplateName(string $templateName): void;

    function getTemplateName(): string;

    function getDefaultTemplateName(): string;

    /**
     * @param string|null $class    A classname.
     *
     * @return string   The short classname.
     */
    function getSimpleClassName(?string $class = null): string;

    function setOutputName(string $outputName): void;

    function getOutputName(): string;

    function mustOverwriteIfExists(): bool;

    function setMustOverwriteIfExists(bool $status = true): void;

    /**
     * @param array $variables  An array of variables.
     */
    function setVariables(array $variables): void;

    /**
     * @return array    An array of variables.
     */
    function getVariables(): array;

    function hasVariable($key): bool;

    function getVariable(string $key, mixed $default = null): mixed;

    function setVariable(string $key, mixed $value): void;

    function writeOnDisk(string $outputDirectory): void;

    function getCode(): string;

    /**
     * Add Twig filters to load for parsing and generating
     * templates.
     *
     * @param array $filters Filters to add
     */
    function addTwigFilters(array $filters): void;

    /**
     * Add Twig extensions to load for parsing and generating
     * templates.
     *
     * @param array $extensions Extensions to add
     */
    function addTwigExtensions(array $extensions): void;
}
