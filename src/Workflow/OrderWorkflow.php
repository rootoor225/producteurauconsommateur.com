<?php

namespace App\Workflow;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

/**
 * Class OrderWorkflow
 * @package App\Workflow
 */
class OrderWorkflow implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * OrderWorkflow constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.order.completed.cancel' => 'onCancel'
        ];
    }

    /**
     * @param Event $event
     */
    public function onCancel(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $order->setCanceledAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }
}
