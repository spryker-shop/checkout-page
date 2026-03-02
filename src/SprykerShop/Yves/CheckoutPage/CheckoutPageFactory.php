<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage;

use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCartClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCheckoutClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToCustomerClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToGlossaryStorageClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToLocaleClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToPaymentClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToProductBundleClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToQuoteClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Client\CheckoutPageToShipmentClientInterface;
use SprykerShop\Yves\CheckoutPage\Dependency\Service\CheckoutPageToShipmentServiceInterface;
use SprykerShop\Yves\CheckoutPage\Expander\AddressViewDataExpanderInterface;
use SprykerShop\Yves\CheckoutPage\Expander\CustomerAddressViewDataExpander;
use SprykerShop\Yves\CheckoutPage\Extractor\PaymentMethodKeyExtractor;
use SprykerShop\Yves\CheckoutPage\Extractor\PaymentMethodKeyExtractorInterface;
use SprykerShop\Yves\CheckoutPage\Form\DataProvider\ShipmentFormDataProvider;
use SprykerShop\Yves\CheckoutPage\Form\Filter\SubFormFilter;
use SprykerShop\Yves\CheckoutPage\Form\Filter\SubFormFilterInterface;
use SprykerShop\Yves\CheckoutPage\Form\FormFactory;
use SprykerShop\Yves\CheckoutPage\Process\StepFactory;
use SprykerShop\Yves\CheckoutPage\Reader\PaymentMethodReader;
use SprykerShop\Yves\CheckoutPage\Reader\PaymentMethodReaderInterface;

/**
 * @method \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class CheckoutPageFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Yves\StepEngine\Process\StepEngineInterface
     */
    public function createCheckoutProcess()
    {
        return $this->createStepFactory()->createStepEngine(
            $this->createStepFactory()->createStepResolver()->resolveSteps(),
        );
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Process\StepFactory
     */
    public function createStepFactory()
    {
        return new StepFactory();
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Form\FormFactory
     */
    public function createCheckoutFormFactory()
    {
        return new FormFactory();
    }

    public function createPaymentMethodReader(): PaymentMethodReaderInterface
    {
        return new PaymentMethodReader(
            $this->getPaymentClient(),
            $this->getQuoteClient(),
            $this->getConfig(),
        );
    }

    public function createPaymentMethodKeyExtractor(): PaymentMethodKeyExtractorInterface
    {
        return new PaymentMethodKeyExtractor();
    }

    /**
     * @return array<string>
     */
    public function getCustomerPageWidgetPlugins(): array
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::PLUGIN_CUSTOMER_PAGE_WIDGETS);
    }

    public function getCheckoutClient(): CheckoutPageToCheckoutClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_CHECKOUT);
    }

    /**
     * @return array<string>
     */
    public function getSummaryPageWidgetPlugins(): array
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::PLUGIN_SUMMARY_PAGE_WIDGETS);
    }

    /**
     * @return \SprykerShop\Yves\CheckoutPage\Form\DataProvider\ShipmentFormDataProvider
     */
    public function createShipmentDataProvider()
    {
        return new ShipmentFormDataProvider(
            $this->getShipmentClient(),
            $this->getGlossaryStorageClient(),
            $this->getLocaleClient(),
            $this->getMoneyPlugin(),
            $this->getShipmentService(),
            $this->getConfig(),
            $this->getProductBundleClient(),
        );
    }

    public function getShipmentClient(): CheckoutPageToShipmentClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_SHIPMENT);
    }

    public function getGlossaryStorageClient(): CheckoutPageToGlossaryStorageClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_GLOSSARY_STORAGE);
    }

    public function getProductBundleClient(): CheckoutPageToProductBundleClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_PRODUCT_BUNDLE);
    }

    public function getQuoteClient(): CheckoutPageToQuoteClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_QUOTE);
    }

    /**
     * @return \Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface
     */
    public function getMoneyPlugin()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::PLUGIN_MONEY);
    }

    public function getShipmentService(): CheckoutPageToShipmentServiceInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::SERVICE_SHIPMENT);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection
     */
    public function getPaymentMethodSubForms()
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::PAYMENT_SUB_FORMS);
    }

    public function getPaymentClient(): CheckoutPageToPaymentClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_PAYMENT);
    }

    public function createSubFormFilter(): SubFormFilterInterface
    {
        return new SubFormFilter(
            $this->getSubFormFilterPlugins(),
            $this->getQuoteClient(),
        );
    }

    /**
     * @return list<\SprykerShop\Yves\CheckoutPage\Expander\AddressViewDataExpanderInterface>
     */
    public function getAddressViewDataExpanders(): array
    {
        return [
            $this->createCustomerAddressViewDataExpander(),
        ];
    }

    public function createCustomerAddressViewDataExpander(): AddressViewDataExpanderInterface
    {
        return new CustomerAddressViewDataExpander(
            $this->getCustomerClient(),
        );
    }

    /**
     * @return array<\SprykerShop\Yves\CheckoutPageExtension\Dependency\Plugin\PaymentCollectionExtenderPluginInterface>
     */
    public function getPaymentCollectionExtenderPlugins(): array
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::PLUGINS_PAYMENT_COLLECTION_EXTENDER);
    }

    /**
     * @return array<\Spryker\Yves\Checkout\Dependency\Plugin\Form\SubFormFilterPluginInterface>
     */
    protected function getSubFormFilterPlugins(): array
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::PLUGIN_SUB_FORM_FILTERS);
    }

    public function getCustomerClient(): CheckoutPageToCustomerClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_CUSTOMER);
    }

    public function getCartClient(): CheckoutPageToCartClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_CART);
    }

    public function getLocaleClient(): CheckoutPageToLocaleClientInterface
    {
        return $this->getProvidedDependency(CheckoutPageDependencyProvider::CLIENT_LOCALE);
    }
}
