<?php

namespace Betalectic\FileManager;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Betalectic\FileManager\Helpers\CloudinaryHelper;
use Intervention\Image\ImageManagerStatic;
use Betalectic\FileManager\Models\Library;
use Betalectic\FileManager\Models\Attachment;
use Betalectic\FileManager\Helpers\Search;
use Betalectic\FileManager\Helpers\Reader as FileReader;


class FileManager {

	public $fileReader;

	public function __construct()
	{
		$this->fileReader = new FileReader();
	}

	public function savePath($file, $fileName = NULL, $owner = NULL, $uploader = NULL, $tags = NULL, $entities = [])
	{
		$fullPath = storage_path('app/files/'.basename($file));

		$uploadedFile = $this->upload($fullPath);

		$file = $this->save(
			$uploadedFile['url'],
			$uploadedFile['disk'],
			$uploadedFile['mime_type'],
			$uploader,
			$tags,
			[
				'file_name' => $fileName
			]
		);

		if(!is_null($owner)) {
			$this->setOwner($file->uuid, $owner->getKey(),get_class($owner));
		}

		if(!empty($entities)) {
			foreach ($entities as $entity) {
				$this->attach($file,$entity,$meta = [],$owner = false);
			}
		}

		return $file;
	}

	public function saveBase64Image($sourceFile, $fileName = NULL, $owner = NULL, $uploader = NULL)
	{
		$savedFile = $this->saveBase64ToDisk($sourceFile);
		$pathOnDisk = $savedFile['path'];
		$fileName = is_null($fileName) ? $savedFile['name'] : $fileName;
		return $this->savePath($pathOnDisk, $fileName, $owner, $uploader);

	}

	public function saveBase64ToDisk($base64String)
	{
		$extension = explode('/', explode(':', substr($base64String, 0, strpos($base64String, ';')))[1])[1];

		$filename = microtime(true).'.'.$extension;

        ImageManagerStatic::make($base64String)->save(storage_path('app/files/') . $filename);

		$storeOnDisk = Storage::url('files/'.$filename);

		return ['path' => $storeOnDisk, 'name' => $filename];
	}

	public function upload($pathToFile)
	{
		$file = new File($pathToFile);

		$mimeType = $file->getMimeType();

		$isImage = substr($mimeType,0,5) == 'image' ? true : false;

		if($isImage && config('file-manager.save_images_to') == 'cloudinary')
		{
			$cloudinaryHelper = new CloudinaryHelper();
			$result = $cloudinaryHelper
							->uploadFile($pathToFile);
			$url = $result['secure_url'];
			$disk = 'cloudinary';
		}
		else
		{
			$s3Key = config('file-manager.file_prefix');
			$s3Key = Storage::disk('s3')->putFileAs($s3Key,$file,$file->getFilename(),'public');
			$url = Storage::disk('s3')->url($s3Key);
			$disk = 's3';
		}

		return ['url' => $url, 'disk' => $disk, 'mime_type' => $mimeType];
	}

	public function save($path, $disk = NULL, $mimeType = NULL, $uploader = NULL, $tags = NULL, $meta = [])
	{

		$file = Library::firstOrCreate(['path' => $path]);
		$file->fill([
			'disk' => $disk,
			'mime_type' => $mimeType,
			'uploaded_by' => $uploader,
			'meta' => $meta,
			'tags' => $tags
		]);

		$file->save();

		return $file;
	}

	public function setOwner($code, $owner_id, $owner_type)
	{
		$file = Library::whereUuid($code)->first();
		$file->owner_id = $owner_id;
		$file->owner_type = $owner_type;
		$file->save();
	}

	public function delete($uuid)
	{
		$file = Library::withTrashed()->whereUuid($uuid)->first();

		$ext = pathinfo($file->path, PATHINFO_EXTENSION);

		if($file->disk == 's3')
		{
			$key = config('file-manager.file_prefix').'/'.basename($file->path);
			Storage::disk('s3')->delete($key);
		}

		if($file->disk == 'cloudinary')
		{
			$cloudinaryId = basename($file->path, ".".$ext);
			\Cloudinary\Uploader::destroy($cloudinaryId);
		}

		$file->forceDelete();

		return true;
	}

	public function attach($file, $entity, $meta = [], $owner = false)
	{
		$attachment = Attachment::firstOrCreate([
			'library_id' => $file->id,
			'of_id' => $entity->getKey(),
			'of_type' => get_class($entity),
		]);

		$attachment->meta = $meta;
		$attachment->owner = $owner;
		$attachment->save();
		return true;
	}

	public function bulkAttach($library_ids,$entities, $meta = [], $owner = false)
	{
		$libraries = Library::whereIn('id',$library_ids)->get();

		foreach ($libraries as $library) {
			foreach ($entities as $entity) {
				$this->attach($library,$entity,$meta,$owner);
			}
		}

		return true;
	}

    public function searchLibrary($filters = [])
    {
        $search = new Search();

        if(isset($filters['q']) && !empty($filters['q'])) {
            $search->setQuery($filters['q']);
        }
        if(isset($filters['per_page']) && !empty($filters['per_page'])) {
            $search->setPerPage($filters['per_page']);
        }
        if(isset($filters['page']) && !empty($filters['page'])) {
            $search->setPage($filters['page']);
        }

        if(isset($filters['excluded_library_ids']) && !empty($filters['excluded_library_ids'])) {
            $search->setExcludedLibraryIds($filters['excluded_library_ids']);
        }

        return $search->getFromLibrary();
    }

	public function search($filters)
	{

		$search = new Search();

		if(isset($filters['q']) && !empty($filters['q'])) {
			$search->setQuery($filters['q']);
		}
		if(isset($filters['of_id']) && !empty($filters['of_id'])) {
			$search->setOfId($filters['of_id']);
		}
		if(isset($filters['of_type']) && !empty($filters['of_type'])) {
			$search->setOfType($filters['of_type']);
		}
		if(isset($filters['per_page']) && !empty($filters['per_page'])) {
			$search->setPerPage($filters['per_page']);
		}
		if(isset($filters['page']) && !empty($filters['page'])) {
			$search->setPage($filters['page']);
		}
		if(isset($filters['type']) && !empty($filters['type'])) {
			$search->setType($filters['type']);
		}
		if(isset($filters['excluded_library_ids']) && !empty($filters['excluded_library_ids'])) {
			$search->setExcludedLibraryIds($filters['excluded_library_ids']);
		}


		return $search->get();
	}

	public function update($code, $data)
	{
		$file = Library::whereUuid($code)->first();
		$file->meta = array_except($data, ['tags']);
		$file->tags = $data['tags'];
		$file->save();

		return true;
	}

	public function removeAttachment($id)
	{
		Attachment::find($id)->delete();
		return true;
	}

	public function getAttachments($library_id = null,$filters =[])
	{
		if(isset($library_id) && !empty($library_id)) {
			$this->fileReader->setLibraryId($library_id);
		}
		if(!empty($filters)) {
			$this->fileReader->setFilters($filters);
		}
		return $this->fileReader->get($filters);

	}




}
