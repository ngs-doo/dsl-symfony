<?php
namespace NGS\Symfony\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use NGS\Symfony\Extension\Messenger;
use Symfony\Component\Form\Extension\Core\Type\FormType;

abstract class BaseController extends Controller
{
    /**
     * Redirection helper
     */
    public function redirectRoute($route, $params=array())
    {
        return $this->redirect($this->generateUrl($route, $params));
    }

    /**
     * Validation helper
     * @return type
     */
    public function validate($form, $onValidCallback=null)
    {
        if($form instanceof FormType)
            $form = $this->createForm($form);

        $request = $this->getRequest();
        if($request->getMethod()==='POST') {
            try {
                $form->bind($request);

                if($form->isValid()) {
                    if(!$onValidCallback)
                        return array('form' => $form->createView());
                    try {
                        return call_user_func_array($onValidCallback, array($form));
                    }
                    catch (\Exception $ex) {
                        $this->get('messenger')->error($ex->getMessage());
                    }
                }
                else {
                    $this->get('messenger')->error('Check your form for errors');
                    // @todo temp debug, maybe error list
                    $this->get('messenger')->error($form->getErrorsAsString());
                }
            }
            catch (\Exception $ex) {
                // error in binding - probably error in constructor
                $this->get('messenger')->error('Check your form for errors');
                $this->get('messenger')->error($ex->getMessage());
            }
        }
        return array('form' => $form->createView());
    }
}
