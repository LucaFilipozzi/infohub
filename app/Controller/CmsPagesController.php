<?php

class CmsPagesController extends AppController {
    //public $helpers = array('Html', 'Form');
    
    public function index() {
        $urlSize = sizeof($this->request->params['pass']);
        if($urlSize>0){
            $pageSlug = $this->request->params['pass'][$urlSize-1];
        }else{
            $pageSlug = 'index';
        }
        
        // load page based on slug
        $page = $this->CmsPage->find('all', array(
            'conditions'=>array('slug'=>$pageSlug),
            'limit'=>1
        ));
        
        if(sizeof($page[0])==0){
            // redirect user to home page if page is not found
            header('location: /');
            exit;
        }else{
            // put page into variable to be passed on to view
            $page = $page[0]['CmsPage'];
            if($page['redirectURL'] != ''){
                header('location: '.$page['redirectURL']);
                exit;
            }
        }
        
        // load template file
        App::uses('CmsTemplate', 'Model');
        $objTemplates = new CmsTemplate();
        $templateResults = $objTemplates->find('all', array(
            'conditions'=>array('id'=>$page['templateID']),
            'fields'=>'file'
        ));
        $templateFile = str_replace('.ctp', '', $templateResults[0]['CmsTemplate']['file']);
        
        $this->loadModel('CmsPage');
        $objCms = new CmsPage();
        $pageNav = $objCms->listPages(0,1);
        $page['body'] = $objCms->loadCmsBody($page['id'], $page['body'], $this->viewVars['isAdmin']);
        
        $this->set('page', $page);
        $this->set('pageNav', $pageNav);
        $this -> render('/Cmspages/'.$templateFile);
    }
    
    public function updatePage(){
        if ($this->request->data) {
            App::uses('Helpers', 'Model');
            $pgID = Helpers::getInt($this->request->data['pgID']);
            $pgBody = $this->request->data['pgBody'];
            
            $this->loadModel('CmsPage');
            $objCmsPage = new CmsPage();
            $page = $objCmsPage->findById($pgID);
            //print_r($page['CmsPage']['body']);
            //print_r($page);exit;
            if (!$page) {
                throw new NotFoundException(__('Invalid page'));
            }else{
                $objCmsPage->id = $pgID;
                $objCmsPage->set('body', $pgBody);
                $objCmsPage->save();
            }
        }
        exit;
    }
}