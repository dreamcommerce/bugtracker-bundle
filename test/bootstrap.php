<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

if (!($loader = @include __DIR__ . '/../vendor/autoload.php')) {
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}

if (!file_exists(__DIR__ . '/app/config/parameters.yml')) {
    die(<<<'EOT'
You must create parameters.yml, run the following command:
cp test/app/config/parameters.yml.dist test/app/config/parameters.yml
EOT
    );
}
