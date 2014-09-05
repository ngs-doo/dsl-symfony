<?php
namespace NGS\Symfony\Controller;

use Settings\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TemplateController extends Controller
{
    /**
     * @Route("/", name="template_save")
     * @Method({"POST"})
     */
    public function saveTemplateAction(Request $request)
    {
        $content = array();
        $uri = $request->get('URI');
        $template = $uri ? Template::find($uri) : new Template();
        $template->Title = $request->get('Title');
        $template->Name = $request->get('Name');
        parse_str($request->get('Content'), $content);
        $template->Content = base64_encode(json_encode($content));
        $template->persist();
        $this->get('messenger')->info('Template "'.$template->Title.'" saved');
        return array('item' => $template);
    }

    /**
     * @Route("/")
     * @Method({"GET"})
     */
    public function findTemplatesAction()
    {

    }
}
