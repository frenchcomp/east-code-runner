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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\CodeRunner\Task\Interfaces;

use Teknoo\Immutable\ImmutableInterface;

/**
 * Interface to define value object to define a status of a task.
 */
interface StatusInterface extends ImmutableInterface, \JsonSerializable
{
    /**
     * Name of the status.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Static method to reconstruct a StatusInterface instance from its json representation.
     *
     * @param array $values
     *
     * @return StatusInterface
     */
    public static function jsonDeserialize(array $values): StatusInterface;
}
