<?php

/*
 * This file is part of the LineMob package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LineMob\Core;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Plugins\LockingMiddleware;
use LineMob\Core\Factory\MessageFactory;
use LineMob\Core\HttpClient\GuzzleHttpClient;
use LineMob\Core\Message\AudioMessage;
use LineMob\Core\Message\ButtonsMessage;
use LineMob\Core\Message\CarouselMessage;
use LineMob\Core\Message\ConfirmMessage;
use LineMob\Core\Message\ImageCarouselMessage;
use LineMob\Core\Message\ImageMapMessage;
use LineMob\Core\Message\ImageMessage;
use LineMob\Core\Message\LocationMessage;
use LineMob\Core\Message\MultiMessage;
use LineMob\Core\Message\StickerMessage;
use LineMob\Core\Message\TextMessage;
use LineMob\Core\Message\VideoMessage;
use LineMob\Core\Sender\LineSender;
use LineMob\Core\Sender\SenderInterface;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class QuickStart
{
    /**
     * @var array
     */
    private $commands = [];

    /**
     * @var array
     */
    private $middlewares = [];

    /**
     * QuickStart constructor.
     *
     * @param array $middlewares
     */
    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @param string $command FQCN
     * @param bool $default
     *
     * @return $this
     */
    public function addCommand($command, $default = false)
    {
        $this->commands[$command] = $default;

        return $this;
    }

    /**
     * @return MessageFactory
     */
    private function createMessageFactory()
    {
        $factory = new MessageFactory();
        $factory->add(new CarouselMessage());
        $factory->add(new ImageMessage());
        $factory->add(new ImageMapMessage());
        $factory->add(new MultiMessage());
        $factory->add(new TextMessage());
        $factory->add(new LocationMessage());
        $factory->add(new StickerMessage());
        $factory->add(new AudioMessage());
        $factory->add(new VideoMessage());
        $factory->add(new ConfirmMessage());
        $factory->add(new ButtonsMessage());
        $factory->add(new ImageCarouselMessage());

        return $factory;
    }

    /**
     * @param string $lineChannelToken
     * @param string $lineChannelSecret
     * @param array $httpConfig
     * @param SenderInterface|null $sender
     *
     * @return Receiver
     */
    public function setup($lineChannelToken, $lineChannelSecret, array $httpConfig = [], SenderInterface $sender = null)
    {
        $sender = $sender ?: new LineSender(
            new GuzzleHttpClient($lineChannelToken, $httpConfig),
            ['channelSecret' => $lineChannelSecret]
        );

        $registry = new Registry();
        $handler = new CommandHandler($sender, $this->createMessageFactory());

        foreach ($this->commands as $command => $default) {
            $registry->add($command, $handler, $default);
        }

        // should be first of all middlewares
        array_unshift($this->middlewares, new LockingMiddleware());

        // must be end of all middlewares
        array_push(
            $this->middlewares,
            new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                new InMemoryLocator($registry->getCommandList()),
                new HandleInflector()
            )
        );

        return new Receiver($sender, $registry, new CommandBus($this->middlewares), $handler);
    }
}
