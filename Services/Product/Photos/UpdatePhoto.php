<?php


namespace Pricat\Services\Product\Photos;

use Image;
use Pricat\Utils\Helper as Utils;
use Pricat\Utils\Prestahop\Helpers;
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

    public function __construct(array $photos)
    {
        $this->pathPhotos = PATH_PHOTOS;
        $this->photos = $photos;
    }

    public function run(Product $product, $n)
    {
        $path = $this->pathPhotos . $n->imagen;

        if (!in_array($n->imagen, $this->photos) && !is_file($path)) {
            Utils::printInfo(sprintf("[Error: actualizaFoto] No se encuentra la foto: %s\n", $path));
            return;
        }

        $product->deleteImages();

        $image = new Image();
        $image->id_product = (int)($product->id);
        $image->position = Image::getHighestPosition($product->id) + 1;
        Image::deleteCover($image->id_product);
        $image->cover = true;
        if (!Validate::isGenericName($n->nombre)) {
            return;
        }

        $image->legend = (new MultiLangField())->run($n->nombre);

        if (!$image->add()) {
            Utils::printInfo(sprintf("[Error: actualizaFoto] No se ha podido añadir la foto: %s, del producto con ID %s\n", $n->imagen, $product->id));
            return;
        }

        if (Helpers::copyImg($product->id, $image->id, $path)) {
            Utils::printInfo(sprintf("[Error: actualizaFoto] No se ha podido copiar la imagen: %s, del producto con ID %s\n", $n->imagen, $product->id));
        }
    }
}
