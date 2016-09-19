<?php

namespace NewsBundle\Controller;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
use NewsBundle\Entity\News;
use NewsBundle\Form\NewsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;



class NewsController extends Controller
{
    /**
     * @Route("/news",name="affi")
     * @Template("NewsBundle::news.html.twig")
     * 
     */
    public function getNews(){
        $em = $this->getDoctrine()->getManager();
        $rsm= new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata("NewsBundle:News",'niouze');
        $query=$em->createNativeQuery("select * from niouze",$rsm);
        $listeNews = $query->getResult();
       return array("niouze"=>$listeNews);
        
    }
    /**
     * @Route("/news/add",name="ajout")
     * @Template("NewsBundle::addNews.html.twig")
     * @param Request $request
     */
    public function addNews(Request $request){
        $formBuilder = $this->createFormBuilder(new News());
        $formBuilder->add("date");
        $formBuilder->add("titre");
        $formBuilder->add("sujet");
        $formBuilder->add("auteur");
        $formBuilder->add("save", SubmitType::class);
        $form = $formBuilder->getForm();
        return array("form" => $form->createView());
    }
     /**
     * @Route("/news/valid",name = "valid")
     */
    public function soumition(Request $request) {
        $niouse = new News();
        $formBuilder = $this->createFormBuilder($niouse);
        $formBuilder->add("date");
        $formBuilder->add("titre");
        $formBuilder->add("sujet");
        $formBuilder->add("auteur");
        $form = $formBuilder->getForm();
        
        if ($request->getMethod() == 'POST') {
            
            $form->handleRequest($request);            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($niouse);
            $em->flush();
            return $this->redirect($this->generateUrl('affi'));
        }
        return $this->redirect($this->generateUrl('ajout'));
    }
     /**
     * @Route("/news/modif/{id}",name="edit")
     * @Template("NewsBundle::addNews.html.twig")
     */
    public function edit($id){
        $em = $this->getDoctrine()->getManager();
        $niouze=$em->find("NewsBundle:News", $id);
        $formBuilder = $this->createFormBuilder($niouze);
        $formBuilder->add("date");
        $formBuilder->add("titre");
        $formBuilder->add("sujet");
        $formBuilder->add("auteur");
        $form = $formBuilder->getForm();
        return array("form" => $form->createView(),"id"=>$id);
    }
         /**
     * @Route("/news/update/{id}",name = "update")
     */
    public function update(Request $request,$id) {
        $em = $this->getDoctrine()->getManager();
        $niouze=$em->find("NewsBundle:News", $id);
        $formBuilder = $this->createFormBuilder($niouze);
        $formBuilder->add("date");
        $formBuilder->add("titre");
        $formBuilder->add("sujet");
        $formBuilder->add("auteur");
        $form = $formBuilder->getForm();
        
        if ($request->getMethod() == 'POST') {
            
            $form->handleRequest($request);            
            $em = $this->getDoctrine()->getEntityManager();
            $em->merge($niouze);
            $em->flush();
            return $this->redirect($this->generateUrl('affi'));
        }
        return $this->redirect($this->generateUrl('edit'));
    }
    /**
     * @Route("/news/delete/{id}",name="delete")
     */
    public function supprime($id){
        $em = $this->getDoctrine()->getManager();
        $niouze=$em->find("NewsBundle:News", $id);
        $em->remove($niouze);
        $em->flush();
        return $this->redirect($this->generateUrl('affi'));
    }
    /**
    * @Route("news/edit/{id}",name="editAnnonce")
    * @Template("NewsBundle::update.html.twig")
    */
   public function editAnnonce(News $id){
       return array("annonce" => $this->createForm(NewsType::class, $id)->createView(),"id"=>$id);
   }
   /**
    * @Route("news/edite")
    */
   public function editOnce(Request $request){
       return new Response("ok");
   }
       /**
     * @Route("/ajax")
     * @param type $param
     */
    public function ajax(Request $r) {
        $encoder = array(new XmlEncoder(),new JsonEncoder());
        $normalizer = array(new ObjectNormalizer());
        $serailizer = new Serializer($normalizer,$encoder);
        if($r->isXmlHttpRequest()){
            $ann = $this->getDoctrine()->getRepository('PticoinBundle:Annonce')->find(1);
            $a = $serailizer->serialize($ann, 'json');
            
            return new Response($a);
        }
        return new Response("ok pour le post");
    }

}