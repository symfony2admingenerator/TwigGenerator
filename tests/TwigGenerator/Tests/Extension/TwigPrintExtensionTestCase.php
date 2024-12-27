<?php
namespace TwigGenerator\Tests\Extension;

use TwigGenerator\Extension\TwigPrintExtension;

/**
 * Test TwigPrintExtension class
 */
class TwigPrintExtensionTestCase extends AbstractExtensionTestCase
{
    protected function getTwigVariables(): array
    {
        return [
            'name' => 'cedric',
            'arr'  => ['obj' => 'val'],
        ];
    }

    protected function getTestedExtension(): TwigPrintExtension
    {
        return new TwigPrintExtension();
    }

    public function testChar(): void
    {
        $tpls = [
            'single' => '{{ char(33) }}',
            'multiple' => '{{ char(33, 35, 35) }}',
        ];

        $returns = [
            'single' => ['!', 'Char return well for single argument'],
            'multiple' => ['!##', 'Char return well for multiple arguments'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoSet(): void
    {
        $tpls = [
            'string' => '{{ echo_set( "foo" , "bar" ) }}',
            'variable_key' => '{{ echo_set( name, "bar" ) }}',
            'variable_value' => '{{ echo_set( "foo", name ) }}',
            'array_key' => '{{ echo_set( arr.obj , "bar" ) }}',
            'array_value' => '{{ echo_set( "foo" , arr.obj ) }}',
            'not_value_as_string' => '{{ echo_set( "foo" , "bar", false ) }}'
        ];

        $returns = [
            'string' => [
                '{% set foo = "bar" %}',
                'Set return a good set tag with string elements'
            ],
            'variable_key' => [
                '{% set cedric = "bar" %}',
                'Set return a good set tag with variable as key'
            ],
            'variable_value' => [
                '{% set foo = "cedric" %}',
                'Set return a good set tag with variable as value'
            ],
            'array_key' => [
                '{% set val = "bar" %}',
                'Set return a good set tag with array element as key'
            ],
            'array_value' => [
                '{% set foo = "val" %}',
                'Set return a good set tag with array element as value'
            ],
            'not_value_as_string' => [
                '{% set foo = bar %}',
                'Set return a good set tag with false for option value_as_string'
            ],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoIf(): void
    {
        $tpls = [
            'string' => '{{ echo_if ( "a = b" ) }}',
            'variable' => '{{ echo_if ( name ~ " = \'cedric\'" ) }}',
            'array' => '{{ echo_if ( arr.obj ) }}',
            'boolean_true' => '{{ echo_if ( true ) }}',
            'boolean_false' => '{{ echo_if ( false ) }}',
        ];

        $returns = [
            'string' => [
                '{% if a = b %}',
                'If return a good If tag with string elements'
            ],
            'variable' => [
                '{% if cedric = \'cedric\' %}',
                'If return a good If tag with variable'
            ],
            'array' => [
                '{% if val %}',
                'If return a good If tag with array element'
            ],
            'boolean_true' => [
                '{% if 1 %}',
                'If return a good If tag with boolean true variable'
            ],
            'boolean_false' => [
                '{% if 0 %}',
                'If return a good If tag with boolean false variable'
            ],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoElseIf(): void
    {
        $tpls = [
            'string' => '{{ echo_elseif ( "a = b" ) }}',
            'variable' => '{{ echo_elseif ( name ~ " = \'cedric\'" ) }}',
            'array' => '{{ echo_elseif ( arr.obj ) }}',
            'boolean_true' => '{{ echo_elseif ( true ) }}',
            'boolean_false' => '{{ echo_elseif ( false ) }}',
        ];

        $returns = [
            'string' => [
                '{% elseif a = b %}',
                'Else If return a good Else If tag with string elements'
            ],
            'variable' => [
                '{% elseif cedric = \'cedric\' %}',
                'Else If return a good Else If tag with variable'
            ],
            'array' => [
                '{% elseif val %}',
                'Else If return a good Else If tag with array element'
            ],
            'boolean_true' => [
                '{% elseif 1 %}',
                'Else If return a good Else If tag with boolean true variable'
            ],
            'boolean_false' => [
                '{% elseif 0 %}',
                'Else If return a good Else If tag with boolean false variable'
            ],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoElse(): void
    {
        $tpls = [
            'empty' => '{{ echo_else() }}',
        ];

        $returns = [
            'empty' => ['{% else %}', 'Else return a good Else tag'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoEndIf(): void
    {
        $tpls = [
            'empty' => '{{ echo_endif () }}',
        ];

        $returns = [
            'empty' => ['{% endif %}', 'EndIf return a good EndIf tag'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoTwig(): void
    {
        $tpls = [
            'string' => '{{ echo_twig( "cedric" ) }}',
            'variable' => '{{ echo_twig( name ~ ".cedric" ) }}',
            'array' => '{{ echo_twig( arr.obj ) }}',
        ];

        $returns = [
            'string' => [
                '{{ cedric }}',
                'echo return a good echo tag with string elements'
            ],
            'variable' => [
                '{{ cedric.cedric }}',
                'echo return a good echo tag with variable'
            ],
            'array' => [
                '{{ val }}',
                'echo return a good echo tag with array element'
            ],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoBlock(): void
    {
        $tpls = [
            'string' => '{{ echo_block( "cedric" ) }}',
            'variable' => '{{ echo_block( name ~ "_cedric" ) }}',
            'array' => '{{ echo_block( arr.obj ) }}',
        ];

        $returns = [
            'string' => [
                '{% block cedric %}',
                'EchoBlock return a good block tag with string elements'
            ],
            'variable' => [
                '{% block cedric_cedric %}',
                'EchoBlock return a good block tag with variable'
            ],
            'array' => [
                '{% block val %}',
                'EchoBlock return a good echo block with array element'
            ],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoEndBlock(): void
    {
        $tpls = [
            'empty' => '{{ echo_endblock() }}',
        ];

        $returns = [
            'empty' => ['{% endblock  %}', 'endblock return a good endblock tag'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoExtends(): void
    {
        $tpls = [
            'string' => '{{ echo_extends( "cedric" ) }}',
            'variable' => '{{ echo_extends( name ~ "_cedric" ) }}',
            'array' => '{{ echo_extends( arr.obj ) }}',
        ];

        $returns = [
            'string' => [
                '{% extends "cedric" %}',
                'Extends return a good Extends tag with string elements'
            ],
            'variable' => [
                '{% extends "cedric_cedric" %}',
                'Extends return a good Extends tag with variable'
            ],
            'array' => [
                '{% extends "val" %}',
                'Extends return a good Extends with array element'
            ],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoFor(): void
    {
        $tpls = [
            'string' => '{{ echo_for( "foo" , "bar" ) }}',
            'variable_key' => '{{ echo_for( name, "bar" ) }}',
            'variable_value' => '{{ echo_for( "foo", name ) }}',
            'array_key' => '{{ echo_for( arr.obj , "bar" ) }}',
            'array_value' => '{{ echo_for( "foo" , arr.obj ) }}',
        ];

        $returns = [
            'string' => ['{% for foo in bar %}', 'for return a good for tag with string elements'],
            'variable_key' => ['{% for cedric in bar %}', 'for return a good for tag with variable as key'],
            'variable_value' => ['{% for foo in cedric %}', 'for return a good for tag with variable as value'],
            'array_key' => ['{% for val in bar %}', 'for return a good for tag with array element as key'],
            'array_value' => ['{% for foo in val %}', 'for return a good for tag with array element as value'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoEndFor(): void
    {
        $tpls = [
            'empty' => '{{ echo_endfor() }}',
        ];

        $returns = [
            'empty' => ['{% endfor %}', 'endfor return a good endfor tag'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetTwigArr(): void
    {
        $tpls = [
            'single_value' => "{{ echo_twig_arr({a: '1'}) }}",
            'several_values' => "{{ echo_twig_arr({alpha: 'abcde', beta: '12345'}) }}",
            'incomplete_variables' => "{{ echo_twig_arr({a:'{{ Item.id '}) }}",
            'complete_valiables' => "{{ echo_twig_arr({a:'{{ Item.id }}'}) }}",
            'mixed_values' => "{{ echo_twig_arr({a:'{{ Item.id }}', b:'Item.id }}', c: 'abcde'}) }}",
        ];

        $returns = [
            'single_value' => ["[ '1' ]", 'Transform correctly array with single value'],
            'several_values' => ["[ 'abcde', '12345' ]", 'Transform correctly array with values'],
            'incomplete_variables' => ["[ '{{ Item.id ' ]", 'Variables with unclosed {{ }} are quoted'],
            'complete_valiables' => ["[ Item.id ]", 'Variables are passed w/o surrounding {{ }}'],
            'mixed_values' => ["[ Item.id, 'Item.id }}', 'abcde' ]", 'Several values in the same expression'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetTwigAssoc(): void
    {
        $tpls = [
            'single_value' => "{{ echo_twig_assoc({a: '1'}) }}",
            'several_values' => "{{ echo_twig_assoc({alpha: 'abcde', beta: '12345'}) }}",
            'incomplete_variables' => "{{ echo_twig_assoc({a:'{{ Item.id '}) }}",
            'complete_valiables' => "{{ echo_twig_assoc({a:'{{ Item.id }}'}) }}",
            'mixed_values' => "{{ echo_twig_assoc({a:'{{ Item.id }}', b:'Item.id }}', c: 'abcde'}) }}",
        ];

        $returns = [
            'single_value' => ["{ a: '1' }", 'Transform correctly array with single value'],
            'several_values' => ["{ alpha: 'abcde', beta: '12345' }", 'Transform correctly array with values'],
            'incomplete_variables' => ["{ a: '{{ Item.id ' }", 'Variables with unclosed {{ }} are quoted'],
            'complete_valiables' => ["{ a: Item.id }", 'Variables are passed w/o surrounding {{ }}'],
            'mixed_values' => ["{ a: Item.id, b: 'Item.id }}', c: 'abcde' }", 'Several values in the same expression'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoTwigFilter(): void
    {
        $tpls = [
            'none'   => '{{ echo_twig_filter( "cedric" ) }}',
            'object_string' => '{{ echo_twig_filter( "cedric", "foo" ) }}',
            'object_array'  => '{{ echo_twig_filter( "cedric", ["foo", "bar"] ) }}',
            'string_string' => '{{ echo_twig_filter( "cedric", "foo", true ) }}',
            'string_array'  => '{{ echo_twig_filter( "cedric", ["foo", "bar"], true ) }}',
        ];

        $returns = [
            'none'   => ['{{ cedric }}', 'echo return a good echo tag with no filters'],
            'object_string' => ['{{ cedric|foo }}', 'echo return a good object echo tag one filter'],
            'object_array'  => ['{{ cedric|foo|bar }}', 'echo return a good object echo tag with multiple filters'],
            'string_string' => ['{{ "cedric"|foo }}', 'echo return a good string echo tag one filter'],
            'string_array'  => ['{{ "cedric"|foo|bar }}', 'echo return a good string echo tag with multiple filters'],
        ];

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoInclude(): void
    {
        $tpls = [
            'string' => '{{ echo_include( "::base.html.twig" ) }}',
            'with_params' => '{{ echo_include( "::base.html.twig", {"hello": name} ) }}',
            'with_empty_params' => '{{ echo_include( "::base.html.twig", {} ) }}',
            'with_params_only' => '{{ echo_include( "::base.html.twig", {"hello": name}, true ) }}',
        ];

        $returns = [
            'string' => [
                '{% include "::base.html.twig" with {  } %}',
                'include return a good include tag with string elements'
            ],
            'with_params' => [
                '{% include "::base.html.twig" with { hello: \'cedric\' } %}',
                'include return a good include tag with string elements and params'
            ],
            'with_empty_params' => [
                '{% include "::base.html.twig" with {  } %}',
                'include return a good include tag with string elements and empty params'
            ],
            'with_params_only' => [
                '{% include "::base.html.twig" with { hello: \'cedric\' } only %}',
                'include return a good include tag with string elements and params only'
            ],
        ];

        $this->runTwigTests($tpls, $returns);
    }
}