<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Controller;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Service\CronService;
use Spyck\AutomationBundle\Utility\DataUtility;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Throwable;

#[AsController]
final class CronController extends AbstractController
{
    public function __construct(private readonly CronService $cronService)
    {
    }

    /**
     * Reset the cron.
     *
     * @throws Throwable
     */
    public function resetAction(): Response
    {
        $this->admin->checkAccess('edit');

        $cron = $this->admin->getSubject();

        DataUtility::assert($cron instanceof Cron, $this->createNotFoundException('Unable to find the cron'));

        $this->cronService->patchCronForReset($cron);

        $this->addFlash('sonata_flash_success', 'Cron has been reset.');

        return $this->redirectToList();
    }

    public function batchResetAction(ProxyQueryInterface $proxyQuery, AdminInterface $admin): RedirectResponse
    {
        $admin->checkAccess('edit');

        foreach ($proxyQuery->getQuery()->toIterable() as $object) {
            $this->cronService->patchCronForReset($object, true);
        }

        $this->addFlash('sonata_flash_success', 'Selected crons has been reset.');

        return $this->redirectToList();
    }
}
