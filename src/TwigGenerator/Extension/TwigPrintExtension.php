<?php

namespace TwigGenerator\Extension;

/**
 * @author Cedric LOMBARDOT
 * @author Piotr Gołębiewski <loostro@gmail.com>
 * @author Stéphane Escandell
 */
class TwigPrintExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    protected $blockNames = array();

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'echo_twig'           => new \Twig_SimpleFunction($this, 'getEchoTwig'),
            'echo_block'          => new \Twig_SimpleFunction($this, 'getEchoBlock'),
            'echo_endblock'       => new \Twig_SimpleFunction($this, 'getEchoEndBlock'),
            'echo_for'            => new \Twig_SimpleFunction($this, 'getEchoFor'),
            'echo_endfor'         => new \Twig_SimpleFunction($this, 'getEchoEndFor'),
            'echo_raw'            => new \Twig_SimpleFunction($this, 'getEchoRaw'),
            'echo_endraw'         => new \Twig_SimpleFunction($this, 'getEchoEndRaw'),
            'echo_spaceless'      => new \Twig_SimpleFunction($this, 'getEchoSpaceless'),
            'echo_endspaceless'   => new \Twig_SimpleFunction($this, 'getEchoEndSpaceless'),
            'echo_extends'        => new \Twig_SimpleFunction($this, 'getEchoExtends'),
            'echo_if'             => new \Twig_SimpleFunction($this, 'getEchoIf'),
            'echo_else'           => new \Twig_SimpleFunction($this, 'getEchoElse'),
            'echo_elseif'         => new \Twig_SimpleFunction($this, 'getEchoElseIf'),
            'echo_endif'          => new \Twig_SimpleFunction($this, 'getEchoEndIf'),
            'echo_set'            => new \Twig_SimpleFunction($this, 'getEchoSet'),
            'echo_twig_arr'       => new \Twig_SimpleFunction($this, 'getEchoTwigArr'),
            'echo_twig_assoc'     => new \Twig_SimpleFunction($this, 'getEchoTwigAssoc'),
            'echo_twig_filter'    => new \Twig_SimpleFunction($this, 'getEchoTwigFilter'),
            'echo_include'        => new \Twig_SimpleFunction($this, 'getEchoInclude'),
            'echo_use'            => new \Twig_SimpleFunction($this, 'getEchoUse'),
            'echo_print_block'    => new \Twig_SimpleFunction($this, 'getEchoPrintBlock'),
            'char'                => new \Twig_SimpleFunction($this, 'char'),
        );
    }

    /**
     * Get characters by code
     *
     * Note: if the code is higher than 256, it will return the code mod 256.
     * For example: chr(321)=A because A=65(256)
     *
     * @param integer Any number of integer codes
     * @return string
     */
    public function char()
    {
        $str = '';

        foreach (func_get_args() as $char) {
            if (is_int($char)) {
                $str .= chr($char);
            }
        }

        return $str;
    }

    /**
     * Print "set" tag for variable $var name with value $value.
     * If $value_as_string is true, wrap the $value with quotes.
     *
     * Examples:
     * {{ echo_set('my_var', 'myvalue') }}
     *      => {% set my_var = "myvalue" %}
     * {{ echo_set('my_var', 'myObjectValue', false) }}
     *      => {% set my_var = myObjectValue %}
     *
     *
     * @param $var
     * @param $value
     * @param bool $value_as_string
     * @return string
     */
    public function getEchoSet($var, $value, $value_as_string = true)
    {
        if ($value_as_string) {
            return strtr('{% set %%var%% = "%%value%%" %}', array('%%var%%' => $var, '%%value%%' => $value));
        } else {
            return strtr('{% set %%var%% = %%value%% %}', array('%%var%%' => $var, '%%value%%' => $value));
        }
    }

    /**
     * Print "if" tag with $condition as condition
     *
     * @param $condition
     * @return string
     */
    public function getEchoIf($condition)
    {
        if (is_bool($condition)) {
            $condition = intval($condition);
        }

        return str_replace('%%condition%%', $condition, '{% if %%condition%% %}');
    }

    /**
     * Print "elseif" tag with $condition as condition
     *
     * @param $condition
     * @return string
     */
    public function getEchoElseIf($condition)
    {
        if (is_bool($condition)) {
            $condition = intval($condition);
        }

        return str_replace('%%condition%%', $condition, '{% elseif %%condition%% %}');
    }

    /**
     * Print "else" tag
     *
     * @return string
     */
    public function getEchoElse()
    {
        return '{% else %}';
    }

    /**
     * Print "endif" tag
     *
     * @return string
     */
    public function getEchoEndIf()
    {
        return '{% endif %}';
    }

    /**
     * Print "print" tag with $str as printed value. Value is printed unquoted.
     *
     * @param $str
     * @return string
     */
    public function getEchoTwig($str)
    {
        return sprintf('{{ %s }}', $str);
    }

    /**
     * Print "print" tag with $str as value and $filters filters applied.
     * If $asString is true, $str value is wrap by quote.
     * If $filters is an array, filters will be piped.
     *
     * Example:
     * {{ echo_twig_filter('myObjectValue', 'capitalize') }}
     *      => {{ myObjectValue|capitalize }}
     * {{ echo_twig_filter('myValue', 'capitalize', true) }}
     *      => {{ 'myValue'|capitalize }}
     * {{ echo_twig_filter('myObjectValue', ['capitalize', 'striptags']) }}
     *      => {{ myObjectValue|capitalize|striptags }}
     * {{ echo_twig_filter('myValue', ['capitalize', 'striptags'], true) }}
     *      => {{ 'myValue'|capitalize|striptags }}
     *
     * @param $str
     * @param string|array|null $filters
     * @param bool $asString
     * @return string
     */
    public function getEchoTwigFilter($str, $filters = null, $asString = false)
    {
        if (null === $filters) {
            return $this->getEchoTwig($str);
        }

        return strtr(
            '{{ %%str%%|%%filters%% }}',
            array(
                '%%str%%' => $asString ? '"'.$str.'"' : $str,
                '%%filters%%' => (is_array($filters) ? implode('|', $filters) : $filters)
            )
        );
    }

    /**
     * Print "block" tag with block name as $name
     *
     * @param $name
     * @return string
     */
    public function getEchoBlock($name)
    {
        $this->blockNames[] = $name;

        return str_replace('%%name%%', $name, '{% block %%name%% %}');
    }

    /**
     * Print "endblock" tag for latest opened block.
     * Latest opened block name is precised.
     *
     * @return string
     */
    public function getEchoEndBlock()
    {
        return str_replace('%%name%%', array_pop($this->blockNames), '{% endblock %%name%% %}');
    }

    /**
     * Print "extends" tag extending $name template.
     * Template name will be automatically wrapped by quotes.
     *
     * @param $name
     * @return string
     */
    public function getEchoExtends($name)
    {
        return str_replace('%%name%%', $name, '{% extends "%%name%%" %}');
    }

    /**
     * Print "for" tag statement.
     *      $object is the variable name for each entry.
     *      $in is the iterated value
     *      $key is the variable name for keys if not null
     *
     * Example:
     * {{ echo_for('item', 'myListObject') }}
     *      => {% for item in myListObject %}
     * {{ echo_for('item', 'myListObject', 'key') }}
     *      => {% for key,item in myListObject %}
     *
     * @param string $object
     * @param string $in
     * @param string $key
     * @return string
     */
    public function getEchoFor($object, $in, $key = null)
    {
        return strtr(
            '{% for %%key%%%%object%% in %%in%% %}',
            array(
                '%%object%%' => $object,
                '%%in%%' => $in,
                '%%key%%' => $key ? $key . ',' : '' )
        );
    }

    /**
     * Print "endfor" tag
     *
     * @return string
     */
    public function getEchoEndFor()
    {
        return '{% endfor %}';
    }

    /**
     * Print "raw" tag
     *
     * @return string
     */
    public function getEchoRaw()
    {
        return '{% raw %}';
    }

    /**
     * Print "endraw" tag
     *
     * @return string
     */
    public function getEchoEndRaw()
    {
        return '{% endraw %}';
    }

    /**
     * Print "spaceless" tag
     *
     * @return string
     */
    public function getEchoSpaceless()
    {
        return '{% spaceless %}';
    }

    /**
     * Print "endspacelss" tag
     *
     * @return string
     */
    public function getEchoEndSpaceless()
    {
        return '{% endspaceless %}';
    }

    /**
     * Converts an array to a twig array expression (string).
     * Only in case a value contains '{{' and '}}' the value won't be
     * wrapped in quotes.
     *
     * An array like:
     * <code>
     * $array = array('a' => 'b', 'c' => 'd');
     * </code>
     *
     * Will be converted to:
     * <code>
     * "[ 'b', 'd']"
     * </code>
     *
     * @return string The parameters to be used in a URL
     */
    public function getEchoTwigArr(array $arr)
    {
        $contents = array();
        foreach ($arr as $key => $value) {
            if (!strstr($value, '{{') || !strstr($value, '}}')) {
                $value = "'$value'";
            } else {
                $value = trim(str_replace(array('{{', '}}'), '', $value));
            }

            $contents[] = "$value";
        }

        return '[ ' . implode(', ', $contents) . ' ]';
    }

    /**
     * Converts an assoc array to a twig array expression (string) .
     * Only in case a value contains '{{' and '}}' the value won't be
     * wrapped in quotes.
     *
     * An array like:
     * <code>
     * $array = array('a' => 'b', 'c' => 'd', 'e' => '{{f}}');
     * </code>
     *
     * Will be converted to:
     * <code>
     * "{ a: 'b', c: 'd', e: f }"
     * </code>
     *
     * @return string
     */
    public function getEchoTwigAssoc(array $arr)
    {
        $contents = array();
        foreach ($arr as $key => $value) {
            if (!strstr($value, '{{') || !strstr($value, '}}')) {
                $value = "'$value'";
            } else {
                $value = trim(str_replace(array('{{', '}}'), '', $value));
            }

            $contents[] = "$key: $value";
        }

        return '{ ' . implode(', ', $contents) . ' }';
    }

    /**
     * Print "include" tag, including $twig template with $params statement.
     * If $paramsOnly is true, append the 'only' Twig keyword to the tag.
     *
     * Example:
     *  {{ echo_include('myawesometemplate.html.twig', {'val1': 'myVal1'}, true) }}
     *      => {% include "myawesometemplate.html.twig" with {'val1': 'myVal1'} only %}
     *
     * @param $twig
     * @param array $params
     * @param bool $paramsOnly
     * @return string
     */
    public function getEchoInclude($twig, array $params = array(), $paramsOnly = false)
    {
        return sprintf(
            '{%% include "%s" with %s %s%%}',
            $twig,
            $this->getEchoTwigAssoc($params),
            $paramsOnly ? 'only ' : ''
        );
    }

    /**
     * Print "use" tag for template $name. Template name will be automatically
     * wrapped by quotes.
     *
     * @param $name
     * @return string
     */
    public function getEchoUse($name)
    {
        return str_replace('%%name%%', $name, '{% use \'%%name%%\' %}');
    }

    /**
     * Print block function call to the block $name.
     * $name will be automatically wrapped by quote.
     *
     * @param $name
     * @return string
     */
    public function getEchoPrintBlock($name)
    {
        return str_replace('%%name%%', $name, '{{ block(\'%%name%%\') }}');
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_generator_twig_print';
    }
}
