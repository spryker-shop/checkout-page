<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\StrategyResolver\ShipmentStep;

use Closure;
use Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig;
use SprykerShop\Yves\CheckoutPage\Process\Steps\BaseActions\PostConditionCheckerInterface;

/**
 * @deprecated Exists for Backward Compatibility reasons only.
 */
class ShipmentStepStrategyResolver implements ShipmentStepStrategyResolverInterface
{
    /**
     * @var array|\Closure[]
     */
    protected $strategyContainer;

    /**
     * @var \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig
     */
    protected $checkoutPageConfig;

    /**
     * @param \Closure[] $strategyContainer
     * @param \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig $checkoutPageConfig
     */
    public function __construct(array $strategyContainer, CheckoutPageConfig $checkoutPageConfig)
    {
        $this->strategyContainer = $strategyContainer;
        $this->checkoutPageConfig = $checkoutPageConfig;
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\Steps\BaseActions\PostConditionCheckerInterface
     */
    public function resolvePostCondition(): PostConditionCheckerInterface
    {
        if (!$this->checkoutPageConfig->isMultiShipmentEnabled()) {
            $this->assertRequiredStrategyPostConditionCheckerWithoutMultiShipmentContainerItems();

            return call_user_func($this->strategyContainer[static::STRATEGY_KEY_POST_CONDITION_CHECKER_WITHOUT_MULTI_SHIPMENT]);
        }

        $this->assertRequiredStrategyPostConditionCheckerWithMultiShipmentContainerItems();

        return call_user_func($this->strategyContainer[static::STRATEGY_KEY_POST_CONDITION_CHECKER_WITH_MULTI_SHIPMENT]);
    }

    /**
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     *
     * @return void
     */
    protected function assertRequiredStrategyPostConditionCheckerWithoutMultiShipmentContainerItems(): void
    {
        if (!isset($this->strategyContainer[static::STRATEGY_KEY_POST_CONDITION_CHECKER_WITHOUT_MULTI_SHIPMENT])
            || !($this->strategyContainer[static::STRATEGY_KEY_POST_CONDITION_CHECKER_WITHOUT_MULTI_SHIPMENT] instanceof Closure)
        ) {
            throw new ContainerKeyNotFoundException($this, static::STRATEGY_KEY_POST_CONDITION_CHECKER_WITHOUT_MULTI_SHIPMENT);
        }
    }

    /**
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     *
     * @return void
     */
    protected function assertRequiredStrategyPostConditionCheckerWithMultiShipmentContainerItems(): void
    {
        if (!isset($this->strategyContainer[static::STRATEGY_KEY_POST_CONDITION_CHECKER_WITH_MULTI_SHIPMENT])
            || !($this->strategyContainer[static::STRATEGY_KEY_POST_CONDITION_CHECKER_WITH_MULTI_SHIPMENT] instanceof Closure)
        ) {
            throw new ContainerKeyNotFoundException($this, static::STRATEGY_KEY_POST_CONDITION_CHECKER_WITH_MULTI_SHIPMENT);
        }
    }
}