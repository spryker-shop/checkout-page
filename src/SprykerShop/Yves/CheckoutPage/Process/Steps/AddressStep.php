<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Process\Steps;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;
use SprykerShop\Yves\CheckoutPage\CheckoutPageConfig;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface;
use Symfony\Component\HttpFoundation\Request;

class AddressStep extends AbstractBaseStep implements StepWithBreadcrumbInterface
{
    /**
     * @var \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface
     */
    protected $calculationClient;

    /**
     * @var \SprykerShop\Yves\CheckoutPageExtension\Dependency\Plugin\CheckoutAddressStepPreCheckPluginInterface[]
     */
    protected $addressStepPreCheckPlugins;

    /**
     * @var \SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface
     */
    protected $stepExecutor;

    /**
     * @var \SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface
     */
    protected $postConditionChecker;

    /**
     * @var \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig
     */
    protected $checkoutPageConfig;

    /**
     * @param \SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCalculationClientInterface $calculationClient
     * @param \SprykerShop\Yves\CheckoutPage\Process\Steps\StepExecutorInterface $stepExecutor
     * @param \SprykerShop\Yves\CheckoutPage\Process\Steps\PostConditionCheckerInterface $postConditionChecker
     * @param \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig $checkoutPageConfig
     * @param string $stepRoute
     * @param string $escapeRoute
     * @param \SprykerShop\Yves\CheckoutPageExtension\Dependency\Plugin\CheckoutAddressStepPreCheckPluginInterface[] $addressStepPreCheckPlugins
     */
    public function __construct(
        CheckoutPageToCalculationClientInterface $calculationClient,
        StepExecutorInterface $stepExecutor,
        PostConditionCheckerInterface $postConditionChecker,
        CheckoutPageConfig $checkoutPageConfig,
        $stepRoute,
        $escapeRoute,
        array $addressStepPreCheckPlugins
    ) {
        parent::__construct($stepRoute, $escapeRoute);

        $this->calculationClient = $calculationClient;
        $this->stepExecutor = $stepExecutor;
        $this->postConditionChecker = $postConditionChecker;
        $this->checkoutPageConfig = $checkoutPageConfig;
        $this->addressStepPreCheckPlugins = $addressStepPreCheckPlugins;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $dataTransfer
     *
     * @return bool
     */
    public function requireInput(AbstractTransfer $dataTransfer)
    {
        return !$this->executeAddressStepPreCheckPlugins($dataTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function preCondition(AbstractTransfer $quoteTransfer)
    {
        return parent::preCondition($quoteTransfer);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function execute(Request $request, AbstractTransfer $quoteTransfer)
    {
        $quoteTransfer = $this->stepExecutor->execute($request, $quoteTransfer);

        return $this->calculationClient->recalculate($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $quoteTransfer)
    {
        return $this->postConditionChecker->check($quoteTransfer);
    }

    /**
     * @return string
     */
    public function getBreadcrumbItemTitle()
    {
        return 'checkout.step.address.title';
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $dataTransfer
     *
     * @return bool
     */
    public function isBreadcrumbItemEnabled(AbstractTransfer $dataTransfer)
    {
        return $this->postCondition($dataTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $dataTransfer
     *
     * @return bool
     */
    public function isBreadcrumbItemHidden(AbstractTransfer $dataTransfer)
    {
        return !$this->requireInput($dataTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $dataTransfer
     *
     * @return bool
     */
    protected function executeAddressStepPreCheckPlugins(AbstractTransfer $dataTransfer): bool
    {
        foreach ($this->addressStepPreCheckPlugins as $addressStepPreCheckPlugin) {
            if (!$addressStepPreCheckPlugin->check($dataTransfer)) {
                return false;
            }
        }

        return true;
    }
}
