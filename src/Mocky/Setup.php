<?php

namespace LineMob\Core\Mocky;

use LineMob\Core\Middleware\CommandSwitcherMiddleware;
use LineMob\Core\Middleware\DummyFallbackMiddleware;
use LineMob\Core\Middleware\DumpLogMiddleware;
use LineMob\Core\Middleware\StoreActiveCmdMiddleware;
use LineMob\Core\Mocky\Auth\AuthenticationFlow;
use LineMob\Core\Mocky\Auth\Command\ClearActiveCommand;
use LineMob\Core\Mocky\Auth\Command\LoginCommand;
use LineMob\Core\Mocky\Auth\Command\LogoutCommand;
use LineMob\Core\Mocky\Auth\Command\NonSecuredCommand;
use LineMob\Core\Mocky\Auth\Command\SecuredCommand;
use LineMob\Core\Mocky\Auth\Middleware\AuthenticationMiddleware;
use LineMob\Core\Mocky\Auth\Middleware\AuthorizationMiddleware;
use LineMob\Core\Mocky\Auth\Middleware\ClearActiveMiddleware;
use LineMob\Core\Mocky\Auth\Middleware\LogoutMiddleware;
use LineMob\Core\Mocky\Doctrine\StorageConnectMiddleware;
use LineMob\Core\Mocky\Doctrine\StoragePersistMiddleware;
use LineMob\Core\Mocky\Switcher\SomeCommand;
use LineMob\Core\Mocky\Switcher\SwitchedCommand;
use LineMob\Core\Mocky\Switcher\SwitchingMiddleware;
use LineMob\Core\QuickStart;
use LineMob\Core\Workflow\WorkflowRegistry;

class Setup
{
    /**
     * @param array $data
     *
     * @return array
     */
    public static function switching(array $data)
    {
        return (new QuickStart(
            [
                new SwitchingMiddleware(),
                new CommandSwitcherMiddleware(),
                new DummyFallbackMiddleware(),
                new DumpLogMiddleware(true),
            ]
        ))
            ->addCommand(SomeCommand::class, true)
            ->addCommand(SwitchedCommand::class)
            ->setup(null, null, [], new Sender())
            ->handle(json_encode($data));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function authen(array $data)
    {
        return (new QuickStart(
            [
                new StorageConnectMiddleware(),
                new ClearActiveMiddleware(), // Clear user's storage active
                new AuthorizationMiddleware(),
                new CommandSwitcherMiddleware(),
                new StoreActiveCmdMiddleware(),
                new LogoutMiddleware(),
                new AuthenticationMiddleware(new AuthenticationFlow(new WorkflowRegistry(null, true))),
                new DummyFallbackMiddleware(),
                new StoragePersistMiddleware(),
                new DumpLogMiddleware(true),
            ]
        ))
            ->addCommand(NonSecuredCommand::class, true)
            ->addCommand(ClearActiveCommand::class)
            ->addCommand(SecuredCommand::class)
            ->addCommand(LoginCommand::class)
            ->addCommand(LogoutCommand::class)
            ->setup(null, null, [], new Sender())
            ->handle(json_encode($data));
    }
}
