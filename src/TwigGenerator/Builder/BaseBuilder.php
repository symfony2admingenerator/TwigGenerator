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
abstract class BaseBuilder implements BuilderInterface
{
    /**
     * Default Twig file extension.
     */
    const TWIG_EXTENSION = '.php.twig';

    /**
     * @var \TwigGenerator\Builder\Generator    The generator.
     */
    protected $generator;

    /**
     * @var array   A list of template directories.
     */
    protected $templateDirectories = array();

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $outputName;

    /**
     * @var Boolean
     */
    protected $mustOverwriteIfExists = false;

    /**
     * @var array
     */
    protected $twigFilters = array(
        'addslashes',
        'var_export',
        'is_numeric',
        'ucfirst',
        'substr',
    );

    /**
     * @var array
     */
    protected $variables = array();

    /**
     * @var array
     */
    protected $twigExtensions = array(
        '\\TwigGenerator\\Extension\\PHPPrintExtension',
        '\\TwigGenerator\\Extension\\TwigPrintExtension',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->templateDirectories = $this->getDefaultTemplateDirs();
        $this->templateName = $this->getDefaultTemplateName();
    }

    /**
     * {@inheritDoc}
     */
    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritDoc}
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * {@inheritDoc}
     */
    public function addTemplateDir($templateDir)
    {
        $this->templateDirectories[$templateDir] = $templateDir;
    }

    /**
     * {@inheritDoc}
     */
    public function setTemplateDirs(array $templateDirs)
    {
        $this->templateDirectories = $templateDirs;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateDirs()
    {
        return $this->templateDirectories;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultTemplateDirs()
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultTemplateName()
    {
        return $this->getSimpleClassName() . self::TWIG_EXTENSION;
    }

    /**
     * {@inheritDoc}
     */
    public function getSimpleClassName($class = null)
    {
        if (null === $class) {
            $class = get_class($this);
        }

        $classParts = explode('\\', $class);
        $simpleClassName = array_pop($classParts);

        return $simpleClassName;
    }

    /**
     * {@inheritDoc}
     */
    public function setOutputName($outputName)
    {
        $this->outputName = $outputName;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputName()
    {
        return $this->outputName;
    }

    /**
     * {@inheritDoc}
     */
    public function mustOverwriteIfExists()
    {
        return $this->mustOverwriteIfExists;
    }

    /**
     * {@inheritDoc}
     */
    public function setMustOverwriteIfExists($status = true)
    {
        $this->mustOverwriteIfExists = $status;
    }

    /**
     * {@inheritDoc}
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * {@inheritDoc}
     */
    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * {@inheritDoc}
     */
    public function hasVariable($key)
    {
        return isset($this->variables[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function getVariable($key, $default = null)
    {
        return $this->hasVariable($key) ? $this->variables[$key] : $default;
    }

    /**
     * {@inheritDoc}
     */
    public function writeOnDisk($outputDirectory)
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

    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        $twig = $this->getTwigEnvironment();
        $template = $twig->loadTemplate($this->getTemplateName());

        $variables = $this->getVariables();
        $variables['builder'] = $this;

        return $template->render($variables);
    }

    /**
     * {@inheritDoc}
     */
    public function addTwigFilters(array $filters)
    {
        $this->twigFilters = array_unique(array_merge($this->twigFilters, $filters));
    }

    /**
     * {@inheritDoc}
     */
    public function addTwigExtensions(array $extensions)
    {
        $this->twigExtensions = array_unique(array_merge($this->twigExtensions, $extensions));
    }

    /**
     * Initialize the Twig Environment which automatically loads
     * extensions and filters.
     *
     * @return \Twig_Environment
     */
    protected function getTwigEnvironment()
    {
        $loader = new \Twig_Loader_Filesystem($this->getTemplateDirs());
        $twig = new \Twig_Environment($loader, array(
            'autoescape' => false,
            'strict_variables' => true,
            'debug' => true,
            'cache' => $this->getGenerator()->getTempDir(),
        ));

        $this->loadTwigExtensions($twig);
        $this->loadTwigFilters($twig);

        return $twig;
    }

    private function loadTwigFilters(\Twig_Environment $twig)
    {
        foreach ($this->twigFilters as $twigFilter) {
            if (($pos = strpos($twigFilter, ':')) !== false) {
                $twigFilterName = substr($twigFilter, $pos + 2);
            } else {
                $twigFilterName = $twigFilter;
            }
            $twig->addFilter($twigFilterName, new \Twig_Filter_Function($twigFilter));
        }
    }

    private function loadTwigExtensions(\Twig_Environment $twig)
    {
        foreach ($this->twigExtensions as $twigExtensionName) {
            $twigExtension = new $twigExtensionName();
            $twig->addExtension($twigExtension);
        }
    }
}
