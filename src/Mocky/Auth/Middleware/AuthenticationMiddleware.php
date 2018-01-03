<?php

namespace LineMob\Core\Mocky\Auth\Middleware;

use League\Tactician\Middleware;
use LineMob\Core\Mocky\Auth\AuthenticationWorkflow;
use LineMob\Core\Mocky\Auth\Command\LoginCommand;
use LineMob\Core\Template\TextTemplate;

class AuthenticationMiddleware implements Middleware
{
    /**
     * @var AuthenticationWorkflow
     */
    private $workflow;

    public function __construct(AuthenticationWorkflow $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        if (!$command->storage) {
            throw new \LogicException("Require storage before using this AuthenticationMiddleware!");
        }

        if (!$command instanceof LoginCommand) {
            return $next($command);
        }

        $command->active = true;
        $command->message = new TextTemplate();

        if ($this->workflow->apply($command)) {
            return $next($command);
        }

        throw new \LogicException("Unknown case!");
    }
}
