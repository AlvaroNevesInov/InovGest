<?php



namespace App\Http\Responses;



use Inertia\Inertia;

use Laravel\Fortify\Contracts\ConfirmPasswordViewResponse as ConfirmPasswordViewResponseContract;



class ConfirmPasswordViewResponse implements ConfirmPasswordViewResponseContract

{

    /**

     * Create an HTTP response that represents the object.

     */

    public function toResponse($request)

    {

        return Inertia::render('Auth/ConfirmPassword')->toResponse($request);

    }

}
