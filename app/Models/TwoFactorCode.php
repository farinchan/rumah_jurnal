<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'method',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Get the user that owns the two factor code.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the code is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the code is valid.
     */
    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Generate a new code for the user.
     */
    public static function generateCode(User $user, string $method = 'email'): self
    {
        // Invalidate any existing codes
        self::where('user_id', $user->id)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Generate new 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'user_id' => $user->id,
            'code' => $code,
            'method' => $method,
            'expires_at' => now()->addMinutes(10), // Code expires in 10 minutes
        ]);
    }
}
