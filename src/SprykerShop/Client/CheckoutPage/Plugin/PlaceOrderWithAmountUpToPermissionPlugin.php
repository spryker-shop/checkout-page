<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Client\CheckoutPage\Plugin;

use Spryker\Shared\PermissionExtension\Dependency\Plugin\ExecutablePermissionPluginInterface;

/**
 * @deprecated Use {@link \Spryker\Shared\Checkout\Plugin\Permission\PlaceOrderWithAmountUpToPermissionPlugin} instead.
 */
class PlaceOrderWithAmountUpToPermissionPlugin implements ExecutablePermissionPluginInterface
{
    /**
     * @var string
     */
    public const KEY = 'PlaceOrderWithAmountUpToPermissionPlugin';

    /**
     * @var string
     */
    protected const FIELD_CENT_AMOUNT = 'cent_amount';

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $configuration
     * @param array|string|int|null $context Cent amount.
     */
    public function can(array $configuration, $context = null): bool
    {
        if (!$context) {
            return false;
        }

        if (!isset($configuration[static::FIELD_CENT_AMOUNT])) {
            return false;
        }

        if (!is_array($context) && (int)$configuration[static::FIELD_CENT_AMOUNT] <= (int)$context) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getConfigurationSignature(): array
    {
        return [
            static::FIELD_CENT_AMOUNT => ExecutablePermissionPluginInterface::CONFIG_FIELD_TYPE_INT,
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getKey(): string
    {
        return static::KEY;
    }
}
