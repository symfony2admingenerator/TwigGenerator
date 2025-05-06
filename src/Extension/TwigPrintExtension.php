<?php

namespace TwigGenerator\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Cedric LOMBARDOT
 * @author Piotr Gołębiewski <loostro@gmail.com>
 * @author Stéphane Escandell
 */
class TwigPrintExtension extends AbstractExtension
{
    /**
     * @var string[]
     */
    protected array $blockNames = [];

    public function getFunctions(): array
    {
        $options = ['is_safe' => ['html']];
        return [
            'echo_twig'           => new TwigFunction('echo_twig'        , $this->getEchoTwig(...), $options),
            'echo_block'          => new TwigFunction('echo_block'       , $this->getEchoBlock(...), $options),
            'echo_endblock'       => new TwigFunction('echo_endblock'    , $this->getEchoEndBlock(...), $options),
            'echo_for'            => new TwigFunction('echo_for'         , $this->getEchoFor(...), $options),
            'echo_endfor'         => new TwigFunction('echo_endfor'      , $this->getEchoEndFor(...), $options),
            'echo_raw'            => new TwigFunction('echo_raw'         , $this->getEchoRaw(...), $options),
            'echo_endraw'         => new TwigFunction('echo_endraw'      , $this->getEchoEndRaw(...), $options),
            'echo_extends'        => new TwigFunction('echo_extends'     , $this->getEchoExtends(...), $options),
            'echo_if'             => new TwigFunction('echo_if'          , $this->getEchoIf(...), $options),
            'echo_else'           => new TwigFunction('echo_else'        , $this->getEchoElse(...), $options),
            'echo_elseif'         => new TwigFunction('echo_elseif'      , $this->getEchoElseIf(...), $options),
            'echo_endif'          => new TwigFunction('echo_endif'       , $this->getEchoEndIf(...), $options),
            'echo_set'            => new TwigFunction('echo_set'         , $this->getEchoSet(...), $options),
            'echo_twig_arr'       => new TwigFunction('echo_twig_arr'    , $this->getEchoTwigArr(...), $options),
            'echo_twig_assoc'     => new TwigFunction('echo_twig_assoc'  , $this->getEchoTwigAssoc(...), $options),
            'echo_twig_filter'    => new TwigFunction('echo_twig_filter' , $this->getEchoTwigFilter(...), $options),
            'echo_include'        => new TwigFunction('echo_include'     , $this->getEchoInclude(...), $options),
            'echo_use'            => new TwigFunction('echo_use'         , $this->getEchoUse(...), $options),
            'echo_print_block'    => new TwigFunction('echo_print_block' , $this->getEchoPrintBlock(...), $options),
            'char'                => new TwigFunction('char'             , $this->char(...), $options),
        ];
    }

    /**
     * Get characters by code.
     *
     * Note: if the code is higher than 256, it will return the code mod 256.
     * For example: chr(321)=A because A=65(256)
     */
    public function char(int ...$chars): string
    {
        $str = '';

        foreach ($chars as $char) {
            $str .= chr($char);
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
     */
    public function getEchoSet(string $var, string $value, bool $value_as_string = true): string
    {
        if ($value_as_string) {
            return strtr('{% set %%var%% = "%%value%%" %}', ['%%var%%' => $var, '%%value%%' => $value]);
        } else {
            return strtr('{% set %%var%% = %%value%% %}', ['%%var%%' => $var, '%%value%%' => $value]);
        }
    }

    /** Print "if" tag with $condition as condition. */
    public function getEchoIf(mixed $condition): string
    {
        if (is_bool($condition)) {
            $condition = intval($condition);
        }

        return str_replace('%%condition%%', $condition, '{% if %%condition%% %}');
    }

    /** Print "elseif" tag with $condition as condition. */
    public function getEchoElseIf(mixed $condition): string
    {
        if (is_bool($condition)) {
            $condition = intval($condition);
        }

        return str_replace('%%condition%%', $condition, '{% elseif %%condition%% %}');
    }

    /** Print "else" tag. */
    public function getEchoElse(): string
    {
        return '{% else %}';
    }

    /** Print "endif" tag. */
    public function getEchoEndIf(): string
    {
        return '{% endif %}';
    }

    /** Print "print" tag with $str as printed value. Value is printed unquoted. */
    public function getEchoTwig(mixed $str): string
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
     */
    public function getEchoTwigFilter(mixed $str, string|array|null $filters = null, bool $asString = false): string
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

    /** Print "block" tag with block name as $name. */
    public function getEchoBlock(string $name): string
    {
        $this->blockNames[] = $name;

        return str_replace('%%name%%', $name, '{% block %%name%% %}');
    }

    /**
     * Print "endblock" tag for latest opened block.
     * Latest opened block name is precised.
     */
    public function getEchoEndBlock(): string
    {
        return str_replace('%%name%%', array_pop($this->blockNames), '{% endblock %%name%% %}');
    }

    /**
     * Print "extends" tag extending $name template.
     * Template name will be automatically wrapped by quotes.
     */
    public function getEchoExtends(string $name): string
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
     */
    public function getEchoFor(string $object, string $in, ?string $key = null): string
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
     */
    public function getEchoEndFor(): string
    {
        return '{% endfor %}';
    }

    /**
     * Print "raw" tag
     */
    public function getEchoRaw(): string
    {
        return '{% raw %}';
    }

    /**
     * Print "endraw" tag
     */
    public function getEchoEndRaw(): string
    {
        return '{% endraw %}';
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
    public function getEchoTwigArr(array $arr): string
    {
        $contents = array();
        foreach ($arr as $key => $value) {
            if (!str_contains($value, '{{') || !str_contains($value, '}}')) {
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
     */
    public function getEchoTwigAssoc(array $arr): string
    {
        $contents = array();
        foreach ($arr as $key => $value) {
            if (!str_contains($value, '{{') || !str_contains($value, '}}')) {
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
     */
    public function getEchoInclude(string $twig, array $params = [], bool $paramsOnly = false): string
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
     */
    public function getEchoUse(string $name): string
    {
        return str_replace('%%name%%', $name, '{% use \'%%name%%\' %}');
    }

    /**
     * Print block function call to the block $name.
     * $name will be automatically wrapped by quote.
     */
    public function getEchoPrintBlock(string $name): string
    {
        return str_replace('%%name%%', $name, '{{ block(\'%%name%%\') }}');
    }

    /**
     * Returns the name of the extension.
     */
    public function getName(): string
    {
        return 'twig_generator_twig_print';
    }
}
