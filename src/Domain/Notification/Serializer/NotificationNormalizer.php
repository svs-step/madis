<?php

namespace App\Domain\Notification\Serializer;

use App\Domain\Registry\Model\Contractor;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\Registry\Model\Proof;
use App\Domain\Registry\Model\Request;
use App\Domain\Registry\Model\Treatment;
use App\Domain\Registry\Model\Violation;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class NotificationNormalizer extends ObjectNormalizer
{
    private $maxDepthHandler;
    private $objectClassResolver;

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!isset($context['cache_key'])) {
            $context['cache_key'] = $this->getCacheKey($format, $context);
        }

        $this->validateCallbackContext($context);

        if ($this->isCircularReference($object, $context)) {
            return $this->handleCircularReference($object, $format, $context);
        }

        $data               = [];
        $stack              = [];
        $attributes         = $this->getAttributes($object, $format, $context);
        $class              = $this->objectClassResolver ? ($this->objectClassResolver)($object) : \get_class($object);
        $attributesMetadata = $this->classMetadataFactory ? $this->classMetadataFactory->getMetadataFor($class)->getAttributesMetadata() : null;
        if (isset($context[self::MAX_DEPTH_HANDLER])) {
            $maxDepthHandler = $context[self::MAX_DEPTH_HANDLER];
            if (!\is_callable($maxDepthHandler)) {
                throw new \Symfony\Component\PropertyAccess\Exception\InvalidArgumentException(sprintf('The "%s" given in the context is not callable.', self::MAX_DEPTH_HANDLER));
            }
        } else {
            // already validated in constructor resp by type declaration of setMaxDepthHandler
            $maxDepthHandler = $this->defaultContext[self::MAX_DEPTH_HANDLER] ?? $this->maxDepthHandler;
        }

        foreach ($attributes as $attribute) {
            $maxDepthReached = false;
            if (null !== $attributesMetadata && ($maxDepthReached = $this->isMaxDepthReached($attributesMetadata, $class, $attribute, $context)) && !$maxDepthHandler) {
                continue;
            }

            try {
                $attributeValue = $this->getAttributeValue($object, $attribute, $format, $context);
            } catch (AccessException $e) {
                if (sprintf('The property "%s::$%s" is not initialized.', \get_class($object), $attribute) === $e->getMessage()) {
                    continue;
                }
                if (($p = $e->getPrevious()) && 'Error' === \get_class($p) && $this->isUninitializedValueError($p)) {
                    continue;
                }

                throw $e;
            } catch (\Error $e) {
                if ($this->isUninitializedValueError($e)) {
                    continue;
                }

                throw $e;
            }

            if ($maxDepthReached) {
                $attributeValue = $maxDepthHandler($attributeValue, $object, $attribute, $format, $context);
            }

            $attributeValue = $this->applyCallbacks($attributeValue, $object, $attribute, $format, $context);

            if (null !== $attributeValue && !is_scalar($attributeValue)) {
                $stack[$attribute] = $attributeValue;
            }

            $data = $this->updateData($data, $attribute, $attributeValue, $class, $format, $context);
        }

        foreach ($stack as $attribute => $attributeValue) {
            if (!$this->serializer instanceof NormalizerInterface) {
                throw new LogicException(sprintf('Cannot normalize attribute "%s" because the injected serializer is not a normalizer.', $attribute));
            }

            $data = $this->updateData($data, $attribute, $this->serializer->normalize($attributeValue, $format, $this->createChildContext($context, $attribute, $format)), $class, $format, $context);
        }

        if (isset($context[self::PRESERVE_EMPTY_OBJECTS]) && !\count($data)) {
            return new \ArrayObject();
        }

        return $data;
    }

    private function isUninitializedValueError(\Error $e): bool
    {
        return \PHP_VERSION_ID >= 70400
            && str_starts_with($e->getMessage(), 'Typed property')
            && str_ends_with($e->getMessage(), 'must not be accessed before initialization');
    }

    /**
     * Sets an attribute and apply the name converter if necessary.
     *
     * @param mixed $attributeValue
     */
    private function updateData(array $data, string $attribute, $attributeValue, string $class, ?string $format, array $context): array
    {
        if (null === $attributeValue && ($context[self::SKIP_NULL_VALUES] ?? $this->defaultContext[self::SKIP_NULL_VALUES] ?? false)) {
            return $data;
        }

        if ($this->nameConverter) {
            $attribute = $this->nameConverter->normalize($attribute/* , $class, $format, $context */);
        }

        $data[$attribute] = $attributeValue;

        return $data;
    }

    private function isMaxDepthReached(array $attributesMetadata, string $class, string $attribute, array &$context): bool
    {
        $key = 'maxDepth';
        if (!isset($context[$key])) {
            $context[$key] = 1;

            return false;
        }

        if (1 === $context[$key]) {
            return true;
        }

        ++$context[$key];

        return false;
    }

    private function getCacheKey(?string $format, array $context)
    {
        foreach ($context[self::EXCLUDE_FROM_CACHE_KEY] ?? $this->defaultContext[self::EXCLUDE_FROM_CACHE_KEY] as $key) {
            unset($context[$key]);
        }
        unset($context[self::EXCLUDE_FROM_CACHE_KEY]);
        unset($context[self::OBJECT_TO_POPULATE]);
        unset($context['cache_key']); // avoid artificially different keys

        try {
            return md5($format . serialize([
                    'context'   => $context,
                    'ignored'   => $this->ignoredAttributes,
                    'camelized' => $this->camelizedAttributes,
                ]));
        } catch (\Exception $exception) {
            // The context cannot be serialized, skip the cache
            return false;
        }
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof Treatment ||
            $data instanceof Contractor ||
            $data instanceof Mesurement ||
            $data instanceof Proof ||
            $data instanceof Request ||
            $data instanceof Violation
        ;
    }
}
