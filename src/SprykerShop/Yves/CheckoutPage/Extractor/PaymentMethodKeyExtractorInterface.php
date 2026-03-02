<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Extractor;

use Generated\Shared\Transfer\PaymentTransfer;

interface PaymentMethodKeyExtractorInterface
{
    public function getPaymentSelectionKey(PaymentTransfer $paymentTransfer): string;

    public function getPaymentMethodKey(PaymentTransfer $paymentTransfer): string;
}
