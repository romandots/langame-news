<?php

namespace App\Services\Users;

use App\DTO\CollectionResponse;
use App\DTO\FetchUsers;
use App\Repositories\UserRepository;
use Psr\Log\LoggerInterface;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected LoggerInterface $logger,
    ) {
    }

    public function fetch(FetchUsers $fetchUsers): CollectionResponse
    {
        ['items' => $items, 'total' => $total] = $this->userRepository->search($fetchUsers->page, $fetchUsers->perPage);

        $items->transform(function ($item) {
            return $item->toArray() + [
                    'html' => view('users.entry', ['user' => $item])->render(),
                ];
        });

        $this->logger->debug('Users fetch performed', [
            'page' => $fetchUsers->page,
            'items_per_page' => $fetchUsers->perPage,
            'results_count' => $total,
        ]);

        return new CollectionResponse(
            data: $items->toArray(),
            last_page: (int)ceil($total / $fetchUsers->perPage),
            current_page: $fetchUsers->page,
        );
    }
}
