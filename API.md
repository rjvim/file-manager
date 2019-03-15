GET /files

POST /files -> 

To create files in database, this won't basically upload file to anywhere.
This is to manage files

DELETE /files

POST /upload

==========

Upload Logic:

1. If File is an image, store in images disk (images key from config).
2. If File is not image, store on file-system
3. Use access while uploading the file

API:

// $disk, $path
FileManager::saveFile($disk, $path, $mimeType, $uploader, $tags, $meta = [])
FileManager::deleteFile($fileUUID);
FileManager::updateFile($fileUUID);
FileManager::getFiles($disk, $path, $mimeType, $tags = [])

FileSearchManager.php

$searchManager = new FileSearchManager();

$searchManager->disk($disk)
			 ->mimeType('png')
			 ->tags(["photo","fish"])
			 ->uploader($user)
			 ->entity($entity)
			 ->get();

FileManager::attach($file, $entity, $meta = [], $owner = false); // get_class($entity), $entity->getKey()
FileManager::attach($file, $class); // get_class($entity), $entity->getKey()

// Uploader

FileManager::uploadFile($pathToFile); // Optional, return url
FileManager::uploadAndSaveFile($pathToFile, $entity);






