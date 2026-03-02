<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationCart\Business\Expander;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\ProductConfigurationCart\Dependency\Service\ProductConfigurationCartToProductConfigurationServiceInterface;

class ProductConfigurationGroupKeyItemExpander implements ProductConfigurationGroupKeyItemExpanderInterface
{
    /**
     * @var \Spryker\Zed\ProductConfigurationCart\Dependency\Service\ProductConfigurationCartToProductConfigurationServiceInterface
     */
    protected $productConfigurationService;

    public function __construct(ProductConfigurationCartToProductConfigurationServiceInterface $productConfigurationService)
    {
        $this->productConfigurationService = $productConfigurationService;
    }

    public function expandProductConfigurationItemsWithGroupKey(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            if (!$this->isProductConfigurationItem($itemTransfer)) {
                continue;
            }

            $itemTransfer->setGroupKey(
                $this->buildProductConfigurationGroupKey($itemTransfer),
            );
        }

        return $cartChangeTransfer;
    }

    protected function isProductConfigurationItem(ItemTransfer $itemTransfer): bool
    {
        return $itemTransfer->getProductConfigurationInstance() !== null;
    }

    protected function buildProductConfigurationGroupKey(ItemTransfer $itemTransfer): string
    {
        $productConfigurationInstanceHashKey = $this->productConfigurationService->getProductConfigurationInstanceHash(
            $itemTransfer->getProductConfigurationInstanceOrFail(),
        );

        return sprintf(
            '%s-%s',
            $itemTransfer->getGroupKeyOrFail(),
            $productConfigurationInstanceHashKey,
        );
    }
}
