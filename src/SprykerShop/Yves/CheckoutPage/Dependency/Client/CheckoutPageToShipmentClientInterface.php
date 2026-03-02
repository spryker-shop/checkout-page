<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Dependency\Client;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodsCollectionTransfer;

interface CheckoutPageToShipmentClientInterface
{
    public function getAvailableMethodsByShipment(QuoteTransfer $quoteTransfer): ShipmentMethodsCollectionTransfer;

    public function isMultiShipmentSelectionEnabled(): bool;

    public function expandQuoteWithShipmentGroups(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
