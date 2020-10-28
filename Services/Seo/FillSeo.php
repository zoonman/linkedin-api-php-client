<?php


namespace Pricat\Services\Seo;


use Db;
use Pricat\Entities\Tire;
use Product;
use Tools;

class FillSeo
{
    /**
     * Rellena los datos para SEO
     */
    public function run(Product &$product, Tire $tire)
    {
        $title = trim($tire->nombre);
        $link_rewrite = $title;
        $meta_title = sprintf('Neumáticos %s mejor precio online', $title);
        $meta_description = sprintf('Busca, encuentra y compra cómodamente neumáticos y ruedas baratas en Yofindo. Selecciona tu neumático, encuentra tu taller de montaje de las ruedas y concreta la cita: Yofindo servicio total. | %s', $title);
        $meta_keywords = [
            'comprar neumaticos ' . $title,
            'comprar ruedas ' . $title
        ];
        $product->link_rewrite[SHOP_LANG] = Tools::link_rewrite($link_rewrite);
        $product->meta_title[SHOP_LANG] = $meta_title;
        $product->meta_description[SHOP_LANG] = $meta_description;
        $product->meta_keywords[SHOP_LANG] = implode(', ', $meta_keywords);

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_lang WHERE link_rewrite=\'' . $product->link_rewrite[SHOP_LANG] . '\'';
        if (isset($product->id) && $product->id) {
            $sql .= ' and id_product != ' . $product->id;
        }
        $result = Db::getInstance()->ExecuteS($sql);
        if (count($result)) {
            $product->link_rewrite[SHOP_LANG] .= '_' . $product->reference;
        }
    }
}
