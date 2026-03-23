<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    public const DEFAULTS = [
        'hotel_name' => 'Bez Tower & Residences',
        'contact_email' => 'beztowerresidences@gmail.com',
        'contact_phone' => '(02) 88075046 or 09171221429',
        'hotel_address' => '205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City, Philippines',
        'check_in_time' => '14:00',
        'check_out_time' => '12:00',
        'terms_and_conditions' => "Full payment is expected upon check-in\n"
            . "Check-in Time: 2:00 PM\n"
            . "Check-out Time: 12:00 PM\n"
            . "Early check-in or late check-out is subject to room availability on the day and must be reconfirmed with the front desk. Fee: PHP 150.00 per hour (maximum of 5 hours only)\n"
            . "Non-smoking is strictly prohibited inside the building premises\n"
            . "Rooms are not soundproof; please be considerate of other guests. No loud music or noise after 10:00 PM\n"
            . "Lost/damaged card/key will be charged PHP 200.00 each\n"
            . "Bedding, pillow/case, towels, toiletries, and bottled water are issued once only. Additional requests will be charged accordingly and must be coordinated with the front desk\n"
            . "Upon check-out, stains or dirt that cannot be easily cleaned will incur extra cleaning charges\n"
            . "All appliances, equipment, utensils, and other items must be complete upon check-out; otherwise, corresponding charges will apply\n"
            . "Any damage to hotel property will be charged\n"
            . "Extension of hours or days must be reported to the front desk on or before 10:00 AM, and corresponding fees must be settled\n"
            . "Failure to pay for two days will automatically result in suspension of access and utilities until full payment is received\n"
            . "Surrender the envelope to the front desk for room inspection/inventory before leaving the building",
        'booking_policies' => "Check-In: 2:00 PM\n"
            . "Check-Out: 12:00 PM\n\n"
            . "Note:\n"
            . "If you or one of your companions is a Senior Citizen or Person with Disability (PWD), we recommend paying only the down payment instead of the full amount. Discounts will be applied upon check-in after the valid ID is presented for verification.\n\n"
            . "Guarantee & Cancellation Policy\n"
            . "This reservation is non-cancellable and non-refundable but may be rebooked.\n"
            . "Rebooking must be requested at least 1 day before arrival, and the new date must be within 2 weeks from the original booking date.\n"
            . "Full payment will be forfeited in case of a no-show. Add-ons will be automatically cancelled.\n\n"
            . "Parking Policy\n"
            . "Parking spaces are limited and subject to availability. Guests are advised to contact the receptionist in advance.\n\n"
            . "Early Check-In / Late Check-Out\n"
            . "Subject to availability. Must coordinate with receptionist.\n"
            . "Charge: PHP 150/hour\n\n"
            . "Housekeeping Policy\n"
            . "Provided once only. Additional requests may incur a fee.",
    ];

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        if ($default === null && array_key_exists($key, self::DEFAULTS)) {
            $default = self::DEFAULTS[$key];
        }

        return static::query()
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function getMany(array $keys): array
    {
        $stored = static::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();

        $resolved = [];
        foreach ($keys as $key) {
            $resolved[$key] = $stored[$key] ?? (self::DEFAULTS[$key] ?? null);
        }

        return $resolved;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
