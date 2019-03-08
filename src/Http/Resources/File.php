<?php

namespace Betalectic\FileManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Betalectic\FileManager\Helpers\CloudinaryHelper;

class File extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [];

        $cloudinaryHelper = new CloudinaryHelper();

        $img_sizes = [];

        if($request->has('img_sizes')){
            $img_sizes = explode(',', $request->get('img_sizes'));
        }

        $data['filename'] = $this->filename;
        $data['path'] = $this->path;
        $data['id'] = $this->id;
        $data['wh_100_100'] = $cloudinaryHelper->createUrl($this->path,100,100);

        foreach($img_sizes as $img_size)
        {
            list($w,$h) = explode("-", $img_size);
            $data['wh_'.$w.'_'.$h] = $cloudinaryHelper->createUrl($this->path,$w,$h);
        }

        return $data;
        
    }
}
