<?php

namespace App\Services;

use Exception;
use App\Enums\HttpStatusCode;
use App\Http\Resources\ContextualCardResource;
use App\Repositories\ContextualCardRepository;

/**
 * Class ContextualCardService
 * @package App\Services
 */
class ContextualCardService extends ApiBaseService
{

    /**
     * @var ContextualCardRepository
     */
    protected $contextualCardRepository;


    /**
     * ContextualCardService constructor.
     * @param ContextualCardRepository $contextualCardRepository
     */
    public function __construct(ContextualCardRepository $contextualCardRepository)
    {
        $this->contextualCardRepository = $contextualCardRepository;
    }

    /**
     * Request for Contextual Card info
     *
     * @return mixed|string
     */
    public function getContextualCardInfo()
    {
        try {
            $data = $this->contextualCardRepository->getContextualCardInfo();
            $formatted_data = ContextualCardResource::collection($data);
            return $this->sendSuccessResponse(
                $formatted_data,
                'Contextual Card Info',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }
}
