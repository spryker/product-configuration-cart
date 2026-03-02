<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationCart\Dependency\Client;

use Generated\Shared\Transfer\ItemReplaceTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface ProductConfigurationCartToCartClientInterface
{
    public function findQuoteItem(QuoteTransfer $quoteTransfer, string $sku, ?string $groupKey = null): ?ItemTransfer;

    public function replaceItem(ItemReplaceTransfer $itemReplaceTransfer): QuoteResponseTransfer;
}
