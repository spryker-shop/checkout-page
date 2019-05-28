<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\StrategyResolver\CheckoutStep;

/**
 * @deprecated Exists for Backward Compatibility reasons only.
 */
interface CheckoutStepTemplateResolverInterface
{
    /**
     * @return string
     */
    public function getTemplateForAddressStep(): string;

    /**
     * @return string
     */
    public function getTemplateForShipmentStep(): string;

    /**
     * @return string
     */
    public function getTemplateForSummaryStep(): string;
}