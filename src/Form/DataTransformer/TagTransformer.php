<?php

namespace App\Form\DataTransformer;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use App\Entity\Tag;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagTransformer implements DataTransformerInterface
{
    public function __construct(private TagRepository $tagRepository)
    {

    }

    /**
     * Преобразует коллекцию Tag в строку для формы
     *
     * @param Collection|Tag[]|null $value
     */
    public function transform($value): string
    {
        if (null === $value || $value->isEmpty()) {
            return '';
        }

        $names = [];

        foreach ($value as $tag) {
            $names[] = $tag->getName();
        }

        return implode(', ', $names);
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $value
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform(mixed $value = null): ArrayCollection
    {
        // no issue number? It's optional, so that's ok
        if (!$value) {
            return new ArrayCollection();
        }

        $items = explode(',', $value);
        $items = array_map('trim', $items);
        $items = array_unique($items);

        $tags = new ArrayCollection();

        foreach ($items as $item) {
            $tag = $this->tagRepository->findOneBy(['name' => $item]);
            if(!$tag){
                $tag = (new Tag())->setName($item);
            }
            $tags->add($tag);
        }

        return $tags;
    }
}
