<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\ProductFlow\ProductTypeStepType;
use App\Form\ProductFlow\ProductDetailsStepType;
use App\Form\ProductFlow\ProductLogisticsStepType;
use App\Form\ProductFlow\ProductLicenseStepType;
use App\Form\ProductFlow\ProductConfirmationStepType;
use App\Form\ProductFlow\ProductSummaryStepType;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class ProductFlow extends FormFlow
{
    protected function loadStepsConfig(): array
    {
        return [
            [
                'label' => 'Type de produit',
                'form_type' => ProductTypeStepType::class,
            ],
            [
                'label' => 'Détails',
                'form_type' => ProductDetailsStepType::class,
            ],
            [
                'label' => 'Logistique',
                'form_type' => ProductLogisticsStepType::class,
                'skip' => function ($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    $product = $flow->getFormData();
                    return $product->getType() !== 'physical';
                },
            ],
            [
                'label' => 'Licence',
                'form_type' => ProductLicenseStepType::class,
                'skip' => function ($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    $product = $flow->getFormData();
                    return $product->getType() !== 'digital';
                },
            ],
            [
                'label' => 'Confirmation',
                'form_type' => ProductConfirmationStepType::class,
                'skip' => function ($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    $product = $flow->getFormData();
                    return $product->getPrice() === null || (float) $product->getPrice() <= 500;
                },
            ],
            [
                'label' => 'Récapitulatif',
                'form_type' => ProductSummaryStepType::class,
            ],
        ];
    }

    public function getName(): string
    {
        return 'productFlow';
    }

    public function getFormOptions($step, array $options = []): array
    {
        $options = parent::getFormOptions($step, $options);
        $options['data_class'] = Product::class;

        return $options;
    }
}
