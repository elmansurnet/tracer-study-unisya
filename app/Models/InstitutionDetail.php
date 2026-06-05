<?php
// app/Models/InstitutionDetail.php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionDetail extends Model
{
    use HasUuids;

    protected $fillable = [
        'institution_id',
        'address', 'city', 'province', 'postal_code',
        'phone', 'fax', 'email',
        'contact_person', 'contact_phone',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}