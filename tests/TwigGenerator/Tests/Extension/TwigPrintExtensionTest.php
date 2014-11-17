<?php
namespace TwigGenerator\Tests\Extension;

use TwigGenerator\Extension\TwigPrintExtension;

/**
 * Test TwigPrintExtension class
 */
class TwigPrintExtensionTest extends BaseExtensionTest
{
    /**
     * @return array
     */
    protected function getTwigVariables()
    {
        return array(
            'name' => 'cedric',
            'arr'  => array('obj' => 'val'),
        );
    }

    /**
     * @return TwigPrintExtension
     */
    protected function getTestedExtension()
    {
        return new TwigPrintExtension();
    }

    public function testChar()
    {
        $tpls = array(
            'single' => '{{ char(33) }}',
            'multiple' => '{{ char(33, 35, 35) }}',
        );

        $returns = array(
            'single' => array('!', 'Char return well for single argument'),
            'multiple' => array('!##', 'Char return well for multiple arguments'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoSet()
    {
        $tpls = array(
            'string' => '{{ echo_set( "foo" , "bar" ) }}',
            'variable_key' => '{{ echo_set( name, "bar" ) }}',
            'variable_value' => '{{ echo_set( "foo", name ) }}',
            'array_key' => '{{ echo_set( arr.obj , "bar" ) }}',
            'array_value' => '{{ echo_set( "foo" , arr.obj ) }}',
            'not_value_as_string' => '{{ echo_set( "foo" , "bar", false ) }}'
        );

        $returns = array(
            'string' => array(
                '{% set foo = "bar" %}',
                'Set return a good set tag with string elements'
            ),
            'variable_key' => array(
                '{% set cedric = "bar" %}',
                'Set return a good set tag with variable as key'
            ),
            'variable_value' => array(
                '{% set foo = "cedric" %}',
                'Set return a good set tag with variable as value'
            ),
            'array_key' => array(
                '{% set val = "bar" %}',
                'Set return a good set tag with array element as key'
            ),
            'array_value' => array(
                '{% set foo = "val" %}',
                'Set return a good set tag with array element as value'
            ),
            'not_value_as_string' => array(
                '{% set foo = bar %}',
                'Set return a good set tag with false for option value_as_string'
            ),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoIf()
    {
        $tpls = array(
            'string' => '{{ echo_if ( "a = b" ) }}',
            'variable' => '{{ echo_if ( name ~ " = \'cedric\'" ) }}',
            'array' => '{{ echo_if ( arr.obj ) }}',
            'boolean_true' => '{{ echo_if ( true ) }}',
            'boolean_false' => '{{ echo_if ( false ) }}',
        );

        $returns = array(
            'string' => array(
                '{% if a = b %}',
                'If return a good If tag with string elements'
            ),
            'variable' => array(
                '{% if cedric = \'cedric\' %}',
                'If return a good If tag with variable'
            ),
            'array' => array(
                '{% if val %}',
                'If return a good If tag with array element'
            ),
            'boolean_true' => array(
                '{% if 1 %}',
                'If return a good If tag with boolean true variable'
            ),
            'boolean_false' => array(
                '{% if 0 %}',
                'If return a good If tag with boolean false variable'
            ),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoElseIf()
    {
        $tpls = array(
            'string' => '{{ echo_elseif ( "a = b" ) }}',
            'variable' => '{{ echo_elseif ( name ~ " = \'cedric\'" ) }}',
            'array' => '{{ echo_elseif ( arr.obj ) }}',
            'boolean_true' => '{{ echo_elseif ( true ) }}',
            'boolean_false' => '{{ echo_elseif ( false ) }}',
        );

        $returns = array(
            'string' => array(
                '{% elseif a = b %}',
                'Else If return a good Else If tag with string elements'
            ),
            'variable' => array(
                '{% elseif cedric = \'cedric\' %}',
                'Else If return a good Else If tag with variable'
            ),
            'array' => array(
                '{% elseif val %}',
                'Else If return a good Else If tag with array element'
            ),
            'boolean_true' => array(
                '{% elseif 1 %}',
                'Else If return a good Else If tag with boolean true variable'
            ),
            'boolean_false' => array(
                '{% elseif 0 %}',
                'Else If return a good Else If tag with boolean false variable'
            ),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoElse()
    {
        $tpls = array(
            'empty' => '{{ echo_else() }}',
        );

        $returns = array(
            'empty' => array('{% else %}', 'Else return a good Else tag'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoEndIf()
    {
        $tpls = array(
            'empty' => '{{ echo_endif () }}',
        );

        $returns = array(
            'empty' => array('{% endif %}', 'EndIf return a good EndIf tag'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoTwig()
    {
        $tpls = array(
            'string' => '{{ echo_twig( "cedric" ) }}',
            'variable' => '{{ echo_twig( name ~ ".cedric" ) }}',
            'array' => '{{ echo_twig( arr.obj ) }}',
        );

        $returns = array(
            'string' => array(
                '{{ cedric }}',
                'echo return a good echo tag with string elements'
            ),
            'variable' => array(
                '{{ cedric.cedric }}',
                'echo return a good echo tag with variable'
            ),
            'array' => array(
                '{{ val }}',
                'echo return a good echo tag with array element'
            ),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoBlock()
    {
        $tpls = array(
            'string' => '{{ echo_block( "cedric" ) }}',
            'variable' => '{{ echo_block( name ~ "_cedric" ) }}',
            'array' => '{{ echo_block( arr.obj ) }}',
        );

        $returns = array(
            'string' => array(
                '{% block cedric %}',
                'EchoBlock return a good block tag with string elements'
            ),
            'variable' => array(
                '{% block cedric_cedric %}',
                'EchoBlock return a good block tag with variable'
            ),
            'array' => array(
                '{% block val %}',
                'EchoBlock return a good echo block with array element'
            ),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoEndBlock()
    {
        $tpls = array(
            'empty' => '{{ echo_endblock() }}',
        );

        $returns = array(
            'empty' => array('{% endblock  %}', 'endblock return a good endblock tag'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoExtends()
    {
        $tpls = array(
            'string' => '{{ echo_extends( "cedric" ) }}',
            'variable' => '{{ echo_extends( name ~ "_cedric" ) }}',
            'array' => '{{ echo_extends( arr.obj ) }}',
        );

        $returns = array(
            'string' => array(
                '{% extends "cedric" %}',
                'Extends return a good Extends tag with string elements'
            ),
            'variable' => array(
                '{% extends "cedric_cedric" %}',
                'Extends return a good Extends tag with variable'
            ),
            'array' => array(
                '{% extends "val" %}',
                'Extends return a good Extends with array element'
            ),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoFor()
    {
        $tpls = array(
            'string' => '{{ echo_for( "foo" , "bar" ) }}',
            'variable_key' => '{{ echo_for( name, "bar" ) }}',
            'variable_value' => '{{ echo_for( "foo", name ) }}',
            'array_key' => '{{ echo_for( arr.obj , "bar" ) }}',
            'array_value' => '{{ echo_for( "foo" , arr.obj ) }}',
        );

        $returns = array(
            'string' => array('{% for foo in bar %}', 'for return a good for tag with string elements'),
            'variable_key' => array('{% for cedric in bar %}', 'for return a good for tag with variable as key'),
            'variable_value' => array('{% for foo in cedric %}', 'for return a good for tag with variable as value'),
            'array_key' => array('{% for val in bar %}', 'for return a good for tag with array element as key'),
            'array_value' => array('{% for foo in val %}', 'for return a good for tag with array element as value'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoEndFor()
    {
        $tpls = array(
            'empty' => '{{ echo_endfor() }}',
        );

        $returns = array(
            'empty' => array('{% endfor %}', 'endfor return a good endfor tag'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetTwigArr()
    {
        $tpls = array(
            'single_value' => "{{ echo_twig_arr({a: '1'}) }}",
            'several_values' => "{{ echo_twig_arr({alpha: 'abcde', beta: '12345'}) }}",
            'incomplete_variables' => "{{ echo_twig_arr({a:'{{ Item.id '}) }}",
            'complete_valiables' => "{{ echo_twig_arr({a:'{{ Item.id }}'}) }}",
            'mixed_values' => "{{ echo_twig_arr({a:'{{ Item.id }}', b:'Item.id }}', c: 'abcde'}) }}",
        );

        $returns = array(
            'single_value' => array("[ '1' ]", 'Transform correctly array with single value'),
            'several_values' => array("[ 'abcde', '12345' ]", 'Transform correctly array with values'),
            'incomplete_variables' => array("[ '{{ Item.id ' ]", 'Variables with unclosed {{ }} are quoted'),
            'complete_valiables' => array("[ Item.id ]", 'Variables are passed w/o surrounding {{ }}'),
            'mixed_values' => array("[ Item.id, 'Item.id }}', 'abcde' ]", 'Several values in the same expression'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetTwigAssoc()
    {
        $tpls = array(
            'single_value' => "{{ echo_twig_assoc({a: '1'}) }}",
            'several_values' => "{{ echo_twig_assoc({alpha: 'abcde', beta: '12345'}) }}",
            'incomplete_variables' => "{{ echo_twig_assoc({a:'{{ Item.id '}) }}",
            'complete_valiables' => "{{ echo_twig_assoc({a:'{{ Item.id }}'}) }}",
            'mixed_values' => "{{ echo_twig_assoc({a:'{{ Item.id }}', b:'Item.id }}', c: 'abcde'}) }}",
        );

        $returns = array(
            'single_value' => array("{ a: '1' }", 'Transform correctly array with single value'),
            'several_values' => array("{ alpha: 'abcde', beta: '12345' }", 'Transform correctly array with values'),
            'incomplete_variables' => array("{ a: '{{ Item.id ' }", 'Variables with unclosed {{ }} are quoted'),
            'complete_valiables' => array("{ a: Item.id }", 'Variables are passed w/o surrounding {{ }}'),
            'mixed_values' => array("{ a: Item.id, b: 'Item.id }}', c: 'abcde' }", 'Several values in the same expression'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoTwigFilter()
    {
        $tpls = array(
            'none'   => '{{ echo_twig_filter( "cedric" ) }}',
            'object_string' => '{{ echo_twig_filter( "cedric", "foo" ) }}',
            'object_array'  => '{{ echo_twig_filter( "cedric", ["foo", "bar"] ) }}',
            'string_string' => '{{ echo_twig_filter( "cedric", "foo", true ) }}',
            'string_array'  => '{{ echo_twig_filter( "cedric", ["foo", "bar"], true ) }}',
        );

        $returns = array(
            'none'   => array('{{ cedric }}', 'echo return a good echo tag with no filters'),
            'object_string' => array('{{ cedric|foo }}', 'echo return a good object echo tag one filter'),
            'object_array'  => array('{{ cedric|foo|bar }}', 'echo return a good object echo tag with multiple filters'),
            'string_string' => array('{{ "cedric"|foo }}', 'echo return a good string echo tag one filter'),
            'string_array'  => array('{{ "cedric"|foo|bar }}', 'echo return a good string echo tag with multiple filters'),
        );

        $this->runTwigTests($tpls, $returns);
    }

    public function testGetEchoInclude()
    {
        $tpls = array(
            'string' => '{{ echo_include( "::base.html.twig" ) }}',
            'with_params' => '{{ echo_include( "::base.html.twig", {"hello": name} ) }}',
            'with_empty_params' => '{{ echo_include( "::base.html.twig", {} ) }}',
            'with_params_only' => '{{ echo_include( "::base.html.twig", {"hello": name}, true ) }}',
        );

        $returns = array(
            'string' => array(
                '{% include "::base.html.twig" with {  } %}',
                'include return a good include tag with string elements'
            ),
            'with_params' => array(
                '{% include "::base.html.twig" with { hello: \'cedric\' } %}',
                'include return a good include tag with string elements and params'
            ),
            'with_empty_params' => array(
                '{% include "::base.html.twig" with {  } %}',
                'include return a good include tag with string elements and empty params'
            ),
            'with_params_only' => array(
                '{% include "::base.html.twig" with { hello: \'cedric\' } only %}',
                'include return a good include tag with string elements and params only'
            ),
        );

        $this->runTwigTests($tpls, $returns);
    }
}