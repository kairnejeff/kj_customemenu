<?php

use PrestaShop\Module\ProductCarrousel\Repository\ProductCarrouselRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__.'/vendor/autoload.php')) {
    require_once __DIR__.'/vendor/autoload.php';
}

class Kj_ProductCarrousel extends Module
{
    public function __construct() {
        $this->name = 'kj_productcarrousel';
        $this->author = 'Jing LEI';
        $this->version = '1.1.2';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Carrousel');
        $this->description = $this->l('Ajouter des carrousels supplémentaires au produit');
        $this->ps_versions_compliancy = array('min' => '1.7.1', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() || !$this->_installSql()
            || !$this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->_unInstallSql();
    }


    /**
    * Modifications sql du module
    * @return boolean
    */
    protected function _installSql()
    {
        $sqlCreate=" CREATE TABLE ". _DB_PREFIX_ ."product_carrousel ".
            "  (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, position INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL,PRIMARY KEY(id))";

        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
            . "ADD product_carrousel VARCHAR(255) NULL";
        $returnSql = Db::getInstance()->execute($sqlInstall);
        $returnCreateSql=Db::getInstance()->execute($sqlCreate);
        return $returnSql&&$returnCreateSql ;
    }

    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    protected function _unInstallSql()
    {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
            . "DROP product_carrousel";
        $sqlDrop = "DROP TABLE IF EXISTS " . _DB_PREFIX_ . "product_carrousel ";
        $returnSql = Db::getInstance()->execute($sqlInstall);
        $returnDropSql = Db::getInstance()->execute($sqlDrop);
        return $returnSql&&$returnDropSql;
    }

    /**
     * Affichage des informations supplémentaires sur la fiche produit
     * @param type $params
     * @return type
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params) {
        $product = new Product($params['id_product']);
        $languages = Language::getLanguages(true);
        $carrousels = $this->getCarrousels($product);
        //dump($carrousels);die;
        $this->context->smarty->assign(array(
                'carrousels' => $product->product_carrousel,
                'languages' => $languages,
                'default_language' => $this->context->employee->id_lang,
                'link' => $this->context->link,
                'image_baseurl' => $this->context->link->getBaseLink() . '/modules/' . $this->name . '/img/p/',
                'token' => Tools::getToken(false),
                'productId'=>$product->id
            )
        );
        return $this->display(__FILE__, 'views/templates/hook/carrousel.tpl');
    }

    public function getCarrousels(Product $product){
        $carrousels = explode(",",$product->product_carrousel);
        /**@var ProductCarrouselRepository $repository**/
        $repository =  $this->get('doctrine.orm.default_entity_manager')->getRepository('PrestaShop\Module\ProductCarrousel\Entity\ProductCarrousel');
        $allCarrousels=[];
        foreach ($carrousels as $carrousel){
            $allCarrousels[]= $repository->find((int)$carrousel);
        }

    }
}