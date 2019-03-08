<?php 
namespace Betalectic\FileManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Betalectic\FileManager\Traits\UUIDTrait;

class MediaLibrary extends Model {

    use SoftDeletes;
    use UUIDTrait;

	protected $table = 'media_library';

    protected $fillable = [];

    protected $guarded = [];
    
    protected $dates = [];

    protected $UUIDCode = 'uuid';

    public static $rules = [
        // Validation rules
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uniquify();
        });

        static::deleting(function ($media) {
            
            foreach($media->mediaLinks as $link)
            {
                $link->delete();
            }

        });

    }

    public function owner()
    {
        return $this->morphTo();
    }
    
    public function mediaLinks()
    {
    	return $this->hasMany('Betalectic\FileManager\Models\MediaLink','media_library_id');
    }

}
