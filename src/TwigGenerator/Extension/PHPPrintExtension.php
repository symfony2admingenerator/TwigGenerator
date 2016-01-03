<?php

namespace TwigGenerator\Extension;

/**
 * @author Cedric LOMBARDOT
 * @author Piotr Gołębiewski <loostro@gmail.com>
 * @author Stéphane Escandell
 */
class PHPPrintExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'as_php'   => new \Twig_SimpleFilter($this, 'asPhp'),
            'php_name' => new \Twig_SimpleFilter($this, 'phpName'),
        );
    }

    /**
     * @param $variable
     * @return string
     */
    public function asPhp($variable)
    {
        if (!is_array($variable)) {
            return $this->export($variable);
        }

        $str = $this->export($variable);

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

    /**
     * Converts string into valid PHP function name
     *
     * @param string $str
     * @return string
     */
    public function phpName($str)
    {
        $str = preg_replace('/[^\w]+/', '', $str);

        return $str;
    }

    /**
     * @param mixed $variable
     * @return string
     */
    private function export($variable)
    {
        return str_replace(array("\n", 'array (', '     '), array('', 'array(', ''), var_export($variable, true));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_generator_php_print';
    }
}
