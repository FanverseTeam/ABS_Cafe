<?php

namespace App\Services;

use App\Models\CafeSetting;
use Carbon\Carbon;

class CafeScheduleService
{
    public const TIMEZONE = 'Asia/Jakarta';

    /**
     * Dapatkan status dan jadwal operasional kafe.
     *
     * @param  Carbon|string|null  $targetTime
     * @return array{
     *     is_open: bool,
     *     status_text: string,
     *     today_hours: string,
     *     open_time: string,
     *     close_time: string,
     *     current_time: string,
     *     day_name: string,
     *     is_weekend: bool,
     *     manual_status: string,
     *     message: string
     * }
     */
    public static function getSchedule($targetTime = null): array
    {
        $now = self::resolveTime($targetTime);

        $manualStatus = (string) CafeSetting::get('cafe_manual_status', 'otomatis');

        // Hari Jumat (5), Sabtu (6), Minggu (0) dianggap akhir pekan
        $isWeekend = in_array($now->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY], true);

        $rawHours = $isWeekend
            ? (string) CafeSetting::get('hours_weekend', '09:00 - 23:00')
            : (string) CafeSetting::get('hours_weekday', '09:00 - 23:00');

        [$openTime, $closeTime] = self::parseHoursRange($rawHours);

        $isOpen = false;
        if ($manualStatus === 'buka_paksa') {
            $isOpen = true;
        } elseif ($manualStatus === 'tutup_sementara') {
            $isOpen = false;
        } else {
            $isOpen = self::isTimeWithinRange($now, $openTime, $closeTime);
        }

        $formattedHours = "{$openTime} - {$closeTime} WIB";
        $customClosedMsg = CafeSetting::get('cafe_closed_message');

        if ($isOpen) {
            $message = "Kafe sedang buka ({$formattedHours}).";
        } else {
            $message = $customClosedMsg ?: "Mohon maaf, kafe saat ini sedang tutup. Jam operasional hari ini adalah {$formattedHours}.";
        }

        return [
            'is_open' => $isOpen,
            'status_text' => $isOpen ? 'Buka' : 'Tutup',
            'today_hours' => $formattedHours,
            'open_time' => $openTime,
            'close_time' => $closeTime,
            'current_time' => $now->format('H:i'),
            'day_name' => $now->translatedFormat('l'),
            'is_weekend' => $isWeekend,
            'manual_status' => $manualStatus,
            'message' => $message,
        ];
    }

    /**
     * Cek apakah kafe sedang buka saat ini.
     */
    public static function isOpen($targetTime = null): bool
    {
        return self::getSchedule($targetTime)['is_open'];
    }

    /**
     * Cek apakah waktu reservasi tertentu (tanggal & jam) sesuai jam operasional kafe.
     */
    public static function isReservationTimeValid(string $date, string $time): bool
    {
        try {
            $reservationDate = Carbon::parse($date, self::TIMEZONE);
            $isWeekend = in_array($reservationDate->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY, Carbon::SUNDAY], true);

            $rawHours = $isWeekend
                ? (string) CafeSetting::get('hours_weekend', '09:00 - 23:00')
                : (string) CafeSetting::get('hours_weekday', '09:00 - 23:00');

            [$openTime, $closeTime] = self::parseHoursRange($rawHours);

            // Bersihkan format waktu input (misal '14:00' atau '14.00')
            $cleanTime = str_replace('.', ':', trim($time));
            if (strlen($cleanTime) === 5) {
                $cleanTime .= ':00';
            }

            $target = Carbon::parse($date.' '.$cleanTime, self::TIMEZONE);

            return self::isTimeWithinRange($target, $openTime, $closeTime);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Parse rentang jam string seperti '09:00 - 23:00' atau '09.00 - 23.00'.
     *
     * @return array{0: string, 1: string}
     */
    public static function parseHoursRange(?string $raw): array
    {
        $default = ['09:00', '23:00'];
        if (! $raw || ! str_contains($raw, '-')) {
            return $default;
        }

        $parts = explode('-', $raw);
        if (count($parts) < 2) {
            return $default;
        }

        $open = self::normalizeTime(trim($parts[0]));
        $close = self::normalizeTime(trim($parts[1]));

        return [
            $open ?: '09:00',
            $close ?: '23:00',
        ];
    }

    private static function normalizeTime(string $time): string
    {
        // Bersihkan karakter non-digit selain ':' dan '.'
        $clean = preg_replace('/[^\d:\.]/', '', $time);
        $clean = str_replace('.', ':', (string) $clean);

        if (preg_match('/^(\d{1,2}):(\d{2})$/', $clean, $matches)) {
            $hour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $minute = $matches[2];

            return "{$hour}:{$minute}";
        }

        return '';
    }

    private static function isTimeWithinRange(Carbon $time, string $openStr, string $closeStr): bool
    {
        $timeStr = $time->format('H:i');

        // Jika jam tutup >= jam buka (contoh: 09:00 s.d 23:00)
        if ($closeStr >= $openStr) {
            return $timeStr >= $openStr && $timeStr <= $closeStr;
        }

        // Jika jam tutup < jam buka (melewati tengah malam, contoh: 18:00 s.d 02:00)
        return $timeStr >= $openStr || $timeStr <= $closeStr;
    }

    private static function resolveTime($time): Carbon
    {
        if ($time instanceof Carbon) {
            return $time->copy()->setTimezone(self::TIMEZONE);
        }

        if (is_string($time) && $time !== '') {
            return Carbon::parse($time, self::TIMEZONE);
        }

        return Carbon::now(self::TIMEZONE);
    }
}
