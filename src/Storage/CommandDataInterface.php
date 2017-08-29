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
interface CommandDataInterface
{
    /**
     * @return string
     */
    public function getLineUserId();

    /**
     * @param string $lineUserId
     */
    public function setLineUserId($lineUserId);

    /**
     * @return string
     */
    public function getLineActiveCmd();

    /**
     * @param string $lineActiveCmd
     */
    public function setLineActiveCmd($lineActiveCmd);

    /**
     * @return array
     */
    public function getLineCommandData();

    /**
     * @param array
     */
    public function setLineCommandData(array $lineCommandData);
}
