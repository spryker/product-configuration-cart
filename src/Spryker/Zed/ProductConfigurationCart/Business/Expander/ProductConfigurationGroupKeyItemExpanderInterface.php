<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationCart\Business\Expander;

use Generated\Shared\Transfer\CartChangeTransfer;

interface ProductConfigurationGroupKeyItemExpanderInterface
{
    public function expandProductConfigurationItemsWithGroupKey(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;
}
