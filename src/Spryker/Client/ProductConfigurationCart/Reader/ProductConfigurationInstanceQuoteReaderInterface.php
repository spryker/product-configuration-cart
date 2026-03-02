<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationCart\Reader;

use Generated\Shared\Transfer\ProductConfigurationInstanceTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface ProductConfigurationInstanceQuoteReaderInterface
{
    public function findProductConfigurationInstanceInQuote(
        string $groupKey,
        string $sku,
        QuoteTransfer $quoteTransfer
    ): ?ProductConfigurationInstanceTransfer;
}
