<?php 

namespace Betalectic\FileManager\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use Betalectic\FileManager\Models\MediaLibrary;
use Auth;
use Betalectic\FileManager\Http\Resources\File as FileResource;
use Betalectic\FileManager\Helpers\CloudinaryHelper;

use Betalectic\FileManager\Helpers\FileUploadHelper as UploadHelper;

class FilesController extends BaseController {

	public function index(Request $request)
	{	
		dd("aaa");	
		$images = MediaLibrary::where('filename','LIKE','%'.$request->get('q','').'%')
							->orderBy('created_at','DESC')
							->paginate(20);

		return FileResource::collection($images);
	}

	public function store(Request $request)
	{
		if($request->get('owner') == 'admin')
		{
			MediaLibrary::create([
				'type' => 'image',
				'format' => $request->get('format'),
				'filename' => $request->get('filename'),
				'provider' => 'cloudinary',
				'path' => $request->get('secure_url'),
				'uploaded_by' => Auth::user()->id
			]);
		}

		return response(['success']);
	}
	public function uploadFiles(Request $request)
	{
		$mediaLibrarys = [];
		$fileObjects = $request->all();
		$uploadHelper = new UploadHelper();
	
		for($i=0; $i<$fileObjects['no_of_files']; $i++) {

			if($request->has('source') && $request->get('source') == 'base64') {
				$mediaLibrarys[] = $uploadHelper->uploadBase64Image($request->get('file'.$i),$request->get('file_name'.$i));
			} else {
				$mediaLibrarys[] = $uploadHelper->upload($request->file('file'.$i),$request->get('file_name'.$i));

			}

			return response()->json([
				'message' => 'Successfully uploaded',
				'data' => $mediaLibrarys,
				'success' => true],200);
		} 

	}

}
