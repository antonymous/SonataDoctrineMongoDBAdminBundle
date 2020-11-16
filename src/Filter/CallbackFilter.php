<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrineMongoDBAdminBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\DefaultType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @final since sonata-project/doctrine-mongodb-admin-bundle 3.5.
 */
class CallbackFilter extends Filter
{
    /**
     * NEXT_MAJOR: Remove $alias parameter.
     *
     * @return void
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $value)
    {
        if (!\is_callable($this->getOption('callback'))) {
            throw new \RuntimeException(sprintf(
                'Please provide a valid callback option "filter" for field "%s"',
                $this->getName()
            ));
        }

        // NEXT_MAJOR: Remove $alias parameter.
        \call_user_func($this->getOption('callback'), $queryBuilder, $alias, $field, $value);

        if (\is_callable($this->getOption('active_callback'))) {
            $this->active = \call_user_func($this->getOption('active_callback'), $value);

            return;
        }

        $this->active = true;
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'callback' => null,
            'active_callback' => static function ($data) {
                return isset($data['value']) && $data['value'];
            },
            'field_type' => TextType::class,
            'operator_type' => HiddenType::class,
            'operator_options' => [],
        ];
    }

    public function getRenderSettings()
    {
        return [DefaultType::class, [
                'field_type' => $this->getFieldType(),
                'field_options' => $this->getFieldOptions(),
                'operator_type' => $this->getOption('operator_type'),
                'operator_options' => $this->getOption('operator_options'),
                'label' => $this->getLabel(),
        ]];
    }
}
