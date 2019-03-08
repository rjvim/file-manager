<?php 
namespace Betalectic\FileManager\Models\SQL;

use Illuminate\Database\Eloquent\Model;

class MediaLink extends Model {

	protected $table = 'media_links';

    protected $fillable = [];

    protected $guarded = [];
    
    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function media()
    {
        return $this->belongsTo('Betalectic\FileManager\Models\MediaLibrary', 'media_library_id');
    }

    public function of()
    {
        return $this->morphTo(); 
    }
}
