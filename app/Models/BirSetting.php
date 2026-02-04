<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BirMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BIR Setting Model
 *
 * @property int $id
 * @property int $company_id
 * @property string|null $api_endpoint
 * @property string|null $api_key
 * @property string|null $api_secret
 * @property string|null $certificate_path
 * @property string|null $private_key_path
 * @property BirMode $mode
 * @property bool $enable_auto_submit
 * @property int $submit_delay_hours
 * @property array|null $configuration_data
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_tested_at
 * @property string|null $last_test_result
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class BirSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'api_endpoint',
        'api_key',
        'api_secret',
        'certificate_path',
        'private_key_path',
        'mode',
        'enable_auto_submit',
        'submit_delay_hours',
        'configuration_data',
        'is_active',
        'last_tested_at',
        'last_test_result',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mode' => BirMode::class,
            'enable_auto_submit' => 'boolean',
            'submit_delay_hours' => 'integer',
            'configuration_data' => 'json',
            'is_active' => 'boolean',
            'last_tested_at' => 'datetime',
            'api_key' => 'encrypted',
            'api_secret' => 'encrypted',
        ];
    }

    /**
     * Get the company that owns this BIR setting.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
