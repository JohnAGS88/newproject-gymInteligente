<?php

namespace App\Http\Requests\Membership;

use App\Http\Requests\ApiRequest;

class UnfreezeMembershipRequest extends ApiRequest
{
    /**
     * Sin cuerpo: la autorización se resuelve en la Policy `unfreeze`.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}