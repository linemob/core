<?php

/*
 * This file is part of the LineMob package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LineMob\Core\Template;

use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

/**
 * @author WATCHDOGS <godoakbrutal@gmail.com>
 */
class MultiTemplate extends AbstractTemplate
{
    /**
     * @var array
     */
    public $multiMessage;

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        $mmb = new MultiMessageBuilder();
        foreach ($this->multiMessage as $message) {
            if (!$message instanceof AbstractTemplate) {
                continue;
            }

            $mmb->add($message->getTemplate());
        }

        return $mmb;
    }
}
