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

namespace Teknoo\East\CodeRunner\Task;

use Teknoo\East\CodeRunner\Runner\Interfaces\CapabilityInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\Immutable\ImmutableTrait;

/**
 * Class PHPCode.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class PHPCode implements CodeInterface
{
    use ImmutableTrait;

    /**
     * @var string
     */
    private $neededCapabilities;

    /**
     * @var string
     */
    private $code;

    /**
     * PHPCode constructor.
     *
     * @param string $code
     * @param array  $neededCapabilities
     */
    public function __construct(string $code, array $neededCapabilities)
    {
        $this->neededCapabilities = $neededCapabilities;
        $this->code = $code;

        $this->uniqueConstructorCheck();
    }

    /**
     * {@inheritdoc}
     */
    public function getNeededCapabilities(): array
    {
        return $this->neededCapabilities;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'class' => static::class,
            'neededCapabilities' => $this->getNeededCapabilities(),
            'code' => $this->code,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function jsonDeserialize(array $values): CodeInterface
    {
        if (!isset($values['class']) || static::class != $values['class']) {
            throw new \InvalidArgumentException('class is not matching with the serialized values');
        }

        $neededCapabilities = [];
        if (!empty($values['neededCapabilities']) && \is_array($values['neededCapabilities'])) {
            foreach ($values['neededCapabilities'] as $serializedCapability) {
                if (isset($serializedCapability['class'])
                    && \is_subclass_of($serializedCapability['class'], CapabilityInterface::class)) {
                    $capabilityClass = $serializedCapability['class'];
                    $neededCapabilities[] = $capabilityClass::jsonDeserialize($serializedCapability);
                }
            }
        }

        return new static($values['code'], $neededCapabilities);
    }
}
