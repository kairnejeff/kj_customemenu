<?php

namespace PrestaShop\Module\ProductCarrousel\Controller;
use Db;
use PrestaShop\Module\ProductCarrousel\Entity\ProductCarrousel;
use PrestaShop\Module\ProductCarrousel\Form\ProductCarrouselType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProductCarrouselController extends FrameworkBundleAdminController
{
    private $filePath;

    /**
     * ProductCarrouselType constructor.
     */
    public function __construct()
    {
        $this->filePath = _PS_MODULE_DIR_ . "kj_productcarrousel" . DIRECTORY_SEPARATOR . "img". DIRECTORY_SEPARATOR ."p";
        parent::__construct();
    }

    public function addAction(Request $request,int $productId){
        $carrousel = new ProductCarrousel();
        $carrousel->setProductId((int)$productId);
        $em = $this->get('doctrine.orm.default_entity_manager');
        $form = $this->createForm(ProductCarrouselType::class,$carrousel)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $fileName=$file->getClientOriginalName();
            if($file){
                try {
                    $file->move(
                        $this->filePath,
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }

            $nextPosition = $em->getRepository('PrestaShop\Module\ProductCarrousel\Entity\ProductCarrousel')->getNextPosition();
            $carrousel->setPosition($nextPosition+1);
            $carrousel->setActive(true);
            $em->persist($carrousel);
            $em->flush();
            $this->addProductCarrousel($productId,$carrousel->getId());
            return $this->redirect($this->generateUrl('admin_product_form',["id"=>$productId]));
        }
        return $this->render('@Modules/kj_productcarrousel/views/templates/admin/form.html.twig', [
            'form' => $form->createView()
        ]);

    }

    public function editAction(){

    }

    public function deleteAction(){

    }

    public function addProductCarrousel($productId, $carrouselId){
        $sqlGetIds= "Select custom_field_file from ". _DB_PREFIX_ . "product  WHERE id_product=".$productId;
        $ids= Db::getInstance()->execute($sqlGetIds);
        if(!empty($ids)){
            $carrouselId=$ids.",".$carrouselId;
        }
        $sql= "UPDATE ". _DB_PREFIX_ . "product SET custom_field_file = '".$carrouselId."' WHERE id_product=".$productId;
        Db::getInstance()->execute($sql);
    }

}