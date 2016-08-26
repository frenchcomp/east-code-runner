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
namespace Teknoo\East\CodeRunnerBundle\Task;

use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\Immutable\ImmutableTrait;

class TextResult implements ResultInterface, \JsonSerializable
{
    use ImmutableTrait;

    /**
     * @var string
     */
    private $output;

    /**
     * @var string
     */
    private $errors;

    /**
     * @var string
     */
    private $versions;

    /**
     * @var int
     */
    private $memorySize;

    /**
     * @var int
     */
    private $timeExecution;

    /**
     * TextResult constructor.
     * @param string $output
     * @param string $errors
     * @param string $versions
     * @param int $memorySize
     * @param int $timeExecution
     */
    public function __construct(string $output, string $errors, string $versions, int $memorySize, int $timeExecution)
    {
        $this->output = $output;
        $this->errors = $errors;
        $this->versions = $versions;
        $this->memorySize = $memorySize;
        $this->timeExecution = $timeExecution;

        $this->uniqueConstructorCheck();
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): string
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return $this->versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getMemorySize(): int
    {
        return $this->memorySize;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeExecution(): int
    {
        return $this->timeExecution;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'class' => static::class,
            'output' => $this->getOutput(),
            'errors' => $this->getErrors(),
            'versions' => $this->getVersion(),
            'memorySize' => $this->getMemorySize(),
            'timeExecution' => $this->getTimeExecution()
        ];
    }

    /**
     * @param array $values
     * @return TextResult
     */
    public static function jsonDeserialize(array $values)
    {
        if (!isset($values['class']) || static::class != $values['class']) {
            throw new \InvalidArgumentException('class is not matching with the serialized values');
        }

        return new static($values['output'], $values['errors'], $values['versions'], $values['memorySize'], $values['timeExecution']);
    }
}