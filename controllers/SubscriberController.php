<?php
require_once 'Mage/Newsletter/controllers/SubscriberController.php';
class Cammino_Newsletterajax_SubscriberController extends Mage_Newsletter_SubscriberController {

    public function newAction()
    {
        $response = array();
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');
            
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $message = $this->__('Please enter a valid email address.');
                $response['status'] = 'ERROR';
                $response['message'] = $message;
            }else if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && !$customerSession->isLoggedIn()) {
                $message = $this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl());
                $response['status'] = 'ERROR';
                $response['message'] = $message;
            }else{
                $ownerId = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->loadByEmail($email)
                    ->getId();

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);

                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    $message = $this->__('This email address is already assigned to another user.');
                    $response['status'] = 'WARNING';
                    $response['message'] = $message;
                }else if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $message = $this->__('Confirmation request has been sent.');
                    $response['status'] = 'SUCCESS';
                    $response['message'] = $message;
                } else {
                    $message = $this->__('Thank you for your subscription.');
                    $response['status'] = 'SUCCESS';
                    $response['message'] = $message;
                }
            }
        }else{
            $message = "Não é um post, ou não existe o campo email";
            $response['status'] = 'ERROR';
            $response['message'] = $message;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
}