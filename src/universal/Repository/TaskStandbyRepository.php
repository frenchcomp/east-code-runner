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

namespace Teknoo\East\CodeRunner\Repository;

use Doctrine\ORM\EntityRepository;
use Teknoo\East\CodeRunner\Entity\TaskStandby;

class TaskStandbyRepository extends EntityRepository
{
    /**
     * @param string $identifier
     *
     * @return TaskStandby|false
     */
    public function fetchNextTaskStandby(string $identifier)
    {
        $queryBuilder = $this->createQueryBuilder('ts');
        $queryBuilder->innerJoin('ts.task', 't');
        $queryBuilder->addSelect('t');
        $queryBuilder->andWhere('ts.runnerIdentifier = :runnerIdentifier');
        $queryBuilder->setParameter('runnerIdentifier', $identifier);
        $queryBuilder->orderBy('te.created_at', 'ASC');
        $queryBuilder->setMaxResults(1);

        $query = $queryBuilder->getQuery();
        $taskStandby = $query->getOneOrNullResult();

        if (!$taskStandby instanceof TaskStandby) {
            return false;
        }

        return $taskStandby;
    }

    /**
     * To perform a batch update request to delete all entries of task standby.
     *
     * @param \DateTime $date
     *
     * @return TaskStandbyRepository
     */
    public function clearAll(\DateTime $date): TaskStandbyRepository
    {
        //Prepare the request update
        $queryBuilder = $this->createQueryBuilder('te');
        $queryBuilder->update();
        $queryBuilder->andWhere('te.deletedAd <> null');
        $queryBuilder->andWhere('te.deletedAt = :dateValue');
        $queryBuilder->setParameter('dateValue', $date->format('Y-m-d H:i:s'));

        //Execute it
        $query = $queryBuilder->getQuery();
        $query->execute();

        return $this;
    }
}
