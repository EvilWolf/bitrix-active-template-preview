<?php

class PortfolioPageTemplate {
    /* 
     * Возвращает массив с описанием шаблона страницы.
     * Выводится при выборе типа страницы, перед открытием мастера.
     */
    public function GetDescription() {
        return [
            'name' => 'Активный шаблон',
            'description' => 'Тестовый активный шаблон',
            'modules' => [],
        ];
    }

    /* 
     * Возвращает HTML формы редактирования активного шаблона страницы
     * Выводится на 3ем этапе создания страницы через мастер
     */
    public function GetFormHtml() {
        $arTemplates = $this->getTemplatesInfo();
        $html = '';
        $tick = 0;

        foreach ($arTemplates as $arTemplate) {
            $html .= "<td><label><input type='radio' value='{$arTemplate['ID']}' name='TEMPLATE_ID'>";
            $html .= $arTemplate['INFO']['NAME'];

            if ($arTemplate['IMAGE'])
                $html .= "<img src='{$arTemplate['IMAGE']}'>";
            
            $html .= "</label></td>";

            $tick++;
            if ($tick % 3 === 0)
                $html .= '</tr><tr>';
        }

        return $html;
    }

    /* 
     * Возвращает HTML готовой страницы.
     * Нужно заметить, что если вы передавали форму в методе GetFormHtml, то её данные не попадут в параметр $arParams;
     * Их нужно отдельно получать через массив _POST или _REQUEST
     * 
     * Массив $arParams содержит данные о странице. 
     *   path - пусть к каталогу с новой страницой. 
     *   file - имя файла новой страницы. 
     *   site - Идентификатор сайта.
     */
    public function GetContent($arParams) {
        $templateID = $_REQUEST['TEMPLATE_ID'];
        $arTemplates = $this->getTemplatesInfo();
        return $arTemplates[$templateID]['CONTENT'];
    }


    /*
     * Возвращает массив с директориями-шаблонами страниц
     */
    private function getTemplatesDirs() {
        $dir = scandir(__DIR__);
        $arReturn = [];

        foreach ($dir as $file) {
            if ($file[0] === '.')
                continue;

            $dirPath = __DIR__ . DIRECTORY_SEPARATOR . $file;
            if (is_dir($dirPath))
                $arReturn[$file] = $dirPath;
        }

        return $arReturn;
    }

    /*
     * Возвращает информацию о шаблоне. Путь, описание, содержимое
     */
    private function getTemplatesInfo() {
        $arTemplateDirs = $this->getTemplatesDirs();
        $arReturn = [];

        $templateWebPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', _normalizePath(__DIR__));

        foreach ($arTemplateDirs as $templateID => $templatePath) {
            $tmpTemplate = [
                'ID' => $templateID,
                'PATH' => $templatePath,
                'CONTENT' => file_get_contents($templatePath . DIRECTORY_SEPARATOR . 'template.php'),
                'INFO' => @include($templatePath . DIRECTORY_SEPARATOR . '.description.php')
            ];

            if (!empty($tmpTemplate['INFO']['PIC']))
                $tmpTemplate['IMAGE'] = $templateWebPath . '/' . $templateID . '/' . $tmpTemplate['INFO']['PIC'];

            $arReturn[$templateID] = $tmpTemplate;
            unset($tmpTemplate);
        }

        return $arReturn;
    }
}

$pageTemplate = new PortfolioPageTemplate();
