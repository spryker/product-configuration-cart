<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationCart\Replacer;

use Generated\Shared\Transfer\ItemReplaceTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\ProductConfiguratorResponseProcessorResponseTransfer;
use Generated\Shared\Transfer\ProductConfiguratorResponseTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToCartClientInterface;
use Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToQuoteClientInterface;

class QuoteItemReplacer implements QuoteItemReplacerInterface
{
    /**
     * @var string
     */
    protected const MESSAGE_TYPE_ERROR = 'error';

    /**
     * @var string
     */
    protected const GLOSSARY_MESSAGE_PARAMETER_SKU = '%sku%';

    /**
     * @var string
     */
    protected const GLOSSARY_MESSAGE_PARAMETER_AVAILABILITY = '%availability%';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_PRODUCT_CONFIGURATION_ITEM_NOT_FOUND_IN_CART = 'product_configuration.error.configured_item_not_found_in_cart';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_PRODUCT_CONFIGURATION_AVAILABILITY_FAILED = 'product_configuration.error.availability.failed';

    /**
     * @var \Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @var \Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToCartClientInterface
     */
    protected $cartClient;

    public function __construct(
        ProductConfigurationCartToQuoteClientInterface $quoteClient,
        ProductConfigurationCartToCartClientInterface $cartClient
    ) {
        $this->quoteClient = $quoteClient;
        $this->cartClient = $cartClient;
    }

    public function replaceItemInQuote(
        ProductConfiguratorResponseProcessorResponseTransfer $productConfiguratorResponseProcessorResponseTransfer
    ): ProductConfiguratorResponseProcessorResponseTransfer {
        $productConfiguratorResponseTransfer = $productConfiguratorResponseProcessorResponseTransfer->getProductConfiguratorResponseOrFail();
        $quoteTransfer = $this->quoteClient->getQuote();

        $itemTransfer = $this->cartClient->findQuoteItem(
            $quoteTransfer,
            $productConfiguratorResponseTransfer->getSkuOrFail(),
            $productConfiguratorResponseTransfer->getItemGroupKeyOrFail(),
        );

        if (!$itemTransfer) {
            $messageTransfer = $this->createConfigurationItemNotFoundMessage(
                $productConfiguratorResponseTransfer->getSkuOrFail(),
            );

            return $productConfiguratorResponseProcessorResponseTransfer
                ->setIsSuccessful(false)
                ->addMessage($messageTransfer);
        }

        $itemReplaceTransfer = $this->createItemReplaceTransfer(
            $productConfiguratorResponseTransfer,
            $itemTransfer,
            $quoteTransfer,
        );

        $productConfiguratorResponseProcessorResponseTransfer = $this->handleQuantityChange(
            $productConfiguratorResponseProcessorResponseTransfer,
            $itemReplaceTransfer,
        );

        $quoteResponseTransfer = $this->cartClient->replaceItem($itemReplaceTransfer);

        if ($quoteResponseTransfer->getIsSuccessful()) {
            return $productConfiguratorResponseProcessorResponseTransfer->setIsSuccessful(true);
        }

        return $this->addQuoteErrors(
            $quoteResponseTransfer,
            $productConfiguratorResponseProcessorResponseTransfer,
        );
    }

    protected function createItemReplaceTransfer(
        ProductConfiguratorResponseTransfer $productConfiguratorResponseTransfer,
        ItemTransfer $itemToBeReplacedTransfer,
        QuoteTransfer $quoteTransfer
    ): ItemReplaceTransfer {
        $newItemTransfer = (new ItemTransfer())
            ->fromArray($itemToBeReplacedTransfer->toArray())
            ->setGroupKey(null)
            ->setProductConfigurationInstance($productConfiguratorResponseTransfer->getProductConfigurationInstance());

        $newItemTransfer = $this->replaceItemQuantity($productConfiguratorResponseTransfer, $newItemTransfer);

        return (new ItemReplaceTransfer())
            ->setItemToBeReplaced($itemToBeReplacedTransfer)
            ->setNewItem($newItemTransfer)
            ->setQuote($quoteTransfer);
    }

    protected function replaceItemQuantity(
        ProductConfiguratorResponseTransfer $productConfiguratorResponseTransfer,
        ItemTransfer $itemTransfer
    ): ItemTransfer {
        $productConfigurationInstanceTransfer = $productConfiguratorResponseTransfer->getProductConfigurationInstance();

        if (!$productConfigurationInstanceTransfer) {
            return $itemTransfer;
        }

        $availableQuantity = $productConfigurationInstanceTransfer->getAvailableQuantity();

        if ($availableQuantity && $availableQuantity < $itemTransfer->getQuantity()) {
            $itemTransfer->setQuantity($availableQuantity);
        }

        return $itemTransfer;
    }

    protected function handleQuantityChange(
        ProductConfiguratorResponseProcessorResponseTransfer $productConfiguratorResponseProcessorResponseTransfer,
        ItemReplaceTransfer $itemReplaceTransfer
    ): ProductConfiguratorResponseProcessorResponseTransfer {
        if ($itemReplaceTransfer->getItemToBeReplacedOrFail()->getQuantity() === $itemReplaceTransfer->getNewItemOrFail()->getQuantity()) {
            return $productConfiguratorResponseProcessorResponseTransfer;
        }

        $messageTransfer = $this->createConfigurationItemAvailabilityMessage(
            $itemReplaceTransfer->getNewItemOrFail()->getQuantityOrFail(),
        );

        return $productConfiguratorResponseProcessorResponseTransfer->addMessage($messageTransfer);
    }

    protected function addQuoteErrors(
        QuoteResponseTransfer $quoteResponseTransfer,
        ProductConfiguratorResponseProcessorResponseTransfer $productConfiguratorResponseProcessorResponseTransfer
    ): ProductConfiguratorResponseProcessorResponseTransfer {
        foreach ($quoteResponseTransfer->getErrors() as $quoteErrorTransfer) {
            $productConfiguratorResponseProcessorResponseTransfer->addMessage(
                (new MessageTransfer())->setValue($quoteErrorTransfer->getMessage()),
            );
        }

        return $productConfiguratorResponseProcessorResponseTransfer->setIsSuccessful(false);
    }

    protected function createConfigurationItemNotFoundMessage(string $sku): MessageTransfer
    {
        return (new MessageTransfer())
            ->setType(static::MESSAGE_TYPE_ERROR)
            ->setValue(static::GLOSSARY_KEY_PRODUCT_CONFIGURATION_ITEM_NOT_FOUND_IN_CART)
            ->setParameters([static::GLOSSARY_MESSAGE_PARAMETER_SKU => $sku]);
    }

    protected function createConfigurationItemAvailabilityMessage(int $availability): MessageTransfer
    {
        return (new MessageTransfer())
            ->setType(static::MESSAGE_TYPE_ERROR)
            ->setValue(static::GLOSSARY_KEY_PRODUCT_CONFIGURATION_AVAILABILITY_FAILED)
            ->setParameters([
                static::GLOSSARY_MESSAGE_PARAMETER_AVAILABILITY => $availability,
            ]);
    }
}
