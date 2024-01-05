<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Admin;

use DateTime;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Spyck\AutomationBundle\Entity\Schedule;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'group' => 'Automation',
    'manager_type' => 'orm',
    'model_class' => Schedule::class,
    'label' => 'Schedule',
])]
final class ScheduleAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $hours = range(0, 23);
        $days = range(1, 31);
        $weeks = range(1, 52);
        $weekdays = range(1, 7);

        $form
            ->with('Fields')
                ->add('name', null, [
                    'required' => true,
                ])
                ->add('hours', ChoiceType::class, [
                    'choices' => array_combine($hours, $hours),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('days', ChoiceType::class, [
                    'choices' => array_combine($days, $days),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('weeks', ChoiceType::class, [
                    'choices' => array_combine($weeks, $weeks),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('weekdays', ChoiceType::class, [
                    'choices' => array_combine($weekdays, $weekdays),
                    'choice_label' => function (int $value) {
                        $date = new DateTime('Sunday');
                        $date->modify(sprintf('+%d day', $value));

                        return $date->format('l');
                    },
                    'multiple' => true,
                    'required' => false,
                ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('name');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'show';
    }
}
