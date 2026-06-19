<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationCart;

use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductConfigurationCartConfig extends AbstractBundleConfig
{
    protected const string CHECKOUT_ERROR_TYPE = 'ProductConfigurationCartUnavailable';

    /**
     * Specification:
     * - Returns the error type identifier used in CheckoutErrorTransfer for errors produced by this module.
     *
     * @api
     */
    public function getCheckoutErrorType(): string
    {
        return static::CHECKOUT_ERROR_TYPE;
    }

    /**
     * @api
     *
     * @return array<string>
     */
    public function getItemFieldsForIsSameItemComparison(): array
    {
        return [
            ItemTransfer::SKU,
        ];
    }
}
