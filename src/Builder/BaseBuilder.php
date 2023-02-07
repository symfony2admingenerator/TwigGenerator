<?php

/**
 * This file is part of the TwigGenerator package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace TwigGenerator\Builder;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use TwigGenerator\Extension\ExtraFilterExtension;
use TwigGenerator\Extension\PHPPrintExtension;
use TwigGenerator\Extension\TwigPrintExtension;

/**
 * @author Cédric Lombardot
 */
abstract class BaseBuilder implements BuilderInterface
{
    /**
     * Default Twig file extension.
     */
    final protected const TWIG_EXTENSION = '.php.twig';

    protected Generator $generator;

    /**
     * @var string[]   A list of template directories.
     */
    protected array $templateDirectories = [];

    protected string $templateName = '';

    protected string $outputName = '';

    protected bool $mustOverwriteIfExists = false;

    protected array $twigFilters = [];

    protected array $variables = [];

    protected array $twigExtensions = [
        PHPPrintExtension::class,
        TwigPrintExtension::class,
        ExtraFilterExtension::class,
    ];

    public function __construct()
    {
        $this->templateDirectories = $this->getDefaultTemplateDirs();
        $this->templateName = $this->getDefaultTemplateName();
    }

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }

    public function getGenerator(): Generator
    {
        return $this->generator;
    }

    public function addTemplateDir($templateDir): void
    {
        $this->templateDirectories[$templateDir] = $templateDir;
    }

    public function setTemplateDirs(array $templateDirs): void
    {
        $this->templateDirectories = $templateDirs;
    }

    /** @return string[] */
    public function getTemplateDirs(): array
    {
        return $this->templateDirectories;
    }

    public function getDefaultTemplateDirs(): array
    {
        return [];
    }

    public function setTemplateName(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function getDefaultTemplateName(): string
    {
        return $this->getSimpleClassName() . self::TWIG_EXTENSION;
    }

    public function getSimpleClassName($class = null): string
    {
        if (null === $class) {
            $class = self::class;
        }

        $classParts = explode('\\', $class);
        return array_pop($classParts);
    }

    public function setOutputName(string $outputName): void
    {
        $this->outputName = $outputName;
    }

    public function getOutputName(): string
    {
        return $this->outputName;
    }

    public function mustOverwriteIfExists(): bool
    {
        return $this->mustOverwriteIfExists;
    }

    public function setMustOverwriteIfExists(bool $status = true): void
    {
        $this->mustOverwriteIfExists = $status;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    public function setVariable(string $key, mixed $value): void
    {
        $this->variables[$key] = $value;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function hasVariable($key): bool
    {
        return isset($this->variables[$key]);
    }

    public function getVariable(string $key, mixed $default = null): mixed
    {
        return $this->hasVariable($key) ? $this->variables[$key] : $default;
    }

    public function writeOnDisk(string $outputDirectory): void
    {
        $path = $outputDirectory . DIRECTORY_SEPARATOR . $this->getOutputName();
        $dir  = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists($path) || (file_exists($path) && $this->mustOverwriteIfExists)) {
            file_put_contents($path, $this->getCode());
        }
    }

    public function getCode(): string
    {
        $twig = $this->getTwigEnvironment();
        $template = $twig->load($this->getTemplateName());

        $variables = $this->getVariables();
        $variables['builder'] = $this;

        return $template->render($variables);
    }

    public function addTwigFilters(array $filters): void
    {
        foreach($filters as $filter) {
            if (is_string($filter)) {
                if (in_array($filter, $this->twigFilters)) {
                    continue;
                }
            }
            $this->twigFilters[] = $filter;
        }
    }

    public function addTwigExtensions(array $extensions): void
    {
        foreach($extensions as $extension) {
            if (is_string($extension)) {
                if (in_array($extension, $this->twigExtensions)) {
                    continue;
                }
            }
            $this->twigExtensions[] = $extension;
        }
    }

    /**
     * Initialize the Twig Environment which automatically loads
     * extensions and filters.
     */
    protected function getTwigEnvironment(): Environment
    {
        $loader = new FilesystemLoader($this->getTemplateDirs());
        $twig = new Environment($loader, array(
            'autoescape' => false,
            'strict_variables' => true,
            'debug' => true,
            'cache' => $this->getGenerator()->getTempDir(),
        ));

        $this->loadTwigExtensions($twig);
        $this->loadTwigFilters($twig);

        return $twig;
    }

    protected function loadTwigFilters(Environment $twig): void
    {
        foreach ($this->twigFilters as $twigFilter) {
            if (is_object($twigFilter)) {
                $twig->addFilter($twigFilter);
                continue;
            } elseif (($pos = strpos($twigFilter, ':')) !== false) {
                $twigFilterName = substr($twigFilter, $pos + 2);
            } else {
                $twigFilterName = $twigFilter;
            }
            $twig->addFilter(new TwigFilter($twigFilterName, $twigFilter));
        }
    }

    protected function loadTwigExtensions(Environment $twig): void
    {
        foreach ($this->twigExtensions as $twigExtensionName) {
            if (is_object($twigExtensionName)) {
                $twigExtension = $twigExtensionName;
            } else {
                $twigExtension = new $twigExtensionName();
            }
            if (!$twig->hasExtension(get_class($twigExtension))) {
                $twig->addExtension($twigExtension);
            }
        }
    }
}
