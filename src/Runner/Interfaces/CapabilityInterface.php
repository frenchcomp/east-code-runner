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
namespace Teknoo\East\CodeRunnerBundle\Runner\Interfaces;

use Teknoo\Immutable\ImmutableInterface;

/**
 * Interface to define as value object, capabilities of a runner
 */
interface CapabilityInterface extends ImmutableInterface
{
    /**
     * Type/identifier of the capability, like the language provided by the runner, its versions, its extensions.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Value of the capability, can be a string, int, boolean or all other needed value.
     *
     * @return mixed
     */
    public function getValue();
}