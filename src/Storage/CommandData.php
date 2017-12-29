<?php

/*
 * This file is part of the LineMob package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LineMob\Core\Storage;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class CommandData implements CommandDataInterface
{
    /**
     * @var string
     */
    protected $lineUserId;

    /**
     * @var string
     */
    protected $lineActiveCmd;

    /**
     * @var \Datetime
     */
    protected $lineActiveCmdExpiredAt;

    /**
     * @var array
     */
    protected $lineCommandData = [];

    /**
     * {@inheritdoc}
     */
    public function getLineUserId()
    {
        return $this->lineUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function setLineUserId($lineUserId)
    {
        $this->lineUserId = $lineUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function getLineActiveCmd()
    {
        return $this->lineActiveCmd;
    }

    /**
     * {@inheritdoc}
     */
    public function setLineActiveCmd($lineActiveCmd)
    {
        $this->lineActiveCmd = $lineActiveCmd;
    }

    /**
     * {@inheritdoc}
     */
    public function getLineCommandData()
    {
        return $this->lineCommandData;
    }

    /**
     * {@inheritdoc}
     */
    public function setLineCommandData(array $lineCommandData)
    {
        $this->lineCommandData = $lineCommandData;
    }

    /**
     * {@inheritdoc}
     */
    public function getLineActiveCmdExpiredAt()
    {
        return $this->lineActiveCmdExpiredAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setLineActiveCmdExpiredAt(\DateTime $lineActiveCmdExpiredAt = null)
    {
        $this->lineActiveCmdExpiredAt = $lineActiveCmdExpiredAt;
    }
}
