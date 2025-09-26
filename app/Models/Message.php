<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages'; // adjust if table name differs

    protected $primaryKey = 'id';

    protected $fillable = [
        'chat_id',
        'sender_id',
        'receiver_id',
        'body',
    ];

    protected $casts = [
        'id' => 'integer',
        'chat_id' => 'integer',
        'sender_id' => 'integer',
        'receiver_id' => 'integer',
        'body' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
