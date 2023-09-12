<?php
namespace App\Form\Transformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NoScriptTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        if (false !== stripos($value, '<script')) {
            throw new TransformationFailedException('Le contenu ne doit pas contenir de balises <script>.');
        }

        return $value;
    }
}
