<?php


namespace Pricat\Services\Product\Photos;

use Image;
use Pricat\Entities\Tire;
use Pricat\Utils\Helper as Utils;
use Pricat\Utils\Prestahop\Helper;
use Pricat\Utils\Prestahop\MultiLangField;
use Product;
use Validate;


class UpdatePhoto
{
    /**
     * @var string
     */
    private $pathPhotos;
    /**
     * @var array
     */
    private $photos;

    public function __construct()
    {
        $this->pathPhotos = PATH_PHOTOS;
        $this->photos = (new GetPhotos())->run();
    }

    public function run(Product $product, Tire $tire)
    {
        $path = $this->pathPhotos . $tire->imagen;

        if ((!in_array($tire->imagen, $this->photos) && !is_file($path))
            && !copy(URL_DOWNLOAD_IMAGE . $tire->imagen, PATH_PHOTOS . $tire->imagen)) {
            Utils::printInfo(sprintf("[Error: actualizaFoto] No se encuentra la foto: %s\n", $path));
            return;
        }

        $product->deleteImages();

        $image = new Image();
        $image->id_product = (int)($product->id);
        $image->position = Image::getHighestPosition($product->id) + 1;
        Image::deleteCover($image->id_product);
        $image->cover = true;
        if (!Validate::isGenericName($tire->nombre)) {
            return;
        }

        $image->legend = (new MultiLangField())->run($tire->nombre);

        if (!$image->add()) {
            Utils::printInfo(sprintf("[Error: actualizaFoto] No se ha podido añadir la foto: %s, del producto con ID %s\n", $tire->imagen, $product->id));
            return;
        }

        if (!Helper::copyImg($product->id, $image->id, $path)) {
            Utils::printInfo(sprintf("[Error: actualizaFoto] No se ha podido copiar la imagen: %s, del producto con ID %s\n", $tire->imagen, $product->id));
        }
    }
}
