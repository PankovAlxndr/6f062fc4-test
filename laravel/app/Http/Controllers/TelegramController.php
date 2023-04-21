<?php

namespace App\Http\Controllers;

use App\Dto\Telegram\AuthDto;
use App\Http\Requests\Telegram\SigInRequest;
use App\Services\AvatarUploader;
use App\Services\Telegram\CheckAuthorizationService;
use App\Services\UserService;

class TelegramController extends Controller
{
    public function __invoke(
        SigInRequest $request,
        CheckAuthorizationService $authService,
        AvatarUploader $avatarUploader
    ) {
        $authDto = AuthDto::createFromRequest($request);
        $authService->check($authDto);

        $userService = new UserService($authDto, $avatarUploader);
        $user = $userService->isExistUser();
        if (! $user) {
            $user = $userService->createUser();
        }
        $userService->authUser($user);

        return redirect()->route('users.index');
    }
}
