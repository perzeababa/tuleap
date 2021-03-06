<?php
/**
 * Copyright (c) Enalean, 2020-Present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tuleap\OAuth2Server\ProjectAdmin;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Mockery as M;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Tuleap\Http\HTTPFactoryBuilder;
use Tuleap\Http\Response\RedirectWithFeedbackFactory;
use Tuleap\Http\Server\NullServerRequest;
use Tuleap\Layout\Feedback\NewFeedback;
use Tuleap\OAuth2Server\App\AppDao;
use Tuleap\OAuth2Server\App\OAuth2App;
use Tuleap\Test\Builders\UserTestBuilder;

final class EditAppControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var EditAppController
     */
    private $controller;
    /**
     * @var M\LegacyMockInterface|M\MockInterface|RedirectWithFeedbackFactory
     */
    private $redirector;
    /**
     * @var M\LegacyMockInterface|M\MockInterface|AppDao
     */
    private $app_dao;

    protected function setUp(): void
    {
        $this->redirector = M::mock(RedirectWithFeedbackFactory::class);
        $this->app_dao    = M::mock(AppDao::class);
        $csrf_token       = M::mock(\CSRFSynchronizerToken::class);
        $this->controller = new EditAppController(
            HTTPFactoryBuilder::responseFactory(),
            $this->redirector,
            $this->app_dao,
            $csrf_token,
            M::mock(EmitterInterface::class)
        );
        $csrf_token->shouldReceive('check');
    }

    public function testGetUrl(): void
    {
        $project = new \Project(['group_id' => 102]);
        $this->assertSame('/plugins/oauth2_server/project/102/admin/edit-app', EditAppController::getUrl($project));
    }

    /**
     * @dataProvider dataProviderInvalidBody
     * @param array|null $parsed_body
     */
    public function testHandleRedirectsWithErrorWhenDataIsInvalid($parsed_body): void
    {
        $request  = $this->buildRequest()->withParsedBody($parsed_body);
        $response = HTTPFactoryBuilder::responseFactory()->createResponse(302);
        $this->redirector->shouldReceive('createResponseForUser')
            ->with(M::type(\PFUser::class), '/plugins/oauth2_server/project/102/admin', M::type(NewFeedback::class))
            ->once()
            ->andReturn($response);
        $this->app_dao->shouldNotReceive('updateApp');

        $this->assertSame($response, $this->controller->handle($request));
    }

    public function dataProviderInvalidBody(): array
    {
        return [
            'No body'              => [null],
            'Missing app id'       => [['not_app_id' => '12']],
            'Missing app name'     => [['app_id' => '72']],
            'Missing redirect URI' => [['app_id' => '72', 'name' => 'Jenkins']]
        ];
    }

    /**
     * @dataProvider dataProviderValidBody
     */
    public function testHandleUpdatesAppAndRedirects(array $parsed_body): void
    {
        $request = $this->buildRequest()->withParsedBody($parsed_body);
        $this->app_dao->shouldReceive('updateApp')
            ->once()
            ->with(M::type(OAuth2App::class));

        $response = $this->controller->handle($request);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/plugins/oauth2_server/project/102/admin', $response->getHeaderLine('Location'));
    }

    public function dataProviderValidBody(): array
    {
        return [
            'Missing PKCE is assumed to be false' => [
                ['app_id' => '72', 'name' => 'Jenkins', 'redirect_uri' => 'https://example.com/redirect']
            ],
            'Present PKCE is true'                => [
                ['app_id' => '72', 'name' => 'Jenkins', 'redirect_uri' => 'https://example.com/redirect', 'use_pkce' => '1']
            ],
        ];
    }

    private function buildRequest(): ServerRequestInterface
    {
        return (new NullServerRequest())->withAttribute(\Project::class, new \Project(['group_id' => 102]))
            ->withAttribute(\PFUser::class, UserTestBuilder::aUser()->build());
    }
}
