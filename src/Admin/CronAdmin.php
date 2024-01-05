<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\DateTimePickerType;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationSonataBundle\Controller\CronController;
use Spyck\SonataExtension\Utility\AutocompleteUtility;
use Spyck\SonataExtension\Utility\DateTimeUtility;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

#[AutoconfigureTag('sonata.admin', [
    'controller' => CronController::class,
    'group' => 'Automation',
    'manager_type' => 'orm',
    'model_class' => Cron::class,
    'label' => 'Cron',
])]
final class CronAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Fields')
                ->add('priority', ChoiceType::class, [
                    'choices' => [
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                        '5' => 5,
                    ],
                ])
                ->add('timestampAvailable', DateTimePickerType::class, [
                    'format' => sprintf('%s HH:mm:ss', DateType::HTML5_FORMAT),
                    'required' => false,
                ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('module', ModelFilter::class, [
                'field_options' => [
                    'callback' => [AutocompleteUtility::class, 'callbackFilter'],
                    'multiple' => true,
                    'property' => [
                        'name',
                    ],
                ],
                'field_type' => ModelAutocompleteType::class,
            ])
            ->add('callback')
            ->add('status', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => Cron::getStatusData(true),
                    'multiple' => true,
                ],
                'field_type' => ChoiceType::class,
            ]);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('module')
            ->add('priority')
            ->add('callback')
            ->add('variables', FieldDescriptionInterface::TYPE_ARRAY)
            ->add('status')
            ->add('duration')
            ->add('error')
            ->add('timestamp', null, [
                'format' => DateTimeUtility::FORMAT_DATETIME,
            ])
            ->add('timestampAvailable', null, [
                'format' => DateTimeUtility::FORMAT_DATETIME,
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'reset' => [
                        'template' => '@SpyckAutomationSonata/cron/list_action_reset.html.twig',
                    ],
                ],
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('module')
            ->add('callback')
            ->add('variables', FieldDescriptionInterface::TYPE_ARRAY)
            ->add('priority')
            ->add('status')
            ->add('duration')
            ->add('messages')
            ->add('error')
            ->add('timestamp', null, [
                'format' => DateTimeUtility::FORMAT_DATETIME,
            ])
            ->add('timestampAvailable', null, [
                'format' => DateTimeUtility::FORMAT_DATETIME,
            ]);
    }

    protected function configureBatchActions(array $actions): array
    {
        if (true === $this->hasAccess('edit')) {
            $actions['reset'] = [
                'ask_confirmation' => true,
                'controller' => sprintf('%s::%s', CronController::class, 'batchResetAction'),
            ];
        }

        return $actions;
    }

    protected function getAddRoutes(): iterable
    {
        yield 'reset';
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'create';
        yield 'delete';
    }
}
