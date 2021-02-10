<?php


namespace TwigGenerator\Extension;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ExtraFilterExtension extends AbstractExtension
{
  /**
   * {@inheritdoc}
   */
  public function getFilters()
  {
    $options = ['is_safe' => ['html']];
    return array(
        'addslashes' => new TwigFilter('addslashes', 'addslashes', $options),
        'var_export' => new TwigFilter('var_export', 'var_export', $options),
        'is_numeric' => new TwigFilter('is_numeric', 'is_numeric', $options),
        'ucfirst'    => new TwigFilter('ucfirst', 'ucfirst', $options),
        'substr'     => new TwigFilter('substr', 'substr', $options),
    );
  }
}