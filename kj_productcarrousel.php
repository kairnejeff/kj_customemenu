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
        $this->version = '1.1.0';
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
            || !$this->registerHook(
                'displayAdminProductsMainStepLeftColumnMiddle'
            )
            ||!$this->registerHook(
                'displayProductCaroussel'
            )
            ||!$this->registerHook(
                'displayCategoryProductCaroussel'
            )
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
            "  (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, file_name VARCHAR(255) DEFAULT NULL,nom VARCHAR(255) NOT NULL, position INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL,PRIMARY KEY(id))";

        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
            . "ADD product_carrousel VARCHAR(255) NULL";
        $sqlHook ="INSERT INTO ". _DB_PREFIX_ ."hook (`name`, `title`, `description`) VALUES ('displayProductCaroussel', 'displayProductCaroussel', 'add a hook carrousel to product page')";
        $sqlHook2 ="INSERT INTO ". _DB_PREFIX_ ."hook (`name`, `title`, `description`) VALUES ('displayCategoryProductCaroussel', 'displayCategoryProductCaroussel', 'add a hook carrousel to product catgeory page')";
        $returnSql = Db::getInstance()->execute($sqlInstall);
        $returnCreateSql=Db::getInstance()->execute($sqlCreate);
        $returnHook=Db::getInstance()->execute($sqlHook);
        $returnHook2=Db::getInstance()->execute($sqlHook2);
        return $returnSql&&$returnCreateSql&&$returnHook&&$returnHook2 ;
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
        $sqlDelete= "DELETE FROM " . _DB_PREFIX_. "hook  WHERE `name` = 'displayProductCaroussel'";
        $sqlDelete2= "DELETE FROM " . _DB_PREFIX_. "hook  WHERE `name` = 'displayCategoryProductCaroussel'";
        $returnSql = Db::getInstance()->execute($sqlInstall);
        $returnDropSql = Db::getInstance()->execute($sqlDrop);
        $returnDeleteHook = Db::getInstance()->execute($sqlDelete);
        $returnDeleteHook2 = Db::getInstance()->execute($sqlDelete2);
        return $returnSql&&$returnDropSql&&$returnDeleteHook&&$returnDeleteHook2;
    }

    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params) {
       $variables = $this->_display($params['id_product']);
       $this->context->smarty->assign($variables);
       return $this->display(__FILE__, 'views/templates/hook/carrousel.tpl');
    }

    public function hookDisplayProductCaroussel($params)
    {
        $variables = $this->_display($params['id_product']);
        $this->context->smarty->assign($variables);
        return $this->display(__FILE__, 'views/templates/hook/frontCaroussel.tpl');
    }
    public function hookDisplayCategoryProductCaroussel($params)
    {
        $variables = $this->_display($params['id_product']);
        $this->context->smarty->assign($variables);
        return $this->display(__FILE__, 'views/templates/hook/frontCategoryCaroussel.tpl');
    }

    public function _display($productId){
        $product = new Product($productId);
        $languages = Language::getLanguages(true);
        $carrousels = $this->getCarrousels($product);
        //dump($carrousels);die;
        return array(
            'carrousels' => $carrousels,
            'languages' => $languages,
            'link' => $this->context->link,
            'image_baseurl' => $this->context->link->getBaseLink() . '/modules/' . $this->name . '/img/p/',
            'token' => Tools::getToken(false),
            'productId'=>$product->id,
            "img"=>array('png','jpg','jpeg'),
            "video"=>array('mp4','mpeg','avi'),
        );

    }

    public function getCarrousels(Product $product){
        if(!empty($product->product_carrousel))
        {
            $carrousels = explode("-",$product->product_carrousel);
            /**@var ProductCarrouselRepository $repository**/
            $repository =  $this->get('doctrine.orm.default_entity_manager')->getRepository('PrestaShop\Module\ProductCarrousel\Entity\ProductCarrousel');
            $allCarrousels=[];
            foreach ($carrousels as $carrousel){
                $allCarrousels[]= $repository->find((int)$carrousel)->toArray();
            }
            return $allCarrousels;
        }
       return null;
    }
}