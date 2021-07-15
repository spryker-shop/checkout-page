<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Reader;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToQuoteClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToUtilEncodingServiceInterface;

class PaymentMethodReader implements PaymentMethodReaderInterface
{
    /**
     * @var \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface
     */
    protected $paymentClient;

    /**
     * @var \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @var \SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface $paymentClient
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToQuoteClientInterface $quoteClient
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        CheckoutPageToPaymentClientInterface $paymentClient,
        CheckoutPageToQuoteClientInterface $quoteClient,
        CheckoutPageToUtilEncodingServiceInterface $utilEncodingService
    ) {
        $this->paymentClient = $paymentClient;
        $this->quoteClient = $quoteClient;
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(): PaymentMethodsTransfer
    {
        $quoteTransfer = $this->quoteClient->getQuote();
        $paymentMethodsTransfer = $this->paymentClient->getAvailableMethods($quoteTransfer);

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            $this->encodeExtraData($paymentMethodTransfer);
        }

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return void
     */
    protected function encodeExtraData(PaymentMethodTransfer $paymentMethodTransfer): void
    {
        if (!$paymentMethodTransfer->getExtraData()) {
            return;
        }

        $paymentMethodTransfer->setExtraDataRaw(
            $this->utilEncodingService->decodeJson($paymentMethodTransfer->getExtraData(), true)
        );
    }
}
