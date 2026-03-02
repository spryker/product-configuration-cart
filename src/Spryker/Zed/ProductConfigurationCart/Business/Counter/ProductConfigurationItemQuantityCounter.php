<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationCart\Business\Counter;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CartItemQuantityTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\ProductConfigurationCart\Business\Comparator\ItemComparatorInterface;

class ProductConfigurationItemQuantityCounter implements ProductConfigurationItemQuantityCounterInterface
{
    /**
     * @var int
     */
    protected const DEFAULT_ITEM_QUANTITY = 0;

    /**
     * @uses \Spryker\Zed\Cart\CartConfig::OPERATION_REMOVE
     *
     * @var string
     */
    protected const OPERATION_REMOVE = 'remove';

    /**
     * @var \Spryker\Zed\ProductConfigurationCart\Business\Comparator\ItemComparatorInterface
     */
    protected $itemComparator;

    public function __construct(ItemComparatorInterface $itemComparator)
    {
        $this->itemComparator = $itemComparator;
    }

    public function countItemQuantity(
        CartChangeTransfer $cartChangeTransfer,
        ItemTransfer $itemTransfer
    ): CartItemQuantityTransfer {
        $currentItemQuantity = static::DEFAULT_ITEM_QUANTITY;
        $quoteItems = $cartChangeTransfer->getQuoteOrFail()->getItems();
        $cartChangeItemsTransfer = $cartChangeTransfer->getItems();

        foreach ($quoteItems as $quoteItemTransfer) {
            if ($this->itemComparator->isSameItem($quoteItemTransfer, $itemTransfer)) {
                $currentItemQuantity += $quoteItemTransfer->getQuantity();
            }
        }

        foreach ($cartChangeItemsTransfer as $cartChangeItemTransfer) {
            if ($this->itemComparator->isSameItem($cartChangeItemTransfer, $itemTransfer)) {
                $currentItemQuantity = $this->changeItemQuantityAccordingToOperation(
                    $currentItemQuantity,
                    $cartChangeItemTransfer->getQuantity(),
                    $cartChangeTransfer->getOperation(),
                );
            }
        }

        return (new CartItemQuantityTransfer())->setQuantity($currentItemQuantity);
    }

    protected function changeItemQuantityAccordingToOperation(int $currentItemQuantity, ?int $deltaQuantity, ?string $operation): int
    {
        if ($operation === static::OPERATION_REMOVE) {
            return $currentItemQuantity - $deltaQuantity;
        }

        return $currentItemQuantity + $deltaQuantity;
    }
}
