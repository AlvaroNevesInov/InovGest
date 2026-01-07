<?php



namespace App\Http\Responses;



use Illuminate\Http\JsonResponse;

use Laravel\Fortify\Contracts\PasswordConfirmedResponse as PasswordConfirmedResponseContract;



class PasswordConfirmedResponse implements PasswordConfirmedResponseContract

{

    /**

     * Create an HTTP response that represents the object.

     */

    public function toResponse($request)

    {

        return $request->wantsJson()

            ? new JsonResponse('', 201)

            : back()->with('status', 'password-confirmed');

    }

}
