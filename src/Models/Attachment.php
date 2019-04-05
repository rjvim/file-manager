<?php 
namespace Betalectic\FileManager\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model {

	protected $table = 'file_manager_attachments';

    protected $fillable = [];

    protected $guarded = [];
    
    protected $dates = [];

    protected $casts = [
            'meta' => 'array',
        ];

    protected function castAttribute($key, $value)
    {
        if ($this->getCastType($key) == 'array' && is_null($value)) {
            return [];
        }
        return parent::castAttribute($key, $value);
    }
    
    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function library()
    {
        return $this->belongsTo('Betalectic\FileManager\Models\Library', 'library_id');
    }

    public function of()
    {
        return $this->morphTo(); 
    }
}
