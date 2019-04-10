<?php

namespace Betalectic\FileManager\Helpers;
use Betalectic\FileManager\Models\Library;
use Betalectic\FileManager\Models\Attachment;
use Betalectic\FileManager\Http\Resources\File as FileResource;
use Illuminate\Support\Facades\DB;

class Search {

    public $q = '';
    public $query = null;
    public $of_id = null;
    public $of_type = null;
    public $per_page = null;
    public $page = null;
    public $type= null;
    public $excludedLibraryIds = null;

    public function setQuery($query='')
    {
        $this->q = $query;
    }
    public function setOfId($of_id)
    {
        $this->of_id = $of_id;
    }
    public function setOfType($of_type)
    {
        $this->of_type = $of_type;
    }
    public function setPerPage($per_page)
    {
        $this->per_page = $per_page;
    }
    public function setPage($page)
    {
        $this->page = $page;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
    public function setExcludedLibraryIds($ids)
    {
        $this->excludedLibraryIds = $ids;   
    }

    public function get()
    {
        $attachments = [];
        
        $this->query = Attachment::with('library','of');

        if($this->q != '') {
            $q = $this->q;
            $this->query = $this->query->whereHas('library', function ($query) use($q) {
                    $query->where('meta','LIKE','%'.$q.'%')
                        ->orWhere('tags','LIKE','%'.$q.'%');
            });
        }
        if(!is_null($this->of_id)) {
            $this->query = $this->query->where('of_id',$this->of_id);
        }
        if(!is_null($this->of_type)) {
            $this->query = $this->query->where('of_type',$this->of_type);
        }
        if(!is_null($this->type)) {
            switch ($this->type) {
                case 'images':
                    $this->query = $this->query->whereHas('library', function ($query) use($q) {
                                        $query->where('mime_type','LIKE','image%');
                                    });
                    break;
                case 'documents':
                    $this->query = $this->query->whereHas('library', function ($query) use($q) {
                                    $query->where('mime_type','NOT LIKE','image%')
                                            ->orWhereNull('mime_type');
                                    });
                    break;
                default:
                    # code...
                    break;
            }
        }
        if(!is_null($this->excludedLibraryIds)) {
            $this->query = $this->query->whereNotIn('library_id',$this->excludedLibraryIds);
        }

        $this->query->orderBy('created_at','desc');

        $attachments = !is_null($this->per_page) ? 
                            $this->query->paginate($this->per_page) : $this->query->get();
        return  $attachments;
            
    }

    
}