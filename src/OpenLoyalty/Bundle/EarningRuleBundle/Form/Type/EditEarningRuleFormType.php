<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EarningRuleBundle\Form\Type;

use OpenLoyalty\Bundle\EarningRuleBundle\Model\EarningRule;
use OpenLoyalty\Bundle\EarningRuleBundle\Form\DataTransformer\LevelsDataTransformer;
use OpenLoyalty\Bundle\EarningRuleBundle\Form\DataTransformer\SegmentsDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EditEarningRuleFormType.
 */
class EditEarningRuleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = $options['type'];

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('target', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'level' => 'level',
                    'segment' => 'segment',
                ],
                'mapped' => false,
            ])
            ->add(
                $builder->create('levels', CollectionType::class, [
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'error_bubbling' => false,
                ])->addModelTransformer(new LevelsDataTransformer())
            )
            ->add(
                $builder->create('segments', CollectionType::class, [
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'error_bubbling' => false,
                ])->addModelTransformer(new SegmentsDataTransformer()))
            ->add('active', CheckboxType::class, [
                'required' => false,
            ])
            ->add('allTimeActive', CheckboxType::class, [
                'required' => false,
            ])
            ->add('startAt', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => DateTimeType::HTML5_FORMAT,
            ])
            ->add('endAt', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => DateTimeType::HTML5_FORMAT,
            ]);
        if ($type == EarningRule::TYPE_POINTS) {
            $builder
                ->add('pointValue', NumberType::class, [
                    'scale' => 2,
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('excludedSKUs', ExcludedSKUsFormType::class)
                ->add('excludedLabels', ExcludedLabelsFormType::class)
                ->add('excludeDeliveryCost', CheckboxType::class, [
                    'required' => false,
                ])
                ->add('minOrderValue', NumberType::class);
        } elseif ($type == EarningRule::TYPE_EVENT) {
            $builder
                ->add('eventName', TextType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('pointsAmount', NumberType::class, [
                    'scale' => 2,
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ]);
        } elseif ($type == EarningRule::TYPE_CUSTOM_EVENT) {
            $builder
                ->add('eventName', TextType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('pointsAmount', NumberType::class, [
                    'scale' => 2,
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('limit', EarningRuleLimitFormType::class);
        } elseif ($type == EarningRule::TYPE_REFERRAL) {
            $builder
                ->add('eventName', TextType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('rewardType', TextType::class, [
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ])
                ->add('pointsAmount', NumberType::class, [
                    'scale' => 2,
                    'required' => true,
                    'constraints' => [new NotBlank()],
                ]);
        } elseif ($type == EarningRule::TYPE_PRODUCT_PURCHASE) {
            $builder->add(
                    'skuIds',
                    CollectionType::class,
                    [
                        'allow_add' => true,
                        'allow_delete' => true,
                        'entry_type' => TextType::class,
                        'constraints' => [new NotBlank(), new Count(['min' => 1])],
                    ]
                )->add(
                    'pointsAmount',
                    NumberType::class,
                    [
                        'scale' => 2,
                        'required' => true,
                        'constraints' => [new NotBlank()],
                    ]
                );
        } elseif ($type == EarningRule::TYPE_MULTIPLY_FOR_PRODUCT) {
            $builder
                ->add('skuIds', CollectionType::class, [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => TextType::class,
                ])
                ->add('multiplier', NumberType::class, [
                    'required' => true,
                    'scale' => 2,
                    'constraints' => [new NotBlank()],
                ])
                ->add('labels', LabelsFormType::class);
        } else {
            throw new InvalidArgumentException('Wrong "type" provided');
        }
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (!isset($data['target'])) {
                return;
            }
            $target = $data['target'];
            if ($target == 'level') {
                $data['segments'] = [];
            } elseif ($target == 'segment') {
                $data['levels'] = [];
            }
            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'type',
        ]);

        $resolver->setDefaults([
            'data_class' => EarningRule::class,
        ]);
    }
}
