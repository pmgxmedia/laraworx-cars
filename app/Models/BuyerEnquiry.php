<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyerEnquiry extends Model
{
    use HasFactory;
    use GeneratesUlid;
    use SoftDeletes;

    public const STATUS_NEW = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_QUALIFIED = 'qualified';
    public const STATUS_APPOINTMENT = 'appointment';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_SPAM = 'spam';

    protected $fillable = [
        'ulid', 'vehicle_id', 'user_id', 'name', 'email', 'phone', 'preferred_contact_method',
        'message', 'preferred_date', 'finance_interest', 'trade_in_interest', 'status',
        'source', 'assigned_to', 'internal_note',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'finance_interest' => 'boolean',
            'trade_in_interest' => 'boolean',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_QUALIFIED => 'Qualified',
            self::STATUS_APPOINTMENT => 'Appointment',
            self::STATUS_CONVERTED => 'Converted',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_SPAM => 'Spam',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }
}