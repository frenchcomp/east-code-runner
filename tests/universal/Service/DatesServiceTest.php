<?php

/**
 * East CodeRunner.
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

namespace Teknoo\Tests\East\CodeRunner\Service;

use Teknoo\East\CodeRunner\Service\DatesService;

/**
 * @cover Teknoo\East\CodeRunner\Service\DatesService
 */
class DatesServiceTest extends \PHPUnit_Framework_TestCase
{
    public function buildService()
    {
        return new DatesService();
    }

    public function testSetDate()
    {
        $datesService = $this->buildService();
        $datesService->setDate(new \DateTime('2011-02-03'));
        self::assertInstanceOf('\DateTime', $datesService->getDate());
        self::assertEquals('2011-02-03', $datesService->getDate()->format('Y-m-d'));
        self::assertInstanceOf('\DateTime', $this->buildService()->getDate());
    }

    public function testRefresh()
    {
        $datesService = $this->buildService();
        $datesService->setDate(new \DateTime('2011-02-03'));
        self::assertInstanceOf('\DateTime', $datesService->getDate());
        self::assertEquals('2011-02-03', $datesService->getDate()->format('Y-m-d'));
        $date = new \DateTime('2011-02-03');
        self::assertEquals($date->format('Y-m-d'), $datesService->getDate()->format('Y-m-d'));
    }
}
