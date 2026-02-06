<?php

declare(strict_types=1);

namespace Spyck\AutomationSonataBundle\Controller;

use Spyck\AutomationBundle\Entity\Task;
use Spyck\AutomationBundle\Service\TaskService;
use Spyck\VisualizationBundle\Utility\DataUtility;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class TaskController extends AbstractController
{
    public function messageAction(TaskService $taskService): Response
    {
        $this->admin->checkAccess('list');

        $task = $this->admin->getSubject();

        DataUtility::assert($task instanceof Task, $this->createNotFoundException('Task not found'));

        $taskService->executeTaskAsMessage($task->getModule(), $task->getVariables());

        $this->addFlash('sonata_flash_success', sprintf('Task "%s" has been executed.', $task));

        return $this->redirectToList();
    }
}
