<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaResponden extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kriteria_responden';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['nilai_post_test', 'nilai_pre_test_minimal', 'nilai_post_test_minimal', 'nilai_kenaikan_pre_post'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['nilai_pre_test_minimal' => 'float', 'nilai_post_test_minimal' => 'float', 'nilai_kenaikan_pre_post' => 'float', 'created_at' => 'datetime:Y-m-d H:i:s', 'updated_at' => 'datetime:Y-m-d H:i:s'];
    }


}
