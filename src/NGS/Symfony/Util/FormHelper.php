<?php
namespace NGS\Symfony\Util;

use Symfony\Component\Form\FormInterface;

abstract class FormHelper
{
    /**
     * Helper for extracting errors from form child elements
     */
    public static function getErrors(FormInterface $form)
    {
        $errors = array();
        foreach($form->all() as $field) {
            $childErrors = self::getErrors($field);
            if($childErrors) {
                $errors[$form->getName().'_'.$field->getName()][] = $childErrors;
            }
        }
        foreach($form->getErrors() as $error) {;
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    public static function getErrorMessages(FormInterface $form) {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach($parameters as $var => $value){
                $template = str_replace($var, $value, $template);
            }

            $errors[$key] = $template;
        }
        if ($form->hasChildren()) {
            foreach ($form->getChildren() as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = self::getErrorMessages($child);
                }
            }
        }

        return $errors;
    }
}
