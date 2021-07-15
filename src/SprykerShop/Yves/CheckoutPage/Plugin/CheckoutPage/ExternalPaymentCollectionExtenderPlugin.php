<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Plugin\CheckoutPage;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;
use SprykerShop\Yves\CheckoutPageExtension\Dependency\Plugin\PaymentCollectionExtenderPluginInterface;

/**
 * @method \SprykerShop\Yves\CheckoutPage\CheckoutPageFactory getFactory()
 */
class ExternalPaymentCollectionExtenderPlugin extends AbstractPlugin implements PaymentCollectionExtenderPluginInterface
{
    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection $paymentSubFormPluginCollection
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection
     */
    public function extendCollection(
        SubFormPluginCollection $paymentSubFormPluginCollection,
        PaymentMethodsTransfer $paymentMethodsTransfer
    ): SubFormPluginCollection {
        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if (!$paymentMethodTransfer->getIsExternal()) {
                continue;
            }

            $this->addExternalPaymentMethod($paymentSubFormPluginCollection, $paymentMethodTransfer);
        }

        return $paymentSubFormPluginCollection;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection $paymentSubFormPluginCollection
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection
     */
    protected function addExternalPaymentMethod(
        SubFormPluginCollection $paymentSubFormPluginCollection,
        PaymentMethodTransfer $paymentMethodTransfer
    ): SubFormPluginCollection {
        $externalPaymentSubFormPlugin = $this->getFactory()->getExternalPaymentSubFormPlugin()
            ->setPaymentMethodTransfer($paymentMethodTransfer);

        return $paymentSubFormPluginCollection->add($externalPaymentSubFormPlugin);
    }
}
