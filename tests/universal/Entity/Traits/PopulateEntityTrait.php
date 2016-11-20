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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\East\CodeRunner\Entity\Traits;

trait PopulateEntityTrait
{
    /**
     * Return the entity instance to use in this tests.
     *
     * @return object
     */
    abstract protected function buildEntity();

    /**
     * @param array $data
     *
     * @return object
     */
    protected function generateEntityPopulated($data = array())
    {
        //Build a new instance of this object
        $entityObject = $this->buildEntity();

        if (!empty($data)) {
            //We must populate the object's var, we use ReflectionClass api to bypass visibility scope constraints
            $reflectionClassObject = new \ReflectionClass($entityObject);

            foreach ($data as $fieldName => $value) {
                if ($reflectionClassObject->hasProperty($fieldName)) {
                    $propertyObject = $reflectionClassObject->getProperty($fieldName);
                    $propertyObject->setAccessible(true);
                    $propertyObject->setValue($entityObject, $value);
                }
            }
        }

        return $entityObject;
    }
}
