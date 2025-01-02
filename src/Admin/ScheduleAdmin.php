<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Admin;

use DateTimeImmutable;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ClassFilter;
use Spyck\AutomationBundle\Entity\AbstractSchedule;
use Spyck\AutomationBundle\Entity\ScheduleForEvent;
use Spyck\AutomationBundle\Entity\ScheduleForSystem;
use Spyck\SonataExtension\Utility\DateTimeUtility;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'group' => 'Automation',
    'manager_type' => 'orm',
    'model_class' => AbstractSchedule::class,
    'label' => 'Schedule',
])]
final class ScheduleAdmin extends AbstractAdmin
{
    public function __construct()
    {
        $this->setSubClasses([
            'Schedule (Event)' => ScheduleForEvent::class,
            'Schedule (System)' => ScheduleForSystem::class,
        ]);

        parent::__construct();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $subject = $this->getSubject();

        $form
            ->with('Fields')
                ->add('name', null, [
                    'required' => true,
                ])
                ->ifTrue($subject instanceof ScheduleForEvent)
                    ->add('code')
                ->ifEnd()
                ->add('matchHours', ChoiceType::class, [
                    'choices' => $this->getMatchHours(),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('matchDays', ChoiceType::class, [
                    'choices' => $this->getMatchDays(),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('matchWeeks', ChoiceType::class, [
                    'choices' => $this->getMatchWeeks(),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('matchWeekdays', ChoiceType::class, [
                    'choices' => $this->getMatchWeekdays(),
                    'choice_label' => function (int $value) {
                        $date = new DateTimeImmutable('Sunday');

                        return $date->modify(sprintf('+%d day', $value))->format('l');
                    },
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('active')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('name')
            ->add('discriminator', ClassFilter::class, [
                'sub_classes' => $this->getSubClasses(),
            ])
            ->add('active');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add('discriminator')
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
            ->add('name')
            ->add('code')
            ->add('matchHours')
            ->add('matchDays')
            ->add('matchWeeks')
            ->add('matchWeekdays')
            ->add('active')
            ->add('timestampCreated', null, [
                'format' => DateTimeUtility::FORMAT_DATETIME,
            ])
            ->add('timestampUpdated', null, [
                'format' => DateTimeUtility::FORMAT_DATETIME,
            ]);
    }

    private function getMatchHours(): array
    {
        $hours = range(0, 23);

        return array_combine($hours, $hours);
    }

    private function getMatchDays(): array
    {
        $days = range(1, 31);

        $data = array_combine($days, $days);
        $data['Last Day of the Month'] = 'L';

        return $data;
    }

    private function getMatchWeeks(): array
    {
        $weeks = range(1, 52);

        return array_combine($weeks, $weeks);
    }

    private function getMatchWeekdays(): array
    {
        $weekdays = range(1, 7);

        return array_combine($weekdays, $weekdays);
    }
}
