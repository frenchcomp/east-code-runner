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

use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\Immutable\ImmutableTrait;

/**
 * Class Status.
 * Default implementation of StatusInterface, as Value Object (aka immutable).
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Status implements StatusInterface
{
    use ImmutableTrait;

    const STATUS_REGISTERED = 'Registered';
    const STATUS_PLANNED = 'Planned';
    const STATUS_EXECUTING = 'Executing';
    const STATUS_EXECUTED = 'Executed';

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isFinal = false;

    /**
     * Status constructor.
     *
     * @param string $name
     * @param bool   $isFinal
     */
    public function __construct(string $name, bool $isFinal = false)
    {
        $this->name = $name;
        $this->isFinal = $isFinal;

        $this->uniqueConstructorCheck();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function isFinal(): bool
    {
        return $this->isFinal;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'class' => static::class,
            'name' => $this->getName(),
            'isFinal' => $this->isFinal(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function jsonDeserialize(array $values): StatusInterface
    {
        if (!isset($values['class']) || static::class != $values['class']) {
            throw new \InvalidArgumentException('class is not matching with the serialized values');
        }

        return new static($values['name'], !empty($values['isFinal']));
    }
}
