<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;
class SendTokenDecorator  implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated
    ) {}


    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['SendTokenResetPassword'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'response' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['EmailtoSendQuery'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'userEmail' => [
                    'type' => 'string',
                    'example' => 'david',
                ]
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'email send token',
            post: new Model\Operation(
                operationId: 'sendTokenResetPassword',
                tags: ['sendTokenResetPassword'],
                responses: [
                    '200' => [
                        'description' => 'respone email find or not',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/SendTokenResetPassword',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'send query to reset password',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token send to email',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/EmailtoSendQuery',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/api/sendToken', $pathItem);

        return $openApi;
    }
}
