<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaBlast extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_blast';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['session_name', 'whatsapp_number', 'status', 'webhook', 'api_key'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['session_name' => 'string', 'whatsapp_number' => 'string', 'webhook' => 'string', 'api_key' => 'string', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s'];
    }


}
