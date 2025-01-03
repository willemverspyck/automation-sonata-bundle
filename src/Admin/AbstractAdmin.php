<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Admin;

use Spyck\SonataExtension\Admin\AbstractAdmin as BaseAbstractAdmin;
use Spyck\SonataExtension\Security\SecurityInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAdmin extends BaseAbstractAdmin implements SecurityInterface
{
    #[Required]
    public function setServiceTranslation(): void
    {
        $this->setTranslationDomain('SpyckAutomationSonataBundle');
    }

    public function getRole(): ?string
    {
        return strtoupper($this->getBaseRouteName());
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = 'DESC';
        $sortValues['_sort_by'] = 'id';
    }
}
