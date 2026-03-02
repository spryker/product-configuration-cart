<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationCart\Business\Expander;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceTransfer;

class ProductConfigurationPriceProductExpander implements ProductConfigurationPriceProductExpanderInterface
{
    /**
     * @param array<\Generated\Shared\Transfer\PriceProductTransfer> $priceProductTransfers
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return array<\Generated\Shared\Transfer\PriceProductTransfer>
     */
    public function expandPriceProductTransfersWithProductConfigurationPrices(
        array $priceProductTransfers,
        CartChangeTransfer $cartChangeTransfer
    ): array {
        $productConfigurationPriceProductTransfers = [];

        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            if (!$this->hasProductConfigurationPrices($itemTransfer)) {
                continue;
            }

            $productConfigurationInstance = $this->fillProductConfigurationPricesWithSku(
                $itemTransfer->getProductConfigurationInstanceOrFail(),
                $itemTransfer,
            );

            $productConfigurationPriceProductTransfers[] = $productConfigurationInstance->getPrices()->getArrayCopy();
        }

        return array_merge($priceProductTransfers, ...$productConfigurationPriceProductTransfers);
    }

    protected function fillProductConfigurationPricesWithSku(
        ProductConfigurationInstanceTransfer $productConfigurationInstance,
        ItemTransfer $itemTransfer
    ): ProductConfigurationInstanceTransfer {
        foreach ($productConfigurationInstance->getPrices() as $priceProductTransfer) {
            $priceProductTransfer->setSkuProduct($itemTransfer->getSku());
        }

        return $productConfigurationInstance;
    }

    protected function hasProductConfigurationPrices(ItemTransfer $itemTransfer): bool
    {
        $productConfigurationInstance = $itemTransfer->getProductConfigurationInstance();

        return $productConfigurationInstance && $productConfigurationInstance->getPrices()->count();
    }
}
