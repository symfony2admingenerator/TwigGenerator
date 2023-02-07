<?php
namespace TwigGenerator\Tests\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;

/**
 *
 * Base class to test extensions. Provide builtin functions to initialize
 * new Twig environment in order to assert a template and its rendered version
 * are coherent.
 *
 * @package TwigGenerator\Tests\Extension
 * @author Stéphane Escandell
 */
abstract class BaseExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Variables used for templates
     */
    protected array $twigVariables = [];

    protected AbstractExtension $extension;

    abstract protected function getTestedExtension(): AbstractExtension;

    abstract protected function getTwigVariables(): array;

    public function setUp(): void
    {
        $this->twigVariables = $this->getTwigVariables();
        $this->extension = $this->getTestedExtension();
    }

    protected function runTwigTests(array $templates, array $returns): void
    {
        if (array_diff(array_keys($templates), array_keys($returns))) {
            throw new \LogicException(sprintf(
                'Error: invalid test case. Templates and returns keys mismatch: templates:[%s], returns:[%s] => [%s]',
                implode(', ', array_keys($templates)),
                implode(', ', array_keys($returns)),
                implode(', ', array_diff(array_keys($templates), array_keys($returns)))
            ));
        }
        $twig = $this->getEnvironment($templates);

        foreach ($templates as $name => $tpl) {
            $this->assertEquals(
                $returns[$name][0],
                $twig->loadTemplate($name)->render($this->twigVariables),
                $returns[$name][1]
            );
        }
    }

    protected function getEnvironment($templates, $options = []): Environment
    {
        $twig = new Environment(
            new ArrayLoader($templates),
            array_merge(
                [
                    'debug' => true,
                    'cache' => false,
                    'autoescape' => false,
                ],
                $options
            )
        );
        $twig->addExtension($this->extension);

        return $twig;
    }
}
