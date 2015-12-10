<?php

namespace Scott\PassbookBundle\Resources\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use \Twig_Extension;

class VarsExtension extends Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'scott_twig.extension';
    }

    public function getFilters()
    {
        return [
            'json_decode'   => new \Twig_Filter_Method($this, 'jsonDecode'),
            'json_decode_array'   => new \Twig_Filter_Method($this, 'jsonDecodeArray'),
        ];
    }

    public function jsonDecode($str)
    {
        return json_decode($str);
    }

    public function jsonDecodeArray($str)
    {
        return json_decode($str, true);
    }
}