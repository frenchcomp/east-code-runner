<?php

/**
 * East CodeRunnerBundle.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\East\CodeRunnerBundle;

use Teknoo\States\Loader\FinderComposerIntegrated;
use Teknoo\States\Loader\LoaderComposer;

date_default_timezone_set('UTC');

error_reporting(E_ALL | E_STRICT);

ini_set('memory_limit', '16M');

$composerInstance = include(__DIR__.'/../vendor/autoload.php');

/**
 * Service to generate a finder for Stated class factory
 * @param string $statedClassName
 * @param string $path
 * @return FinderComposerIntegrated
 */
$finderFactory = function (string $statedClassName, string $path) use ($composerInstance) {
    return new FinderComposerIntegrated($statedClassName, $path, $composerInstance);
};

$factoryRepository = new \ArrayObject();
$loader = new LoaderComposer($composerInstance, $finderFactory, $factoryRepository);

//Register autoload function in the spl autoloader stack
spl_autoload_register(
    array($loader, 'loadClass'),
    true,
    true
);

