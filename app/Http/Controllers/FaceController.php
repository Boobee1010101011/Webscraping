<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\IssuesJwt;
use App\Http\Requests\Auth\FaceLoginRequest;
use App\Models\User;
use App\Services\FaceIdService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class FaceController extends Controller
{
    use IssuesJwt;

    public function __construct(private FaceIdService $face) {}

    /** Does the signed-in user have Face ID set up? `enabled: false` tells the page to hide the feature. */
    public function status(Request $request): JsonResponse
    {
        if (! $this->face->enabled()) {
            return response()->json(['enabled' => false]);
        }

        try {
            return response()->json(['enabled' => true, 'has_face' => $this->face->hasFace($request->user())]);
        } catch (RuntimeException|RequestException) {
            return response()->json(['enabled' => false]);
        }
    }

    /** Link to the scan page (opened in a popup) that registers the signed-in user's face. */
    public function setup(Request $request): JsonResponse
    {
        try {
            return response()->json(['url' => $this->face->setupUrl($request->user())]);
        } catch (RuntimeException|RequestException) {
            return response()->json(['message' => 'Face ID is not available right now.'], 502);
        }
    }

    /** Sign in with the one-time code the face scan page hands back. */
    public function login(FaceLoginRequest $request): JsonResponse
    {
        try {
            $userId = $this->face->userIdForLoginCode($request->input('code'));
        } catch (RuntimeException) {
            return response()->json(['message' => 'Face ID is not available right now.'], 502);
        }

        if (! $userId) {
            return response()->json(['message' => 'Face login failed or expired. Please try again.'], 401);
        }

        $user = User::find($userId);

        if (! $user) {
            return response()->json(['message' => 'No dashboard account matches this face.'], 401);
        }

        return $this->tokenResponse($user, 'face');
    }
}
