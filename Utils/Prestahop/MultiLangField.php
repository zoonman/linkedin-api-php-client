<?php

namespace Pricat\Utils\Prestahop;

use Language;

class MultiLangField
{
    /**
     * Función de Prestashop para crear campos multi-idioma
     * @param string $field
     * @return array
     */
    public function run($field)
    {
        $languages = Language::getLanguages(false);
        $langFields = array();
        foreach ($languages as $lang) {
            $langFields[$lang['id_lang']] = $field;
        }
        return $langFields;
    }
}
