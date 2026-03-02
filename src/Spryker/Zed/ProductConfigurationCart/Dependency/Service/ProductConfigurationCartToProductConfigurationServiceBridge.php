<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationCart\Dependency\Service;

use Generated\Shared\Transfer\ProductConfigurationInstanceTransfer;

class ProductConfigurationCartToProductConfigurationServiceBridge implements ProductConfigurationCartToProductConfigurationServiceInterface
{
    /**
     * @var \Spryker\Service\ProductConfiguration\ProductConfigurationServiceInterface
     */
    protected $productConfigurationService;

    /**
     * @param \Spryker\Service\ProductConfiguration\ProductConfigurationServiceInterface $productConfigurationService
     */
    public function __construct($productConfigurationService)
    {
        $this->productConfigurationService = $productConfigurationService;
    }

    public function getProductConfigurationInstanceHash(ProductConfigurationInstanceTransfer $productConfigurationInstanceTransfer): string
    {
        return $this->productConfigurationService->getProductConfigurationInstanceHash($productConfigurationInstanceTransfer);
    }
}
