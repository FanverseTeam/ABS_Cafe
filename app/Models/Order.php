<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    public const SOURCE_LABELS = [
        'offline' => 'Offline',
        'web' => 'Web',
    ];

    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'table_number',
        'order_type',
        'source',
        'subtotal',
        'tax',
        'service_charge',
        'discount',
        'promo_id',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'cashier_name',
        'cashier_id',
        'cash_shift_id',
        'paid_at',
        'received_at',
        'cancel_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'table_number' => 'integer',
        'paid_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(OrderTimeline::class)->orderBy('logged_at', 'asc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function cashShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'cash_shift_id');
    }

    /**
     * Nama kasir efektif (nama penjaga kasir atau nama user kasir).
     */
    public function cashierLabel(): string
    {
        return $this->cashier_name ?: ($this->cashier?->name ?: 'Kasir');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'lunas';
    }

    public function isCancelled(): bool
    {
        return in_array($this->payment_status, ['dibatalkan'], true) || $this->order_status === 'dibatalkan';
    }

    public function sourceLabel(): string
    {
        return self::SOURCE_LABELS[$this->source] ?? ucfirst((string) $this->source);
    }

    public function orderTypeLabel(): string
    {
        return $this->order_type === 'take_away' ? 'Take-away' : 'Dine-in';
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'tunai' => 'Tunai',
            'qris' => 'QRIS',
            'debit' => 'Kartu Debit',
            'transfer' => 'Transfer Bank',
            'ewallet' => 'E-Wallet',
            default => ucfirst((string) $this->payment_method),
        };
    }

    /**
     * @return array{label: string, color: string}
     */
    public function orderStatusBadge(): array
    {
        if ($this->isCancelled()) {
            return ['label' => 'Dibatalkan', 'color' => 'danger'];
        }

        return match ($this->order_status) {
            'baru', 'menunggu' => ['label' => 'Menunggu', 'color' => 'warning'],
            'diterima' => ['label' => 'Diterima', 'color' => 'info'],
            'diproses' => ['label' => 'Diproses', 'color' => 'warning'],
            'siap' => ['label' => 'Siap / Disajikan', 'color' => 'info'],
            'selesai' => ['label' => 'Selesai', 'color' => 'success'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'color' => 'danger'],
            default => ['label' => ucfirst((string) $this->order_status), 'color' => 'muted'],
        };
    }

    /**
     * @return array{label: string, color: string}
     */
    public function paymentStatusBadge(): array
    {
        return match ($this->payment_status) {
            'lunas' => ['label' => 'Lunas', 'color' => 'success'],
            'menunggu_pembayaran' => ['label' => 'Belum Dibayar', 'color' => 'warning'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'color' => 'danger'],
            default => ['label' => ucfirst((string) $this->payment_status), 'color' => 'muted'],
        };
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'menunggu_pembayaran')
            ->where('order_status', '!=', 'dibatalkan');
    }

    public function scopeActive($query)
    {
        return $query->where('order_status', '!=', 'dibatalkan')
            ->where('order_status', '!=', 'selesai');
    }

    /**
     * Scope untuk membatasi pesanan khusus pada hari ini saja (tanggal saat ini).
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope untuk pesanan yang masuk ke daftar kasir (semua pesanan offline/pos, dan pesanan web termasuk status 'baru').
     */
    public function scopeForKasir($query)
    {
        return $query->where(function ($q) {
            $q->where('source', '!=', 'web')
                ->orWhereIn('order_status', ['baru', 'diterima', 'diproses', 'siap', 'selesai']);
        });
    }

    public function latestPayment(): ?Payment
    {
        return $this->payments()->latest('id')->first();
    }

    /**
     * Ringkasan "metode + status" untuk tautan cetak ulang.
     */
    public function payMethodReceipt(): string
    {
        $p = $this->latestPayment();

        return $p
            ? $p->methodLabel().' &bull; Lunas '.($p->paid_at?->format('d/m/y H:i') ?? $this->paid_at?->format('d/m/y H:i') ?? '-')
            : $this->paymentMethodLabel().' &bull; '.$this->paymentStatusBadge()['label'];
    }

    public function paymentCashReceived(): float
    {
        return (float) ($this->latestPayment()?->cash_received ?? $this->total);
    }

    public function paymentChange(): float
    {
        return (float) ($this->latestPayment()?->change_given ?? 0);
    }

    /**
     * URL yang dibuka kasir saat memindai QR Code pesanan pelanggan.
     */
    public function cashierScanUrl(): string
    {
        $baseUrl = CafeSetting::get('order_base_url');
        if ($baseUrl) {
            return rtrim($baseUrl, '/').'/kasir/pesanan/scan/'.urlencode($this->order_code);
        }

        return route('kasir.orders.scan', ['code' => $this->order_code]);
    }

    /**
     * URL API QR Code yang di-generate untuk pesanan ini.
     */
    public function qrCodeApiUrl(int $size = 300): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size='.$size.'x'.$size.'&data='.urlencode($this->cashierScanUrl());
    }
}
