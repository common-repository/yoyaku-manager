<?php declare(strict_types=1);

defined('ABSPATH') or die('No script kiddies please!');

/**
 * PHP-DIを使ってコンテナを生成する
 */

use Psr\Container\ContainerInterface;
use Yoyaku\Application\EmailLog\EmailLogApplicationService;
use Yoyaku\Application\EventType\Event\EventApplicationService;
use Yoyaku\Application\EventType\EventBooking\EventBookingApplicationService;
use Yoyaku\Application\Google\GoogleApplicationService;
use Yoyaku\Application\Helper\HelperApplicationService;
use Yoyaku\Application\Meeting\LiteMeetingApplicationService;
use Yoyaku\Application\Meeting\MeetingApplicationService;
use Yoyaku\Application\Notification\EmailApplicationService;
use Yoyaku\Application\Notification\NotificationQuery;
use Yoyaku\Application\Payment\PaymentApplicationService;
use Yoyaku\Application\Placeholder\PlaceholderApplicationService;
use Yoyaku\Domain\EventType\Event\EventService;
use Yoyaku\Domain\Notification\EmailLogService;
use Yoyaku\Infrastructure\Repository\EventType\EventBookingRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketBookingRepository;
use Yoyaku\Infrastructure\Repository\Notification\EmailLogRepository;
use Yoyaku\Infrastructure\Repository\Notification\NotificationRepository;
use Yoyaku\Infrastructure\Services\Zoom\ZoomService;


$definitions = [
    'event.application.service' => function (ContainerInterface $c) {
        return new EventApplicationService(
            $c->get(EventService::class),
            $c->get(EventRepository::class),
            $c->get(NotificationRepository::class),
        );
    },
    'eventBooking.application.service' => function (ContainerInterface $c) {
        return new EventBookingApplicationService(
            $c->get(EventBookingRepository::class),
            $c->get(EventTicketBookingRepository::class),
        );
    },
    'email.application.service' => function (ContainerInterface $c) {
        return new EmailApplicationService(
            $c->get('event.application.service'),
            $c->get('payment.application.service'),
            $c->get(PlaceholderApplicationService::class),
            $c->get(NotificationQuery::class),
            $c->get(EmailLogService::class),
            $c->get(EmailLogRepository::class),
            $c->get(EventBookingRepository::class),
        );
    },
    'mailLog.application.service' => function (ContainerInterface $c) {
        return new EmailLogApplicationService(
            $c->get(EmailLogRepository::class),
        );
    },
    'payment.application.service' => function (ContainerInterface $c) {
        return new PaymentApplicationService(
            $c->get(PlaceholderApplicationService::class),
        );
    },
    'meeting.application.service' => function (ContainerInterface $c) {
        if (HelperApplicationService::is_pro()) {
            return new MeetingApplicationService(
                $c->get(GoogleApplicationService::class),
                $c->get(ZoomService::class),
            );
        } else {
            return new LiteMeetingApplicationService();

        }
    },
];

return $definitions;
