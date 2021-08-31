<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Form;

use Generated\Shared\Transfer\PaymentTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerShop\Yves\CheckoutPage\Form\StepEngine\AbstractPaymentExternalSubFormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentExternalSubForm extends AbstractPaymentExternalSubFormType
{
    protected const FIELD_PAYMENT_METHOD_NAME = 'paymentMethodName';
    protected const FIELD_PAYMENT_PROVIDER_NAME = 'paymentProviderName';

    /**
     * @return string
     */
    public function getLabelName(): string
    {
        return $this->paymentMethodTransfer->getLabelName();
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->paymentMethodTransfer->getGroupName();
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return sprintf(
            '%s[%s]',
            PaymentTransfer::EXTERNAL_PAYMENTS,
            $this->paymentMethodTransfer->getPaymentMethodKey()
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->paymentMethodTransfer->getPaymentMethodKey();
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => null,
        ])->setRequired([
            SubFormInterface::OPTIONS_FIELD_NAME,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $paymentMethodTransfer = $options[static::OPTION_PAYMENT_METHOD_TRANSFER];

        $builder->add(static::FIELD_PAYMENT_METHOD_NAME, HiddenType::class, [
            'data' => $paymentMethodTransfer->getLabelName(),
        ]);
        $builder->add(static::FIELD_PAYMENT_PROVIDER_NAME, HiddenType::class, [
            'data' => $paymentMethodTransfer->getPaymentProvider()->getPaymentProviderKey(),
        ]);
    }

    /**
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return '';
    }
}
