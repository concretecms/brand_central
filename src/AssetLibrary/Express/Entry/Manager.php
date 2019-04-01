<?php

namespace Concrete5\AssetLibrary\Express\Entry;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Entry\Manager as EntryManager;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Tree\Node\Type\FileFolder;

class Manager extends EntryManager
{

    public function deleteEntry(Entry $entry)
    {
        // Check to see if this entry has a folder ID for files (it should). If so, move to the deleted
        // files node in the file manager.
        $folderId = $entry->getAssetFolderId();
        if ($folderId) {
            $folder = FileFolder::getByID($folderId);
            if ($folder) {
                $filesystem = new Filesystem();
                $deleted = $filesystem->getRootFolder()->getChildFolderByName('Deleted Assets');
                if ($deleted) {
                    $folder->move($deleted);
                }
            }
        }

        parent::deleteEntry($entry);
    }

}
