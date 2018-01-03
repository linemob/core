<?php

/*
 * This file is part of the LineMob package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LineMob\Core\Workflow;

use LineMob\Core\Command\AbstractCommand;

/**
 * @author Bonn <im_bonbonn@hotmail.com>
 */
abstract class AbstractWorkflow
{
    /**
     * @var WorkflowRegistryInterface
     */
    protected $registry;

    /**
     * @var array
     */
    protected $mappingMethods;

    /**
     * @param WorkflowRegistryInterface $registry
     */
    public function __construct(WorkflowRegistryInterface $registry)
    {
        $registry->register($this->getConfig());
        $this->registry = $registry;
    }

    /**
     * @return array
     */
    abstract protected function getConfig();

    /**
     * @param AbstractCommand $command
     *
     * @return bool
     */
    public function apply(AbstractCommand $command)
    {
        foreach ($this->mappingMethods as $method) {
            if (call_user_func_array([$this, $method], [$command, $this->registry->get($command->storage)])) {
                return true;
            }
        }

        return false;
    }
}
