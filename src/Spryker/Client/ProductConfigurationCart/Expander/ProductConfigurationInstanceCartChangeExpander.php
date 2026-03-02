<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationCart\Expander;

use ArrayObject;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceCollectionTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceConditionsTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceCriteriaTransfer;
use Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToProductConfigurationStorageClientInterface;

class ProductConfigurationInstanceCartChangeExpander implements ProductConfigurationInstanceCartChangeExpanderInterface
{
    /**
     * @var \Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToProductConfigurationStorageClientInterface
     */
    protected ProductConfigurationCartToProductConfigurationStorageClientInterface $productConfigurationStorageClient;

    public function __construct(ProductConfigurationCartToProductConfigurationStorageClientInterface $productConfigurationStorageClient)
    {
        $this->productConfigurationStorageClient = $productConfigurationStorageClient;
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     * @param array<string, mixed> $params
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandCartChangeWithProductConfigurationInstance(
        CartChangeTransfer $cartChangeTransfer,
        array $params = []
    ): CartChangeTransfer {
        $productConfigurationInstanceCollectionTransfer = $this->getProductConfigurationInstanceCollection($cartChangeTransfer);

        if (!$productConfigurationInstanceCollectionTransfer->getProductConfigurationInstances()->count()) {
            return $cartChangeTransfer;
        }

        return $this->expandItemsWithProductConfigurationInstance(
            $cartChangeTransfer,
            $productConfigurationInstanceCollectionTransfer,
        );
    }

    protected function getProductConfigurationInstanceCollection(
        CartChangeTransfer $cartChangeTransfer
    ): ProductConfigurationInstanceCollectionTransfer {
        $productConfigurationInstanceConditionsTransfer = new ProductConfigurationInstanceConditionsTransfer();

        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            if ($itemTransfer->getProductConfigurationInstance()) {
                continue;
            }

            $productConfigurationInstanceConditionsTransfer->addSku($itemTransfer->getSkuOrFail());
        }

        if (!$productConfigurationInstanceConditionsTransfer->getSkus()) {
            return new ProductConfigurationInstanceCollectionTransfer();
        }

        $productConfigurationInstanceCriteriaTransfer = (new ProductConfigurationInstanceCriteriaTransfer())
            ->setProductConfigurationInstanceConditions($productConfigurationInstanceConditionsTransfer);

        return $this->productConfigurationStorageClient
            ->getProductConfigurationInstanceCollection($productConfigurationInstanceCriteriaTransfer);
    }

    protected function expandItemsWithProductConfigurationInstance(
        CartChangeTransfer $cartChangeTransfer,
        ProductConfigurationInstanceCollectionTransfer $productConfigurationInstanceCollectionTransfer
    ): CartChangeTransfer {
        $expandedCartChangeItemTransfers = new ArrayObject();
        $productConfigurationInstanceTransfersIndexedBySku = $productConfigurationInstanceCollectionTransfer->getProductConfigurationInstances();

        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            $productConfigurationInstanceTransfer = $productConfigurationInstanceTransfersIndexedBySku[$itemTransfer->getSkuOrFail()] ?? null;

            if (!$itemTransfer->getProductConfigurationInstance() && $productConfigurationInstanceTransfer) {
                $itemTransfer->setProductConfigurationInstance($productConfigurationInstanceTransfer);
            }

            $expandedCartChangeItemTransfers->append($itemTransfer);
        }

        return $cartChangeTransfer->setItems($expandedCartChangeItemTransfers);
    }
}
