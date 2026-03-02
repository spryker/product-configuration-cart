<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationCart\Dependency\Facade;

use Generated\Shared\Transfer\ProductConfigurationCollectionTransfer;
use Generated\Shared\Transfer\ProductConfigurationCriteriaTransfer;

interface ProductConfigurationCartToProductConfigurationFacadeInterface
{
    public function getProductConfigurationCollection(
        ProductConfigurationCriteriaTransfer $productConfigurationCriteriaTransfer
    ): ProductConfigurationCollectionTransfer;
}
