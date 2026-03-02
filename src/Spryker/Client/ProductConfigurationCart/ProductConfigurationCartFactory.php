<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationCart;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ProductConfigurationCart\Checker\QuoteProductConfigurationChecker;
use Spryker\Client\ProductConfigurationCart\Checker\QuoteProductConfigurationCheckerInterface;
use Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToCartClientInterface;
use Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToProductConfigurationClientInterface;
use Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToProductConfigurationStorageClientInterface;
use Spryker\Client\ProductConfigurationCart\Dependency\Client\ProductConfigurationCartToQuoteClientInterface;
use Spryker\Client\ProductConfigurationCart\Expander\ProductConfigurationInstanceCartChangeExpander;
use Spryker\Client\ProductConfigurationCart\Expander\ProductConfigurationInstanceCartChangeExpanderInterface;
use Spryker\Client\ProductConfigurationCart\Processor\ProductConfiguratorResponseProcessor;
use Spryker\Client\ProductConfigurationCart\Processor\ProductConfiguratorResponseProcessorInterface;
use Spryker\Client\ProductConfigurationCart\Reader\ProductConfigurationInstanceQuoteReader;
use Spryker\Client\ProductConfigurationCart\Reader\ProductConfigurationInstanceQuoteReaderInterface;
use Spryker\Client\ProductConfigurationCart\Replacer\QuoteItemReplacer;
use Spryker\Client\ProductConfigurationCart\Replacer\QuoteItemReplacerInterface;
use Spryker\Client\ProductConfigurationCart\Resolver\ProductConfiguratorRedirectResolver;
use Spryker\Client\ProductConfigurationCart\Resolver\ProductConfiguratorRedirectResolverInterface;
use Spryker\Client\ProductConfigurationCart\Validator\ProductConfiguratorResponseValidator;
use Spryker\Client\ProductConfigurationCart\Validator\ProductConfiguratorResponseValidatorInterface;

class ProductConfigurationCartFactory extends AbstractFactory
{
    public function createProductConfigurationInstanceCartChangeExpander(): ProductConfigurationInstanceCartChangeExpanderInterface
    {
        return new ProductConfigurationInstanceCartChangeExpander(
            $this->getProductConfigurationStorageClient(),
        );
    }

    public function createProductConfigurationInstanceQuoteReader(): ProductConfigurationInstanceQuoteReaderInterface
    {
        return new ProductConfigurationInstanceQuoteReader($this->getCartClient());
    }

    public function createQuoteProductConfigurationChecker(): QuoteProductConfigurationCheckerInterface
    {
        return new QuoteProductConfigurationChecker();
    }

    public function createQuoteItemReplacer(): QuoteItemReplacerInterface
    {
        return new QuoteItemReplacer(
            $this->getQuoteClient(),
            $this->getCartClient(),
        );
    }

    public function createProductConfiguratorResponseProcessor(): ProductConfiguratorResponseProcessorInterface
    {
        return new ProductConfiguratorResponseProcessor(
            $this->getProductConfigurationClient(),
            $this->createProductConfiguratorResponseValidator(),
            $this->createQuoteItemReplacer(),
        );
    }

    public function createProductConfiguratorResponseValidator(): ProductConfiguratorResponseValidatorInterface
    {
        return new ProductConfiguratorResponseValidator(
            $this->getProductConfigurationClient(),
        );
    }

    public function createProductConfiguratorRedirectResolver(): ProductConfiguratorRedirectResolverInterface
    {
        return new ProductConfiguratorRedirectResolver(
            $this->getProductConfigurationClient(),
            $this->getQuoteClient(),
            $this->createProductConfigurationInstanceQuoteReader(),
        );
    }

    public function getProductConfigurationClient(): ProductConfigurationCartToProductConfigurationClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationCartDependencyProvider::CLIENT_PRODUCT_CONFIGURATION);
    }

    public function getProductConfigurationStorageClient(): ProductConfigurationCartToProductConfigurationStorageClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationCartDependencyProvider::CLIENT_PRODUCT_CONFIGURATION_STORAGE);
    }

    public function getCartClient(): ProductConfigurationCartToCartClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationCartDependencyProvider::CLIENT_CART);
    }

    public function getQuoteClient(): ProductConfigurationCartToQuoteClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationCartDependencyProvider::CLIENT_QUOTE);
    }
}
