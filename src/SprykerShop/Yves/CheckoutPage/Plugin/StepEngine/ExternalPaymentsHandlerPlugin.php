<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Plugin\StepEngine;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use Symfony\Component\HttpFoundation\Request;

class ExternalPaymentsHandlerPlugin extends AbstractPlugin implements StepHandlerPluginInterface
{
    /**
     * @uses \SprykerShop\Yves\CheckoutPage\Form\ExternalPaymentSubForm::FIELD_PAYMENT_METHOD_NAME
     */
    protected const FIELD_PAYMENT_METHOD_NAME = 'paymentMethodName';

    /**
     * @uses \SprykerShop\Yves\CheckoutPage\Form\ExternalPaymentSubForm::FIELD_PAYMENT_PROVIDER_NAME
     */
    protected const FIELD_PAYMENT_PROVIDER_NAME = 'paymentProviderName';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addToDataClass(Request $request, AbstractTransfer $quoteTransfer)
    {
        $paymentSelection = $quoteTransfer->getPayment()->getPaymentSelection();
        $paymentMethodKey = $this->getPaymentMethodKey($paymentSelection);
        $paymentMethodFormData = $quoteTransfer->getPayment()->getExternalPayments()[$paymentMethodKey];

        $quoteTransfer->getPayment()
            ->setPaymentProvider($paymentMethodFormData[static::FIELD_PAYMENT_PROVIDER_NAME])
            ->setPaymentMethod($paymentMethodFormData[static::FIELD_PAYMENT_METHOD_NAME]);

        return $quoteTransfer;
    }

    /**
     * @param string $paymentSelection
     *
     * @return string
     */
    protected function getPaymentMethodKey(string $paymentSelection): string
    {
        preg_match('/\[([\w]+)\]/', $paymentSelection, $matches);

        if (!isset($matches[1])) {
            return $paymentSelection;
        }

        return $matches[1];
    }
}
