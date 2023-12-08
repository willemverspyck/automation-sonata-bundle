<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Admin;

use Doctrine\Common\Collections\Criteria;
use Sonata\AdminBundle\Admin\AbstractAdmin as SonataAbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAdmin extends SonataAbstractAdmin
{
    protected array $addRoutes = [];
    protected array $removeRoutes = ['create', 'delete', 'show'];

    #[Required]
    public function setServiceTranslation(): void
    {
        $this->setTranslationDomain('SpyckAutomationSonataBundle');
    }

    public function getAutocompleteSearch(AdminInterface $admin, array $properties, string $value): void
    {
        $datagrid = $admin->getDatagrid();
        $query = $datagrid->getQuery();

        $keywords = $this->getKeywords($value);

        foreach ($keywords as $index => $keyword) {
            $orX = $query->expr()->orX();

            foreach ($properties as $property) {
                if (false === $datagrid->hasFilter($property)) {
                    throw new BadRequestHttpException(sprintf('Filter "%s" not found', $property));
                }

                $filter = $datagrid->getFilter($property);

                $alias = $query->entityJoin($filter->getParentAssociationMappings());

                $key = sprintf('%s_%d', $filter->getFormName(), $index + 1);

                $orX->add(sprintf('%s.%s LIKE :%s', $alias, $filter->getFieldName(), $key));

                $query->setParameter($key, sprintf('%%%s%%', $keyword));
            }

            $query->andWhere($orX);
        }
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = Criteria::DESC;
        $sortValues['_sort_by'] = 'id';
    }

    protected function configureRoutes(RouteCollectionInterface $routeCollection): void
    {
        foreach ($this->addRoutes as $route) {
            $routeCollection->add($route, sprintf('%s/%s', $this->getRouterIdParameter(), $route));
        }

        foreach ($this->removeRoutes as $route) {
            $routeCollection->remove($route);
        }
    }

    private function getKeywords(string $data): array
    {
        return array_filter(str_getcsv($data, ' '));
    }
}
