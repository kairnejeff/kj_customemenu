<?php

namespace PrestaShop\Module\ProductCarrousel\Controller;
use Db;
use PrestaShop\Module\ProductCarrousel\Entity\ProductCarrousel;
use PrestaShop\Module\ProductCarrousel\Form\ProductCarrouselType;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProductCarrouselController extends FrameworkBundleAdminController
{
    private $filePath;
    private $em;
    private $repository;

    /**
     * ProductCarrouselType constructor.
     */
    public function __construct($em,$repository)
    {
        $this->filePath = _PS_MODULE_DIR_ . "kj_productcarrousel" . DIRECTORY_SEPARATOR . "img". DIRECTORY_SEPARATOR ."p";
        $this->em=$em;
        $this->repository=$repository;
        parent::__construct();
    }

    public function addAction(Request $request,int $productId){
        $carrousel = new ProductCarrousel();
        $carrousel->setProductId((int)$productId);
        $form = $this->createForm(ProductCarrouselType::class,$carrousel)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nextPosition = $this->repository->getNextPosition($productId);
            $carrousel->setPosition($nextPosition+1);
            $carrousel->setActive(true);
            $this->em->persist($carrousel);
            $this->em->flush();
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $fileName=$productId."-".$carrousel->getId().".".$file->getClientOriginalExtension();
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
            $carrousel->setFileName($fileName);
            $this->em->persist($carrousel);
            $this->em->flush();
            $this->addProductCarrousel($productId);
            return $this->redirect($this->generateUrl('admin_product_form',["id"=>$productId]));
        }
        return $this->render('@Modules/kj_productcarrousel/views/templates/admin/form.html.twig', [
            'form' => $form->createView()
        ]);

    }

    public function editAction(Request $request,int $productId,int $carrouselId){
        $carrousel = $this->repository->findOneBy(['id' => $carrouselId]);
        $form = $this->createForm(ProductCarrouselType::class,$carrousel)->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->em->persist($carrousel);
            $this->em->flush();
            $this->addProductCarrousel($productId);
            return $this->redirect($this->generateUrl('admin_product_form',["id"=>$productId]));
        }
        return $this->render('@Modules/kj_productcarrousel/views/templates/admin/form.html.twig', [
            'form' => $form->createView()
        ]);

    }

    public function deleteAction(Request $request,int $productId,int $carrouselId){
        $carrousel=$this->repository->find($carrouselId);
        $file_name=$carrousel->getFileName();
        $this->em->remove($carrousel);
        $this->em->flush();
        $this->addProductCarrousel($productId);
        unlink($this->filePath. DIRECTORY_SEPARATOR .$file_name);
        $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
        return $this->redirect($this->generateUrl('admin_product_form',["id"=>$productId]));
    }


    public function addProductCarrousel($productId){
        /*$sqlGetIds= "Select product_carrousel from ". _DB_PREFIX_ . "product  WHERE id_product=".$productId;
        $ids= Db::getInstance()->executeS($sqlGetIds);
        $carrouselIds=$carrouselId;
        if(!empty($ids[0]['product_carrousel'])&&$ids[0]['product_carrousel']!=NULL){
            $carrouselIds=$ids[0]['product_carrousel']."-".$carrouselId;
        }*/
        $carrouselIds=$this->repository->getCarousselIds($productId);
        $stringIds="";
        foreach ($carrouselIds as $carrouselId){
            $stringIds.=$carrouselId['id'].'-';
        }
        $stringIds=rtrim($stringIds,'-');
        $sql= "UPDATE ". _DB_PREFIX_ . "product SET product_carrousel = '".$stringIds."' WHERE id_product=".$productId;
        Db::getInstance()->execute($sql);
    }

}