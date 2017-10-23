<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Quote\Dependency\Plugin;

class QuoteToCurrencyBridge implements QuoteToCurrencyInterface
{
    /**
     * @var \Spryker\Client\Currency\Plugin\CurrencyPluginInterface
     */
    protected $currencyPlugin;

    /**
     * @param \Spryker\Client\Currency\Plugin\CurrencyPluginInterface $currencyPlugin
     */
    public function __construct($currencyPlugin)
    {
        $this->currencyPlugin = $currencyPlugin;
    }

    /**
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    public function getCurrent()
    {
        return $this->currencyPlugin->getCurrent();
    }
}
