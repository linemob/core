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
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $packageId;

    /**
     * @var string
     */
    public $stickerId;

    /**
     * @var string
     */
    public $image;

    /**
     * @var string
     */
    public $imagePreview;

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        $mmb = new MultiMessageBuilder();

        foreach ($this->multiMessage as $message) {
            switch ($message->getType()) {
                case 'text':
                    $mmb->add(new TextMessageBuilder(
                        $this->text
                    ));
                    break;
                case 'sticker':
                    $mmb->add(new StickerMessageBuilder(
                        $this->packageId,
                        $this->stickerId
                    ));
                    break;
                case 'image':
                    $mmb->add(new ImageMessageBuilder(
                        $this->image,
                        $this->imagePreview
                    ));
                    break;
            }
        }
        
        return $mmb;
    }
}
