<?php

namespace App\Http\Transformers;

use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\JsonApiSerializer;

class ChecklistSerializer extends JsonApiSerializer
{
    /**
     * serialize a collection
     *
     * @return void
     */
    public function collection($resourceKey, array $data)
    {
        return ['data' => $data];
    }

    /**
     * serialize an item
     *
     * @return void
     */
    public function item($resourceKey, array $data)
    {
        return ['data' => 'data'];
    }
    
    /**
     * Serialize the paginator.
     *
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $currentPage = (int)$paginator->getCurrentPage();
        $lastPage = (int)$paginator->getLastPage();

        $pagination = [
            'total' => (int)$paginator->getTotal(),
            'count' => (int)$paginator->getCount(),
            //'per_page' => (int)$paginator->getPerPage(),
            //'current_page' => $currentPage,
            //'total_pages' => $lastPage,
        ];

        $pagination['links'] = [];

        $pagination['links']['self'] = $paginator->getUrl($currentPage);
        $pagination['links']['first'] = $paginator->getUrl(1);

        if ($currentPage > 1) {
            $pagination['links']['prev'] = $paginator->getUrl($currentPage - 1);
        }else{
            $pagination['links']['prev'] = 'null';
        }

        if ($currentPage < $lastPage) {
            $pagination['links']['next'] = $paginator->getUrl($currentPage + 1);
        }else{
            $pagination['links']['next'] = 'null';
        }

        $pagination['links']['last'] = $paginator->getUrl($lastPage);

        return ['pagination' => $pagination];
    }

    /**
     * Serialize the meta.
     *
     * @param array $meta
     *
     * @return array
     */
    public function meta(array $meta)
    {
        if (empty($meta)) {
            return [];
        }

        $result['meta'] = $meta;

        if (array_key_exists('pagination', $result['meta'])) {
            $result['links'] = $result['meta']['pagination']['links'];
            $result['meta'] = $result['meta'] ['pagination'];
            unset($result['meta']['pagination']);
            unset($result['meta']['links']);
        }

        return $result;
    }
}


?>
