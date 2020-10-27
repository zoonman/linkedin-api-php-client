<?php


namespace Pricat\Services\Product\Photos;


use DirectoryIterator;

class GetPhotos
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var array
     */
    private $photos;

    public function __construct()
    {
        $this->path = PATH_PHOTOS;
        $this->photos = [];
    }

    /**
     * @return array
     */
    public function run()
    {
        $iter = new DirectoryIterator($this->path);
        foreach ($iter as $fileInfo) {
            if ($fileInfo->isFile() && !in_array($fileInfo->getFilename(), $this->photos)) {
                $this->photos[$fileInfo->getFilename()] = $fileInfo->getMTime();
            }
        }
        return $this->photos;
    }
}
