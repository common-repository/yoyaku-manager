<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Yoyaku\Application\Customer\CustomersController;
use Yoyaku\Application\EmailLog\EmailLogsController;
use Yoyaku\Application\EventType\Event\EventsController;
use Yoyaku\Application\EventType\EventBooking\EventBookingsController;
use Yoyaku\Application\EventType\EventPeriod\EventPeriodsController;
use Yoyaku\Application\EventType\EventTicket\EventTicketsController;
use Yoyaku\Application\Notification\NotificationQuery;
use Yoyaku\Application\Notification\NotificationsController;
use Yoyaku\Application\Payment\PaymentsController;
use Yoyaku\Application\Settings\SettingsController;
use Yoyaku\Application\Worker\WorkersController;
use Yoyaku\Domain\Customer\CustomerService;
use Yoyaku\Domain\EventType\Event\EventService;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriodService;
use Yoyaku\Domain\EventType\EventTicket\EventTicketService;
use Yoyaku\Domain\EventType\EventTicketBooking\EventTicketBookingService;
use Yoyaku\Domain\Notification\NotificationEventService;
use Yoyaku\Domain\Notification\NotificationService;
use Yoyaku\Domain\Worker\WorkerService;
use Yoyaku\Infrastructure\Repository\Customer\CustomerRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventBookingRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventPeriodRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketBookingRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketRepository;
use Yoyaku\Infrastructure\Repository\Notification\EmailLogRepository;
use Yoyaku\Infrastructure\Repository\Notification\NotificationEventRepository;
use Yoyaku\Infrastructure\Repository\Notification\NotificationRepository;
use Yoyaku\Infrastructure\Repository\Worker\WorkerRepository;
use Yoyaku\Infrastructure\Repository\Worker\WPUserRepository;

class Routes
{
    /**
     * @param Container $container
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function init($container)
    {
        $route_group = new Event(
            new EventsController(
                $container->get('event.application.service'),
                $container->get(NotificationQuery::class),
                $container->get(EventService::class),
                $container->get(NotificationEventService::class),
                $container->get(EventRepository::class),
                $container->get(EventTicketRepository::class),
                $container->get(NotificationEventRepository::class),
            )
        );
        $route_group->register_routes();

        $route_group = new EventPeriod(
            new EventPeriodsController(
                $container->get('meeting.application.service'),
                $container->get(EventPeriodService::class),
                $container->get(EventRepository::class),
                $container->get(EventPeriodRepository::class),
                $container->get(EventTicketRepository::class),
            )
        );
        $route_group->register_routes();

        $route_group = new EventBooking(
            new EventBookingsController(
                $container->get('eventBooking.application.service'),
                $container->get('payment.application.service'),
                $container->get(CustomerService::class),
                $container->get(EventTicketBookingService::class),
                $container->get(CustomerRepository::class),
                $container->get(EventRepository::class),
                $container->get(EventPeriodRepository::class),
                $container->get(EventBookingRepository::class),
                $container->get(EventTicketBookingRepository::class),
                $container->get('email.application.service'),
            )
        );
        $route_group->register_routes();

        $route_group = new EventTicket(
            new EventTicketsController(
                $container->get(EventTicketService::class),
                $container->get(EventTicketRepository::class),
            )
        );
        $route_group->register_routes();

        $route_group = new Notification(
            new NotificationsController(
                $container->get('email.application.service'),
                $container->get(NotificationService::class),
                $container->get(NotificationRepository::class),
            )
        );
        $route_group->register_routes();

        $route_group = new Payment(
            new PaymentsController(
                $container->get(EventBookingRepository::class),
            )
        );
        $route_group->register_routes();

        $route_group = new Settings(
            new SettingsController()
        );
        $route_group->register_routes();

        $route_group = new Customer(
            new CustomersController(
                $container->get(CustomerService::class),
                $container->get(CustomerRepository::class),
            )
        );
        $route_group->register_routes();

        $route_group = new Worker(
            new WorkersController(
                $container->get(WorkerService::class),
                $container->get(WorkerRepository::class),
                $container->get(WPUserRepository::class),
            )
        );
        $route_group->register_routes();

        $route_group = new EmailLog(
            new EmailLogsController(
                $container->get('mailLog.application.service'),
                $container->get(EmailLogRepository::class)
            )
        );
        $route_group->register_routes();
    }
}
