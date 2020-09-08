<?php

$year = date('Y');
$header = <<<EOF
(c) 2017-$year DreamCommerce

@package DreamCommerce\Component\BugTracker
@author Michał Korus <michal.korus@dreamcommerce.com>
@link https://www.dreamcommerce.com
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

return \PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2' => true,
        'header_comment' => [
            'commentType' => 'PHPDoc',
            'header' => $header,
            'location' => 'after_open',
            'separate' => 'both',
        ]
    ))
    ->setFinder($finder)
;