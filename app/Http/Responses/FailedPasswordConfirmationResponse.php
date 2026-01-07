<?php



namespace App\Http\Responses;



use Illuminate\Validation\ValidationException;

use Laravel\Fortify\Contracts\FailedPasswordConfirmationResponse as FailedPasswordConfirmationResponseContract;



class FailedPasswordConfirmationResponse implements FailedPasswordConfirmationResponseContract

{

    /**

     * Create an HTTP response that represents the object.

     */

    public function toResponse($request)

    {

        $message = __('The password is incorrect.');



        if ($request->wantsJson()) {

            throw ValidationException::withMessages([

                'password' => [$message],

            ]);

        }



        return back()->withErrors(['password' => $message]);

    }

}
