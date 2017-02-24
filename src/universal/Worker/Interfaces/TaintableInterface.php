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

namespace Teknoo\East\CodeRunner\Worker\Interfaces;

/**
 * Interface TaintableInterface.
 * Interface to build a worker, dedicated to a runner into a Code Runner platform, working with PHP and Composer to
 * execute some tasks into a secured and isolated environment.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface TaintableInterface
{
    /**
     * To taint this consumer to stop and exit.
     *
     * @return TaintableInterface
     */
    public function enableTaintedFlag(): TaintableInterface;

    /**
     * To dispatch the information about tainted consumer.
     *
     * @param callable $callback
     *
     * @return TaintableInterface
     */
    public function tellMeIfYouAreTainted(callable $callback): TaintableInterface;
}
