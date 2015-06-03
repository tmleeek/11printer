<?php

class TM_FireCheckout_Model_Observer
{
    public function setCustomerComment($data)
    {
        $comment = trim(Mage::getSingleton('customer/session')->getOrderCustomerComment());
        if (!empty($comment)) {
            $data['order']->setCustomerOrderComment($comment);
            $data['order']->addStatusHistoryComment($comment)
                ->setIsVisibleOnFront(true)
                ->setIsCustomerNotified(false);
        }
    }

    public function unsetCustomerComment()
    {
        Mage::getSingleton('customer/session')->setOrderCustomerComment(null);
    }

    public function addToCartComplete(Varien_Event_Observer $observer)
    {
        $generalConfig = Mage::getStoreConfig('firecheckout/general');
        if ($generalConfig['enabled'] && $generalConfig['redirect_to_checkout']) {
            $observer->getResponse()
                ->setRedirect(
                    Mage::helper('firecheckout/url')->getCheckoutUrl()
                );
            Mage::getSingleton('checkout/session')->setNoCartRedirect(true);
        }
    }

    /**
     * Called before captcha check
     */
    public function setCheckoutMethod($observer)
    {
        $data  = $observer->getControllerAction()->getRequest()->getPost('billing', array());
        $checkout = Mage::getSingleton('firecheckout/type_standard');
        $quote = $checkout->getQuote();
        if (isset($data['register_account']) && $data['register_account']) {
            $quote->setCheckoutMethod(TM_FireCheckout_Model_Type_Standard::METHOD_REGISTER);
        } else if ($checkout->getCustomerSession()->isLoggedIn()) {
            $quote->setCheckoutMethod(TM_FireCheckout_Model_Type_Standard::METHOD_CUSTOMER);
        } else {
            $quote->setCheckoutMethod(TM_FireCheckout_Model_Type_Standard::METHOD_GUEST);
        }
        return $this;
    }

/* See Mage_Captcha_Model_Observer for the source of the next methods */

    /**
     * Check Captcha On Forgot Password Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkForgotpassword($observer)
    {
        $formId = 'user_forgotpassword';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
//            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
//                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
//                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
//                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
//            }

            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $result = array(
                    'success' => false,
                    'error' => Mage::helper('captcha')->__('Incorrect CAPTCHA.')
                );
                $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
        return $this;
    }

    /**
     * Check Captcha On User Login Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserLogin($observer)
    {
        $formId = 'user_login';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $controller = $observer->getControllerAction();
        $loginParams = $controller->getRequest()->getPost('login');
        $login = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($captchaModel->isRequired($login)) {
            $word = $this->_getCaptchaString($controller->getRequest(), $formId);
            if (!$captchaModel->isCorrect($word)) {
//                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setUsername($login);
//                $beforeUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
//                $url =  $beforeUrl ? $beforeUrl : Mage::helper('customer')->getLoginUrl();
//                $controller->getResponse()->setRedirect($url);

                $result = array(
                    'success' => false,
                    'error' => Mage::helper('captcha')->__('Incorrect CAPTCHA.')
                );
                $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }

    /**
     * Get Captcha String
     *
     * @param Varien_Object $request
     * @param string $formId
     * @return string
     */
    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }
}
