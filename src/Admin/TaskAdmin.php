<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Spyck\AutomationBundle\Entity\Task;
use Spyck\SonataExtension\Form\Type\ParameterType;
use Spyck\SonataExtension\Utility\DateTimeUtility;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sonata.admin', [
    'group' => 'Automation',
    'manager_type' => 'orm',
    'model_class' => Task::class,
    'label' => 'Task',
])]
final class TaskAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $subject = $this->getSubject();

        $form
            ->with('Fields')
                ->add('module', null, [
                    'required' => true,
                ])
                ->add('schedule', null, [
                    'required' => false,
                ])
                ->add('variables', ParameterType::class)
                ->add('priority')
                ->add('active')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('module')
            ->add('schedule')
            ->add('active');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('module')
            ->add('schedule')
            ->add('active')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('module')
            ->add('schedule')
            ->add('variables', FieldDescriptionInterface::TYPE_ARRAY)
            ->add('priority')
            ->add('active')
            ->add('timestampCreated', null, [
                'format' => DateTimeUtility::FORMAT_DATETIME,
            ])
            ->add('timestampUpdated', null, [
                'format' => DateTimeUtility::FORMAT_DATETIME,
            ]);
    }
}
