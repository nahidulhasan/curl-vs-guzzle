<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationListCollection extends ResourceCollection
{

    private $pagination;

    public function __construct($resource)
    {
        $this->pagination = [
            'total' => $resource->total(),
            'current_page' => $resource->currentPage(),
            'next_page' => ($resource->hasMorePages()) ? $resource->currentPage() + 1 : null
        ];

        $resource = $resource->getCollection();

        parent::__construct($resource);
    }


    public function toArray($request)
    {
        return [
            'notifications' => NotificationListResource::collection($this->collection),
            'pagination' => $this->pagination
        ];
    }
}
