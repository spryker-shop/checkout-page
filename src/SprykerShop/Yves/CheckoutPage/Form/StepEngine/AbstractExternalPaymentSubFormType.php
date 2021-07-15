<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Form\StepEngine;

use Generated\Shared\Transfer\PaymentMethodTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractExternalPaymentSubFormType extends AbstractSubFormType implements StandaloneSubFormInterface
{
    protected const OPTION_PAYMENT_METHOD_TRANSFER = 'paymentMethodTransfer';

    /**
     * @var \Generated\Shared\Transfer\PaymentMethodTransfer|null
     */
    protected $paymentMethodTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return $this
     */
    public function setPaymentMethodTransfer(PaymentMethodTransfer $paymentMethodTransfer)
    {
        $this->paymentMethodTransfer = $paymentMethodTransfer;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getExtraOptions(): array
    {
        return [
            static::OPTION_PAYMENT_METHOD_TRANSFER => $this->paymentMethodTransfer,
        ];
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            static::OPTION_PAYMENT_METHOD_TRANSFER,
        ]);
    }
}
