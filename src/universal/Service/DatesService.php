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

namespace Teknoo\East\CodeRunner\Service;

class DatesService
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * DatesService constructor.
     *
     * @param \DateTime|null $date
     */
    public function __construct(\DateTime $date = null)
    {
        if (!$this->date instanceof \DateTime) {
            //To prevent recall to __construct method
            $this->date = $date;
        }
    }

    /**
     * Initialize the date property value.
     */
    private function generateDateTimeInstance()
    {
        $this->date = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        if (!$this->date instanceof \DateTime) {
            $this->generateDateTimeInstance();
        }

        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return DatesService
     */
    public function setDate(\DateTime $date): DatesService
    {
        $this->date = $date;

        return $this;
    }
}
