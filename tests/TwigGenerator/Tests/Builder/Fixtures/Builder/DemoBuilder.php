<?php

namespace TwigGenerator\Tests\Builder\Fixtures\Builder;

use TwigGenerator\Builder\BaseBuilder;

class DemoBuilder extends BaseBuilder
{

    public function getDefaultTemplateDirs(): array
    {
        return [__DIR__.'/../Templates'];
    }
}
