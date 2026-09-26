<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'username',
        'email',
        'full_name',
        'phone',
        'avatar_url',
        'role',
        'password',
        'remember_token',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ==== Relasi ====

    public function designerStat(): HasOne
    {
        return $this->hasOne(DesignerStat::class, 'designer_id');
    }

    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function designerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'designer_id');
    }

    public function customerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'customer_id');
    }

    public function designerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'designer_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function orderLogs(): HasMany
    {
        return $this->hasMany(OrderLog::class, 'actor_id');
    }

    public function orderSplits(): HasMany
    {
        return $this->hasMany(OrderSplit::class, 'designer_id');
    }

    public function customerReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function designerReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'designer_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    // ==== Helper ====

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDesigner(): bool
    {
        return $this->role === 'designer';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}