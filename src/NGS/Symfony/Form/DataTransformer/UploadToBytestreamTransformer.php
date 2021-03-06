<?php
namespace NGS\Symfony\Form\DataTransformer;

use NGS\ByteStream;
use NGS\ModelBundle\Form\DataTransformer\InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadToBytestreamTransformer implements DataTransformerInterface
{
/*    private $form;
    private $property;
*/
   /* public function __construct(FormTypeInterface $form, $property)
    {
        $this->form = $form;
        $this->property = $property;
    }*/

    public function transform($value)
    {
        if ($value === null)
            return null;
        else if($value instanceof ByteStream)
            return $value;
        else
            throw new TransformationFailedException('Could not transform value from "'.\NGS\Utils::gettype($value).'" to string');
    }

    public function reverseTransform($value)
    {
        if($value === null)
            return null;

        try {
            if($value instanceof UploadedFile) {
                $contents = '';
                $file = $value->openFile();
                while (!$file->eof())
                    $contents .= $file->fgets();

                return new ByteStream($contents, false);
            }
            else if(is_string($value)) {
                return new ByteStream($value);
            }
            else {
                throw new TransformationFailedException('Could not transform value from "'.\NGS\Utils::gettype($value).'" to NGS\Bytestream');
            }
        }
        catch(\InvalidArgumentException $ex) {
            throw new TransformationFailedException($ex->getMessage());
        }
    }
}
