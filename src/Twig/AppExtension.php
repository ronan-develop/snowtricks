<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt']),
        ];
    }

    public function excerpt($text, $num)
    {
        return substr($text, 0, $num) . " ...";
    }
}
