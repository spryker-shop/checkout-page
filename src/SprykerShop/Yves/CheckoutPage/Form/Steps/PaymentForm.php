<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\CheckoutPage\Form\Steps;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \SprykerShop\Yves\CheckoutPage\CheckoutPageFactory getFactory()
 * @method \SprykerShop\Yves\CheckoutPage\CheckoutPageConfig getConfig()
 */
class PaymentForm extends AbstractType
{
    public const PAYMENT_PROPERTY_PATH = QuoteTransfer::PAYMENT;
    public const PAYMENT_SELECTION = PaymentTransfer::PAYMENT_SELECTION;
    public const PAYMENT_SELECTION_PROPERTY_PATH = self::PAYMENT_PROPERTY_PATH . '.' . self::PAYMENT_SELECTION;

    protected const VALIDATION_NOT_BLANK_MESSAGE = 'validation.not_blank';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'paymentForm';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addPaymentMethods($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addPaymentMethods(FormBuilderInterface $builder, array $options)
    {
        $paymentMethodSubForms = $this->getPaymentMethodSubForms();
        $paymentMethodChoices = $this->getPaymentMethodChoices($paymentMethodSubForms);

        $this->addPaymentMethodChoices($builder, $paymentMethodChoices)
            ->addPaymentMethodSubForms($builder, $paymentMethodSubForms, $options);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $paymentMethodChoices
     *
     * @return $this
     */
    protected function addPaymentMethodChoices(FormBuilderInterface $builder, array $paymentMethodChoices)
    {
        $builder->add(
            self::PAYMENT_SELECTION,
            ChoiceType::class,
            [
                'choices' => $paymentMethodChoices,
                'label' => false,
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
                'property_path' => self::PAYMENT_SELECTION_PROPERTY_PATH,
                'constraints' => [
                    $this->createNotBlankConstraint(),
                ],
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[] $paymentMethodSubForms
     * @param array $options
     *
     * @return $this
     */
    protected function addPaymentMethodSubForms(FormBuilderInterface $builder, array $paymentMethodSubForms, array $options)
    {
        foreach ($paymentMethodSubForms as $paymentMethodSubForm) {
            $builder->add(
                $paymentMethodSubForm->getName(),
                get_class($paymentMethodSubForm),
                [
                    'property_path' => self::PAYMENT_PROPERTY_PATH . '.' . $paymentMethodSubForm->getPropertyPath(),
                    'error_bubbling' => true,
                    'select_options' => $options['select_options'],
                ]
            );
        }

        return $this;
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection|\Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[]
     */
    protected function getPaymentMethodSubForms()
    {
        $paymentMethodSubForms = [];

        $availablePaymentMethodSubFormPlugins = $this->getFactory()->getPaymentMethodSubForms();
        $availablePaymentMethodSubFormPlugins = $this->filterOutNotAvailableForms($availablePaymentMethodSubFormPlugins);
        $filteredPaymentMethodSubFormPlugins = $this->filterPaymentMethodSubFormPlugins($availablePaymentMethodSubFormPlugins);

        foreach ($filteredPaymentMethodSubFormPlugins as $paymentMethodSubFormPlugin) {
            $paymentMethodSubForms[] = $this->createSubForm($paymentMethodSubFormPlugin);
        }

        return $paymentMethodSubForms;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection $paymentMethodSubFormPlugins
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection
     */
    protected function filterOutNotAvailableForms(SubFormPluginCollection $paymentMethodSubFormPlugins): SubFormPluginCollection
    {
        $paymentMethodNames = $this->getAvailablePaymentMethodNames();
        $paymentMethodNames = array_combine($paymentMethodNames, $paymentMethodNames);

        foreach ($paymentMethodSubFormPlugins as $key => $subFormPlugin) {
            $subFormName = $subFormPlugin->createSubForm()->getName();

            if (!isset($paymentMethodNames[$subFormName])) {
                unset($paymentMethodSubFormPlugins[$key]);
            }
        }

        $paymentMethodSubFormPlugins->reset();

        return $paymentMethodSubFormPlugins;
    }

    /**
     * @return string[]
     */
    protected function getAvailablePaymentMethodNames(): array
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();
        $paymentMethodsTransfer = $this->getFactory()->getPaymentClient()->getAvailableMethods($quoteTransfer);

        $paymentMethodNames = [];
        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            $paymentMethodNames[] = $paymentMethodTransfer->getMethodName();
        }

        return $paymentMethodNames;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[] $paymentMethodSubForms
     *
     * @return array
     */
    protected function getPaymentMethodChoices(array $paymentMethodSubForms)
    {
        $choices = [];

        foreach ($paymentMethodSubForms as $paymentMethodSubForm) {
            $subFormName = ucfirst($paymentMethodSubForm->getName());

            if (!$paymentMethodSubForm instanceof SubFormProviderNameInterface) {
                $choices[$subFormName] = $paymentMethodSubForm->getPropertyPath();

                continue;
            }

            if (!isset($choices[$paymentMethodSubForm->getProviderName()])) {
                $choices[$paymentMethodSubForm->getProviderName()] = [];
            }

            $choices[$paymentMethodSubForm->getProviderName()][$subFormName] = $paymentMethodSubForm->getPropertyPath();
        }

        return $choices;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface $paymentMethodSubForm
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    protected function createSubForm(SubFormPluginInterface $paymentMethodSubForm)
    {
        return $paymentMethodSubForm->createSubForm();
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $validationGroups = [Constraint::DEFAULT_GROUP];

                $paymentSelectionFormData = $form->get(static::PAYMENT_SELECTION)->getData();
                if (is_string($paymentSelectionFormData)) {
                    $validationGroups[] = $paymentSelectionFormData;
                }

                return $validationGroups;
            },
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);

        $resolver->setRequired(SubFormInterface::OPTIONS_FIELD_NAME);
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection $availablePaymentMethodSubFormPlugins
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection
     */
    protected function filterPaymentMethodSubFormPlugins(SubFormPluginCollection $availablePaymentMethodSubFormPlugins)
    {
        return $this->getFactory()
            ->createSubFormFilter()
            ->filterFormsCollection($availablePaymentMethodSubFormPlugins);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\NotBlank
     */
    protected function createNotBlankConstraint(): NotBlank
    {
        return new NotBlank(['message' => static::VALIDATION_NOT_BLANK_MESSAGE]);
    }
}
