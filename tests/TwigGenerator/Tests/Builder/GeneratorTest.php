<?php

namespace TwigGenerator\Tests\Builder;

use PHPUnit\Framework\TestCase;
use TwigGenerator\Tests\Builder\Fixtures\Builder\DemoBuilder;
use TwigGenerator\Builder\Generator;

class GeneratorTest extends TestCase
{
    public function testAddBuilder(): void
    {
        $builder = new DemoBuilder();
        $builder->setVariables(['foo' => 'bar']);
        
        $generator = new Generator();
        $generator->setVariables(['foo' => 'common bar', 'baz' => 'common baz']);
        $generator->addBuilder($builder);

        $this->assertEquals('bar', $builder->getVariable('foo'), 'Builder variable is more important than common builder variable');
        $this->assertEquals('common baz', $builder->getVariable('baz'), 'Expected common builder variable');
    }

    public function testAddBuilderOnlyBuilderVariables(): void
    {
        $builder = new DemoBuilder();
        $builder->setVariables(['foo' => 'bar']);
        
        $generator = new Generator();        
        $generator->addBuilder($builder);

        $this->assertEquals('bar', $builder->getVariable('foo'), 'Expected builder variable');
    }

    public function testAddBuilderOnlyGeneratorCommonVariables(): void
    {
        $builder1 = new DemoBuilder();        
        $builder2 = new DemoBuilder();        
        
        $generator = new Generator();        
        $generator->setVariables(['foo' => 'common foo']);
        $generator->addBuilder($builder1);
        $generator->addBuilder($builder2);

        $this->assertEquals('common foo', $builder1->getVariable('foo'), 'Expected common builder variable');
        $this->assertEquals('common foo', $builder2->getVariable('foo'), 'Expected common builder variable');
    }

    public function testCallAddBuilderBeforeSetVariables(): void
    {
        $builder = new DemoBuilder();

        $generator = new Generator();        
        $generator->addBuilder($builder);
        $generator->setVariables(['foo' => 'bar']);
        
        $this->assertNull($builder->getVariable('foo'), ' If addBuilder is called before setVariables then common builder variables will be skipped');
    }    
}
