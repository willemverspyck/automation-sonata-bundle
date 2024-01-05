<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Admin;

use Doctrine\Common\Collections\Criteria;
use Spyck\SonataExtension\Admin\AbstractAdmin as BaseAbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAdmin extends BaseAbstractAdmin
{
    #[Required]
    public function setServiceTranslation(): void
    {
        $this->setTranslationDomain('SpyckAutomationSonataBundle');
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = Criteria::DESC;
        $sortValues['_sort_by'] = 'id';
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'create';
        yield 'delete';
        yield 'show';
    }
}
