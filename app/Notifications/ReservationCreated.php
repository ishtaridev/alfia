<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class ReservationCreated extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        $reservation = $this->reservation;
        $variant = $reservation->variant;
        $offer = $variant?->offer;
        $pricing = $variant?->pricing;

        $roomType = $reservation->room_type?->label() ?? 'N/A';
        $feeding = $reservation->include_feeding ? 'نعم' : 'لا';
        $travelDate = $variant?->travel_date?->format('Y-m-d') ?? 'N/A';
        $airport = $variant?->airport ?? 'N/A';
        $offerTitle = $offer?->title ?? 'N/A';
        $totalPrice = number_format($reservation->total_price, 2);

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chatId'))
            ->line('*🔔 تنبيه حجز جديد*')
            ->line('')
            ->line("*العرض:* {$offerTitle}")
            ->line("*تاريخ السفر:* {$travelDate}")
            ->line("*المطار:* {$airport}")
            ->line('')
            ->line("*العميل:* {$reservation->customer}")
            ->line("*الهاتف:* {$reservation->phone}")
            ->line("*الولاية:* {$reservation->wilaya}")
            ->line('')
            ->line("*عدد المسافرين:* {$reservation->travellers_number}")
            ->line("*نوع الغرفة:* {$roomType}")
            ->line("*الوجبات مشمولة:* {$feeding}")
            ->line("*السعر الإجمالي:* {$totalPrice} دج")
            ->line('')
            ->line("*الحالة:* {$reservation->status}");
    }
}
