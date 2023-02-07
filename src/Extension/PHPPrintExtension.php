<?php

namespace TwigGenerator\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Cedric LOMBARDOT
 * @author Piotr Gołębiewski <loostro@gmail.com>
 * @author Stéphane Escandell
 */
class PHPPrintExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        $options = ['is_safe' => ['html']];
        return [
            'as_php'   => new TwigFilter('as_php'  , $this->asPhp(...), $options),
            'php_name' => new TwigFilter('php_name', $this->phpName(...), $options),
        ];
    }

    public function asPhp(mixed $variable): string
    {
        $str = $this->export($variable);

        if (!is_array($variable)) {
            return $str;
        }

        preg_match_all('/[^> ]+::__set_state\(array\((.+),\'loaded/i', $str, $matches);

        if (isset($matches[1][0])) {
            $params = 'return array('.$matches[1][0].')';
            $params = eval($params. '?>');

            $str_param = '';
            foreach ($params as $p) {
                if ('' !== $str_param) {
                    $str_param .= ', ';
                }
                $str_param .= $this->export($p);
            }

            $str = preg_replace("/([^> ]+)::__set_state\(/i", ' new \\\$0', $str);
            $str = str_replace('::__set_state', '', $str);
            $str = str_replace('array('.$matches[1][0].',\'loaded\' => false,  )', $str_param, $str);
        }

        return $str;
    }

    /** Converts string into valid PHP function name */
    public function phpName(string $str): string
    {
        $str = preg_replace('/[^\w]+/', '', $str);

        return $str;
    }

    private function export(mixed $variable): string
    {
        return str_replace(array("\n", 'array (', '     '), array('', 'array(', ''), var_export($variable, true));
    }

    public function getName(): string
    {
        return 'twig_generator_php_print';
    }
}
