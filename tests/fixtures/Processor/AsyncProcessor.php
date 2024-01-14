<?php

declare(strict_types=1);

/*
 * This file is part of the DataImporter package.
 *
 * (c) Loïc Sapone <loic@sapone.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IQ2i\DataImporter\Tests\fixtures\Processor;

use IQ2i\DataImporter\Bundle\Messenger\AsyncMessage;
use IQ2i\DataImporter\Bundle\Processor\AbstractAsyncProcessor;

class AsyncProcessor extends AbstractAsyncProcessor
{
    public function processItem(AsyncMessage $message)
    {
    }
}
