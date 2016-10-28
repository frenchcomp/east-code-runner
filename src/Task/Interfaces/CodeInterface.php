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
namespace Teknoo\East\CodeRunnerBundle\Task\Interfaces;

use Teknoo\Immutable\ImmutableInterface;

/**
 * Interface to define values objects to store PHP code to execute. These value object can also required several PHP
 * composer packages, or PHP extensions, returned by the method getNeededPackages
 */
interface CodeInterface extends ImmutableInterface, \JsonSerializable
{
    /**
     * Return the list of composer package or php extensions, like under the field "require" of composer.json file.
     *
     * @return array, name of package as key, version as value, support composer's syntax
     */
    public function getNeededPackages(): array;

    /**
     * Return the PHP code to execute.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Static method to reconstruct a CodeInterface instance from its json representation
     * @param array $values
     * @return CodeInterface
     */
    public static function jsonDeserialize(array $values): CodeInterface;
}