<?php

namespace Betalectic\FileManager\Helpers;
use Betalectic\FileManager\Models\Library;
use Betalectic\FileManager\Models\Attachment;
use Illuminate\Support\Facades\DB;

class Reader {

    public $library_id = null;
    public $filters = [];
    public $query;

    public function setLibraryId($id)
    {
        $this->library_id = $id;
    }
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }
    
    public function get()
    {
        $this->query = Attachment::with('library','of');

        if(!is_null($this->library_id)) {
            $this->query = $this->query->where('library_id',$this->library_id);
        }

        if(!empty($this->filters)) {
            if(array_has($this->filters, 'of_type') && !empty($this->filters['of_type'])) {
                $this->query = $this->query->where('of_type',$this->filters['of_type']);
            }
            if(array_has($this->filters, 'of_ids') && !empty($this->filters['of_ids'])) {
                $this->query = $this->query->whereIn('of_id',$this->filters['of_ids']);
            }
            if(array_has($this->filters, 'library_ids') && !empty($this->filters['library_ids'])) {
                $this->query = $this->query->whereIn('library_id',$this->filters['library_ids']);
            }
        }
        $this->query->orderBy('created_at','desc');

        $this->resetFilters();
        return $this->query->get()->unique(function($item) {
            return $item['library_id'].$item['of_id'].$item['of_type'];
        });

    }

    public function resetFilters()
    {
        $this->filters = [];
    }

    
}